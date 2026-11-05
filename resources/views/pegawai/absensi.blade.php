@extends('layouts.pegawai')

@section('title', 'Absensi Wajah')

@section('content')
<style>
    .video-container {
        position: relative;
        display: inline-block;
        width: 100%;
        max-width: 450px;
        margin: 0 auto;
    }
    video {
        width: 100%;
        border: 3px solid #e2e8f0;
        border-radius: 15px;
        background: #000;
        transform: scaleX(-1); /* Efek cermin */
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    #overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        transform: scaleX(-1);
        border-radius: 15px;
    }
    .btn-checkin {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        font-weight: bold;
        border-radius: 10px;
        padding: 12px 30px;
        border: none;
        transition: 0.3s;
    }
    .btn-checkout {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        color: white;
        font-weight: bold;
        border-radius: 10px;
        padding: 12px 30px;
        border: none;
        transition: 0.3s;
    }
    .btn-checkin:disabled, .btn-checkout:disabled {
        background: #cbd5e1;
        color: #64748b;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .status-box {
        border-radius: 10px;
        padding: 15px;
        font-weight: bold;
        font-size: 16px;
        transition: 0.3s;
    }
    .status-waiting { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .status-processing { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .status-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .status-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
</style>

<div class="container py-4">
    <!-- Header Halaman -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-camera-video-fill text-primary me-2"></i>Presensi Wajah</h3>
            <p class="text-muted mb-0">Pastikan pencahayaan cukup dan wajah terlihat jelas.</p>
        </div>
        <a href="{{ route('pegawai.dashboard') }}" class="btn btn-outline-secondary fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body text-center p-4">
                    
                    <h5 class="fw-bold mb-4">Halo, <span class="text-primary">{{ auth()->user()->name }}</span></h5>

                    <!-- Kamera -->
                    <div class="video-container mb-4">
                        <video id="video" autoplay muted></video>
                        <canvas id="overlay"></canvas>
                    </div>
                    <canvas id="canvas" style="display:none;"></canvas>

                    <!-- Tombol Aksi -->
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <button id="checkInBtn" class="btn-checkin" {{ $isCheckedIn || $isCheckedOut ? 'disabled' : '' }}>
                            <i class="bi bi-box-arrow-in-right me-1"></i> Check-In
                        </button>
                        <button id="checkOutBtn" class="btn-checkout" {{ !$isCheckedIn || $isCheckedOut ? 'disabled' : '' }}>
                            <i class="bi bi-box-arrow-right me-1"></i> Check-Out
                        </button>
                    </div>

                    <!-- Status -->
                    <div id="status" class="status-box status-waiting mb-3">
                        ⏳ Menyiapkan kamera...
                    </div>

                    <!-- Lokasi -->
                    <div class="text-muted small bg-light py-2 rounded">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i> 
                        Lokasi: <span id="location">(menunggu)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const canvas = document.getElementById('canvas');
    const statusDiv = document.getElementById('status');
    const locationDiv = document.getElementById('location');
    const checkInBtn = document.getElementById('checkInBtn');
    const checkOutBtn = document.getElementById('checkOutBtn');

    let currentLocation = null;
    let currentDescriptor = null;
    let isVerified = false;
    let isProcessing = false; 
    let isCheckedIn = {{ $isCheckedIn ? 'true' : 'false' }};
    let isCheckedOut = {{ $isCheckedOut ? 'true' : 'false' }};
    const token = '{{ auth()->user()->createToken("web-token")->plainTextToken }}';

    // 1. Mulai Kamera
    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
            });
            video.srcObject = stream;
            updateStatus('✅ Kamera aktif. Posisikan wajah Anda.', 'waiting');
        } catch (err) {
            updateStatus('❌ Gagal akses kamera: ' + err.message, 'error');
        }
    }
    startCamera();

    // 2. Load Model
    async function loadModels() {
        try {
            const MODEL_URL = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/';
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        } catch (err) {
            console.error('Gagal load model', err);
        }
    }

    // Mulai deteksi setelah model dan video siap
    Promise.all([loadModels(), new Promise(resolve => video.addEventListener('loadeddata', resolve))]).then(() => {
        startRealTimeDetection();
    });

    // 3. Deteksi Wajah Real-Time
    function startRealTimeDetection() {
        // Pengaturan ringan dan cepat
        const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.4 });
        const displaySize = { width: video.clientWidth, height: video.clientHeight };
        faceapi.matchDimensions(overlay, displaySize);

        setInterval(async () => {
            if (video.readyState !== 4 || isProcessing || isCheckedOut) return; 

            try {
                const detection = await faceapi.detectSingleFace(video, options).withFaceLandmarks().withFaceDescriptor();
                const ctx = overlay.getContext('2d');
                ctx.clearRect(0, 0, overlay.width, overlay.height);

                if (detection) {
                    currentDescriptor = Array.from(detection.descriptor);
                    
                    // Gambar kotak deteksi hijau
                    const resizedDetections = faceapi.resizeResults(detection, displaySize);
                    faceapi.draw.drawDetections(overlay, resizedDetections);

                    // Kirim ke server untuk verifikasi jika belum terverifikasi
                    if (!isVerified) {
                        autoVerify();
                    }
                } else {
                    currentDescriptor = null;
                    if (!isVerified) {
                        updateStatus('❌ Arahkan wajah ke tengah kamera', 'error');
                        if (!isCheckedIn) checkInBtn.disabled = true;
                        checkOutBtn.disabled = true;
                    }
                }
            } catch (e) {}
        }, 500); // Cek setiap 0.5 detik
    }

    // 4. Verifikasi ke Server
    async function autoVerify() {
        if (!currentDescriptor || isVerified || isProcessing) return;
        
        isProcessing = true;
        updateStatus('⏳ Memverifikasi wajah...', 'processing');

        try {
            const response = await fetch('/api/attendance/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                },
                body: JSON.stringify({ descriptor: currentDescriptor })
            });
            
            const text = await response.text(); 
            let data = {};
            try { data = JSON.parse(text); } catch (e) { console.error('Server response:', text); }

            if (response.ok && data.valid) {
                isVerified = true;
                updateStatus('✅ Wajah terverifikasi!', 'success');
                
                if (!isCheckedIn && !isCheckedOut) {
                    checkInBtn.disabled = false;
                    checkOutBtn.disabled = true;
                } else if (isCheckedIn && !isCheckedOut) {
                    checkInBtn.disabled = true;
                    checkOutBtn.disabled = false;
                }
            } else {
                updateStatus('❌ Wajah tidak dikenali', 'error');
                if (!isCheckedIn) checkInBtn.disabled = true;
                checkOutBtn.disabled = true;
            }
        } catch (error) {
            updateStatus('❌ Gagal koneksi server', 'error');
        } finally {
            isProcessing = false; 
        }
    }

    // 5. GPS
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    currentLocation = { latitude: pos.coords.latitude, longitude: pos.coords.longitude };
                    locationDiv.innerText = `${pos.coords.latitude.toFixed(6)}, ${pos.coords.longitude.toFixed(6)}`;
                },
                (err) => { locationDiv.innerText = 'Gagal ambil lokasi (Izinkan GPS)'; },
                { enableHighAccuracy: false, timeout: 5000 }
            );
        }
    }
    getLocation();
    setInterval(getLocation, 10000);

    // 6. Kirim Absensi
    async function sendAttendance(action) {
        if (isProcessing) return;
        if (!isVerified) { alert('Wajah belum terverifikasi'); return; }
        if (!currentLocation) { alert('Lokasi GPS belum didapatkan'); return; }

        isProcessing = true;
        updateStatus('⏳ Memproses absensi...', 'processing');
        checkInBtn.disabled = true;
        checkOutBtn.disabled = true;

        // Ambil foto (format JPEG agar ringan)
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const photoData = canvas.toDataURL('image/jpeg', 0.7); 

        const url = action === 'in' ? '/api/attendance/check-in' : '/api/attendance/check-out';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                },
                body: JSON.stringify({
                    latitude: currentLocation.latitude,
                    longitude: currentLocation.longitude,
                    descriptor: currentDescriptor,
                    photo: photoData
                })
            });

            const text = await response.text();
            let data = {};
            try { data = JSON.parse(text); } catch (e) { 
                alert('Server Error: ' + text.substring(0, 100)); 
                isProcessing = false;
                return;
            }
            
            if (response.ok) {
                alert(data.message);
                if (action === 'in') {
                    isCheckedIn = true;
                    updateStatus('✅ Check-In berhasil. Arahkan wajah untuk Check-Out', 'success');
                    isVerified = false; 
                    isProcessing = false;
                    if (!isCheckedIn) checkInBtn.disabled = true;
                } else {
                    isCheckedOut = true;
                    alert('Check-out berhasil!');
                    window.location.href = '{{ route("pegawai.dashboard") }}';
                }
            } else {
                alert('Error: ' + (data.message || 'Gagal memproses'));
                isProcessing = false;
                if (action === 'in') checkInBtn.disabled = false;
                else checkOutBtn.disabled = false;
            }
        } catch (error) {
            alert('Gagal koneksi: ' + error.message);
            isProcessing = false;
            if (action === 'in') checkInBtn.disabled = false;
            else checkOutBtn.disabled = false;
        }
    }

    function updateStatus(text, type) {
        statusDiv.innerText = text;
        statusDiv.className = `status-box status-${type} mb-3`;
    }

    checkInBtn.addEventListener('click', () => sendAttendance('in'));
    checkOutBtn.addEventListener('click', () => sendAttendance('out'));
</script>
@endsection