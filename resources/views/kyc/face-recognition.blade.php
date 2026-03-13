@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-person-bounding-box fs-2"></i>
                </div>
                <h2 class="fw-bold text-dark mb-2">Face Verification</h2>
                <p class="text-secondary">Please verify your identity using facial recognition</p>
            </div>

            <div class="card border-0 shadow-lg bg-dark bg-opacity-50 backdrop-blur rounded-4 overflow-hidden border-top border-primary border-opacity-25" style="min-height: 500px;">
                <div class="card-body p-0 position-relative">
                    <!-- Loading Overlay -->
                    <div id="loadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 bg-dark z-3 d-flex flex-column align-items-center justify-content-center transition-all">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                        <h5 class="text-white fw-medium mb-1">Loading Models...</h5>
                        <p class="text-white-50 small">Please wait while we initialize the AI</p>
                    </div>

                    <!-- Main Content -->
                    <div class="row g-0 h-100">
                        <!-- Camera Section -->
                        <div class="col-lg-12 position-relative bg-black d-flex align-items-center justify-content-center" style="min-height: 400px;">
                            <video id="video" class="w-100 h-100 object-fit-cover" autoplay muted playsinline style="transform: scaleX(-1);"></video>
                            <canvas id="overlay" class="position-absolute top-0 start-0 w-100 h-100"></canvas>
                            
                            <!-- Face Frame Guide -->
                            <div class="position-absolute top-50 start-50 translate-middle pointer-events-none d-none" id="faceFrame">
                                <div class="border border-2 border-white border-opacity-50 rounded-circle" style="width: 280px; height: 280px; box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);"></div>
                            </div>

                            <!-- Start Button Overlay -->
                            <div id="startContainer" class="position-absolute top-50 start-50 translate-middle text-center z-2">
                                <button id="startBtn" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-lg fw-bold d-none mb-3">
                                    <i class="bi bi-camera-video-fill me-2"></i> Start Camera
                                </button>
                                <button id="registerBtn" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3 shadow-lg fw-bold d-none">
                                    <i class="bi bi-person-plus-fill me-2"></i> Register My Face
                                </button>
                            </div>

                            <!-- Status Badge -->
                            <div class="position-absolute top-0 start-0 m-3 z-2">
                                <span id="statusBadge" class="badge bg-dark bg-opacity-75 border border-white border-opacity-10 rounded-pill px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="spinner-grow spinner-grow-sm text-secondary" role="status"></span>
                                    <span id="statusText" class="fw-medium">Initializing...</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-dark border-top border-white border-opacity-10 p-4 transition-all opacity-0 pointer-events-none" id="actionFooter">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center bg-secondary bg-opacity-25 rounded-3" style="width: 60px; height: 60px; overflow: hidden;">
                                <canvas id="snapshot" width="320" height="240" class="w-100 h-100 object-fit-cover opacity-50"></canvas>
                            </div>
                            <div>
                                <h6 class="text-white mb-1">Captured Image</h6>
                                <p class="text-white-50 small mb-0" id="captureStatus">No image captured yet</p>
                            </div>
                        </div>
                        
                        <button id="captureBtn" class="btn btn-success btn-lg px-4 fw-bold rounded-pill shadow-lg transform-scale hover-scale text-white" disabled>
                            <i class="bi bi-check-circle-fill me-2 text-white"></i> Verify Identity
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('dashboard') }}" class="text-white-50 text-decoration-none small hover-text-white">
                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .backdrop-blur { backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
    .pointer-events-none { pointer-events: none; }
    .hover-text-white:hover { color: #fff !important; }
    .transition-all { transition: all 0.3s ease; }
    .hover-scale:hover { transform: scale(1.05); }
    .slide-up-fade { animation: slideUpFade 0.4s ease forwards; }
    @keyframes slideUpFade {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
    const video = document.getElementById('video');
    const startBtn = document.getElementById('startBtn');
    const registerBtn = document.getElementById('registerBtn');
    const captureBtn = document.getElementById('captureBtn');
    const statusText = document.getElementById('statusText');
    const statusBadge = document.querySelector('#statusBadge .spinner-grow');
    const snapshotCanvas = document.getElementById('snapshot');
    const overlay = document.getElementById('overlay');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const startContainer = document.getElementById('startContainer');
    const faceFrame = document.getElementById('faceFrame');
    const captureStatus = document.getElementById('captureStatus');
    const actionFooter = document.getElementById('actionFooter');

    let isModelLoaded = false;
    let detectionInterval;
    let faceMatcher = null;
    let labeledFaceDescriptors = null;

    async function loadModels() {
        try {
            statusText.innerText = 'Loading AI Models...';
            // Use local models for reliability
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                faceapi.nets.faceExpressionNet.loadFromUri('/models')
            ]);
            
            isModelLoaded = true;
            loadingOverlay.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => loadingOverlay.classList.add('d-none'), 300);
            
            statusText.innerText = 'Ready';
            statusBadge.classList.replace('text-secondary', 'text-success');
            statusBadge.classList.remove('spinner-grow');
            statusBadge.classList.add('bg-success', 'rounded-circle');
            statusBadge.style.width = '10px';
            statusBadge.style.height = '10px';
            
            startBtn.classList.remove('d-none');
            
            // Check if user has registered face (from Server - Optimized Variable)
            @if($face_descriptor)
                const serverDescriptor = @json($face_descriptor);
                const descriptor = new Float32Array(JSON.parse(serverDescriptor));
                labeledFaceDescriptors = new faceapi.LabeledFaceDescriptors('{{ $user_name }}', [descriptor]);
                faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);
                
                registerBtn.classList.add('d-none');
                startBtn.classList.remove('d-none');
                startBtn.innerHTML = '<i class="bi bi-camera-video-fill me-2"></i> Start Recognition';
            @else
                registerBtn.classList.remove('d-none');
                startBtn.classList.add('d-none');
            @endif
            
        } catch (error) {
            console.error(error);
            statusText.innerText = 'Error loading models';
            statusBadge.classList.replace('text-secondary', 'text-danger');
        }
    }

    registerBtn.addEventListener('click', async () => {
        if (!isModelLoaded) return;
        
        registerBtn.classList.add('d-none');
        statusText.innerText = 'Starting Camera for Registration...';
        
        startCamera(true); // true = registration mode
    });

    startBtn.addEventListener('click', async () => {
        if (!isModelLoaded) return;
        
        startBtn.classList.add('d-none');
        // Ensure Register button is also hidden when starting recognition
        registerBtn.classList.add('d-none');
        
        statusText.innerText = 'Starting Camera...';
        
        startCamera(false); // false = recognition mode
    });

    function startCamera(isRegistration) {
        if (navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: {} })
                .then(function (stream) {
                    video.srcObject = stream;
                    statusText.innerText = isRegistration ? 'Hold still for registration...' : 'Detecting Face...';
                    statusBadge.classList.add('spinner-grow');
                    statusBadge.classList.remove('bg-success');
                    faceFrame.classList.remove('d-none');
                    
                    video.addEventListener('play', () => {
                        const displaySize = { width: video.videoWidth, height: video.videoHeight };
                        faceapi.matchDimensions(overlay, displaySize);
                        
                        // For registration, we only need to capture one good frame
                        if (isRegistration) {
                            detectForRegistration(displaySize);
                        } else {
                            detectForRecognition(displaySize);
                        }
                    });
                })
                .catch(function (error) {
                    console.log("Something went wrong!", error);
                    statusText.innerText = 'Camera Error';
                    statusBadge.classList.replace('text-secondary', 'text-danger');
                    startBtn.classList.remove('d-none');
                });
        }
    }

    async function detectForRegistration(displaySize) {
        const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
        
        if (detection) {
            // Convert descriptor to JSON string
            const descriptorJson = JSON.stringify(Array.from(detection.descriptor));
            
            // Send to Server
            try {
                const response = await fetch('{{ route("kyc.registerFaceDescriptor") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ descriptor: descriptorJson })
                });
                
                if (!response.ok) throw new Error('Failed to save face descriptor');
                
                // Update local state
                labeledFaceDescriptors = new faceapi.LabeledFaceDescriptors('{{ auth()->user()->name }}', [detection.descriptor]);
                faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);
                
                // Stop camera
                video.pause();
                const stream = video.srcObject;
                if (stream) {
                    const tracks = stream.getTracks();
                    tracks.forEach(track => track.stop());
                }
                
                // Show success
                statusText.innerText = 'Registration Complete!';
                statusBadge.classList.remove('spinner-grow');
                statusBadge.classList.add('bg-success');
                
                alert('Face Registered Successfully! You can now use Face Verification.');
                
                // Reset UI
                faceFrame.classList.add('d-none');
                startBtn.classList.remove('d-none');
                startBtn.innerHTML = '<i class="bi bi-camera-video-fill me-2"></i> Start Recognition';
                
            } catch (err) {
                console.error(err);
                alert('Failed to register face. Please try again.');
            }
            
        } else {
            requestAnimationFrame(() => detectForRegistration(displaySize));
        }
    }

    function detectForRecognition(displaySize) {
        detectionInterval = setInterval(async () => {
            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors(); // Need descriptors for matching
            
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            const context = overlay.getContext('2d');
            context.clearRect(0, 0, overlay.width, overlay.height);
            
            if (detections.length > 0) {
                // Match faces
                const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));
                
                results.forEach((result, i) => {
                    const box = resizedDetections[i].detection.box;
                    const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
                    drawBox.draw(overlay);
                    
                    if (result.label !== 'unknown') {
                        // Known user detected
                        statusText.innerText = '';
                        statusBadge.classList.remove('text-secondary', 'text-danger', 'spinner-grow', 'bg-dark', 'border-white');
                        statusBadge.classList.add('bg-success', 'text-white', 'border-success');
                        statusBadge.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> ' + result.label;
                        
                        captureBtn.disabled = false;
                        faceFrame.querySelector('div').classList.replace('border-white', 'border-success');
                        faceFrame.querySelector('div').style.boxShadow = '0 0 0 9999px rgba(0,0,0,0.7)';
                        
                        actionFooter.classList.remove('opacity-0', 'pointer-events-none');
                        actionFooter.classList.add('slide-up-fade');
                    }
                });

            } else {
                statusText.innerText = 'Searching...';
                statusBadge.classList.remove('bg-success', 'text-white', 'border-success');
                statusBadge.classList.add('text-secondary', 'spinner-grow', 'bg-dark', 'border-white');
                statusBadge.innerHTML = ''; 
                
                captureBtn.disabled = true;
                faceFrame.querySelector('div').classList.replace('border-success', 'border-white');
                faceFrame.querySelector('div').style.boxShadow = '0 0 0 9999px rgba(0,0,0,0.5)';
                
                actionFooter.classList.add('opacity-0', 'pointer-events-none');
                actionFooter.classList.remove('slide-up-fade');
            }
        }, 100);
    }

    captureBtn.addEventListener('click', () => {
        const context = snapshotCanvas.getContext('2d');
        snapshotCanvas.classList.remove('opacity-50');
        // Draw the current video frame to the canvas
        // We need to account for the scaleX(-1) flip
        context.save();
        context.scale(-1, 1);
        context.drawImage(video, -320, 0, 320, 240);
        context.restore();
        
        captureStatus.innerText = 'Verifying identity...';
        captureStatus.classList.remove('text-success');
        captureStatus.classList.add('text-info');
        captureBtn.disabled = true;
        captureBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
        
        // Stop camera and detection
        clearInterval(detectionInterval);
        video.pause();
        
        // Convert canvas to blob/base64
        const dataURL = snapshotCanvas.toDataURL('image/jpeg');
        
        // Send to server
        fetch('{{ route("kyc.faceVerify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ image: dataURL })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                captureStatus.innerText = 'Identity Verified Successfully';
                captureStatus.classList.replace('text-info', 'text-success');
                statusText.innerText = 'Verified';
                statusBadge.classList.remove('spinner-grow');
                statusBadge.classList.add('bg-success', 'rounded-circle');
                
                captureBtn.innerHTML = '<i class="bi bi-check-all me-2"></i> Verified';
                captureBtn.classList.replace('btn-success', 'btn-secondary');
                
                // Stop camera stream completely
                const stream = video.srcObject;
                if (stream) {
                    const tracks = stream.getTracks();
                    tracks.forEach(track => track.stop());
                }
            } else {
                throw new Error(data.message || 'Verification failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            captureStatus.innerText = 'Verification failed. Please try again.';
            captureStatus.classList.replace('text-info', 'text-danger');
            
            // Reset UI to allow retry
            captureBtn.disabled = false;
            captureBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i> Retry';
            captureBtn.classList.replace('btn-success', 'btn-warning');
            
            // Resume camera
            video.play();
            // Re-attach detection interval if needed or just let user click retry to reload page
        });
    });

    // Load models on page load
    loadModels();
</script>
@endsection
