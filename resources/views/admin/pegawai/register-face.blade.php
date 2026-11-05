@extends('layouts.admin')

@section('title', 'Registrasi Wajah')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0"><i class="bi bi-person-badge"></i> Registrasi Wajah - {{ $pegawai->name }}</h4>
            </div>
            <div class="card-body text-center">
                
                @if($hasFace)
                    <div class="alert alert-success border-success">
                        <i class="bi bi-check-circle-fill"></i> Wajah sudah terdaftar di sistem.
                    </div>
                    
                    <div class="mb-4">
                        @if($pegawai->faceEmbedding->photo_path)
                            <img src="{{ asset('storage/' . $pegawai->faceEmbedding->photo_path) }}" 
                                 alt="Foto Wajah {{ $pegawai->name }}" 
                                 class="img-fluid rounded shadow" 
                                 style="max-width: 300px; max-height: 300px; object-fit: cover;">
                        @else
                            <div class="alert alert-warning">Foto wajah tidak tersedia.</div>
                        @endif
                    </div>

                    <form action="{{ route('admin.pegawai.face.destroy', $pegawai->id) }}" method="POST" class="mt-3">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus data wajah ini? Pegawai harus registrasi ulang.')">
                            <i class="bi bi-trash"></i> Hapus & Registrasi Ulang
                        </button>
                    </form>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i> Belum ada data wajah. Silakan ambil foto wajah pegawai.
                    </div>

                    <!-- Bungkus video dengan container relative untuk overlay canvas -->
                    <div class="mt-4 position-relative d-inline-block" id="videoContainer">
                        <video id="video" width="100%" style="max-width: 400px; border-radius: 10px; border: 3px solid #ddd; transform: scaleX(-1);" autoplay muted></video>
                        <!-- Canvas untuk menggambar kotak deteksi -->
                        <canvas id="overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; transform: scaleX(-1);"></canvas>
                    </div>
                    
                    <canvas id="canvas" style="display:none;"></canvas>
                    
                    <div id="status" class="mt-3 font-monospace text-warning fw-bold fs-5">
                        ⏳ Menunggu wajah terdeteksi...
                    </div>

                    <button id="captureBtn" class="btn btn-primary btn-lg mt-3" disabled>
                        <i class="bi bi-camera"></i> Ambil Foto & Daftarkan Wajah
                    </button>
                @endif

                <div class="mt-4">
                    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pegawai
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$hasFace)
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const overlay = document.getElementById('overlay');
    const statusDiv = document.getElementById('status');
    const captureBtn = document.getElementById('captureBtn');

    let photoData = null;
    let isDetecting = false;

    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } 
            });
            video.srcObject = stream;
        } catch (err) {
            statusDiv.innerText = '❌ Gagal akses kamera: ' + err.message;
            statusDiv.className = 'mt-3 font-monospace text-danger fw-bold fs-5';
        }
    }
    startCamera();

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

    // Fungsi untuk mendeteksi wajah secara terus menerus (Real-Time)
    async function startRealTimeDetection() {
        if (isDetecting) return;
        isDetecting = true;

        // Opsi Deteksi: Lebih sensitif (scoreThreshold diturunkan, inputSize dinaikkan)
        const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 512, scoreThreshold: 0.3 });

        const displaySize = { width: video.clientWidth, height: video.clientHeight };
        faceapi.matchDimensions(overlay, displaySize);

        setInterval(async () => {
            // Deteksi wajah
            const detection = await faceapi.detectSingleFace(video, options).withFaceLandmarks();

            const ctx = overlay.getContext('2d');
            ctx.clearRect(0, 0, overlay.width, overlay.height);

            if (detection) {
                // Wajah terdeteksi
                statusDiv.innerText = '✅ Wajah terdeteksi! Silakan klik tombol di bawah.';
                statusDiv.className = 'mt-3 font-monospace text-success fw-bold fs-5';
                captureBtn.disabled = false;

                // Gambar kotak deteksi
                const resizedDetections = faceapi.resizeResults(detection, displaySize);
                faceapi.draw.drawDetections(overlay, resizedDetections);
            } else {
                // Wajah tidak terdeteksi
                statusDiv.innerText = '⏳ Menunggu wajah terdeteksi... (Pastikan pencahayaan cukup)';
                statusDiv.className = 'mt-3 font-monospace text-warning fw-bold fs-5';
                captureBtn.disabled = true;
            }
        }, 200); // Cek setiap 0.2 detik
    }

    // Saat tombol di klik
    captureBtn.addEventListener('click', async () => {
        if (video.readyState !== 4) {
            alert('Kamera belum siap');
            return;
        }

        captureBtn.innerHTML = '<i class="spinner-border spinner-border-sm"></i> Memproses...';
        captureBtn.disabled = true;

        try {
            const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 512, scoreThreshold: 0.3 });
            
            const detection = await faceapi.detectSingleFace(video, options)
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (!detection) {
                alert('❌ Gagal mengambil data. Pastikan wajah terlihat jelas.');
                captureBtn.innerHTML = '<i class="bi bi-camera"></i> Ambil Foto & Daftarkan Wajah';
                captureBtn.disabled = false;
                return;
            }

            const descriptor = Array.from(detection.descriptor);

            // Ambil gambar dari video ke canvas (Untuk disimpan)
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            
            // Karena video di-mirror (transform: scaleX(-1)), kita balik lagi saat menyimpan foto agar tidak mirror
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            photoData = canvas.toDataURL('image/png');

            // Kirim ke server
            const response = await fetch('{{ route("admin.pegawai.face.store", $pegawai->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    descriptor: descriptor,
                    photo: photoData
                })
            });

            const data = await response.json();

            if (response.ok) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ Error: ' + (data.error || 'Gagal menyimpan'));
                captureBtn.innerHTML = '<i class="bi bi-camera"></i> Ambil Foto & Daftarkan Wajah';
                captureBtn.disabled = false;
            }
        } catch (err) {
            alert('Error: ' + err.message);
            captureBtn.innerHTML = '<i class="bi bi-camera"></i> Ambil Foto & Daftarkan Wajah';
            captureBtn.disabled = false;
        }
    });
</script>
@endif
@endsection