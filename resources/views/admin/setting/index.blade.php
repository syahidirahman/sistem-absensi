@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<!-- Tambahkan CSS Leaflet untuk Peta -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="row">
    <div class="col-md-10 mx-auto">
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
                                       value="{{ old('jam_masuk', $setting->jam_masuk ? \Carbon\Carbon::parse($setting->jam_masuk)->format('H:i') : '08:00') }}" required>
                                @error('jam_masuk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Jam Pulang Kerja</label>
                                <input type="time" name="jam_pulang" class="form-control @error('jam_pulang') is-invalid @enderror" 
                                       value="{{ old('jam_pulang', $setting->jam_pulang ? \Carbon\Carbon::parse($setting->jam_pulang)->format('H:i') : '16:00') }}" required>
                                @error('jam_pulang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Toleransi Keterlambatan (menit)</label>
                                <input type="number" name="toleransi_keterlambatan" class="form-control @error('toleransi_keterlambatan') is-invalid @enderror" 
                                       value="{{ old('toleransi_keterlambatan', $setting->toleransi_keterlambatan ?? 15) }}" min="0" max="120" required>
                                @error('toleransi_keterlambatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Radius Default Kantor (meter)</label>
                                <!-- Tambahkan id="radius_input" agar bisa dibaca oleh Javascript -->
                                <input type="number" name="radius_default" id="radius_input" class="form-control @error('radius_default') is-invalid @enderror" 
                                       value="{{ old('radius_default', $setting->radius_default ?? 100) }}" min="10" max="10000" required>
                                @error('radius_default')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Lokasi Kantor (Geser Pin di Peta)</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Latitude (Lintang)</label>
                                <input type="text" name="office_latitude" id="office_latitude" class="form-control @error('office_latitude') is-invalid @enderror" 
                                       value="{{ old('office_latitude', $setting->office_latitude ?? '-3.3282') }}" required readonly>
                                @error('office_latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Longitude (Bujur)</label>
                                <input type="text" name="office_longitude" id="office_longitude" class="form-control @error('office_longitude') is-invalid @enderror" 
                                       value="{{ old('office_longitude', $setting->office_longitude ?? '114.587919') }}" required readonly>
                                @error('office_longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Container untuk Peta -->
                    <div id="map" style="height: 400px; border-radius: 8px; border: 1px solid #ddd;" class="mb-4"></div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save"></i> Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan JS Leaflet untuk Peta -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ambil nilai awal dari input
        var lat = document.getElementById('office_latitude').value || -3.3282;
        var lng = document.getElementById('office_longitude').value || 114.587919;
        var radiusInput = document.getElementById('radius_input');
        var initialRadius = parseInt(radiusInput.value) || 100;

        // Inisialisasi peta
        var map = L.map('map').setView([lat, lng], 16); // 16 adalah level zoom

        // Tambahkan tile layer (tampilan peta dari OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Buat marker (pin) yang bisa digeser
        var marker = L.marker([lat, lng], {
            draggable: true
        }).addTo(map);

        // Buat efek lingkaran radius (warna biru transparan)
        var radiusCircle = L.circle([lat, lng], {
            color: '#0d6efd',      // Warna garis tepi
            fillColor: '#0d6efd',  // Warna isi
            fillOpacity: 0.2,      // Transparansi isi (0 - 1)
            radius: initialRadius  // Radius dalam meter
        }).addTo(map);

        // Fungsi saat marker digeser
        marker.on('dragend', function (e) {
            var position = marker.getLatLng();
            // Update nilai input
            document.getElementById('office_latitude').value = position.lat.toFixed(8);
            document.getElementById('office_longitude').value = position.lng.toFixed(8);
            // Pindahkan lingkaran agar tetap center di pin
            radiusCircle.setLatLng(position);
        });

        // Fungsi: Jika admin klik di peta, pin dan lingkaran pindah ke situ
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById('office_latitude').value = e.latlng.lat.toFixed(8);
            document.getElementById('office_longitude').value = e.latlng.lng.toFixed(8);
            radiusCircle.setLatLng(e.latlng);
        });

        // Fungsi: Jika admin mengubah angka radius secara manual, lingkaran langsung membesar/mengecil
        radiusInput.addEventListener('input', function() {
            var newRadius = parseInt(this.value) || 0;
            radiusCircle.setRadius(newRadius);
        });
    });
</script>
@endsection