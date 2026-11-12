@extends('layouts.pegawai')

@section('title', 'Dashboard Pegawai - BAWASLU')

@section('content')

<style>
    :root {
        --bawaslu-orange: #f4511e;
        --bawaslu-red: #8d1117;
        --bawaslu-gold: #b98212;
        --bawaslu-navy: #1b2636;
        --bawaslu-dark-blue: #263449;
        --bawaslu-muted: #718096;
        --bawaslu-page: #f0f2f5;
    }

    /* Background halaman */
    body {
        background-color: var(--bawaslu-page);
        color: #172033;
        font-family: Arial, Helvetica, sans-serif;
    }

    /* Banner selamat datang */
    .bawaslu-banner {
        background: #8d1117;
        border: none;
        border-left: 6px solid var(--bawaslu-orange);
        border-radius: 10px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(15, 23, 42, .08);
        min-height: 126px;
        padding: 24px !important;
    }

    .bawaslu-banner::after {
        content: none;
    }

    .bawaslu-banner h2 {
        color: #fff;
        font-size: 23px;
        font-weight: 700;
        margin-bottom: 5px !important;
    }

    .bawaslu-banner p {
        color: #c7cbd2 !important;
        font-size: 12px !important;
    }

    /* Badge Bawaslu */
    .badge-bawaslu-gold {
        display: inline-block;
        background: #b98212;
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .35px;
        line-height: 1.2;
        padding: 4px 11px;
        border-radius: 20px;
    }

    /* Card */
    .card-bawaslu {
        background: #fff;
        border: 0;
        border-radius: 11px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .card-bawaslu .card-body {
        padding: 22px !important;
    }

    .card-bawaslu-header {
        background: #fff;
        border-bottom: 1px solid #e8edf3;
        padding: 4px 20px !important;
        min-height: 25px;
    }

    .card-bawaslu-header h5 {
        color: #263449 !important;
        font-size: 16px;
        font-weight: 700;
    }

    /* Tombol presensi masuk */
    .btn-bawaslu-masuk {
        background: #f4511e;
        color: #fff;
        border: 0;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        padding: 9px 17px;
        box-shadow: none;
        transition: background-color .2s ease, transform .2s ease;
    }

    .btn-bawaslu-masuk:hover {
        background: #df4215;
        color: #fff;
        transform: translateY(-1px);
    }

    /* Tombol presensi pulang */
    .btn-bawaslu-pulang {
        background: #b98212;
        color: #fff;
        border: 0;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        padding: 9px 17px;
        box-shadow: none;
        transition: background-color .2s ease, transform .2s ease;
    }

    .btn-bawaslu-pulang:hover {
        background: #98690d;
        color: #fff;
        transform: translateY(-1px);
    }

    /* Status kehadiran */
    .status-bawaslu-box {
        background: #fff;
        border-left: 5px solid var(--bawaslu-orange);
        border-radius: 9px;
        padding: 18px 15px !important;
        min-height: 86px;
    }

    .status-bawaslu-box .rounded-circle {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 !important;
        border: 1px solid #ffd1b8;
        background: #fffaf7 !important;
        box-shadow: none !important;
    }

    .status-bawaslu-box .rounded-circle i {
        font-size: 19px !important;
    }

    .status-bawaslu-box h5 {
        color: #172033 !important;
        font-size: 21px;
        font-weight: 700;
    }

    .status-bawaslu-box .text-muted {
        color: #8293ad !important;
        font-size: 10px;
        letter-spacing: .45px;
    }

    /* Tabel riwayat presensi */
    .table-bawaslu {
        margin-bottom: 0;
        width: 100%;
        border-collapse: collapse;
    }

    .table-bawaslu thead th {
        background: #1b2636;
        color: #fff;
        border: 0;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .35px;
        text-transform: uppercase;
        padding: 10px 12px;
        white-space: nowrap;
    }

    .table-bawaslu tbody td {
        background: #fff;
        color: #334155;
        border-top: 1px solid #edf0f4;
        font-size: 12px;
        padding: 10px 12px;
        vertical-align: middle;
    }

    .table-bawaslu tbody tr {
        height: 45px;
    }

    .table-bawaslu tbody tr:hover td {
        background: #fafbfc;
    }

    /* Badge status */
    .table-bawaslu .badge,
    .badge-selesai,
    .badge-belum {
        display: inline-block;
        border: 0;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        line-height: 1.2;
        padding: 5px 11px;
    }

    .badge-selesai {
        background: #d9f9e7;
        color: #168044;
    }

    .badge-belum {
        background: #fff1cc;
        color: #a86b00;
    }

    .table-bawaslu .badge.bg-danger {
        background: #e93649 !important;
        color: #fff !important;
    }

    .table-bawaslu .badge.bg-success {
        background: #148342 !important;
        color: #fff !important;
    }

    .table-bawaslu .badge.bg-light {
        background: #f8fafc !important;
        color: #475569 !important;
        border: 1px solid #e5eaf0 !important;
    }

    /* Responsive mobile */
    @media (max-width: 767.98px) {
        .bawaslu-banner {
            padding: 20px !important;
        }

        .bawaslu-banner h2 {
            font-size: 20px;
        }

        .status-bawaslu-box {
            align-items: flex-start !important;
        }

        .card-bawaslu-header {
            padding: 10px 14px !important;
        }

        .table-bawaslu thead th,
        .table-bawaslu tbody td {
            padding-left: 9px;
            padding-right: 9px;
        }
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