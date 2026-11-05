@extends('layouts.pegawai')

@section('title', 'Dashboard Pegawai - BAWASLU')

@section('content')
<style>
    :root {
        --bawaslu-orange: #f26522;
        --bawaslu-red: #d9381e;
        --bawaslu-gold: #d49a26;
        --bawaslu-navy: #0f172a;
        --bawaslu-dark-blue: #1b263b;
    }

    /* Banner Selamat Datang Tema Bawaslu */
    .bawaslu-banner {
        background: linear-gradient(135deg, var(--bawaslu-navy) 0%, var(--bawaslu-dark-blue) 100%);
        border-radius: 16px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        border-left: 6px solid var(--bawaslu-orange);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);
    }

    .bawaslu-banner::after {
        content: "";
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(242, 101, 34, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .badge-bawaslu-gold {
        background-color: var(--bawaslu-gold);
        color: #ffffff;
        font-weight: 600;
        letter-spacing: 1px;
        font-size: 11px;
        padding: 5px 12px;
        border-radius: 20px;
    }

    /* Card Custom */
    .card-bawaslu {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        background: #ffffff;
    }

    .card-bawaslu-header {
        background: transparent;
        border-bottom: 2px solid #f1f5f9;
        padding: 18px 24px;
    }

    /* Tombol Aksi Khas Bawaslu */
    .btn-bawaslu-masuk {
        background: linear-gradient(135deg, var(--bawaslu-orange) 0%, var(--bawaslu-red) 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 24px;
        box-shadow: 0 4px 15px rgba(242, 101, 34, 0.3);
        transition: all 0.3s ease;
    }

    .btn-bawaslu-masuk:hover {
        background: linear-gradient(135deg, var(--bawaslu-red) 0%, #b82a14 100%);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(242, 101, 34, 0.4);
    }

    .btn-bawaslu-pulang {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 24px;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        transition: all 0.3s ease;
    }

    .btn-bawaslu-pulang:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        color: #ffffff;
        transform: translateY(-2px);
    }

    /* Status Box */
    .status-bawaslu-box {
        border-left: 4px solid var(--bawaslu-orange);
        background-color: #fff8f5;
        border-radius: 12px;
        padding: 18px 22px;
    }

    /* Tabel Riwayat */
    .table-bawaslu th {
        background-color: var(--bawaslu-navy);
        color: #ffffff;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    .table-bawaslu td {
        vertical-align: middle;
        color: #334155;
        font-size: 14px;
    }

    .badge-selesai {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
    }

    .badge-belum {
        background-color: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
    }
</style>

<!-- Banner Header Bawaslu -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bawaslu-banner p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <span class="badge badge-bawaslu-gold mb-2">
                        <i class="bi bi-shield-check me-1"></i> BADAN PENGAWAS PEMILIHAN UMUM
                    </span>
                    <h2 class="h3 font-weight-bold mb-1">Selamat Datang, {{ auth()->user()->name }}</h2>
                    <p class="mb-0 text-white-50" style="font-size: 14px;">
                        <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>
                <div>
                    @if(($status ?? 'Belum absen') == 'Belum absen')
                        <a href="{{ route('pegawai.absensi.index') }}" class="btn btn-bawaslu-masuk">
                            <i class="bi bi-camera-fill me-2"></i> Presensi Masuk
                        </a>
                    @elseif(($status ?? '') == 'Sudah Check-In, belum Check-Out')
                        <a href="{{ route('pegawai.absensi.index') }}" class="btn btn-bawaslu-pulang">
                            <i class="bi bi-box-arrow-right me-2"></i> Presensi Pulang
                        </a>
                    @else
                        <button class="btn btn-light text-muted fw-bold" disabled>
                            <i class="bi bi-check-circle-fill text-success me-1"></i> Presensi Hari Ini Selesai
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informasi Status Absensi Hari Ini -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card card-bawaslu">
            <div class="card-body p-4">
                <div class="status-bawaslu-box d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-white rounded-circle shadow-sm text-warning">
                            <i class="bi bi-clock-history fs-3" style="color: var(--bawaslu-orange);"></i>
                        </div>
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold">Status Kehadiran Hari Ini</span>
                            <h5 class="mb-0 fw-bold text-dark mt-1">
                                {{ $status ?? 'Belum absen' }}
                            </h5>
                        </div>
                    </div>
                    <div>
                        <span class="text-muted small">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> Terverifikasi GPS &amp; Face Recognition
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Riwayat Absensi -->
<div class="row">
    <div class="col-md-12">
        <div class="card card-bawaslu">
            <div class="card-header card-bawaslu-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-journal-check me-2" style="color: var(--bawaslu-orange);"></i>Riwayat Presensi (7 Hari Terakhir)
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bawaslu align-middle mb-0">
                                                <thead>
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Jam Check-In</th>
                                <th>Jam Check-Out</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Keterangan</th> <!-- TAMBAHAN -->
                            </tr>
                        </thead>
                                                <tbody>
                            @forelse($histories as $h)
                            <tr>
                                <td class="ps-4 fw-semibold">
                                    <i class="bi bi-calendar-event me-2 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($h['date'])->format('d-m-Y') }}
                                </td>
                                
                                @if($h['is_alpha'])
                                    <td><span class="text-muted">-</span></td>
                                    <td><span class="text-muted">-</span></td>
                                    <td class="text-center">
                                        <span class="badge bg-danger" style="border-radius:20px; padding:6px 14px;">
                                            <i class="bi bi-x-circle-fill me-1"></i> Alpa
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger" style="border-radius:20px; padding:6px 14px;">
                                            Tidak Hadir
                                        </span>
                                    </td>
                                @else
                                    @php $a = $h['attendance']; @endphp
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-box-arrow-in-right text-success me-1"></i>
                                            {{ $a->check_in_time->format('H:i:s') }} WITA
                                        </span>
                                    </td>
                                    <td>
                                        @if($a->check_out_time)
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-box-arrow-right text-danger me-1"></i>
                                                {{ $a->check_out_time->format('H:i:s') }} WITA
                                            </span>
                                        @else
                                            <span class="text-muted fs-6">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($a->check_out_time)
                                            <span class="badge-selesai">
                                                <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                            </span>
                                        @else
                                            <span class="badge-belum">
                                                <i class="bi bi-exclamation-circle-fill me-1"></i> Belum Check-Out
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($setting && $setting->jam_masuk)
                                            @php
                                                $jamMasuk = \Carbon\Carbon::parse($setting->jam_masuk);
                                                $toleransi = $setting->toleransi_keterlambatan ?? 0;
                                                $batasTepatWaktu = $jamMasuk->copy()->addMinutes($toleransi);
                                                
                                                if ($a->check_in_time->gt($batasTepatWaktu)) {
                                                    // Tambahkan fungsi abs() agar hasilnya positif
                                                    $selisih = abs(round($a->check_in_time->diffInMinutes($batasTepatWaktu)));
                                                    echo '<span class="badge bg-danger" style="border-radius:20px; padding:6px 14px;">Terlambat ' . $selisih . ' Menit</span>';
                                                } else {
                                                    echo '<span class="badge bg-success" style="border-radius:20px; padding:6px 14px;">Tepat Waktu</span>';
                                                }
                                            @endphp
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                    Belum ada riwayat presensi dalam 7 hari terakhir.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection