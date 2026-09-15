@extends('layouts.admin')

@section('title', 'Data Pegawai')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Data Pegawai</h3>
    <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Pegawai
    </a>
</div>

<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>NIP</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pegawai as $p)
        <tr>
            <td>{{ $p->nip }}</td>
            <td>{{ $p->name }}</td>
            <td>{{ $p->email }}</td>
            <td>
                <span class="badge {{ $p->role == 'admin' ? 'bg-danger' : 'bg-primary' }}">
                    {{ ucfirst($p->role) }}
                </span>
            </td>
            <td>
                <span class="badge bg-{{ $p->is_active ? 'success' : 'danger' }}">
                    {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </td>
            <td>
                <a href="{{ route('admin.pegawai.edit', $p->id) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="{{ route('admin.pegawai.face.register', $p->id) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-person-badge"></i> Wajah
                </a>
                <a href="{{ route('admin.pegawai.toggle-active', $p->id) }}" class="btn btn-sm btn-info">
                    <i class="bi bi-arrow-repeat"></i>
                </a>
                <form action="{{ route('admin.pegawai.destroy', $p->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">Belum ada data pegawai</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection