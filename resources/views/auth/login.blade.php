<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin - Sistem Absensi</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- CSS DARI ANDA -->
    <style>
        :root {
            --navy: #921318;
            --blue: #e62129;
            --paper: #ffffff;
            --ink: #273142;
            --muted: #697386;
            --line: #dce1e8;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            min-height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            color: var(--ink);
            background: #fff;
        }

        /* LAYOUT UTAMA */
        .login-shell {
            width: 100%;
            min-height: 100vh;
            height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            background: #fff;
        }

        /* PANEL KIRI */
        .brand-panel {
            position: relative;
            isolation: isolate;
            min-width: 0;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 5.2vh 5vw;
            color: #fff;
            background: #921318;
        }

        /* Pola geometris */
        .brand-panel::before {
            content: '';
            position: absolute;
            z-index: -1;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='52' height='90' viewBox='0 0 52 90'%3E%3Cpath d='M26 0V15L52 30V60L26 75V90M26 15L0 30V60L26 75' fill='none' stroke='%23ffffff' stroke-opacity='.18'/%3E%3C/svg%3E");
            background-size: 78px 135px;
            -webkit-mask-image: radial-gradient(circle at 12% 10%, #000 0%, transparent 62%);
            mask-image: radial-gradient(circle at 12% 10%, #000 0%, transparent 62%);
            pointer-events: none;
        }

        .brand-panel > * {
            position: relative;
            z-index: 1;
        }

        /* Judul Bawaslu */
        .brand-title {
            margin: 0;
            color: #fff;
            font-size: clamp(1.5rem, 2.2vw, 2rem);
            font-weight: 700;
            line-height: 1.12;
            letter-spacing: -.035em;
        }

        .brand-title::after {
            content: '';
            display: block;
            width: 42px;
            height: 3px;
            margin-top: .75rem;
            border-radius: 2px;
            background: #e62129;
        }

        /* Logo */
        .logo-stage {
            flex: 1;
            min-height: 0;
            display: grid;
            place-items: center;
        }

        .logo-tile {
            width: min(40%, 310px);
            min-width: 190px;
            aspect-ratio: 1;
            display: grid;
            place-items: center;
            padding: 1.8rem;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
        }

        .logo-tile img {
            display: block;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* PANEL KANAN */
        .form-panel {
            min-width: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 5vh 4vw;
            background: #fff;
        }

        .login-wrap {
            width: 100%;
            max-width: 520px;
        }

        /* Sembunyikan link kembali */
        .back-link {
            display: none;
        }

        /* CARD LOGIN */
        .login-card {
            width: 100%;
            max-width: 520px;
            padding: clamp(2rem, 3vw, 3rem);
            border: 1px solid #e1e4e9;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 8px 25px rgba(20, 30, 45, .045);
        }

        .login-card h1 {
            margin: 0 0 .45rem;
            color: #273142;
            font-size: clamp(1.35rem, 1.7vw, 1.65rem);
            font-weight: 700;
            line-height: 1.3;
            text-align: center;
        }

        .login-card .lead-text {
            margin: 0 0 1.65rem;
            color: #697386;
            font-size: .78rem;
            line-height: 1.5;
            text-align: center;
        }

        .login-card hr {
            margin: 0 0 1.5rem;
            border-color: var(--line);
            opacity: .5;
        }

        /* LABEL */
        .form-label {
            display: block;
            margin-bottom: .45rem;
            color: #273142;
            font-size: .76rem;
            font-weight: 700;
        }

        /* INPUT */
        .input-group {
            flex-wrap: nowrap;
            border-radius: 7px;
            transition: box-shadow .15s ease;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 .2rem rgba(230, 33, 41, .10);
        }

        .input-group .input-group-text,
        .input-group .form-control,
        .input-group .btn-toggle {
            min-height: 43px;
            padding: .65rem .75rem;
            border-color: var(--line);
            font-size: .78rem;
        }

        .input-group .input-group-text {
            color: #7b8493;
            background: #fff;
        }

        .input-group .form-control {
            min-width: 0;
            color: #273142;
            background: #fff;
        }

        .input-group .form-control::placeholder {
            color: #9aa3b2;
            opacity: 1;
        }

        /* Border merah input NIP */
        .login-card form > .mb-3:first-of-type .input-group .input-group-text,
        .login-card form > .mb-3:first-of-type .input-group .form-control {
            border-color: #ff777d;
        }

        .input-group .form-control:focus {
            border-color: #e62129;
            box-shadow: none;
        }

        /* TOMBOL LIHAT PASSWORD */
        .btn-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 43px;
            color: #8993a3;
            background: #fff;
            border: 1px solid var(--line);
            border-left: 0;
            cursor: pointer;
        }

        .btn-toggle:hover,
        .btn-toggle:focus-visible {
            color: #273142;
            background: #f8f9fb;
        }

        /* INGAT SAYA */
        .form-check {
            min-height: auto;
            margin-bottom: 1.25rem !important;
            padding-left: 1.5rem;
        }

        .form-check-input {
            width: .9rem;
            height: .9rem;
            margin-top: .15rem;
            margin-left: -1.5rem;
            border-color: #cbd2dc;
        }

        .form-check-input:checked {
            border-color: #e62129;
            background-color: #e62129;
        }

        .form-check-label {
            color: #778091 !important;
            font-size: .72rem !important;
            line-height: 1.4;
        }

        /* TOMBOL MASUK */
        .btn-masuk {
            --bs-btn-bg: #e62129;
            --bs-btn-border-color: #e62129;
            --bs-btn-hover-bg: #c91b22;
            --bs-btn-hover-border-color: #c91b22;
            --bs-btn-active-bg: #ad171d;
            --bs-btn-active-border-color: #ad171d;
            --bs-btn-disabled-bg: #e62129;
            --bs-btn-disabled-border-color: #e62129;
            --bs-btn-focus-shadow-rgb: 230, 33, 41;

            min-height: 43px;
            padding: .7rem 1rem;
            border-radius: 7px;
            color: #fff;
            font-size: .78rem;
            font-weight: 600;
        }

        /* PEMISAH */
        .divider-atau {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 1.1rem 0;
            color: #9aa2af;
            font-size: .72rem;
        }

        .divider-atau::before,
        .divider-atau::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e8ed;
        }

        /* TOMBOL ADMIN */
        .btn-admin {
            min-height: 43px;
            padding: .65rem 1rem;
            border-color: var(--line);
            border-radius: 7px;
            color: #273142;
            font-size: .76rem;
            font-weight: 500;
        }

        .btn-admin:hover {
            border-color: #c5ccd7;
            color: #202938;
            background: #f7f8fa;
        }

        .text-danger.small {
            font-size: .75rem;
        }

        /* RESPONSIVE TABLET DAN HP */
        @media (max-width: 767.98px) {

            .login-shell {
                min-height: 100vh;
                height: auto;
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }

            .brand-panel {
                min-height: 24vh;
                flex-direction: row;
                align-items: center;
                gap: 1.25rem;
                padding: 1.25rem 1.5rem;
            }

            .brand-title {
                font-size: 1.35rem;
            }

            .brand-title::after {
                width: 36px;
                margin-top: .55rem;
            }

            .logo-stage {
                flex: 0 0 auto;
                order: -1;
            }

            .logo-tile {
                width: 88px;
                min-width: 88px;
                padding: .65rem;
                border-radius: 12px;
            }

            .form-panel {
                min-height: 76vh;
                padding: 2.5rem 1.25rem;
            }

            .login-wrap,
            .login-card {
                max-width: 440px;
            }

            .login-card {
                padding: 2rem 1.5rem;
            }
        }

        @media (max-width: 380px) {

            .brand-panel {
                padding: 1rem;
                gap: .9rem;
            }

            .brand-title {
                font-size: 1.1rem;
            }

            .logo-tile {
                width: 72px;
                min-width: 72px;
            }

            .login-card {
                padding: 1.5rem 1.1rem;
            }
        }
    </style>
</head>
<body>

<div class="login-shell">
    <!-- Panel Kiri (Identitas) -->
    <div class="brand-panel">
        <h1 class="brand-title">Sistem Absensi<br>Bawaslu Prov Kalsel</h1>
        <div class="logo-stage">
            <div class="logo-tile">
                <!-- Pastikan gambar logo ada di public/images/logo-bawaslu.png -->
                <img src="{{ asset('images/logo-bawaslu.png') }}" alt="Logo Bawaslu">
            </div>
        </div>
    </div>

    <!-- Panel Kanan (Form Login) -->
    <div class="form-panel">
        <div class="login-wrap">
            <a href="{{ url('/') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Kembali ke halaman utama
            </a>

            <div class="login-card">
                <h1>Selamat Datang</h1>
                <p class="lead-text">Silahkan masuk menggunakan akun admin untuk melanjutkan</p>
                <hr>

                <!-- Form Login Admin -->
<form method="POST" action="{{ route('login') }}" autocomplete="on">
    @csrf

    <!-- Input Email -->
    <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <!-- Tambahan autocomplete="username" -->
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                   placeholder="Masukkan email" value="{{ old('email') }}" required autofocus autocomplete="username">
        </div>
        @error('email')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- Input Password -->
    <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <!-- Tambahan autocomplete="current-password" -->
            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                   placeholder="Masukkan password" required autocomplete="current-password">
            <span class="btn-toggle" style="cursor:pointer;" onclick="togglePassword()">
                <i class="bi bi-eye" id="toggleIcon"></i>
            </span>
        </div>
        @error('password')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <!-- INGAT SAYA (Remember Me) -->
    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label text-muted" for="remember" style="font-size: 0.9rem;">
            Ingat saya
        </label>
    </div>

    <!-- Tombol Masuk -->
    <button type="submit" class="btn btn-masuk w-100">Masuk</button>

    <div class="divider-atau">atau</div>

    <!-- Tombol ke Login Pegawai -->
    <a href="{{ url('/pegawai/login') }}" class="btn btn-outline-secondary btn-admin w-100">
        <i class="bi bi-person-badge me-1"></i> Masuk sebagai pegawai
    </a>
</form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
</body>
</html>