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

        /* SIDEBAR SUBMENU COLLAPSIBLE SYSTEM */
        .sidebar-group {
            margin-bottom: 4px;
        }
        .sidebar-group-header {
            padding: 12px 18px;
            margin: 4px 12px;
            border-radius: 12px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: rgba(255,255,255,0.7);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none !important;
        }
        .sidebar-group-header:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
        }
        .sidebar-group-header.active-group {
            color: var(--accent);
            background: rgba(255, 204, 0, 0.12);
        }
        .sidebar-group-header .chevron-icon {
            font-size: 0.7rem;
            transition: transform 0.3s ease;
        }
        .sidebar-group-header[aria-expanded="true"] .chevron-icon {
            transform: rotate(90deg);
        }
        .sidebar-submenu {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        .sidebar-submenu li a {
            padding: 10px 16px 10px 34px !important;
            margin: 3px 12px !important;
            border-radius: 10px !important;
            font-size: 0.88rem !important;
            min-height: 40px !important;
            color: rgba(255,255,255,0.7) !important;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            text-decoration: none !important;
        }
        .sidebar-submenu li a:hover {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.08) !important;
            transform: translateX(4px);
        }
        .sidebar-submenu li.active > a {
            color: #fff !important;
            background: var(--sidebar-active) !important;
            border-left: 3px solid var(--accent);
            font-weight: 600;
        }

        #wrapper.sidebar-collapsed .sidebar-group-header span,
        #wrapper.sidebar-collapsed .sidebar-group-header .chevron-icon {
            display: none;
        }
        #wrapper.sidebar-collapsed .sidebar-submenu li a span,
        #wrapper.sidebar-collapsed .sidebar-submenu li a .menu-text {
            display: none;
        }
        #wrapper.sidebar-collapsed .sidebar-submenu li a {
            padding: 12px !important;
            justify-content: center;
        }
    </style>
</head>
<?php 
    $module = $this->uri->segment(1); 
    $seg2 = $this->uri->segment(2);
    $jenis_get = $this->input->get('jenis');

    $is_tender_active = in_array($seg2, ['input_pemenang', 'input_pemenang_konsultansi', 'input_pemenang_non_konstruksi']);
    $is_teknis_active = in_array($seg2, ['manajer_teknik', 'manajer_keuangan', 'personel_lapangan', 'personel_k3', 'peralatan', 'pemilik_alat']);
    $is_monitoring_active = in_array($seg2, ['data_tender', 'detail', 'detail_non_konstruksi']);
    $is_master_active = in_array($seg2, ['daftar_perusahaan', 'manage']);
    $is_mgmt_active = in_array($seg2, ['akun_pokja', 'akun_sekretariat', 'verifikasi_penyedia']);
