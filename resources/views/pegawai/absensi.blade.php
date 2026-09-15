@extends('layouts.pegawai')

@section('title', 'Absensi Wajah')

@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Wajah</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 20px; background: #f4f4f4; }
        .container { max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        video { width: 100%; max-width: 400px; border: 2px solid #333; border-radius: 10px; background: #000; }
        button { padding: 12px 24px; margin: 10px; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; }
        #checkInBtn { background: #28a745; color: white; }
        #checkOutBtn { background: #dc3545; color: white; }
        #checkInBtn:disabled, #checkOutBtn:disabled { background: #ccc; cursor: not-allowed; }
        #status { margin-top: 20px; font-weight: bold; }
        #location { margin-top: 10px; color: #555; }
        .info { background: #e9ecef; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .btn-back { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Absensi Wajah</h1>
        <p>Halo, <strong>{{ auth()->user()->name }}</strong></p>
        <video id="video" autoplay muted></video>   
        <canvas id="canvas" style="display:none;"></canvas> <!-- hidden canvas for screenshot -->
        <div>
            <button id="checkInBtn" {{ $isCheckedIn || $isCheckedOut ? 'disabled' : '' }}>✅ Check-In</button>
            <button id="checkOutBtn" {{ !$isCheckedIn || $isCheckedOut ? 'disabled' : '' }}>❌ Check-Out</button>
        </div>
        <div id="status" class="info">⏳ Menyiapkan...</div>
        <div id="location" class="info">📍 Lokasi: (menunggu)</div>
        <div class="btn-back">
            <a href="{{ route('pegawai.dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const statusDiv = document.getElementById('status');
        const locationDiv = document.getElementById('location');
        const checkInBtn = document.getElementById('checkInBtn');
        const checkOutBtn = document.getElementById('checkOutBtn');

        let currentLocation = null;
        let currentDescriptor = null;
        let isVerified = false;
        let verifyTimeout = null;
        let isProcessing = false;
        let photoData = null;

        // State dari server
        let isCheckedIn = {{ $isCheckedIn ? 'true' : 'false' }};
        let isCheckedOut = {{ $isCheckedOut ? 'true' : 'false' }};

        // 1. Mulai kamera
        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    } 
                });
                video.srcObject = stream;
                statusDiv.innerText = '📷 Kamera aktif';
            } catch (err) {
                statusDiv.innerText = '❌ Gagal akses kamera: ' + err.message;
            }
        }
        startCamera();

        // 2. Load model face-api.js
        async function loadModels() {
            try {
                const MODEL_URL = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/';
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                statusDiv.innerText = '✅ Model siap';
            } catch (err) {
                statusDiv.innerText = '❌ Gagal load model: ' + err.message;
            }
        }
        loadModels();

        // 3. Deteksi wajah + ambil screenshot setiap 500ms
        setInterval(async () => {
            if (video.readyState === 4 && !isProcessing) {
                try {
                    const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({
                        inputSize: 224,
                        scoreThreshold: 0.5
                    }))
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                    if (detection) {
                        currentDescriptor = Array.from(detection.descriptor);

                        // Ambil screenshot
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        photoData = canvas.toDataURL('image/png');

                        if (verifyTimeout) clearTimeout(verifyTimeout);
                        verifyTimeout = setTimeout(() => autoVerify(), 500);
                    } else {
                        currentDescriptor = null;
                        photoData = null;
                        isVerified = false;
                        statusDiv.innerText = '❌ Arahkan wajah ke kamera';
                        if (!isCheckedIn && !isCheckedOut) {
                            checkInBtn.disabled = true;
                            checkOutBtn.disabled = true;
                        }
                    }
                } catch (e) {}
            }
        }, 500);

        // 4. Auto Verify
        async function autoVerify() {
            if (!currentDescriptor || isProcessing) return;
            isProcessing = true;
            statusDiv.innerText = '⏳ Memverifikasi wajah...';
            
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

                const data = await response.json();
                
                if (response.ok && data.valid) {
                    isVerified = true;
                    statusDiv.innerText = '✅ Wajah terverifikasi!';
                    if (!isCheckedIn && !isCheckedOut) {
                        checkInBtn.disabled = false;
                        checkOutBtn.disabled = true;
                    } else if (isCheckedIn && !isCheckedOut) {
                        checkInBtn.disabled = true;
                        checkOutBtn.disabled = false;
                    } else {
                        checkInBtn.disabled = true;
                        checkOutBtn.disabled = true;
                    }
                } else {
                    isVerified = false;
                    statusDiv.innerText = '❌ Wajah tidak dikenali oleh sistem';
                    checkInBtn.disabled = true;
                    checkOutBtn.disabled = true;
                }
            } catch (error) {
                statusDiv.innerText = '❌ Gagal verifikasi: ' + error.message;
            } finally {
                isProcessing = false;
            }
        }

        // 5. Lokasi GPS
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        currentLocation = {
                            latitude: pos.coords.latitude,
                            longitude: pos.coords.longitude
                        };
                        locationDiv.innerText = `📍 Lokasi: ${pos.coords.latitude.toFixed(6)}, ${pos.coords.longitude.toFixed(6)}`;
                    },
                    (err) => {
                        locationDiv.innerText = '❌ Gagal ambil lokasi: ' + err.message;
                    },
                    { enableHighAccuracy: false, timeout: 5000 }
                );
            } else {
                locationDiv.innerText = '❌ Geolocation tidak didukung';
            }
        }
        getLocation();
        setInterval(getLocation, 10000);

        // 6. Token
        const token = '{{ auth()->user()->createToken("web-token")->plainTextToken }}';

        // 7. Kirim Check-In/Out (dengan foto)
        async function sendAttendance(action) {
            if (isProcessing) return;
            if (!isVerified) {
                alert('Wajah belum terverifikasi');
                return;
            }
            if (!currentLocation) {
                alert('Lokasi belum didapatkan');
                return;
            }
            if (action === 'in' && isCheckedIn) {
                alert('Anda sudah check-in hari ini');
                return;
            }
            if (action === 'out' && !isCheckedIn) {
                alert('Anda belum check-in');
                return;
            }

            isProcessing = true;
            statusDiv.innerText = '⏳ Memproses...';
            checkInBtn.disabled = true;
            checkOutBtn.disabled = true;

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
                        photo: photoData // kirim foto base64
                    })
                });

                const data = await response.json();
                
                if (response.ok) {
                    statusDiv.innerText = '✅ ' + data.message;
                    alert(data.message);
                    if (action === 'in') {
                        isCheckedIn = true;
                        checkInBtn.disabled = true;
                        checkOutBtn.disabled = false;
                        statusDiv.innerText = '✅ Check-In berhasil, arahkan wajah untuk Check-Out';
                    } else {
                        isCheckedOut = true;
                        checkOutBtn.disabled = true;
                        alert('Check-out berhasil!');
                        window.location.href = '{{ route("pegawai.dashboard") }}';
                    }
                } else {
                    alert('Error: ' + (data.message || JSON.stringify(data)));
                    if (action === 'in') {
                        checkInBtn.disabled = isCheckedIn;
                    } else {
                        checkOutBtn.disabled = !isCheckedIn || isCheckedOut;
                    }
                }
            } catch (error) {
                alert('Gagal koneksi: ' + error.message);
                if (action === 'in') {
                    checkInBtn.disabled = isCheckedIn;
                } else {
                    checkOutBtn.disabled = !isCheckedIn || isCheckedOut;
                }
            } finally {
                isProcessing = false;
            }
        }

        checkInBtn.addEventListener('click', () => sendAttendance('in'));
        checkOutBtn.addEventListener('click', () => sendAttendance('out'));
    </script>
</body>
</html>
@endsection