<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Ranay Nusantara Sejahtera - Penyedia Alat Kesehatan Terpercaya</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-dark: #1e3a8a;
            --secondary-color: #3b82f6;
            --accent-color: #60a5fa;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --bg-light: #f9fafb;
            --border-light: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            backdrop-filter: blur(20px);
            background-color: rgba(255, 255, 255, 0.98);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .navbar-brand img {
            height: 55px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .btn-login {
            padding: 0.6rem 1.8rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        .btn-login:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30, 64, 175, 0.2);
        }

        /* Hero Section */
        .hero-section {
            padding: 140px 0 100px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 50%, #bfdbfe 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%231e40af" fill-opacity="0.03" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,144C960,149,1056,139,1152,122.7C1248,107,1344,85,1392,74.7L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat;
            background-size: cover;
            opacity: 0.5;
        }

        /* Gradient overlay for smooth transition */
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 150px;
            background: linear-gradient(to bottom, 
                rgba(191, 219, 254, 0) 0%,
                rgba(219, 234, 254, 0.3) 20%,
                rgba(239, 246, 255, 0.5) 40%,
                rgba(249, 250, 251, 0.7) 60%,
                rgba(255, 255, 255, 0.9) 80%,
                rgba(255, 255, 255, 1) 100%
            );
            pointer-events: none;
            z-index: 1;
        }

        .badge-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.2);
        }

        .hero-title {
            font-weight: 800;
            font-size: 3.75rem;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: var(--text-dark);
            letter-spacing: -0.02em;
        }
        
        .hero-highlight {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            font-weight: 400;
            line-height: 1.8;
            max-width: 600px;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 1rem 2.5rem;
            font-weight: 600;
            border-radius: 50px;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(30, 64, 175, 0.3);
            font-size: 1.05rem;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(30, 64, 175, 0.4);
            color: white;
        }

        .btn-outline-custom {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 1rem 2.5rem;
            font-weight: 600;
            border-radius: 50px;
            background: white;
            transition: all 0.3s ease;
            font-size: 1.05rem;
        }

        .btn-outline-custom:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(30, 64, 175, 0.2);
        }

        /* Stats Section */
        .stats-section {
            background: white;
            padding: 80px 0;
            border-bottom: 1px solid var(--border-light);
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 1rem;
        }

        /* Section Styling */
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
            letter-spacing: -0.02em;
        }

        .section-subtitle {
            color: var(--text-muted);
            font-size: 1.125rem;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.8;
        }

        /* Feature Cards */
        .feature-card {
            border: none;
            border-radius: 20px;
            padding: 3rem 2rem;
            background: white;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(30, 64, 175, 0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .icon-blue { 
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: var(--primary-color);
        }
        
        .icon-teal { 
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #059669;
        }
        
        .icon-orange { 
            background: linear-gradient(135deg, #fed7aa, #fdba74);
            color: #ea580c;
        }

        .feature-title {
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.375rem;
            color: var(--text-dark);
        }

        .feature-desc {
            color: var(--text-muted);
            line-height: 1.8;
            font-size: 1rem;
        }

        /* Contact Section */
        .contact-section {
            background: var(--bg-light);
            padding: 100px 0;
        }
        
        .contact-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2.5rem;
            text-align: center;
            border: 2px solid var(--border-light);
            transition: all 0.3s ease;
            height: 100%;
        }

        .contact-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(30, 64, 175, 0.1);
        }

        .contact-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }

        .contact-card h4 {
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--text-dark);
        }

        .contact-card .contact-value {
            font-weight: 700;
            font-size: 1.25rem;
            margin-top: 1rem;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            color: white;
            padding: 80px 0 30px;
        }
        
        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .footer a:hover {
            color: white;
        }

        .footer h6 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.125rem;
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .floating-img {
            animation: float 6s ease-in-out infinite;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.1));
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 0.75rem 0;
            }

            .navbar-brand img {
                height: 40px;
            }

            .btn-login {
                padding: 0.5rem 1.2rem;
                font-size: 0.9rem;
            }

            .hero-section {
                padding: 100px 0 60px;
            }

            .hero-title {
                font-size: 2rem;
                line-height: 1.2;
            }

            .hero-subtitle {
                font-size: 1rem;
                margin-bottom: 2rem;
            }

            .badge-custom {
                font-size: 0.75rem;
                padding: 0.5rem 1rem;
            }

            .btn-primary-custom,
            .btn-outline-custom {
                padding: 0.85rem 1.8rem;
                font-size: 0.95rem;
            }

            .section-title {
                font-size: 1.75rem;
            }

            .section-subtitle {
                font-size: 1rem;
            }

            .stats-section {
                padding: 60px 0;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }

            .stat-label {
                font-size: 0.9rem;
            }

            .feature-card {
                padding: 2rem 1.5rem;
                margin-bottom: 1rem;
            }

            .feature-icon {
                width: 65px;
                height: 65px;
                font-size: 1.75rem;
            }

            .feature-title {
                font-size: 1.125rem;
            }

            .feature-desc {
                font-size: 0.95rem;
            }

            .contact-section {
                padding: 60px 0;
            }

            .contact-card {
                padding: 2rem 1.5rem;
                margin-bottom: 1rem;
            }

            .contact-icon {
                font-size: 2.5rem;
            }

            .contact-card .contact-value {
                font-size: 1.1rem;
            }

            .footer {
                padding: 40px 0 30px;
            }

            .footer .row {
                row-gap: 1.5rem !important;
            }

            .footer h6 {
                font-size: 0.85rem;
                margin-top: 0.5rem;
                margin-bottom: 0.5rem;
                font-weight: 700;
            }

            .footer .footer-logo-section {
                margin-bottom: 0;
                text-align: center;
            }

            .footer .footer-logo-section .d-flex {
                flex-direction: column !important;
                align-items: center !important;
                gap: 0.75rem;
                margin-bottom: 0.75rem !important;
            }

            .footer .footer-logo-section img {
                margin-right: 0 !important;
                height: 35px !important;
            }

            .footer .footer-logo-section h5 {
                font-size: 0.9rem;
                text-align: center;
            }

            .footer .footer-logo-section p {
                font-size: 0.8rem;
                margin-bottom: 0;
            }

            /* 3 kolom horizontal untuk Tautan, Layanan, Alamat */
            .footer .col-4 {
                text-align: left;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .footer ul {
                padding-left: 0;
                margin-bottom: 0;
            }

            .footer ul li {
                margin-bottom: 0.4rem !important;
            }

            .footer ul li:last-child {
                margin-bottom: 0 !important;
            }

            .footer ul li a {
                font-size: 0.8rem;
            }

            .footer .footer-address {
                font-size: 0.75rem;
                margin-bottom: 0;
                line-height: 1.6 !important;
            }

            .floating-img {
                max-width: 100% !important;
                margin-top: 2rem;
            }

            /* Stack buttons vertically on very small screens */
            @media (max-width: 480px) {
                .hero-title {
                    font-size: 1.75rem;
                }

                .d-flex.gap-3 {
                    flex-direction: column;
                    gap: 0.75rem !important;
                }

                .btn-primary-custom,
                .btn-outline-custom {
                    width: 100%;
                    text-align: center;
                }

                .stat-number {
                    font-size: 2rem;
                }
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('assets/images/hp-logo.png') }}" alt="Logo RNS" class="d-inline-block align-text-top">
            </a>
            <div class="ms-auto">
                <a href="{{ route('login') }}" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <!-- Text Content - Order 1 on mobile, Order 1 on desktop -->
                <div class="col-lg-6 order-1 order-lg-1 fade-in-up">
                    <span class="badge-custom">
                        <i class="bi bi-shield-check me-2"></i>Distributor Resmi & Terpercaya
                    </span>
                    <h1 class="hero-title">
                        Solusi Profesional<br>
                        <span class="hero-highlight">Alat Kesehatan Radiologi</span>
                    </h1>
                    <p class="hero-subtitle">
                        Partner terpercaya untuk kebutuhan peralatan medis radiologi Anda. Kami menyediakan produk berkualitas tinggi dengan layanan konsultasi profesional untuk rumah sakit dan fasilitas kesehatan.
                    </p>
                    <!-- Buttons on desktop only -->
                    <div class="d-none d-lg-flex gap-3 flex-wrap">
                        <a href="#contact" class="btn btn-primary-custom">
                            <i class="bi bi-whatsapp me-2"></i>Hubungi Kami
                        </a>
                        <a href="#products" class="btn btn-outline-custom">
                            Lihat Produk <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Image - Order 2 on mobile, Order 2 on desktop -->
                <div class="col-lg-6 text-center order-2 order-lg-2 mb-4 mb-lg-0">
                    <img src="{{ asset('assets/images/TaeAugust19.jpg') }}" alt="Medical Equipment" class="img-fluid floating-img" style="max-width: 85%;">
                </div>
                
                <!-- Buttons on mobile only - Order 3 -->
                <div class="col-12 d-lg-none order-3">
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="#contact" class="btn btn-primary-custom">
                            <i class="bi bi-whatsapp me-2"></i>Hubungi Kami
                        </a>
                        <a href="#products" class="btn btn-outline-custom">
                            Lihat Produk <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number" data-target="3">0</div>
                        <div class="stat-label">Tahun Pengalaman</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number" data-target="500">0</div>
                        <div class="stat-label">Klien Terlayani</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-number" data-target="100">0</div>
                        <div class="stat-label">Produk Bergaransi</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="products" class="py-5 bg-white">
        <div class="container py-5">
            <div class="section-header">
                <h2 class="section-title">Mengapa Memilih Kami?</h2>
                <p class="section-subtitle">
                    Kami berkomitmen memberikan solusi terbaik dengan standar kualitas internasional dan layanan purna jual yang komprehensif.
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon icon-blue">
                            <i class="bi bi-hospital"></i>
                        </div>
                        <h3 class="feature-title">Peralatan Medis Lengkap</h3>
                        <p class="feature-desc">
                            Menyediakan berbagai jenis alat kesehatan radiologi dan umum dari brand terkemuka dengan teknologi terkini.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon icon-teal">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="feature-title">Terstandarisasi & Berizin</h3>
                        <p class="feature-desc">
                            Seluruh produk memiliki izin edar resmi dan memenuhi standar keselamatan Kementerian Kesehatan RI.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon icon-orange">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h3 class="feature-title">Layanan Purna Jual</h3>
                        <p class="feature-desc">
                            Dukungan teknis 24/7, garansi resmi, dan maintenance berkala untuk performa optimal peralatan Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Hubungi Kami</h2>
                <p class="section-subtitle">
                    Tim profesional kami siap membantu kebutuhan alat kesehatan Anda. Konsultasikan kebutuhan fasilitas kesehatan Anda bersama kami.
                </p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-5">
                    <a href="https://wa.me/6285280002289" target="_blank" class="text-decoration-none">
                        <div class="contact-card">
                            <i class="bi bi-whatsapp contact-icon text-success"></i>
                            <h4>WhatsApp</h4>
                            <p class="text-muted mb-0">Chat langsung untuk respons cepat</p>
                            <p class="contact-value text-success">0852-8000-2289</p>
                        </div>
                    </a>
                </div>
                
                <div class="col-md-5">
                    <a href="mailto:ranaysejahtera@gmail.com" class="text-decoration-none">
                        <div class="contact-card">
                            <i class="bi bi-envelope contact-icon text-primary"></i>
                            <h4>Email</h4>
                            <p class="text-muted mb-0">Kirim permintaan penawaran resmi</p>
                            <p class="contact-value text-primary">ranaysejahtera@gmail.com</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-12 footer-logo-section">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('assets/images/hp-logo.png') }}" alt="Logo RNS" height="45" class="me-3 bg-white rounded p-2">
                        <h5 class="mb-0 fw-bold">PT. Ranay Nusantara Sejahtera</h5>
                    </div>
                    <p style="color: rgba(255, 255, 255, 0.8); line-height: 1.8;">
                        Mitra terpercaya dalam penyediaan alat kesehatan radiologi berkualitas untuk menunjang pelayanan kesehatan Indonesia.
                    </p>
                </div>
                <div class="col-lg-2 offset-lg-1 col-4">
                    <h6>Tautan</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Beranda</a></li>
                        <li class="mb-2"><a href="#products">Produk</a></li>
                        <li class="mb-2"><a href="#contact">Kontak</a></li>
                        <li><a href="{{ route('login') }}">Login Staff</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-4">
                    <h6>Layanan</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Alat Radiologi</a></li>
                        <li class="mb-2"><a href="#">Konsultan Alkes</a></li>
                        <li class="mb-2"><a href="#">Maintenance</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-4">
                    <h6>Alamat</h6>
                    <p class="footer-address" style="color: rgba(255, 255, 255, 0.8); line-height: 1.8;">
                        Jl. Raya Serang - Jakarta Km. 6,5<br>
                        Kepuren Residence, Kota Serang<br>
                        Banten - 42183
                    </p>
                </div>
            </div>
            <hr style="border-color: rgba(255, 255, 255, 0.2); margin: 3rem 0 2rem;">
            <div class="text-center" style="color: rgba(255, 255, 255, 0.7);">
                <small>&copy; {{ date('Y') }} PT. Ranay Nusantara Sejahtera. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Counter animation for stats
        function animateCounter(element, target, suffix = '') {
            let current = 0;
            const increment = target / 100;
            const duration = 2000; // 2 seconds
            const stepTime = duration / 100;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = target + suffix;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current) + suffix;
                }
            }, stepTime);
        }

        // Intersection Observer for triggering counter animation
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    entry.target.classList.add('counted');
                    const target = parseInt(entry.target.getAttribute('data-target'));
                    const suffix = entry.target.parentElement.querySelector('.stat-label').textContent.includes('Bergaransi') ? '%' : '+';
                    animateCounter(entry.target, target, suffix);
                }
            });
        }, observerOptions);

        // Observe all stat numbers
        document.querySelectorAll('.stat-number').forEach(stat => {
            observer.observe(stat);
        });
    </script>
</body>
</html>
