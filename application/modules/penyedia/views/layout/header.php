<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyedia Panel - SIPETA PPU</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo_ukpbj.png') ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Datepicker positioning & modern look */
        .datepicker-dropdown {
            z-index: 2000 !important;
            padding: 8px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
        }
        .datepicker table { margin: 0; }
        .datepicker table tr td,
        .datepicker table tr th { border-radius: 10px; }

        :root {
            --sidebar-bg: #1e293b;
            --sidebar-active: #059669;
            --content-bg: #f8fafc;
            --primary: #0f172a;
            --accent: #10b981;
        }

        body { 
            font-family: 'Roboto', sans-serif; 
            background: var(--content-bg);
            color: #475569;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, .sidebar-brand { 
            font-family: 'Montserrat', sans-serif; 
            font-weight: 700;
        }

        #wrapper { display: flex; width: 100%; align-items: stretch; }

        /* Sidebar Styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: var(--sidebar-bg);
            color: #fff;
            transition: all 0.3s;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            min-height: 100vh;
            z-index: 1000;
        }

        .sidebar-header { padding: 40px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar-brand { color: #fff; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px; }

        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a {
            padding: 14px 25px;
            font-size: 0.85rem;
            display: block;
            color: #94a3b8;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 500;
        }
        #sidebar ul li a i { width: 25px; font-size: 1rem; margin-right: 12px; }
        #sidebar ul li a:hover { color: #fff; background: rgba(255,255,255,0.03); }
        #sidebar ul li.active > a {
            color: #fff;
            background: var(--sidebar-active);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        /* Content Area */
        #content { width: 100%; padding: 0; min-height: 100vh; }
        
        .navbar-custom {
            background: #fff;
            padding: 15px 35px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 40px;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            padding: 24px 30px;
            font-weight: 700;
            color: var(--primary);
        }

        .btn-primary { background-color: var(--sidebar-active); border-color: var(--sidebar-active); border-radius: 10px; font-weight: 600; padding: 10px 24px; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2); }
        .btn-primary:hover { background-color: #047857; border-color: #047857; transform: translateY(-1px); }
        
        .table thead th {
            background: #f8fafc;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            color: #64748b;
            border: none;
            padding: 18px 20px;
        }
        .table td { padding: 18px 20px; border-top: 1px solid #f1f5f9; }

        /* Select2 Theme */
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 5px;
        }

    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="fas fa-building mr-2 text-success"></i>PORTAL PENYEDIA
                </div>
                <small class="text-secondary d-block mt-2" style="font-size: 0.7rem;"><?= $this->session->userdata('nama_perusahaan') ?></small>
            </div>

            <ul class="list-unstyled components">
                <li class="<?= $this->uri->segment(2) == '' ? 'active' : '' ?>">
                    <a href="<?= base_url('penyedia') ?>"><i class="fas fa-home"></i> Beranda</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'personel' ? 'active' : '' ?>">
                    <a href="<?= base_url('penyedia/personel') ?>"><i class="fas fa-user-tie"></i> Data Personel</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'peralatan' ? 'active' : '' ?>">
                    <a href="<?= base_url('penyedia/peralatan') ?>"><i class="fas fa-truck-pickup"></i> Data Peralatan</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'tender' ? 'active' : '' ?>">
                    <a href="<?= base_url('penyedia/tender') ?>"><i class="fas fa-clipboard-list"></i> Partisipasi Tender</a>
                </li>
            </ul>

            <div class="mt-auto p-4">
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-light btn-block btn-sm rounded-pill py-2" style="border-color: rgba(255,255,255,0.1); color: #94a3b8;">
                    <i class="fas fa-power-off mr-2"></i> Logout
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-light d-lg-none">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="m-0 text-dark font-weight-bold ml-3">
                        <?php 
                            if($this->uri->segment(2) == 'personel') echo 'Manajemen Personel Teknik';
                            elseif($this->uri->segment(2) == 'peralatan') echo 'Inventaris Peralatan Konstruksi';
                            elseif($this->uri->segment(2) == 'tender') echo 'Data Partisipasi Tender';
                            else echo 'Dashboard Penyedia';
                        ?>
                    </h5>
                    <div class="ml-auto d-flex align-items-center">
                        <div class="text-right mr-3 d-none d-md-block">
                            <small class="text-muted d-block">Operator Akun,</small>
                            <span class="font-weight-bold text-dark"><?= $this->session->userdata('username') ?></span>
                        </div>
                        <img src="https://ui-avatars.com/api/?name=<?= $this->session->userdata('username') ?>&background=10b981&color=fff" class="rounded-circle shadow-sm" width="38">
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-5">
