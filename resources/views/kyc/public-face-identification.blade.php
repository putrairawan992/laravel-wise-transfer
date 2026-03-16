@extends('layouts.public')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-5">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 64px; height: 64px;">
                    <i class="bi bi-people-fill fs-2"></i>
                </div>
                <h2 class="fw-bold text-dark mb-2">Public Face Identification</h2>
                <p class="text-secondary">Identify any registered user using facial recognition</p>
            </div>

            <div class="card border-0 shadow-lg bg-dark bg-opacity-50 backdrop-blur rounded-4 overflow-hidden border-top border-primary border-opacity-25" style="min-height: 500px;">
                <div class="card-body p-0 position-relative">
                    <!-- Loading Overlay -->
                    <div id="loadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 bg-dark z-3 d-flex flex-column align-items-center justify-content-center transition-all">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                        <h5 class="text-white fw-medium mb-1">Loading Models & Database...</h5>
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
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <h4 class="text-white mb-2" id="identifiedName">Unknown</h4>
                            <p class="text-white-50 small mb-0">Identified User</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-white-50 text-decoration-none small hover-text-white">
                    <i class="bi bi-arrow-left me-1"></i> Back to Login
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
    const statusText = document.getElementById('statusText');
    const statusBadge = document.querySelector('#statusBadge .spinner-grow');
    const overlay = document.getElementById('overlay');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const startContainer = document.getElementById('startContainer');
    const faceFrame = document.getElementById('faceFrame');
    const actionFooter = document.getElementById('actionFooter');
    const identifiedName = document.getElementById('identifiedName');

    let isModelLoaded = false;
    let detectionInterval;
    let faceMatcher = null;
    let descriptorPool = [];
    let stableMatchLabel = null;
    let stableMatchCount = 0;

    const MATCH_THRESHOLD = Number(@json($match_threshold));
    const MIN_STABLE_FRAMES = Number(@json($stable_frames));
    const AMBIGUOUS_GAP = Number(@json($ambiguous_gap));

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
            
            // Load all registered faces
            const users = @json($labeledDescriptors);
            
            if (users.length > 0) {
                const labeledDescriptorsArray = users.map(user => {
                    const descriptor = new Float32Array(JSON.parse(user.descriptor));
                    descriptorPool.push({
                        label: user.label,
                        descriptor,
                    });
                    return new faceapi.LabeledFaceDescriptors(user.label, [descriptor]);
                });

                faceMatcher = new faceapi.FaceMatcher(labeledDescriptorsArray, MATCH_THRESHOLD);
            } else {
                alert('No registered faces found in the system.');
            }
            
        } catch (error) {
            console.error(error);
            statusText.innerText = 'Error loading models';
            statusBadge.classList.replace('text-secondary', 'text-danger');
        }
    }

    startBtn.addEventListener('click', async () => {
        if (!isModelLoaded) return;
        
        startBtn.classList.add('d-none');
        statusText.innerText = 'Starting Camera...';
        
        startCamera();
    });

    function startCamera() {
        if (navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: {} })
                .then(function (stream) {
                    video.srcObject = stream;
                    statusText.innerText = 'Detecting Faces...';
                    statusBadge.classList.add('spinner-grow');
                    statusBadge.classList.remove('bg-success');
                    faceFrame.classList.remove('d-none');
                    
                    video.addEventListener('play', () => {
                        const displaySize = { width: video.videoWidth, height: video.videoHeight };
                        faceapi.matchDimensions(overlay, displaySize);
                        detectForRecognition(displaySize);
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

    function getStableBestMatch(queryDescriptor) {
        if (descriptorPool.length === 0) {
            stableMatchLabel = null;
            stableMatchCount = 0;
            return null;
        }

        const ranked = descriptorPool
            .map(item => ({
                label: item.label,
                distance: faceapi.euclideanDistance(item.descriptor, queryDescriptor),
            }))
            .sort((a, b) => a.distance - b.distance);

        const best = ranked[0];
        const second = ranked[1] ?? null;
        const isAmbiguous = second ? (second.distance - best.distance) < AMBIGUOUS_GAP : false;
        const accepted = !isAmbiguous && best.distance <= MATCH_THRESHOLD;

        if (!accepted) {
            stableMatchLabel = null;
            stableMatchCount = 0;
            return null;
        }

        if (stableMatchLabel === best.label) {
            stableMatchCount += 1;
        } else {
            stableMatchLabel = best.label;
            stableMatchCount = 1;
        }

        if (stableMatchCount < MIN_STABLE_FRAMES) {
            return null;
        }

        return best;
    }

    function detectForRecognition(displaySize) {
        detectionInterval = setInterval(async () => {
            if (!faceMatcher) return;

            const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptors(); // Need descriptors for matching
            
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            const context = overlay.getContext('2d');
            context.clearRect(0, 0, overlay.width, overlay.height);
            
            if (detections.length > 0) {
                const stableBestMatch = getStableBestMatch(resizedDetections[0].descriptor);
                
                if (stableBestMatch) {
                    // Known user detected
                    statusText.innerText = '';
                    statusBadge.classList.remove('text-secondary', 'text-danger', 'spinner-grow', 'bg-dark', 'border-white');
                    statusBadge.classList.add('bg-success', 'text-white', 'border-success');
                    statusBadge.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> ' + stableBestMatch.label;
                    
                    identifiedName.innerText = stableBestMatch.label;
                    faceFrame.querySelector('div').classList.replace('border-white', 'border-success');
                    faceFrame.querySelector('div').style.boxShadow = '0 0 0 9999px rgba(0,0,0,0.7)';
                    
                    actionFooter.classList.remove('opacity-0', 'pointer-events-none');
                    actionFooter.classList.add('slide-up-fade');
                } else {
                    // Only unknown faces detected
                    statusText.innerText = 'Unknown Face';
                    statusBadge.classList.remove('bg-success', 'text-white', 'border-success');
                    statusBadge.classList.add('text-danger', 'bg-dark', 'border-white');
                    statusBadge.innerHTML = '<i class="bi bi-exclamation-circle me-2"></i>';
                    
                    identifiedName.innerText = 'Unknown';
                    faceFrame.querySelector('div').classList.replace('border-success', 'border-white');
                    faceFrame.querySelector('div').style.boxShadow = '0 0 0 9999px rgba(0,0,0,0.5)';
                    
                    actionFooter.classList.add('opacity-0', 'pointer-events-none');
                    actionFooter.classList.remove('slide-up-fade');
                }

                const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));
                results.forEach((result, i) => {
                    const box = resizedDetections[i].detection.box;
                    const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
                    drawBox.draw(overlay);
                });

            } else {
                stableMatchLabel = null;
                stableMatchCount = 0;
                statusText.innerText = 'Searching...';
                statusBadge.classList.remove('bg-success', 'text-white', 'border-success');
                statusBadge.classList.add('text-secondary', 'spinner-grow', 'bg-dark', 'border-white');
                statusBadge.innerHTML = ''; 
                
                identifiedName.innerText = '...';
                faceFrame.querySelector('div').classList.replace('border-success', 'border-white');
                faceFrame.querySelector('div').style.boxShadow = '0 0 0 9999px rgba(0,0,0,0.5)';
                
                actionFooter.classList.add('opacity-0', 'pointer-events-none');
                actionFooter.classList.remove('slide-up-fade');
            }
        }, 100);
    }

    // Load models on page load
    loadModels();
</script>
@endsection
