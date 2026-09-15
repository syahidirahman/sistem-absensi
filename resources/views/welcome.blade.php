<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistem Absensi Bawaslu</title>


    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <style>

        /* =====================================================
           RESET
        ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        html {
            width: 100%;
            min-height: 100%;
        }


        body {
            width: 100%;
            min-height: 100vh;

            font-family: 'Montserrat', sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #ffffff 0%,
                    #f8f8f8 50%,
                    #eeeeee 100%
                );

            position: relative;

            overflow-x: hidden;
        }


        /* =====================================================
           BACKGROUND
        ===================================================== */

        .background-decoration {
            position: fixed;

            z-index: 0;

            pointer-events: none;
        }


        /*
         * Dekorasi kiri bawah
         */

        .decoration-left {
            width: 700px;
            height: 190px;

            left: -220px;
            bottom: -30px;

            background:
                linear-gradient(
                    120deg,
                    #151515 0%,
                    #151515 63%,

                    #b8943d 63%,
                    #b8943d 67%,

                    #c90000 67%,
                    #c90000 100%
                );

            transform: rotate(25deg);
        }


        /*
         * Dekorasi kanan bawah
         */

        .decoration-right {
            width: 750px;
            height: 170px;

            right: -280px;
            bottom: -30px;

            background:
                linear-gradient(
                    120deg,

                    transparent 0%,
                    transparent 15%,

                    #c90000 15%,
                    #c90000 55%,

                    #ffffff 55%,
                    #ffffff 70%,

                    #b8943d 70%,
                    #b8943d 74%,

                    #151515 74%,
                    #151515 100%
                );

            transform: rotate(-25deg);
        }


        /*
         * Pola Bawaslu transparan di background
         */

        .background-symbol {
            position: fixed;

            left: 4%;
            top: 42%;

            width: 220px;
            height: 260px;

            opacity: 0.035;

            z-index: 0;

            border: 35px solid #111;

            border-radius: 45% 45% 20% 20%;

            transform: rotate(0deg);
        }


        /* =====================================================
           HEADER BRAND
        ===================================================== */

        .brand-header {

            position: absolute;

            top: 30px;
            left: 45px;

            display: flex;

            align-items: center;

            z-index: 10;
        }


        .brand-logo {

            width: 65px;
            height: 65px;

            object-fit: contain;

            display: block;
        }


        .brand-text {

            margin-left: 14px;
        }


        .brand-text h2 {

            color: #111111;

            font-size: 28px;

            font-weight: 800;

            letter-spacing: 2px;

            line-height: 1;

            margin: 0;
        }


        .brand-text p {

            color: #333333;

            font-size: 9px;

            font-weight: 600;

            letter-spacing: 1px;

            margin-top: 5px;

            line-height: 1.2;
        }


        /* =====================================================
           MAIN CONTAINER
        ===================================================== */

        .main-container {

            min-height: 100vh;

            width: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 40px 20px;

            position: relative;

            z-index: 2;
        }


        /* =====================================================
           CARD
        ===================================================== */

        .attendance-card {

            width: 100%;

            max-width: 510px;

            background: rgba(255, 255, 255, 0.97);

            border-radius: 25px;

            padding: 42px 45px 28px;

            text-align: center;

            position: relative;

            border: 1px solid rgba(0, 0, 0, 0.06);

            box-shadow:
                0 25px 70px rgba(0, 0, 0, 0.15);

            backdrop-filter: blur(5px);
        }


        /*
         * Garis merah di atas card
         */

        .attendance-card::before {

            content: "";

            position: absolute;

            top: 0;
            left: 50%;

            transform: translateX(-50%);

            width: 120px;
            height: 5px;

            background:
                linear-gradient(
                    90deg,
                    #c90000 0%,
                    #c90000 50%,
                    #b8943d 50%,
                    #b8943d 100%
                );

            border-radius: 0 0 10px 10px;
        }


        /* =====================================================
           LOGO CARD
        ===================================================== */

        .logo-container {

            width: 100px;
            height: 100px;

            margin: 0 auto 10px;

            display: flex;

            align-items: center;
            justify-content: center;
        }


        .logo-container img {

            width: 92px;
            height: 92px;

            object-fit: contain;

            display: block;
        }


        /* =====================================================
           DECORATIVE LINE
        ===================================================== */

        .gold-line {

            width: 125px;

            height: 4px;

            margin: 0 auto 20px;

            border-radius: 10px;

            background:
                linear-gradient(
                    90deg,

                    #c90000 0%,
                    #c90000 45%,

                    #b8943d 45%,
                    #b8943d 100%
                );
        }


        /* =====================================================
           TITLE
        ===================================================== */

        .attendance-card h1 {

            color: #111111;

            font-size: 33px;

            font-weight: 800;

            line-height: 1.2;

            margin-bottom: 6px;
        }


        .attendance-card h2 {

            color: #c90000;

            font-size: 19px;

            font-weight: 700;

            line-height: 1.3;

            margin-bottom: 17px;
        }


        /* =====================================================
           DESCRIPTION
        ===================================================== */

        .description {

            max-width: 410px;

            margin: 0 auto 28px;

            color: #666666;

            font-size: 13px;

            font-weight: 400;

            line-height: 1.7;
        }


        /* =====================================================
           LOGIN BUTTON AREA
        ===================================================== */

        .login-buttons {

            width: 100%;

            display: flex;

            flex-direction: column;

            gap: 14px;
        }


        /* =====================================================
           LOGIN BUTTON
        ===================================================== */

        .login-button {

            width: 100%;

            min-height: 74px;

            padding: 11px 18px;

            border: none;

            border-radius: 17px;

            display: flex;

            align-items: center;

            text-align: left;

            color: #ffffff;

            font-family: 'Montserrat', sans-serif;

            cursor: pointer;

            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease;

            outline: none;
        }


        .login-button:active {

            transform: scale(0.98);
        }


        /* =====================================================
           ADMIN BUTTON
        ===================================================== */

        .admin-button {

            background:
                linear-gradient(
                    135deg,
                    #d00000,
                    #a80000
                );

            box-shadow:
                0 8px 20px rgba(201, 0, 0, 0.25);
        }


        .admin-button:hover {

            transform: translateY(-3px);

            box-shadow:
                0 13px 28px rgba(201, 0, 0, 0.35);
        }


        /* =====================================================
           PEGAWAI BUTTON
        ===================================================== */

        .employee-button {

            background:
                linear-gradient(
                    135deg,
                    #c7a34c,
                    #a98532
                );

            box-shadow:
                0 8px 20px rgba(169, 133, 50, 0.25);
        }


        .employee-button:hover {

            transform: translateY(-3px);

            box-shadow:
                0 13px 28px rgba(169, 133, 50, 0.35);
        }


        /* =====================================================
           BUTTON ICON
        ===================================================== */

        .button-icon {

            width: 45px;
            height: 45px;

            min-width: 45px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: rgba(255, 255, 255, 0.18);

            font-size: 20px;
        }


        /* =====================================================
           BUTTON TEXT
        ===================================================== */

        .button-text {

            margin-left: 15px;

            flex: 1;

            display: flex;

            flex-direction: column;
        }


        .button-text strong {

            font-size: 15px;

            font-weight: 700;

            line-height: 1.3;

            margin-bottom: 3px;
        }


        .button-text span {

            font-size: 10px;

            font-weight: 400;

            opacity: 0.85;

            line-height: 1.3;
        }


        /* =====================================================
           ARROW
        ===================================================== */

        .arrow {

            font-size: 28px;

            font-weight: 300;

            line-height: 1;

            transition:
                transform 0.25s ease;
        }


        .login-button:hover .arrow {

            transform: translateX(5px);
        }


        /* =====================================================
           INFORMATION BOX
        ===================================================== */

        .information {

            margin-top: 23px;

            padding: 12px 15px;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            border-radius: 11px;

            background: #f8f8f8;

            border: 1px solid #eeeeee;
        }


        .information-icon {

            font-size: 16px;

            flex-shrink: 0;
        }


        .information p {

            margin: 0;

            color: #666666;

            font-size: 10px;

            font-weight: 400;

            line-height: 1.5;
        }


        .information strong {

            color: #c90000;

            font-weight: 700;
        }


        /* =====================================================
           FOOTER LINE
        ===================================================== */

        .footer-line {

            display: flex;

            justify-content: center;

            align-items: center;

            gap: 5px;

            margin-top: 23px;
        }


        .footer-line span {

            display: block;

            height: 3px;

            border-radius: 10px;
        }


        .footer-line span:first-child {

            width: 65px;

            background: #c90000;
        }


        .footer-line span:last-child {

            width: 65px;

            background: #b8943d;
        }


        /* =====================================================
           COPYRIGHT
        ===================================================== */

        .copyright {

            color: #999999;

            font-size: 9px;

            font-weight: 400;

            margin-top: 10px;

            margin-bottom: 0;
        }


        /* =====================================================
           TABLET
        ===================================================== */

        @media (max-width: 768px) {

            .brand-header {

                top: 20px;

                left: 25px;
            }


            .brand-logo {

                width: 52px;

                height: 52px;
            }


            .brand-text {

                margin-left: 10px;
            }


            .brand-text h2 {

                font-size: 22px;
            }


            .brand-text p {

                font-size: 7px;

                letter-spacing: 0.7px;
            }


            .main-container {

                padding-top: 110px;
            }


            .attendance-card {

                max-width: 470px;
            }

        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 576px) {

            body {

                display: block;

                padding-top: 20px;
            }


            .brand-header {

                position: relative;

                top: auto;

                left: auto;

                width: 100%;

                justify-content: center;

                margin-bottom: 20px;
            }


            .brand-logo {

                width: 48px;

                height: 48px;
            }


            .brand-text h2 {

                font-size: 21px;
            }


            .brand-text p {

                font-size: 6.5px;
            }


            .main-container {

                min-height: auto;

                padding: 10px 15px 50px;
            }


            .attendance-card {

                padding: 36px 22px 25px;

                border-radius: 20px;
            }


            .attendance-card::before {

                width: 90px;

                height: 4px;
            }


            .logo-container {

                width: 80px;

                height: 80px;
            }


            .logo-container img {

                width: 75px;

                height: 75px;
            }


            .gold-line {

                width: 100px;

                height: 3px;

                margin-bottom: 18px;
            }


            .attendance-card h1 {

                font-size: 27px;
            }


            .attendance-card h2 {

                font-size: 16px;

                margin-bottom: 14px;
            }


            .description {

                font-size: 11px;

                line-height: 1.7;

                margin-bottom: 23px;
            }


            .login-buttons {

                gap: 12px;
            }


            .login-button {

                min-height: 67px;

                padding: 10px 14px;

                border-radius: 15px;
            }


            .button-icon {

                width: 40px;

                min-width: 40px;

                height: 40px;

                font-size: 18px;
            }


            .button-text {

                margin-left: 12px;
            }


            .button-text strong {

                font-size: 13px;
            }


            .button-text span {

                font-size: 8.5px;
            }


            .arrow {

                font-size: 23px;
            }


            .information {

                margin-top: 20px;

                padding: 10px 12px;
            }


            .information p {

                font-size: 9px;
            }


            .copyright {

                font-size: 8px;
            }


            .decoration-left {

                width: 450px;

                height: 120px;

                left: -180px;

                bottom: -10px;
            }


            .decoration-right {

                width: 480px;

                height: 110px;

                right: -190px;

                bottom: -10px;
            }

        }


        /* =====================================================
           EXTRA SMALL MOBILE
        ===================================================== */

        @media (max-width: 380px) {

            .attendance-card {

                padding-left: 17px;

                padding-right: 17px;
            }


            .brand-text h2 {

                font-size: 18px;
            }


            .brand-text p {

                font-size: 5.5px;
            }


            .attendance-card h1 {

                font-size: 24px;
            }


            .attendance-card h2 {

                font-size: 14px;
            }


            .description {

                font-size: 10px;
            }

        }

    </style>

