<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Presensi Pegawai - BAWASLU')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bawaslu-orange: #f26522;
            --bawaslu-orange-hover: #d95318;
            --bawaslu-navy: #0f172a;
            --bawaslu-dark-blue: #1b263b;
            --bawaslu-gold: #d49a26;
            --bawaslu-bg: #f8fafc;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bawaslu-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar Utama */
        .navbar-bawaslu {
            background-color: var(--bawaslu-navy) !important;
            border-bottom: 4px solid var(--bawaslu-orange);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            padding: 12px 0;
        }

        .navbar-bawaslu .navbar-brand {
            font-weight: 700;
            font-size: 1.1rem;
            color: #ffffff !important;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar-bawaslu .navbar-brand img {
            height: 40px;
            width: auto;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-title {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .brand-sub {
            font-size: 11px;
            color: var(--bawaslu-gold);
            font-weight: 500;
        }

        /* Menu Navigasi */
        .navbar-bawaslu .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-bawaslu .nav-link:hover,
        .navbar-bawaslu .nav-link.active {
            color: #ffffff !important;
            background-color: rgba(242, 101, 34, 0.2);
        }

        /* Tombol Logout */
        .btn-logout {
            background: rgba(220, 38, 38, 0.15);
            color: #f87171 !important;
            border: 1px solid rgba(220, 38, 38, 0.3);
            border-radius: 8px;
            padding: 6px 16px !important;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: #dc2626 !important;
            color: #ffffff !important;
            border-color: #dc2626;
        }

        /* Alert Notifikasi */
        .custom-alert {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Footer */
        .footer-bawaslu {
            margin-top: auto;
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 20px 0;
            font-size: 13px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Navbar Bawaslu -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-bawaslu sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('pegawai.dashboard') }}">
                <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Logo BAWASLU">
                <div class="brand-text">
                    <span class="brand-title">E-PRESENSI</span>
                    <span class="brand-sub">BAWASLU RI</span>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pegawai.dashboard') ? 'active' : '' }}" href="{{ route('pegawai.dashboard') }}">
                            <i class="bi bi-grid-1x2-fill me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pegawai.absensi.*') ? 'active' : '' }}" href="{{ route('pegawai.absensi.index') }}">
                            <i class="bi bi-camera-fill me-1"></i> Presensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pegawai.profile.*') ? 'active' : '' }}" href="{{ route('pegawai.profile.index') }}">
                            <i class="bi bi-person-circle me-1"></i> Profil
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <form method="POST" action="{{ route('pegawai.logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-logout w-100 text-start">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="container my-4">
        @if(session('success'))
            <div class="alert alert-success custom-alert alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger custom-alert alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="footer-bawaslu text-center">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} <strong>Badan Pengawas Pemilihan Umum</strong>. Sistem Absensi Berbasis Wajah &amp; GPS.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>