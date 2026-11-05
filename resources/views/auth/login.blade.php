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
            --navy: #82651f;
            --blue: #E62129;
            --paper: #fbf5f5;
            --ink: #34361c;
            --muted: #34361c;
            --line: #82651f;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            color: var(--ink);
            background: #fff;
        }

        .login-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        }

        /* ---------- Panel kiri: identitas ---------- */
        .brand-panel {
            position: relative;
            overflow: hidden;
            color: #fff;
            padding: 3rem 3.5rem;
            display: flex;
            flex-direction: column;
            background-color: var(--navy);
            background-image:
                radial-gradient(90% 70% at 0% 0%, rgba(255, 255, 255, .10), transparent 55%),
                radial-gradient(120% 80% at 100% 100%, rgba(0, 0, 0, .35), transparent 60%);
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='52' height='90' viewBox='0 0 52 90'%3E%3Cpath d='M26 0V15L52 30V60L26 75V90M26 15L0 30V60L26 75' fill='none' stroke='%23ffffff' stroke-opacity='.2'/%3E%3C/svg%3E");
            background-size: 78px 135px;
            -webkit-mask-image: radial-gradient(circle at 50% 62%, #000 0%, transparent 62%);
            mask-image: radial-gradient(circle at 50% 62%, #000 0%, transparent 62%);
            pointer-events: none;
        }

        .brand-panel > * {
            position: relative;
            z-index: 1;
        }

        .brand-title::after {
            content: '';
            display: block;
            width: 56px;
            height: 4px;
            border-radius: 2px;
            background: #E62129;
            margin-top: 1rem;
        }

        .brand-title {
            font-size: clamp(1.75rem, 3vw, 2.6rem);
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.01em;
            margin: 0;
        }

        .logo-stage {
            flex: 1;
            display: grid;
            place-items: center;
        }

        .logo-tile {
            width: min(320px, 72%);
            aspect-ratio: 1;
            background: #fff;
            border-radius: 20px;
            display: grid;
            place-items: center;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .logo-tile img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* ---------- Panel kanan: form ---------- */
        .form-panel {
            background: var(--paper);
            display: grid;
            place-items: center;
            padding: 2rem 1.25rem;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 2.25rem 2rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        .login-wrap {
            width: 100%;
            max-width: 440px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            margin-bottom: .9rem;
            padding: .25rem .1rem;
            color: var(--ink);
            font-size: .9rem;
            font-weight: 500;
            text-decoration: none;
            border-radius: .35rem;
        }

        .back-link i { transition: transform .15s ease; }
        .back-link:hover { color: var(--navy); text-decoration: underline; text-underline-offset: 3px; }
        .back-link:hover i { transform: translateX(-3px); }

        .login-card h1 {
            font-size: 1.6rem;
            font-weight: 700;
            text-align: center;
            margin: 0 0 .35rem;
        }

        .login-card .lead-text {
            color: var(--muted);
            text-align: center;
            margin: 0 0 1.25rem;
            font-size: 0.95rem;
        }

        .login-card hr {
            border-color: var(--line);
            opacity: 0.3;
            margin: 0 0 1.5rem;
        }

        .form-label {
            font-weight: 600;
            font-size: .9rem;
            margin-bottom: .4rem;
        }

        .input-group {
            border-radius: .5rem;
            transition: box-shadow .15s ease;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 .2rem #e621276d;
        }

        .input-group .input-group-text,
        .input-group .form-control,
        .input-group .btn-toggle {
            border-color: var(--line);
            padding-top: .65rem;
            padding-bottom: .65rem;
        }

        .input-group .input-group-text {
            background: #fff;
            color: var(--muted);
        }

        .input-group .form-control:focus {
            box-shadow: none;
            border-color: var(--line);
        }

        .btn-toggle {
            background: #fff;
            color: var(--muted);
            border: 1px solid var(--line);
        }

        .btn-toggle:hover, .btn-toggle:focus-visible {
            background: #fff;
            color: var(--ink);
        }

        .btn-masuk {
            --bs-btn-bg: var(--blue);
            --bs-btn-border-color: var(--blue);
            --bs-btn-hover-bg: #9b191d;
            --bs-btn-hover-border-color: #430808;
            --bs-btn-active-bg: #6d1316;
            --bs-btn-active-border-color: #360606;
            --bs-btn-disabled-bg: var(--blue);
            --bs-btn-disabled-border-color: var(--blue);
            --bs-btn-focus-shadow-rgb: 91, 11, 11;
            font-weight: 600;
            padding: .7rem 1rem;
            color: white;
        }

        .divider-atau {
            display: flex;
            align-items: center;
            gap: .75rem;
            color: var(--muted);
            font-size: .85rem;
            margin: 1.25rem 0;
        }

        .divider-atau::before, .divider-atau::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--line);
            opacity: 0.3;
        }

        .btn-admin {
            border-color: var(--line);
            color: var(--ink);
            font-weight: 500;
            padding: .65rem 1rem;
        }

        .btn-admin:hover {
            background: var(--paper);
            border-color: var(--muted);
            color: var(--ink);
        }

        /* ---------- Layar kecil ---------- */
        @media (max-width: 991.98px) {
            .login-shell {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }

            .brand-panel {
                flex-direction: row;
                align-items: center;
                gap: 1rem;
                padding: 1.25rem;
            }

            .brand-title { font-size: 1.25rem; }
            .brand-title::after { width: 40px; height: 3px; margin-top: .6rem; }
            
            .logo-stage { flex: 0 0 auto; order: -1; }
            .logo-tile { width: 64px; border-radius: 14px; padding: .6rem; }

            .form-panel {
                align-items: start;
                padding-top: 1.5rem;
            }
            .login-card { padding: 1.75rem 1.25rem; }
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