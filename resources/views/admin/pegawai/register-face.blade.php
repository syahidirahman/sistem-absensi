@extends('layouts.admin')

@section('title', 'Registrasi Wajah')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4>Registrasi Wajah - {{ $pegawai->name }}</h4>
            </div>
            <div class="card-body text-center">
                @if($hasFace)
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Wajah sudah terdaftar.
                    </div>
                    <div>
                        <img src="{{ asset('storage/' . $pegawai->faceEmbedding->photo_path) }}" alt="Foto Wajah" class="img-thumbnail" width="200">
                    </div>
                    <form action="{{ route('admin.pegawai.face.destroy', $pegawai->id) }}" method="POST" class="mt-3">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus data wajah?')">
                            <i class="bi bi-trash"></i> Hapus Data Wajah
                        </button>
                    </form>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i> Belum ada data wajah. Silakan registrasi.
                    </div>
                @endif

                <div class="mt-4">
                    <video id="video" width="400" autoplay muted style="border:2px solid #ddd; border-radius:8px;"></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <div id="status" class="mt-2"></div>
                </div>

                <button id="captureBtn" class="btn btn-primary mt-3" {{ $hasFace ? 'disabled' : '' }}>
                    <i class="bi bi-camera"></i> Ambil Wajah
                </button>

                <div class="mt-3">
                    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const statusDiv = document.getElementById('status');
    const captureBtn = document.getElementById('captureBtn');

    let descriptor = null;

    // 1. Mulai kamera
    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
        } catch (err) {
            statusDiv.innerText = '❌ Gagal akses kamera: ' + err.message;
        }
    }
    startCamera();

    // 2. Load model face-api.js
    async function loadModels() {
        const MODEL_URL = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/';
        await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
        await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
        await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
        statusDiv.innerText = '✅ Model siap';
    }
    loadModels();

    // 3. Saat tombol diklik
    captureBtn.addEventListener('click', async () => {
        if (video.readyState !== 4) {
            alert('Kamera belum siap');
            return;
        }

        try {
            // Deteksi wajah
            const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (!detection) {
                alert('Tidak ada wajah terdeteksi. Pastikan wajah terlihat jelas.');
                return;
            }

            descriptor = Array.from(detection.descriptor);
            statusDiv.innerText = '✅ Wajah berhasil diambil!';

            // Kirim ke server (tanpa foto)
            const response = await fetch('{{ route("admin.pegawai.face.store", $pegawai->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    descriptor: descriptor
                })
            });

            // Baca response
            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                alert('Server error: ' + text.substring(0, 300));
                return;
            }

            if (response.ok) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + (data.error || data.message || 'Gagal menyimpan'));
            }
        } catch (err) {
            alert('Error: ' + err.message);
        }
    });
</script>
@endsection