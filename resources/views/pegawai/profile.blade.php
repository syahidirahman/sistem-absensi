@extends('layouts.pegawai')

@section('title', 'Profil Saya')

@section('content')
<!-- ===== CSS CROPPER ===== -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-person-circle"></i> Profil Saya</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form id="profileForm" action="{{ route('pegawai.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- ===== FOTO PROFIL BULAT ===== -->
                    <div class="text-center mb-4">
                        @if($user->foto_profil)
                            <img src="{{ asset('storage/' . $user->foto_profil) }}" 
                                 id="previewAvatar"
                                 class="rounded-circle img-thumbnail" 
                                 style="width:150px; height:150px; object-fit:cover; border-radius:50%; border:3px solid #ddd;"
                                 alt="Foto Profil">
                        @else
                            <div id="previewAvatar" class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white" 
                                 style="width:150px; height:150px; font-size:50px; border-radius:50%;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label>NIP</label>
                        <input type="text" class="form-control" value="{{ $user->nip }}" disabled readonly>
                        <small class="text-muted">NIP tidak dapat diubah</small>
                    </div>

                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Foto Profil</label>
                        <input type="file" id="imageInput" class="form-control" accept="image/*">
                        <small class="text-muted">Pilih gambar, lalu Anda bisa memotongnya sesuai keinginan</small>
                        <input type="hidden" name="foto_profil_crop" id="foto_profil_crop">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label>Password Baru (kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Masukkan password baru">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" 
                               placeholder="Konfirmasi password baru">
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('pegawai.dashboard') }}" class="btn btn-secondary me-md-2">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL CROP ===== -->
<div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Potong Gambar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img id="cropImage" src="" style="max-width:100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="cropButton" class="btn btn-primary">Potong & Upload</button>
            </div>
        </div>
    </div>
</div>

<!-- ===== CROPPER JS ===== -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('imageInput');
        const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
        const cropImage = document.getElementById('cropImage');
        const cropButton = document.getElementById('cropButton');
        const fotoProfilCrop = document.getElementById('foto_profil_crop');
        const previewAvatar = document.getElementById('previewAvatar');
        const profileForm = document.getElementById('profileForm');

        let cropper = null;

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                cropImage.src = event.target.result;
                cropModal.show();

                // Inisialisasi cropper setelah gambar dimuat
                setTimeout(() => {
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(cropImage, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 1,
                        cropBoxResizable: true,
                        cropBoxMovable: true,
                        minCropBoxWidth: 100,
                        minCropBoxHeight: 100,
                    });
                }, 100);
            };
            reader.readAsDataURL(file);
        });

        cropButton.addEventListener('click', function() {
            if (!cropper) return;

            // Dapatkan hasil crop sebagai canvas
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            // Ubah ke base64
            const croppedImage = canvas.toDataURL('image/png');
            fotoProfilCrop.value = croppedImage; // simpan ke hidden input

            // Tampilkan preview
            previewAvatar.src = croppedImage;
            previewAvatar.style.display = 'inline-block';
            previewAvatar.style.width = '150px';
            previewAvatar.style.height = '150px';
            previewAvatar.style.objectFit = 'cover';
            previewAvatar.style.borderRadius = '50%';

            cropModal.hide();
            imageInput.value = ''; // reset input file
        });

        // Reset cropper saat modal ditutup
        document.getElementById('cropModal').addEventListener('hidden.bs.modal', function() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        // Sebelum submit, pastikan tidak ada input file yang kosong
        profileForm.addEventListener('submit', function(e) {
            // Jika ada foto hasil crop, kirim base64
            // Tidak perlu validasi tambahan
        });
    });
</script>

<!-- ===== CSS TAMBAHAN UNTUK CROPPER ===== -->
<style>
    .cropper-container {
        width: 100% !important;
        height: auto !important;
        max-height: 500px;
    }
    .cropper-image {
        max-width: 100% !important;
    }
</style>
@endsection