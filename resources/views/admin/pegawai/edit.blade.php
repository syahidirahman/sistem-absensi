@extends('layouts.admin')

@section('title', 'Edit Pegawai')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Edit Pegawai</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pegawai.update', $pegawai->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nip" class="form-label">NIP</label>
                <input type="text" name="nip" id="nip" 
                       class="form-control @error('nip') is-invalid @enderror" 
                       value="{{ old('nip', $pegawai->nip) }}" required>
                @error('nip')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <input type="text" name="name" id="name" 
                       class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name', $pegawai->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email', $pegawai->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                    <option value="pegawai" {{ old('role', $pegawai->role) == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    <option value="admin" {{ old('role', $pegawai->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" id="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       placeholder="Masukkan password baru">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="foto_profil" class="form-label">Foto Profil</label>
                @if($pegawai->foto_profil)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $pegawai->foto_profil) }}" 
                             alt="Foto Profil" 
                             class="img-thumbnail" 
                             width="100">
                    </div>
                @endif
                <input type="file" name="foto_profil" id="foto_profil" 
                       class="form-control @error('foto_profil') is-invalid @enderror">
                @error('foto_profil')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Upload foto baru jika ingin mengganti</small>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" 
                           class="form-check-input" 
                           value="1" 
                           {{ old('is_active', $pegawai->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Akun Aktif
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update
            </button>
            <a href="{{ route('admin.pegawai.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </form>
    </div>
</div>
@endsection