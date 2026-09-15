@extends('layouts.admin')

@section('title', 'Detail Absensi')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Detail Absensi</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr><th>Pegawai</th><td>{{ $attendance->user->name ?? '-' }}</td></tr>
                    <tr><th>Tanggal</th><td>{{ $attendance->check_in_time ? $attendance->check_in_time->format('d-m-Y') : '-' }}</td></tr>
                    <tr><th>Check-In</th><td>{{ $attendance->check_in_time ? $attendance->check_in_time->format('H:i:s') : '-' }}</td></tr>
                    <tr><th>Check-Out</th><td>{{ $attendance->check_out_time ? $attendance->check_out_time->format('H:i:s') : '-' }}</td></tr>
                    <tr><th>Lokasi Check-In</th><td>{{ $attendance->latitude }}, {{ $attendance->longitude }}</td></tr>
                </table>
            </div>
            <div class="col-md-6 text-center">
                @if($attendance->check_in_photo)
                    <h5>Foto Check-In</h5>
                    <img src="{{ asset('storage/' . $attendance->check_in_photo) }}" class="img-fluid img-thumbnail" style="max-height:300px;">
                @endif
                @if($attendance->check_out_photo)
                    <h5>Foto Check-Out</h5>
                    <img src="{{ asset('storage/' . $attendance->check_out_photo) }}" class="img-fluid img-thumbnail" style="max-height:300px;">
                @endif
            </div>
        </div>
        <a href="{{ route('admin.absensi.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>
@endsection