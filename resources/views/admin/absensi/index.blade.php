@extends('layouts.admin')

@section('title', 'Data Absensi')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>📊 Data Absensi</h4>
    </div>
    <div class="card-body">
        <!-- ========== FORM FILTER ========== -->
        <form method="GET" action="{{ route('admin.absensi.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-2">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" 
                               value="{{ $request->tanggal_mulai ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-2">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" 
                               value="{{ $request->tanggal_selesai ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-2">
                        <label>Pilih Pegawai</label>
                        <select name="pegawai_id" class="form-control">
                            <option value="">Semua Pegawai</option>
                            @foreach($pegawai as $p)
                                <option value="{{ $p->id }}" 
                                    {{ $request->pegawai_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->nip }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-2">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ $request->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="terlambat" {{ $request->status == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            <option value="selesai" {{ $request->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="belum_checkout" {{ $request->status == 'belum_checkout' ? 'selected' : '' }}>Belum Check-Out</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <div class="mb-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <a href="{{ route('admin.absensi.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                    </a>
                </div>
            </div>
        </form>

        <!-- ========== TABEL DATA ========== -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pegawai</th>
                        <th>Tanggal</th>
                        <th>Check-In</th>
                        <th>Foto In</th>
                        <th>Check-Out</th>
                        <th>Foto Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $i => $a)
                    <tr>
                        <td>{{ $attendances->firstItem() + $i }}</td>
                        <td>{{ $a->user->name ?? '-' }}</td>
                        <td>{{ $a->check_in_time ? $a->check_in_time->format('d-m-Y') : '-' }}</td>
                        <td>{{ $a->check_in_time ? $a->check_in_time->format('H:i:s') : '-' }}</td>
                        <td>
                            @if($a->photo_in)
                                <img src="{{ asset('storage/' . $a->photo_in) }}" width="50" height="50" class="img-thumbnail" style="cursor:pointer; object-fit:cover;" onclick="window.open(this.src)" alt="Foto Check-In">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $a->check_out_time ? $a->check_out_time->format('H:i:s') : '-' }}</td>
                        <td>
                            @if($a->photo_out)
                                <img src="{{ asset('storage/' . $a->photo_out) }}" width="50" height="50" class="img-thumbnail" style="cursor:pointer; object-fit:cover;" onclick="window.open(this.src)" alt="Foto Check-Out">
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @php
                                $status = 'Hadir';
                                $badge = 'success';
                                if ($a->check_in_time && $a->check_in_time->format('H:i:s') > '08:00:00') {
                                    $status = 'Terlambat';
                                    $badge = 'warning';
                                }
                                if (!$a->check_out_time) {
                                    $status = 'Belum Check-Out';
                                    $badge = 'danger';
                                }
                                if ($a->check_in_time && $a->check_out_time) {
                                    $status = 'Selesai';
                                    $badge = 'success';
                                }
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data absensi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ========== PAGINATION ========== -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} 
                dari {{ $attendances->total() }} data
            </div>
            <div>
                {{ $attendances->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection