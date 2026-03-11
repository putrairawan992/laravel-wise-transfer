const tf = require('@tensorflow/tfjs');
require('@tensorflow/tfjs-backend-wasm');
const faceapi = require('@vladmandic/face-api/dist/face-api.node-wasm.js');
const { Canvas, Image, ImageData, loadImage } = require('canvas');
const crypto = require('crypto');
const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');
const ENV_PATH = path.join(__dirname, '../../.env');
require('dotenv').config({ path: ENV_PATH });

// Setup Environment
faceapi.env.monkeyPatch({ Canvas, Image, ImageData });

// Constants
const MODELS_PATH = path.join(__dirname, '../../public/models');

function slugify(value) {
    return String(value)
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
}

function buildImportEmail(folder, userName) {
    const slug = slugify(userName).slice(0, 50) || 'user';
    const hash = crypto.createHash('sha1').update(String(folder)).digest('hex').slice(0, 10);
    return `${slug}.${hash}@import.local`;
}

async function main() {
    const datasetPath = process.argv[2];
    
    if (!datasetPath) {
        console.error("❌ Usage: node index.js <dataset_path>");
        process.exit(1);
    }

    if (!fs.existsSync(datasetPath)) {
        console.error(`❌ Dataset path not found: ${datasetPath}`);
        process.exit(1);
    }

    console.log("🚀 Starting Face Importer Worker...");
    console.log(`📂 Dataset: ${datasetPath}`);
    console.log(`🧠 Models: ${MODELS_PATH}`);
    console.log(`🧾 Env: ${ENV_PATH} (${fs.existsSync(ENV_PATH) ? 'found' : 'missing'})`);

    // 1. Connect DB
    const dbHost = process.env.DB_HOST || '127.0.0.1';
    const dbUser = process.env.DB_USERNAME || 'root';
    const dbDatabase = process.env.DB_DATABASE || 'laravel_transfer';
    const connection = await mysql.createConnection({
        host: dbHost,
        user: dbUser,
        password: process.env.DB_PASSWORD || '',
        database: dbDatabase
    });
    console.log(`🗄️ DB: ${dbUser}@${dbHost}/${dbDatabase}`);
    console.log("✅ DB Connected");

    // 2. Load Models
    await tf.setBackend('wasm');
    await tf.ready();
    await faceapi.nets.tinyFaceDetector.loadFromDisk(MODELS_PATH);
    await faceapi.nets.faceLandmark68Net.loadFromDisk(MODELS_PATH);
    await faceapi.nets.faceRecognitionNet.loadFromDisk(MODELS_PATH);
    console.log("✅ Models Loaded");

    // 3. Scan Folders
    let datasetRoot = datasetPath;
    let folders = fs
        .readdirSync(datasetPath)
        .filter(f => fs.statSync(path.join(datasetPath, f)).isDirectory());

    if (folders.length === 0) {
        datasetRoot = path.dirname(datasetPath);
        folders = [path.basename(datasetPath)];
    }

    console.log(`📊 Found ${folders.length} user folders.`);

    let processed = 0;
    let failed = 0;

    for (const folder of folders) {
        try {
            // Parse Folder Name: "1234-John Doe" -> ID: 1234, Name: John Doe
            const match = String(folder).match(/^(\d+)\s*-\s*(.+)$/);
            const userName = (match ? match[2] : folder).trim();
            const userEmail = buildImportEmail(folder, userName);

            // Find Image
            const userDir = path.join(datasetRoot, folder);
            const files = fs.readdirSync(userDir);
            const imgFiles = files
                .filter(f => f.match(/\.(jpg|jpeg|png)$/i))
                .sort((a, b) => a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' }));

            if (imgFiles.length === 0) {
                console.log(`⚠️ No image in ${folder}`);
                continue;
            }

            // Detect Face
            const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 416, scoreThreshold: 0.2 });
            let bestDetection = null;
            let bestScore = -1;
            let bestImgPath = null;
            let bestImgFile = null;

            for (const candidate of imgFiles) {
                const candidatePath = path.join(userDir, candidate);
                let img;
                try {
                    img = await loadImage(candidatePath);
                } catch (e) {
                    continue;
                }

                if (!img || !img.width || !img.height) {
                    continue;
                }

                let detection;
                try {
                    detection = await faceapi
                        .detectSingleFace(img, options)
                        .withFaceLandmarks()
                        .withFaceDescriptor();
                } catch (e) {
                    continue;
                }

                if (!detection) {
                    continue;
                }

                const score = detection.detection?.score ?? detection.score ?? 0;
                if (score > bestScore) {
                    bestScore = score;
                    bestDetection = detection;
                    bestImgPath = candidatePath;
                    bestImgFile = candidate;
                }
            }

            if (!bestDetection || !bestImgPath) {
                console.log(`⚠️ No face detected for ${userName} (${imgFiles.join(', ')})`);
                failed++;
                continue;
            }

            const imgPath = bestImgPath;
            const imgFile = bestImgFile ?? imgFiles[0];
            const descriptor = JSON.stringify(Array.from(bestDetection.descriptor));

            // DB Operations
            // A. Create/Get User (Using UUID or ID?)
            // Check if user exists
            const [users] = await connection.execute('SELECT id FROM users WHERE email = ?', [userEmail]);
            let userId;
            let userAction = 'existing_by_email';

            if (users.length > 0) {
                userId = users[0].id;
            } else {
                const [nameMatches] = await connection.execute('SELECT id FROM users WHERE name = ? LIMIT 2', [userName]);
                if (nameMatches.length === 1) {
                    userId = nameMatches[0].id;
                    userAction = 'existing_by_name';
                } else {
                // Insert New User
                // Assuming UUID is primary key. If Auto Increment, remove UUID()
                const [result] = await connection.execute(
                    `INSERT INTO users (id, name, email, password, role, created_at, updated_at) 
                     VALUES (UUID(), ?, ?, ?, 'user', NOW(), NOW())`,
                    [userName, userEmail, '$2y$12$DefAuLtPaSsWoRdHaSh...'] // Default password hash
                );
                userAction = 'created';
                
                // Get the ID back
                const [newUser] = await connection.execute('SELECT id FROM users WHERE email = ?', [userEmail]);
                userId = newUser[0].id;
                }
            }

            // B. Upsert KYC Profile
            const [kyc] = await connection.execute('SELECT id FROM kyc_profiles WHERE user_id = ?', [userId]);
            let kycAction = 'updated';
            
            if (kyc.length > 0) {
                await connection.execute(
                    'UPDATE kyc_profiles SET face_descriptor = ?, face_straight_path = ?, updated_at = NOW() WHERE user_id = ?',
                    [descriptor, imgPath, userId] // Saving local path for reference
                );
            } else {
                kycAction = 'inserted';
                await connection.execute(
                    'INSERT INTO kyc_profiles (id, user_id, face_descriptor, face_straight_path, created_at, updated_at) VALUES (UUID(), ?, ?, ?, NOW(), NOW())',
                    [userId, descriptor, imgPath]
                );
            }

            console.log(`✅ Saved: ${userName} | ${userAction} | ${kycAction} | user_id=${userId} | img=${imgFile}`);

            processed++;
            if (processed % 10 === 0) console.log(`✅ Processed ${processed}/${folders.length}...`);

        } catch (error) {
            console.error(`❌ Error on ${folder}:`, error.message);
            failed++;
        }
    }

    console.log(`\n🏁 Done! Processed: ${processed}, Failed: ${failed}`);
    await connection.end();
}

main();
