<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPETA PPU - Sistem Informasi Personel dan Peralatan</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo_ukpbj.png') ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700;800&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f4c75;
            --secondary: #3282b8;
            --accent: #ffcc00;
            --light-bg: #f8f9fc;
            --dark: #1b262c;
            --glass: rgba(255, 255, 255, 0.9);
        }

        body { 
            font-family: 'Roboto', sans-serif; 
            background: var(--light-bg);
            color: var(--dark);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, .navbar-brand { 
            font-family: 'Montserrat', sans-serif; 
            font-weight: 700;
        }

        /* Glassmorphism Navbar */
        .navbar { 
            background: transparent; 
            transition: 0.5s all ease-in-out; 
            padding: 20px 0;
        }
        .navbar.scrolled { 
            background: rgba(15, 76, 117, 0.9) !important; 
            backdrop-filter: blur(10px); 
            padding: 10px 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .nav-link { 
            color: white !important; 
            font-weight: 600; 
            margin: 0 10px;
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background: var(--accent);
            transition: 0.3s;
        }
        .nav-link:hover::after { width: 100%; }

        /* Hero Section with Construction Overlay */
        .hero { 
            background: linear-gradient(rgba(15, 76, 117, 0.85), rgba(27, 38, 44, 0.85)), url('https://images.unsplash.com/photo-1541888946425-d81bb19480c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white; 
            padding: 180px 0 120px; 
            clip-path: ellipse(150% 100% at 50% 0%);
        }

        .btn-accent {
            background: var(--accent);
            color: var(--dark);
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.4s;
            box-shadow: 0 10px 20px rgba(255, 204, 0, 0.3);
        }
        .btn-accent:hover {
            background: #e6b800;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(255, 204, 0, 0.5);
            color: var(--dark);
        }

        /* Float Animation */
        .float-img {
            animation: floating 3s ease-in-out infinite;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        @keyframes floating {
            0% { transform: translate(0, 0px); }
            50% { transform: translate(0, -20px); }
            100% { transform: translate(0, 0px); }
        }

        /* Modern Feature Cards */
        .card-modern {
            border: none;
            border-radius: 20px;
            background: white;
            transition: 0.4s;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            height: 100%;
        }
        .card-modern:hover {
            transform: scale(1.05);
            box-shadow: 0 25px 50px rgba(0,0,0,0.1);
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            transition: 0.4s;
        }
        .card-modern:hover .icon-circle {
            background: var(--primary);
            color: white !important;
        }

        /* Construction Icons Styles */
        .const-icon { font-size: 2.5rem; color: var(--primary); }

        /* Counter Section */
        .counter-section {
            background: var(--primary);
            padding: 80px 0;
            color: white;
            text-align: center;
        }
        .counter-num { font-size: 3rem; font-weight: 800; display: block; }
        .counter-label { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }

        /* Steps Section */
        .step-item {
            position: relative;
            padding: 20px;
        }
        .step-number {
            font-size: 4rem;
            color: rgba(15, 76, 117, 0.1);
            font-weight: 800;
            position: absolute;
            top: -10px;
            left: 0;
            z-index: 0;
        }
        .step-content { position: relative; z-index: 1; }

        footer {
            background: var(--dark);
            color: white;
            padding: 80px 0 40px;
        }
        .footer-logo { font-size: 1.5rem; margin-bottom: 20px; display: inline-block; }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-hard-hat mr-2 text-warning"></i>
                <span>SIPETA PPU</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#fitur">Fitur</a></li>
                    <li class="nav-item ml-lg-4">
                        <a class="btn btn-accent rounded-pill px-4 py-2" href="<?= base_url('auth') ?>">
                            <i class="fas fa-sign-in-alt mr-2"></i> Akses Portal
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header id="beranda" class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 text-left">
                    <span class="badge badge-warning mb-3 px-3 py-2 text-uppercase font-weight-bold" style="letter-spacing: 1px;">Sistem Pengadaan Terintegrasi</span>
                    <h1 class="display-3 mb-4">Membangun SIPETA dengan <span style="color: var(--accent);">Presisi Teknik</span></h1>
                    <p class="lead mb-5" style="opacity: 0.9; max-width: 600px;">SIPETA (Sistem Informasi Personel dan Peralatan) hadir untuk mengoptimalkan manajemen aset konstruksi di Kabupaten Penajam Paser Utara melalui transformasi digital pengadaan yang transparan, akurat, dan akuntabel.</p>
                    <div class="d-flex flex-wrap">
                        <a href="<?= base_url('auth') ?>" class="btn btn-accent btn-lg mr-3 mb-3">Login Ke Portal</a>
                        <a href="#tentang" class="btn btn-outline-light btn-lg rounded-pill px-4 mb-3">Pelajari Lebih Lanjut</a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <img src="<?= base_url('assets/img/landing-mockup.png') ?>" class="img-fluid float-img" alt="Modern Dashboard">
                    <div class="glass-info p-3 mt-4 text-center rounded shadow" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); border: 1px solid rgba(255,255,255,0.2);">
                        <small class="d-block text-white mb-2">Didukung oleh Standar Pemeriksaan</small>
                        <i class="fas fa-check-double text-success mr-2"></i>
                        <i class="fas fa-medal text-warning mr-2"></i>
                        <i class="fas fa-shield-alt text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="fitur" class="py-5" style="margin-top: -80px;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card card-modern p-5 text-center">
                        <div class="icon-circle border">
                            <i class="fas fa-users-cog const-icon"></i>
                        </div>
                        <h4 class="mb-3">Personel Ahli</h4>
                        <p class="text-muted">Akses database tenaga ahli konstruksi dengan validasi SKK yang akurat dan terverifikasi.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card card-modern p-5 text-center">
                        <div class="icon-circle border">
                            <i class="fas fa-truck-monster const-icon"></i>
                        </div>
                        <h4 class="mb-3">Alat Berat</h4>
                        <p class="text-muted">Pantau ketersediaan dan legalitas alat berat secara real-time untuk kebutuhan tender.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card card-modern p-5 text-center">
                        <div class="icon-circle border">
                            <i class="fas fa-file-contract const-icon"></i>
                        </div>
                        <h4 class="mb-3">History Tender</h4>
                        <p class="text-muted">Tracking otomatis penggunaan aset pada riwayat pengadaan sebelumnya untuk transparansi data.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="tentang" class="py-5 bg-white overflow-hidden">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-lg shadow-2xl" alt="Construction Site">
                        <div style="position: absolute; bottom: -30px; right: -30px; background: var(--accent); padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                            <h2 class="m-0 font-weight-bold">100%</h2>
                            <small class="font-weight-bold text-uppercase">Data Valid</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 pl-lg-5">
                    <h5 class="text-primary font-weight-bold text-uppercase mb-3" style="letter-spacing: 2px;">Visi & Misi</h5>
                    <h2 class="display-5 mb-4 font-weight-bold">Transformasi Digital Sektor Konstruksi</h2>
                    <p class="text-muted mb-4 lead">Kami berkomitmen untuk menyediakan platform yang mempermudah tracking aset antara Penyedia dan Pokja dalam ekosistem tender PPU melalui validasi data personel dan peralatan yang akurat.</p>
                    <div class="row mt-4">
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-shield-alt text-primary mr-3 fa-lg"></i>
                                <span class="font-weight-bold">Validasi Aset Ganda</span>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar-check text-primary mr-3 fa-lg"></i>
                                <span class="font-weight-bold">Pelacakan Tahun Anggaran</span>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-check text-primary mr-3 fa-lg"></i>
                                <span class="font-weight-bold">Audit Trail Operator</span>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-database text-primary mr-3 fa-lg"></i>
                                <span class="font-weight-bold">Data Terpusat & Akurat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="counter-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 border-right">
                    <span class="counter-num"><?= $total_penyedia ?></span>
                    <span class="counter-label">Penyedia Aktif</span>
                </div>
                <div class="col-md-3 border-right">
                    <span class="counter-num"><?= $total_personel ?></span>
                    <span class="counter-label">Total Personel</span>
                </div>
                <div class="col-md-3 border-right">
                    <span class="counter-num"><?= $total_peralatan ?></span>
                    <span class="counter-label">Peralatan Terdaftar</span>
                </div>
                <div class="col-md-3">
                    <span class="counter-num"><?= $total_tender ?></span>
                    <span class="counter-label">Paket Tender</span>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5">
                    <div class="footer-logo font-weight-bold">
                        <i class="fas fa-hard-hat text-warning mr-2"></i> SIPETA PPU
                    </div>
                    <p style="opacity: 0.7;">Sistem Informasi Personel dan Peralatan Tender (SIPETA) Kabupaten Penajam Paser Utara. Meningkatkan standar kualitas dan akuntabilitas pengadaan daerah.</p>
                    <div class="d-flex mt-4">
                        <a href="#" class="btn btn-primary rounded-circle mr-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-info rounded-circle mr-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-danger rounded-circle"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 ml-auto mb-5">
                    <h5 class="mb-4">Tautan Cepat</h5>
                    <ul class="list-unstyled" style="opacity: 0.7;">
                        <li class="mb-2"><a href="#" class="text-white">Beranda</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Tentang</a></li>
                        <li class="mb-2"><a href="#" class="text-white">Bantuan</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-5">
                    <h5 class="mb-4">Kontak Kami</h5>
                    <ul class="list-unstyled" style="opacity: 0.7;">
                        <li class="mb-2"><i class="fas fa-map-marker-alt mr-3 text-warning"></i> Jl. Propinsi Km. 9, Penajam Paser Utara</li>
                        <li class="mb-2"><i class="fas fa-phone mr-3 text-warning"></i> (0542) 123456</li>
                        <li class="mb-2"><i class="fas fa-envelope mr-3 text-warning"></i> lpse@penajam.go.id</li>
                    </ul>
                </div>
            </div>
            <hr style="background: rgba(255,255,255,0.1);">
            <div class="text-center small mt-4" style="opacity: 0.5;">
                &copy; 2026 SIPETA PPU - Pemerintah Kabupaten Penajam Paser Utara. Build with <i class="fas fa-heart text-danger"></i> for PPU.
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('.navbar').addClass('scrolled');
            } else {
                $('.navbar').removeClass('scrolled');
            }
        });

        // Smooth Scrolling
        $('a.nav-link[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if( target.length ) {
                event.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 80
                }, 1000);
            }
        });
    </script>
</body>
</html>
