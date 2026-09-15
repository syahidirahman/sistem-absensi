@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-gear"></i> Pengaturan Sistem</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('admin.setting.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Jam Masuk Kerja</label>
                                <input type="time" name="jam_masuk" class="form-control @error('jam_masuk') is-invalid @enderror" 
                                       value="{{ old('jam_masuk', $setting->jam_masuk->format('H:i')) }}" required>
                                @error('jam_masuk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Jam Pulang Kerja</label>
                                <input type="time" name="jam_pulang" class="form-control @error('jam_pulang') is-invalid @enderror" 
                                       value="{{ old('jam_pulang', $setting->jam_pulang->format('H:i')) }}" required>
                                @error('jam_pulang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Toleransi Keterlambatan (menit)</label>
                                <input type="number" name="toleransi_keterlambatan" class="form-control @error('toleransi_keterlambatan') is-invalid @enderror" 
                                       value="{{ old('toleransi_keterlambatan', $setting->toleransi_keterlambatan) }}" min="0" max="120" required>
                                @error('toleransi_keterlambatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Radius Default Kantor (meter)</label>
                                <input type="number" name="radius_default" class="form-control @error('radius_default') is-invalid @enderror" 
                                       value="{{ old('radius_default', $setting->radius_default) }}" min="10" max="10000" required>
                                @error('radius_default')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection