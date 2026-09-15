@extends('layouts.admin')

@section('title', 'Laporan Absensi')

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Export Laporan Absensi</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.laporan.pdf') }}" method="POST" id="formLaporan">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Pilih Pegawai</label>
                        <select name="pegawai_id" class="form-control">
                            <option value="">Semua Pegawai</option>
                            @foreach($pegawai as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->nip }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <button type="submit" formaction="{{ route('admin.laporan.pdf') }}" class="btn btn-danger w-100">
                        <i class="bi bi-file-pdf"></i> Export PDF
                    </button>
                </div>
                <div class="col-md-6">
                    <button type="submit" formaction="{{ route('admin.laporan.excel') }}" class="btn btn-success w-100">
                        <i class="bi bi-file-excel"></i> Export Excel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection