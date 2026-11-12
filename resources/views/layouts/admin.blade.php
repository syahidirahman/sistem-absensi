<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
        min-height: 100vh;
        height: 100vh;
        background: #8d1117;
        padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            border-radius: 5px;
            margin: 2px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: #700d13;
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: #3b82f6;
            color: #fff;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .main-content {
            padding: 20px;
            background: #f4f6f9;
            min-height: 100vh;
        }
        .navbar-top {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 10px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .brand-text {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
        .brand-text i {
            color: #3498db;
            margin-right: 10px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .card-header {
            background: #fff;
            border-bottom: 2px solid #f0f0f0;
            font-weight: 600;
        }
        .btn-primary {
            background: #3498db;
            border: none;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-success {
            background: #27ae60;
            border: none;
        }
        .btn-success:hover {
            background: #229954;
        }
        .btn-danger {
            background: #e74c3c;
            border: none;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .btn-warning {
            background: #f39c12;
            border: none;
            color: #fff;
        }
        .btn-warning:hover {
            background: #d68910;
            color: #fff;
        }
        .btn-info {
            background: #17a2b8;
            border: none;
            color: #fff;
        }
        .btn-info:hover {
            background: #138496;
            color: #fff;
        }
        .table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .badge-success {
            background: #27ae60;
        }
        .badge-danger {
            background: #e74c3c;
        }
        .badge-warning {
            background: #f39c12;
        }
        .alert {
            border-radius: 8px;
        }
        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        /* =========================
   TOMBOL LOGOUT SIDEBAR
========================= */

.sidebar nav form {
    margin-top: 20px;
    padding: 0 12px;
}

.sidebar nav form .nav-link {
    width: 100%;
    margin: 0;
    padding: 11px 15px;

    background-color: #a91d27 !important;
    color: #ffffff !important;

    border: 1px solid #ff7777 !important;
    border-radius: 7px;

    font-size: 14px;
    font-weight: 600;

    text-align: left;
    opacity: 1 !important;

    transition: all 0.3s ease;
}

.sidebar nav form .nav-link i {
    color: #ffffff !important;
    margin-right: 10px;
}

.sidebar nav form .nav-link:hover {
    background-color: #dc3545 !important;
    border-color: #ff9999 !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- ========== SIDEBAR ========== -->
        <div class="col-md-2 col-lg-2 d-md-block sidebar p-0">
            <div class="text-center py-3">
                <h4 class="text-white">
                    <i class="bi bi-shield-lock"></i> Admin Panel
                </h4>
                <hr class="bg-light">
            </div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('admin.pegawai.*') ? 'active' : '' }}" 
                href="{{ route('admin.pegawai.index') }}">
                    <i class="bi bi-people-fill"></i> Pegawai
                </a>
                <a class="nav-link {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}" 
                href="{{ route('admin.absensi.index') }}">
                    <i class="bi bi-calendar-check-fill"></i> Absensi
                </a>
                <a class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}" 
                href="{{ route('admin.laporan.index') }}">
                    <i class="bi bi-file-earmark-text-fill"></i> Laporan
                </a>
                <a class="nav-link {{ request()->routeIs('admin.setting.*') ? 'active' : '' }}" 
                href="{{ route('admin.setting.index') }}">
                    <i class="bi bi-gear-fill"></i> Pengaturan  <!-- ← TAMBAHKAN INI -->
                </a>
                <a class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" 
                href="{{ route('admin.profile.index') }}">
                    <i class="bi bi-person-circle"></i> Profil
                </a>
                <hr class="bg-light">
                <form method="POST" action="{{ route('logout') }}" class="px-3">
                    @csrf
                    <button type="submit" class="nav-link text-danger" style="background:none; border:none; width:100%; text-align:left;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </nav>
        </div>

        <!-- ========== MAIN CONTENT ========== -->
        <div class="col-md-10 col-lg-10 main-content">
            
            <!-- Navbar Top -->
            <div class="navbar-top d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="bi bi-folder2-open"></i> 
                        @yield('title', 'Dashboard')
                    </h5>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3">
                        <i class="bi bi-person-circle"></i> 
                        {{ auth()->user()->name ?? 'Admin' }}
                    </span>
                    @if(auth()->user()->foto_profil)
                        <img src="{{ asset('storage/' . auth()->user()->foto_profil) }}" 
                             class="profile-avatar" alt="Foto Profil">
                    @else
                        <div class="profile-avatar bg-primary d-flex align-items-center justify-content-center text-white">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> 
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Content -->
            @yield('content')

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>