const tf = require('@tensorflow/tfjs'); // Load TFJS + register kernels/backends
global.tf = tf; // Ensure face-api.js uses the same TFJS instance (avoid duplicated tfjs-core)
const faceapi = require('face-api.js');
const { Canvas, Image, ImageData, loadImage } = require('canvas');
const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');
require('dotenv').config({ path: '../../.env' }); // Adjust relative path to .env

// Setup Environment
faceapi.env.monkeyPatch({ Canvas, Image, ImageData });

// Constants
const MODELS_PATH = path.join(__dirname, '../../public/models');

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

    // 1. Connect DB
    const connection = await mysql.createConnection({
        host: process.env.DB_HOST || '127.0.0.1',
        user: process.env.DB_USERNAME || 'root',
        password: process.env.DB_PASSWORD || '',
        database: process.env.DB_DATABASE || 'laravel_transfer'
    });
    console.log("✅ DB Connected");

    // 2. Load Models
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
            const parts = folder.split('-');
            const userIdRaw = parts[0].trim();
            const userName = parts.slice(1).join('-').trim();
            const userEmail = `${userIdRaw}@example.com`; // Unique Email Dummy

            // Find Image
            const userDir = path.join(datasetRoot, folder);
            const files = fs.readdirSync(userDir);
            const imgFile = files.find(f => f.match(/\.(jpg|jpeg|png)$/i));

            if (!imgFile) {
                console.log(`⚠️ No image in ${folder}`);
                continue;
            }

            // Detect Face
            const imgPath = path.join(userDir, imgFile);
            let img;
            try {
                img = await loadImage(imgPath);
            } catch (e) {
                console.log(`⚠️ Failed to load image for ${userName} (${imgFile})`);
                failed++;
                continue;
            }

            if (!img || !img.width || !img.height) {
                console.log(`⚠️ Invalid image (0x0) for ${userName} (${imgFile})`);
                failed++;
                continue;
            }

            let detection;
            try {
                detection = await faceapi
                    .detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptor();
            } catch (e) {
                console.log(`⚠️ Face detection error for ${userName} (${imgFile})`);
                failed++;
                continue;
            }

            if (!detection) {
                console.log(`⚠️ No face detected for ${userName} (${imgFile})`);
                failed++;
                continue;
            }

            const descriptor = JSON.stringify(Array.from(detection.descriptor));

            // DB Operations
            // A. Create/Get User (Using UUID or ID?)
            // Check if user exists
            const [users] = await connection.execute('SELECT id FROM users WHERE email = ?', [userEmail]);
            let userId;

            if (users.length > 0) {
                userId = users[0].id;
            } else {
                // Insert New User
                // Assuming UUID is primary key. If Auto Increment, remove UUID()
                const [result] = await connection.execute(
                    `INSERT INTO users (id, name, email, password, role, created_at, updated_at) 
                     VALUES (UUID(), ?, ?, ?, 'user', NOW(), NOW())`,
                    [userName, userEmail, '$2y$12$DefAuLtPaSsWoRdHaSh...'] // Default password hash
                );
                
                // Get the ID back
                const [newUser] = await connection.execute('SELECT id FROM users WHERE email = ?', [userEmail]);
                userId = newUser[0].id;
            }

            // B. Upsert KYC Profile
            const [kyc] = await connection.execute('SELECT id FROM kyc_profiles WHERE user_id = ?', [userId]);
            
            if (kyc.length > 0) {
                await connection.execute(
                    'UPDATE kyc_profiles SET face_descriptor = ?, face_straight_path = ?, updated_at = NOW() WHERE user_id = ?',
                    [descriptor, imgPath, userId] // Saving local path for reference
                );
            } else {
                await connection.execute(
                    'INSERT INTO kyc_profiles (user_id, face_descriptor, face_straight_path, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())',
                    [userId, descriptor, imgPath]
                );
            }

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
