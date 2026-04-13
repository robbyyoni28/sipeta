<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SIPETA (Sistem Informasi Personel dan Peralatan) PPU</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo_ukpbj.png') ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            --sidebar-bg: #1b262c;
            --sidebar-active: #0f4c75;
            --content-bg: #f4f7f6;
            --primary: #0f4c75;
            --accent: #ffcc00;
        }

        body { 
            font-family: 'Roboto', sans-serif; 
            background: var(--content-bg);
            color: #33475b;
        }

        h1, h2, h3, h4, h5, .sidebar-brand { 
            font-family: 'Montserrat', sans-serif; 
            font-weight: 700;
        }

        #wrapper { display: flex; width: 100%; align-items: stretch; }

        /* Sidebar Styling */
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: var(--sidebar-bg);
            color: #fff;
            height: 100vh;
            position: sticky;
            top: 0;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        #wrapper.sidebar-collapsed #sidebar {
            min-width: 88px;
            max-width: 88px;
        }
        #wrapper.sidebar-collapsed .sidebar-header {
            padding: 24px 16px;
            text-align: center;
        }
        #wrapper.sidebar-collapsed .sidebar-header small {
            display: none;
        }
        #wrapper.sidebar-collapsed .sidebar-brand {
            font-size: 1.05rem;
            text-align: center;
        }
        #wrapper.sidebar-collapsed .sidebar-brand i {
            margin-right: 0 !important;
        }
        #wrapper.sidebar-collapsed #sidebar ul li a {
            justify-content: center;
        }
        #wrapper.sidebar-collapsed #sidebar ul li a span,
        #wrapper.sidebar-collapsed #sidebar ul li a .menu-text {
            display: none;
        }

        .navbar-icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #0f172a;
            transition: all 0.2s;
        }
        .navbar-icon-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .notif-wrap {
            position: relative;
            margin-right: 12px;
        }
        .notif-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            padding: 2px 6px;
            font-size: 0.65rem;
            line-height: 1;
            border-radius: 999px;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            #sidebar {
                min-width: 0;
                max-width: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            #wrapper.sidebar-open #sidebar {
                transform: translateX(0);
                min-width: 250px;
                max-width: 250px;
            }
            #wrapper.sidebar-collapsed #sidebar {
                min-width: 0;
                max-width: 0;
                transform: translateX(-100%);
            }
            #wrapper.sidebar-collapsed.sidebar-open #sidebar {
                min-width: 88px;
                max-width: 88px;
                transform: translateX(0);
            }
            #content {
                margin-left: 0;
            }
            .navbar-custom {
                padding: 12px 20px;
            }
            .navbar-custom h5 {
                font-size: 1rem;
            }
        }

        .sidebar-header { padding: 30px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar-brand { color: var(--accent); font-size: 1.2rem; text-transform: uppercase; letter-spacing: 2px; }

        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a {
            padding: 14px 18px;
            margin: 6px 14px;
            border-radius: 14px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 48px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
            font-weight: 500;
        }
        #sidebar ul li a i { width: 22px; font-size: 1.05rem; margin-right: 0; opacity: 0.9; }
        #sidebar ul li a:hover { color: #fff; background: rgba(255,255,255,0.08); transform: translateX(4px); }
        #sidebar ul li.active > a {
            color: #fff;
            background: var(--sidebar-active);
            border-left: 4px solid var(--accent);
        }

        /* Content Area */
        #content { width: 100%; padding: 0; min-height: 100vh; }
        
        .navbar-custom {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        /* Modern Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 20px 25px;
            font-weight: 700;
            color: var(--primary);
        }

        /* Stats Cards */
        .card-stats { border-left: 5px solid var(--primary); }
        .card-stats .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .btn-primary { background-color: var(--primary); border-color: var(--primary); border-radius: 8px; font-weight: 600; padding: 10px 20px; }
        .btn-primary:hover { background-color: var(--sidebar-bg); border-color: var(--sidebar-bg); }
        
        .badge-soft-success { background: rgba(28, 200, 138, 0.1); color: #1cc88a; padding: 5px 12px; border-radius: 6px; }
        .badge-soft-danger { background: rgba(231, 74, 59, 0.1); color: #e74a3b; padding: 5px 12px; border-radius: 6px; }

        /* DataTables Customization */
        .table thead th {
            background: #f8f9fc;
            border-top: none;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            color: #858796;
            padding: 15px;
        }
        .table td { vertical-align: middle; padding: 15px; }

        /* DataTables spacing inside cards */
        .card .dataTables_wrapper > .row {
            margin-left: 0;
            margin-right: 0;
        }
        .card .dataTables_wrapper > .row:first-child {
            padding: 16px 16px 8px 16px;
        }
        .card .dataTables_wrapper > .row:last-child {
            padding: 8px 16px 16px 16px;
        }
        .card .dataTables_wrapper .dataTables_filter input,
        .card .dataTables_wrapper .dataTables_length select {
            border-radius: 12px;
        }

    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="fas fa-hard-hat mr-2"></i>SIPETA PPU
                </div>
                <small class="text-muted text-uppercase" style="font-size: 0.5rem; letter-spacing: 1px;">Sistem Informasi Personel dan Peralatan</small>
            </div>

            <ul class="list-unstyled components">
                <li class="<?= $this->uri->segment(2) == '' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin') ?>"><i class="fas fa-th-large"></i> Dashboard</a>
                </li>

                <div class="sidebar-heading small text-muted px-4 mt-3 mb-1 text-uppercase font-weight-bold" style="font-size: 0.65rem;">Management</div>
                <li class="<?= $this->uri->segment(2) == 'akun_pokja' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/akun_pokja') ?>"><i class="fas fa-users-cog"></i> Akun POKJA</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'akun_sekretariat' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/akun_sekretariat') ?>"><i class="fas fa-user-shield"></i> Akun Sekretariat</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'verifikasi_penyedia' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/verifikasi_penyedia') ?>"><i class="fas fa-check-circle"></i> Verifikasi Penyedia</a>
                </li>

                <div class="sidebar-heading small text-muted px-4 mt-3 mb-1 text-uppercase font-weight-bold" style="font-size: 0.65rem;">Aktivitas Tender</div>
                <li class="<?= $this->uri->segment(2) == 'data_tender' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/data_tender') ?>"><i class="fas fa-file-invoice-dollar"></i> Monitoring Tender</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'input_pemenang' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/input_pemenang') ?>"><i class="fas fa-edit"></i> Input Pemenang</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'input_pemenang_konsultansi' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/input_pemenang_konsultansi') ?>"><i class="fas fa-handshake"></i> Input Konsultansi</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'daftar_perusahaan' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/daftar_perusahaan') ?>"><i class="fas fa-building"></i> Data Perusahaan</a>
                </li>

                <div class="sidebar-heading small text-muted px-4 mt-3 mb-1 text-uppercase font-weight-bold" style="font-size: 0.65rem;">Basis Data</div>
                <li class="<?= $this->uri->segment(2) == 'personel_lapangan' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/personel_lapangan') ?>"><i class="fas fa-users"></i> Personel Lapangan</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'personel_k3' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/personel_k3') ?>"><i class="fas fa-hard-hat"></i> Personel K3</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'peralatan' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/peralatan') ?>"><i class="fas fa-truck-pickup"></i> Peralatan Utama</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'pemilik_alat' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/pemilik_alat') ?>"><i class="fas fa-user-tag"></i> Pemilik Alat</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'regulasi' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/regulasi') ?>"><i class="fas fa-book"></i> Regulasi</a>
                </li>

                <div class="sidebar-heading small text-muted px-4 mt-3 mb-1 text-uppercase font-weight-bold" style="font-size: 0.65rem;">Search Tools</div>
                <li class="<?= $this->uri->segment(2) == 'cari_personel' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/cari_personel') ?>"><i class="fas fa-search"></i> Cari Personel</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'cari_peralatan' ? 'active' : '' ?>">
                    <a href="<?= base_url('admin/cari_peralatan') ?>"><i class="fas fa-search-plus"></i> Cari Peralatan</a>
                </li>
            </ul>

            <div class="mt-auto p-4">
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger btn-block btn-sm rounded-pill">
                    <i class="fas fa-sign-out-alt mr-2"></i> Keluar Sistem
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container-fluid">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarToggle" class="navbar-icon-btn mr-3" aria-label="Toggle Sidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h5 class="m-0 text-primary font-weight-bold">
                        <?php 
                            if($this->uri->segment(2) == 'verifikasi_penyedia') echo 'Verifikasi Akun Penyedia';
                            elseif($this->uri->segment(2) == 'akun_pokja') echo 'Manajemen Akun POKJA';
                            else echo 'Global Overview';
                        ?>
                        </h5>
                    </div>
                    <div class="ml-auto d-flex align-items-center">
                        <div class="notif-wrap" style="position: relative;">
                            <a href="#" class="navbar-icon-btn" id="notifBell" aria-label="Notifikasi">
                                <i class="far fa-bell"></i>
                                <span class="badge badge-danger notif-badge" id="notifBadge">0</span>
                            </a>
                            <div id="notifDropdown" class="dropdown-menu" style="display:none; position:absolute; right:0; top:calc(100% + 10px); width:360px; max-height:420px; overflow-y:auto; padding:0; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);">
                                <div class="p-3 border-bottom bg-light">
                                    <strong class="small">Notifikasi</strong>
                                </div>
                                <div id="notifList"></div>
                            </div>
                        </div>
                        <div class="text-right mr-3 d-none d-sm-block">
                            <small class="text-muted d-block">Selamat datang,</small>
                            <span class="font-weight-bold text-dark"><?= $this->session->userdata('username') ?></span>
                        </div>
                        <div class="dropdown ml-3">
                            <?php 
                                $username_safe = $this->session->userdata('username');
                                $profil_foto = $this->db->get_where('users', ['username' => $username_safe])->row('foto');
                                if (!empty($profil_foto) && $profil_foto != 'default.png') {
                                    $ava_url = base_url('assets/img/profile/' . $profil_foto);
                                } else {
                                    $ava_url = "https://ui-avatars.com/api/?name=" . urlencode($username_safe) . "&background=6c5ce7&color=fff";
                                }
                            ?>
                            <a class="nav-link dropdown-toggle p-0" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="<?= $ava_url ?>" class="rounded-circle shadow-sm" width="40" style="object-fit: cover; height: 40px; border: 2px solid #fff;">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in mt-2" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= base_url('admin/edit_profil') ?>">
                                    <i class="fas fa-user-edit fa-sm fa-fw mr-2 text-primary"></i> Edit Profil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= base_url('auth/logout') ?>" style="color: #e74a3b;">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <script>
                (function() {
                    var wrapper = document.getElementById('wrapper');
                    var btn = document.getElementById('sidebarToggle');
                    if (!wrapper || !btn) return;

                    var stored = localStorage.getItem('sipeta_sidebar_collapsed');
                    if (stored === '1') wrapper.classList.add('sidebar-collapsed');

                    btn.addEventListener('click', function() {
                        wrapper.classList.toggle('sidebar-collapsed');
                        localStorage.setItem('sipeta_sidebar_collapsed', wrapper.classList.contains('sidebar-collapsed') ? '1' : '0');
                    });

                    // Mobile: add sidebar-open class when opening from collapsed state
                    if (window.innerWidth <= 768) {
                        wrapper.classList.add('sidebar-open');
                        btn.addEventListener('click', function() {
                            wrapper.classList.toggle('sidebar-open');
                        });
                    }
                })();
            </script>

            <div class="container-fluid px-4">
