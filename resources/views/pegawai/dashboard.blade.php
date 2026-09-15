@extends('layouts.pegawai')

@section('title', 'Dashboard Pegawai')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>Selamat Datang, {{ auth()->user()->name }}</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Status Hari Ini:</strong> {{ $status ?? 'Belum absen' }}
                </div>

                @if(($status ?? 'Belum absen') == 'Belum absen')
                    <a href="{{ route('pegawai.absensi.index') }}" class="btn btn-primary">
                        <i class="bi bi-camera"></i> Mulai Absen
                    </a>
                @elseif(($status ?? '') == 'Sudah Check-In, belum Check-Out')
                    <a href="{{ route('pegawai.absensi.index') }}" class="btn btn-warning">
                        <i class="bi bi-camera"></i> Check-Out
                    </a>
                @else
                    <a href="#" class="btn btn-secondary" disabled>
                        <i class="bi bi-check-circle"></i> Selesai
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Riwayat Absensi (7 Hari Terakhir)</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories ?? [] as $h)
                        <tr>
                            <td>{{ $h->check_in_time->format('d-m-Y') }}</td>
                            <td>{{ $h->check_in_time->format('H:i:s') }}</td>
                            <td>{{ $h->check_out_time ? $h->check_out_time->format('H:i:s') : '-' }}</td>
                            <td>
                                @if($h->check_out_time)
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning">Belum Check-Out</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada riwayat absensi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection