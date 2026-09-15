<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Wajah & Lokasi</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Absensi</h1>
        <p>Halo, <strong>{{ auth()->user()->name ?? 'User' }}</strong></p>
        <video id="video" autoplay muted></video>
        <div>
            <button id="checkInBtn">✅ Check-In</button>
            <button id="checkOutBtn" disabled>❌ Check-Out</button>
        </div>
        <div id="status" class="info">⏳ Menyiapkan...</div>
        <div id="location" class="info">📍 Lokasi: (menunggu)</div>
    </div>

    <script>
        const video = document.getElementById('video');
        const statusDiv = document.getElementById('status');
        const locationDiv = document.getElementById('location');
        const checkInBtn = document.getElementById('checkInBtn');
        const checkOutBtn = document.getElementById('checkOutBtn');

        let currentLocation = null;
        let faceVerified = false;

        async function startCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                video.srcObject = stream;
                statusDiv.innerText = '📷 Kamera aktif, tunggu deteksi wajah...';
            } catch (err) {
                statusDiv.innerText = '❌ Gagal akses kamera: ' + err.message;
            }
        }
        startCamera();

        async function loadModels() {
            try {
                const MODEL_URL = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/';
                await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
                await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                statusDiv.innerText = '✅ Model wajah siap';
            } catch (err) {
                statusDiv.innerText = '❌ Gagal load model: ' + err.message;
            }
        }
        loadModels();

        setInterval(async () => {
            if (video.readyState === 4) {
                try {
                    const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions());
                    if (detections) {
                        faceVerified = true;
                        statusDiv.innerText = '✅ Wajah terdeteksi';
                    } else {
                        faceVerified = false;
                        statusDiv.innerText = '❌ Wajah tidak terdeteksi';
                    }
                } catch (e) {}
            }
        }, 1500);

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
                    { enableHighAccuracy: true }
                );
            } else {
                locationDiv.innerText = '❌ Geolocation tidak didukung browser';
            }
        }
        getLocation();
        setInterval(getLocation, 10000);

        const token = '{{ auth()->user()->createToken("web-token")->plainTextToken }}';

        async function sendAttendance(action) {
            if (!currentLocation) {
                alert('Lokasi belum didapatkan');
                return;
            }
            if (!faceVerified) {
                alert('Wajah belum terverifikasi');
                return;
            }

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
                        face_verified: true
                    })
                });

                const data = await response.json();
                if (response.ok) {
                    alert(data.message);
                    if (action === 'in') {
                        checkInBtn.disabled = true;
                        checkOutBtn.disabled = false;
                    } else {
                        checkOutBtn.disabled = true;
                    }
                } else {
                    alert('Error: ' + (data.message || JSON.stringify(data)));
                }
            } catch (error) {
                alert('Gagal koneksi ke server: ' + error.message);
            }
        }

        checkInBtn.addEventListener('click', () => sendAttendance('in'));
        checkOutBtn.addEventListener('click', () => sendAttendance('out'));
    </script>
</body>
</html>