<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .card {
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
            background: white;
            max-width: 500px;
            width: 90%;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .btn {
            border-radius: 30px;
            padding: 12px 40px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-login {
            background: #667eea;
            color: white;
        }
        .btn-login:hover {
            background: #5a67d8;
        }
        .btn-pegawai {
            background: #48bb78;
            color: white;
        }
        .btn-pegawai:hover {
            background: #38a169;
        }
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>📋 Selamat Datang</h1>
        <p class="text-muted">Sistem Absensi Berbasis Wajah &amp; GPS</p>
        <div class="btn-group">
            <button class="btn btn-login" onclick="window.location.href='/login'">🔑 Login Admin</button>
            <button class="btn btn-pegawai" onclick="window.location.href='/pegawai/login'">👤 Login Pegawai</button>
        </div>
        <p class="mt-3 text-muted" style="font-size:14px;">
            Pendaftaran hanya melalui Admin Panel.
        </p>
    </div>
</body>
</html>