</head>


<body>


    <!-- =====================================================
         BACKGROUND DECORATION
    ====================================================== -->

    <div class="background-decoration decoration-left"></div>

    <div class="background-decoration decoration-right"></div>

    <div class="background-symbol"></div>


    <!-- =====================================================
         HEADER BAWASLU
    ====================================================== -->

    <header class="brand-header">

        <img
            src="{{ asset('images/logo-bawaslu.png') }}"
            alt="Logo Bawaslu"
            class="brand-logo"
        >

        <div class="brand-text">

            <h2>BAWASLU</h2>

            <p>
                BADAN PENGAWAS PEMILIHAN UMUM
            </p>

        </div>

    </header>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-container">


        <!-- =================================================
             CARD
        ================================================== -->

        <section class="attendance-card">


            <!-- LOGO -->

            <div class="logo-container">

                <img
                    src="{{ asset('images/logo-bawaslu.png') }}"
                    alt="Logo Bawaslu"
                >

            </div>


            <!-- DECORATIVE LINE -->

            <div class="gold-line"></div>


            <!-- TITLE -->

            <h1>
                Selamat Datang
            </h1>


            <h2>
                Sistem Absensi Bawaslu
            </h2>


            <!-- DESCRIPTION -->

            <p class="description">

                Sistem absensi berbasis wajah dan GPS
                untuk mendukung proses kehadiran yang
                lebih mudah, transparan, dan akurat.

            </p>


            <!-- =================================================
                 LOGIN BUTTONS
            ================================================== -->

            <div class="login-buttons">


                <!-- LOGIN ADMIN -->

                <button
                    type="button"
                    class="login-button admin-button"
                    onclick="window.location.href='{{ url('/login') }}'"
                >

                    <div class="button-icon">
                        🔐
                    </div>


                    <div class="button-text">

                        <strong>
                            Login Admin
                        </strong>

                        <span>
                            Masuk ke Admin Panel
                        </span>

                    </div>


                    <div class="arrow">
                        →
                    </div>

                </button>


                <!-- LOGIN PEGAWAI -->

                <button
                    type="button"
                    class="login-button employee-button"
                    onclick="window.location.href='{{ url('/pegawai/login') }}'"
                >

                    <div class="button-icon">
                        👤
                    </div>


                    <div class="button-text">

                        <strong>
                            Login Pegawai
                        </strong>

                        <span>
                            Masuk ke Sistem Absensi
                        </span>

                    </div>


                    <div class="arrow">
                        →
                    </div>

                </button>


            </div>


            <!-- =================================================
                 INFORMATION
            ================================================== -->

            <div class="information">

                <span class="information-icon">
                    🛡️
                </span>


                <p>

                    Pendaftaran akun hanya dapat dilakukan
                    melalui <strong>Admin Panel</strong>.

                </p>

            </div>


            <!-- =================================================
                 FOOTER DECORATION
            ================================================== -->

            <div class="footer-line">

                <span></span>

                <span></span>

            </div>


            <!-- COPYRIGHT -->

            <p class="copyright">

                © 2026 Sistem Absensi Bawaslu

            </p>


        </section>

    </main>


</body>

</html>