?>
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

            <div class="sidebar-menu-wrapper mt-3 mb-4">
                <!-- DASHBOARD -->
                <div class="sidebar-group mb-2">
                    <ul class="list-unstyled sidebar-submenu p-0">
                        <li class="<?= $seg2 == '' ? 'active' : '' ?>">
                            <a href="<?= base_url('admin') ?>" style="padding-left: 18px !important;"><i class="fas fa-th-large mr-2"></i> <span class="menu-text">Dashboard</span></a>
                        </li>
                    </ul>
                </div>

                <!-- MANAGEMENT -->
                <div class="sidebar-group">
                    <a class="sidebar-group-header <?= $is_mgmt_active ? 'active-group' : 'collapsed' ?>" data-toggle="collapse" href="#groupMgmt" role="button" aria-expanded="<?= $is_mgmt_active ? 'true' : 'false' ?>" aria-controls="groupMgmt">
                        <span><i class="fas fa-users-cog mr-2"></i> MANAGEMENT</span>
                        <i class="fas fa-chevron-right chevron-icon"></i>
                    </a>
                    <div class="collapse <?= $is_mgmt_active ? 'show' : '' ?>" id="groupMgmt">
                        <ul class="list-unstyled sidebar-submenu">
                            <li class="<?= $seg2 == 'akun_pokja' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/akun_pokja') ?>"><i class="fas fa-users-cog mr-2"></i> <span class="menu-text">Akun POKJA</span></a>
                            </li>
                            <li class="<?= $seg2 == 'akun_sekretariat' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/akun_sekretariat') ?>"><i class="fas fa-user-shield mr-2"></i> <span class="menu-text">Akun Sekretariat</span></a>
                            </li>
                            <li class="<?= $seg2 == 'verifikasi_penyedia' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/verifikasi_penyedia') ?>"><i class="fas fa-check-circle mr-2"></i> <span class="menu-text">Verifikasi Penyedia</span></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- GROUP 1: TENDER -->
                <div class="sidebar-group">
                    <a class="sidebar-group-header <?= $is_tender_active ? 'active-group' : 'collapsed' ?>" data-toggle="collapse" href="#groupTender" role="button" aria-expanded="<?= $is_tender_active ? 'true' : 'false' ?>" aria-controls="groupTender">
                        <span><i class="fas fa-folder-open mr-2"></i> TENDER</span>
                        <i class="fas fa-chevron-right chevron-icon"></i>
                    </a>
                    <div class="collapse <?= $is_tender_active ? 'show' : '' ?>" id="groupTender">
                        <ul class="list-unstyled sidebar-submenu">
                            <li class="<?= $seg2 == 'input_pemenang' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/input_pemenang') ?>"><i class="far fa-star mr-2"></i> <span class="menu-text">Tender Konstruksi</span></a>
                            </li>
                            <li class="<?= $seg2 == 'input_pemenang_konsultansi' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/input_pemenang_konsultansi') ?>"><i class="fas fa-handshake mr-2"></i> <span class="menu-text">Tender Konsultansi</span></a>
                            </li>
                            <li class="<?= $seg2 == 'input_pemenang_non_konstruksi' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/input_pemenang_non_konstruksi') ?>"><i class="fas fa-boxes mr-2"></i> <span class="menu-text">Tender Non Konstruksi</span></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- GROUP 2: DATA TEKNIS -->
                <div class="sidebar-group">
                    <a class="sidebar-group-header <?= $is_teknis_active ? 'active-group' : 'collapsed' ?>" data-toggle="collapse" href="#groupTeknis" role="button" aria-expanded="<?= $is_teknis_active ? 'true' : 'false' ?>" aria-controls="groupTeknis">
                        <span><i class="fas fa-cogs mr-2"></i> DATA TEKNIS</span>
                        <i class="fas fa-chevron-right chevron-icon"></i>
                    </a>
                    <div class="collapse <?= $is_teknis_active ? 'show' : '' ?>" id="groupTeknis">
                        <ul class="list-unstyled sidebar-submenu">
                            <li class="<?= $seg2 == 'manajer_teknik' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/manajer_teknik') ?>"><i class="fas fa-user-cog mr-2"></i> <span class="menu-text">Manajer Teknik</span></a>
                            </li>
                            <li class="<?= $seg2 == 'manajer_keuangan' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/manajer_keuangan') ?>"><i class="fas fa-user-tie mr-2"></i> <span class="menu-text">Manajer Keuangan</span></a>
                            </li>
                            <li class="<?= $seg2 == 'personel_lapangan' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/personel_lapangan') ?>"><i class="fas fa-users mr-2"></i> <span class="menu-text">Pelaksana Lapangan</span></a>
                            </li>
                            <li class="<?= $seg2 == 'personel_k3' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/personel_k3') ?>"><i class="fas fa-hard-hat mr-2"></i> <span class="menu-text">Personel K3</span></a>
                            </li>
                            <li class="<?= $seg2 == 'peralatan' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/peralatan') ?>"><i class="fas fa-truck-pickup mr-2"></i> <span class="menu-text">Peralatan</span></a>
                            </li>
                            <li class="<?= $seg2 == 'pemilik_alat' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/pemilik_alat') ?>"><i class="fas fa-truck mr-2"></i> <span class="menu-text">Pemilik Alat</span></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- GROUP 3: MONITORING -->
                <div class="sidebar-group">
                    <a class="sidebar-group-header <?= $is_monitoring_active ? 'active-group' : 'collapsed' ?>" data-toggle="collapse" href="#groupMonitoring" role="button" aria-expanded="<?= $is_monitoring_active ? 'true' : 'false' ?>" aria-controls="groupMonitoring">
                        <span><i class="fas fa-chart-line mr-2"></i> MONITORING</span>
                        <i class="fas fa-chevron-right chevron-icon"></i>
                    </a>
                    <div class="collapse <?= $is_monitoring_active ? 'show' : '' ?>" id="groupMonitoring">
                        <ul class="list-unstyled sidebar-submenu">
                            <li class="<?= $seg2 == 'data_tender' && ($jenis_get == 'konstruksi' || !$jenis_get) ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/data_tender?jenis=konstruksi') ?>"><i class="fas fa-file-contract mr-2"></i> <span class="menu-text">Monitoring Konstruksi</span></a>
                            </li>
                            <li class="<?= $seg2 == 'data_tender' && $jenis_get == 'konsultansi' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/data_tender?jenis=konsultansi') ?>"><i class="fas fa-handshake mr-2"></i> <span class="menu-text">Detail Tender Konsultansi</span></a>
                            </li>
                            <li class="<?= $seg2 == 'data_tender' && $jenis_get == 'non_konstruksi' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/data_tender?jenis=non_konstruksi') ?>"><i class="fas fa-layer-group mr-2"></i> <span class="menu-text">Monitoring Non Konstruksi</span></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- GROUP 4: MASTER DATA -->
                <div class="sidebar-group">
                    <a class="sidebar-group-header <?= $is_master_active ? 'active-group' : 'collapsed' ?>" data-toggle="collapse" href="#groupMaster" role="button" aria-expanded="<?= $is_master_active ? 'true' : 'false' ?>" aria-controls="groupMaster">
                        <span><i class="fas fa-database mr-2"></i> MASTER DATA</span>
                        <i class="fas fa-chevron-right chevron-icon"></i>
                    </a>
                    <div class="collapse <?= $is_master_active ? 'show' : '' ?>" id="groupMaster">
                        <ul class="list-unstyled sidebar-submenu">
                            <li class="<?= $seg2 == 'daftar_perusahaan' || $seg2 == 'manage' ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/daftar_perusahaan') ?>"><i class="fas fa-address-card mr-2"></i> <span class="menu-text">Data Perusahaan</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

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
