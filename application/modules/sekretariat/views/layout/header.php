<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sekretariat Panel - SIPETA (Sistem Informasi Personel dan Peralatan) PPU</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo_ukpbj.png') ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Force datepicker to use dd/mm/yyyy format */
        input.datepicker {
            position: relative;
        }
        .datepicker-days .table-condensed {
            width: auto;
        }
        .datepicker-dropdown {
            position: absolute !important;
            z-index: 2000 !important;
            padding: 8px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
        }
        .datepicker table {
            margin: 0;
        }
        .datepicker table tr td,
        .datepicker table tr th {
            border-radius: 10px;
        }
        .datepicker table tr td.active,
        .datepicker table tr td.active:hover,
        .datepicker table tr td.active.disabled,
        .datepicker table tr td.active.disabled:hover {
            background: linear-gradient(to bottom, #4361ee, #4895ef);
            color: #fff;
        }
    </style>
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #7209b7;
            --sidebar-bg: #0f172a;
            --sidebar-active: #4361ee;
            --content-bg: #f8fafc;
        }

        /* QUICK FILTER CHIPS */
        .quick-filter-wrapper {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 5px 0 15px 0;
            scrollbar-width: thin;
        }
        .quick-filter-wrapper::-webkit-scrollbar { height: 4px; }
        .quick-filter-wrapper::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        .filter-chip {
            white-space: nowrap;
            padding: 10px 20px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 100px;
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
        }
        .filter-chip i { margin-right: 8px; font-size: 0.9rem; }
        .filter-chip:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(67, 97, 238, 0.05);
            transform: translateY(-1px);
        }
        .filter-chip.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--content-bg);
            color: #1e293b;
            letter-spacing: -0.01em;
        }

        #sidebar {
            min-width: 280px;
            max-width: 280px;
            background: var(--sidebar-bg);
            color: #fff;
            height: 100vh;
            position: sticky;
            top: 0;
            box-shadow: 10px 0 30px rgba(0,0,0,0.05);
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        #wrapper.sidebar-collapsed #sidebar {
            min-width: 88px;
            max-width: 88px;
        }
        #wrapper.sidebar-collapsed .sidebar-header {
            padding: 28px 16px;
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
                min-width: 280px;
                max-width: 280px;
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

        .sidebar-header { 
            padding: 40px 25px; 
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.1) 0%, rgba(15, 23, 42, 0) 100%);
        }
        
        .sidebar-brand { 
            font-weight: 800; 
            font-size: 1.4rem; 
            background: linear-gradient(to right, #4cc9f0, #4361ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
        }

        #sidebar ul li a {
            padding: 14px 18px;
            margin: 6px 14px;
            border-radius: 14px;
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.25;
            color: #94a3b8;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 48px;
        }

        #sidebar ul li a i {
            width: 22px;
            font-size: 1.05rem;
            margin-right: 0 !important;
            opacity: 0.9;
        }
        
        #sidebar ul li a:hover { 
            color: #fff; 
            background: rgba(255,255,255,0.08); 
            transform: translateX(4px);
        }
        
        #sidebar ul li.active > a {
            color: #fff;
            background: var(--sidebar-active);
            box-shadow: 0 10px 15px -3px rgba(67, 97, 238, 0.3);
        }

        #wrapper { display: flex; width: 100%; align-items: stretch; }

        #content { flex: 1; min-width: 0; }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            padding: 20px 30px;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 24px;
        }

        .card { 
            border: none; 
            border-radius: 16px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease;
        }

        /* Modern Modal Buttons */
        .btn-mockup-cancel {
            border: 1.5px solid #e2e8f0;
            color: #64748b !important;
            background: #fff;
            border-radius: 14px;
            font-weight: 700;
            padding: 12px 28px;
            font-size: 0.85rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .btn-mockup-cancel:hover {
            background: #f8fafc;
            color: #1e293b !important;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-decoration: none;
        }

        .btn-mockup-save {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white !important;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            padding: 12px 36px;
            font-size: 0.85rem;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .btn-mockup-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.4);
            color: white !important;
            text-decoration: none;
        }

        .btn-mockup-save:active {
            transform: translateY(-1px);
        }

        .btn-mockup-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white !important;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            padding: 12px 36px;
            font-size: 0.85rem;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .btn-mockup-warning:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
            color: white !important;
            text-decoration: none;
        }

        .btn-mockup-danger {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: white !important;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            padding: 12px 36px;
            font-size: 0.85rem;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .btn-mockup-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
            color: white !important;
            text-decoration: none;
        }

        .btn-mockup-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white !important;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            padding: 12px 36px;
            font-size: 0.85rem;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .btn-mockup-info:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(6, 182, 212, 0.4);
            color: white !important;
            text-decoration: none;
        }

        .btn-premium-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white !important;
            border: none;
            border-radius: 16px;
            font-weight: 700;
            padding: 14px 30px;
            font-size: 0.95rem;
            box-shadow: 0 8px 15px rgba(99, 102, 241, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none !important;
        }

        .btn-premium-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(99, 102, 241, 0.35);
            color: white !important;
        }
        
        .card:hover { transform: translateY(-2px); }

        .bg-gradient-primary { background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%) !important; }

        /* Select2 Minimal Filter Style */
        .select2-minimal + .select2-container .select2-selection--single {
            height: 48px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            transition: all 0.3s;
        }

        .select2-minimal + .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 48px;
            padding-left: 15px;
            color: #1e293b;
            font-weight: 600;
        }

        .select2-minimal + .select2-container .select2-selection--single .select2-selection__arrow {
            height: 48px;
        }

        .select2-minimal + .select2-container.select2-container--open .select2-selection--single {
            border-color: #4361ee;
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
            background: white;
        }
        .bg-gradient-info { background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%) !important; }
        .bg-gradient-warning { background: linear-gradient(135deg, #f72585 0%, #b5179e 100%) !important; }
        .bg-gradient-success { background: linear-gradient(135deg, #4ad66d 0%, #2fb344 100%) !important; }

        .btn-pill { border-radius: 30px; padding-left: 20px; padding-right: 20px; font-weight: 600; }
        
        /* Glassmorphism utility */
        .glass-card {
            background: rgba(255, 255, 255, 0.7) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* GLOBAL PREMIUM COMPONENTS */
        .select2-container .select2-selection--single {
            height: 52px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            background-color: #f8fafc !important;
            display: flex;
            align-items: center;
            padding: 0 1rem;
            transition: all 0.2s;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px !important;
            right: 10px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b !important;
            font-weight: 600;
            padding-left: 0 !important;
        }

        /* RESULTS DROPDOWN FIXES */
        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
        }
        .select2-results__options {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .select2-results__option {
            padding: 10px 15px !important;
            font-size: 0.9rem !important;
        }

        .select2-hidden-accessible {
            border: 0 !important;
            clip: rect(0 0 0 0) !important;
            height: 1px !important;
            margin: -1px !important;
            overflow: hidden !important;
            padding: 0 !important;
            position: absolute !important;
            width: 1px !important;
        }

        .filter-card-premium {
            border-radius: 20px;
            border: 1px solid #eef2ff;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

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
        <nav id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i class="fas fa-edit mr-2"></i> SIPETA PPU
                </div>
                <small class="text-muted" style="font-size: 0.5rem; letter-spacing: 1px; display: block; margin-top: 5px;">Sistem Informasi Personel dan Peralatan</small>
            </div>

            <ul class="list-unstyled components mt-3">
                <?php if ($this->session->userdata('role') === 'sekretariat'): ?>
                    <li class="<?= $this->uri->segment(2) == 'input_pemenang' ? 'active' : '' ?>">
                        <a href="<?= base_url('sekretariat/input_pemenang') ?>"><i class="far fa-star mr-2"></i><span class="menu-text">PEMENANG TENDER</span></a>
                    </li>
                    <li class="<?= $this->uri->segment(2) == 'input_pemenang_konsultansi' ? 'active' : '' ?>">
                        <a href="<?= base_url('sekretariat/input_pemenang_konsultansi') ?>"><i class="fas fa-handshake mr-2"></i><span class="menu-text">TENDER KONSULTANSI</span></a>
                    </li>
                <?php else: ?>
                    <li class="<?= $this->uri->segment(2) == 'input_pemenang' ? 'active' : '' ?>">
                        <a href="<?= base_url('sekretariat/input_pemenang') ?>"><i class="far fa-star mr-2"></i><span class="menu-text">PEMENANG TENDER</span></a>
                    </li>
                    <li class="<?= $this->uri->segment(2) == 'input_pemenang_konsultansi' ? 'active' : '' ?>">
                        <a href="<?= base_url('sekretariat/input_pemenang_konsultansi') ?>"><i class="fas fa-handshake mr-2"></i><span class="menu-text">TENDER KONSULTANSI</span></a>
                    </li>
                    <li class="<?= $this->uri->segment(2) == 'personel_lapangan' ? 'active' : '' ?>">
                        <a href="<?= base_url('sekretariat/personel_lapangan') ?>"><i class="fas fa-users mr-2"></i><span class="menu-text">PERSONEL LAPANGAN</span></a>
                    </li>
                    <li class="<?= $this->uri->segment(2) == 'personel_k3' ? 'active' : '' ?>">
                        <a href="<?= base_url('sekretariat/personel_k3') ?>"><i class="fas fa-briefcase-medical mr-2"></i><span class="menu-text">PERSONEL K3</span></a>
                    </li>
                    <li class="<?= $this->uri->segment(2) == 'peralatan' ? 'active' : '' ?>">
                        <a href="<?= base_url('sekretariat/peralatan') ?>"><i class="fas fa-truck-pickup mr-2"></i><span class="menu-text">PERALATAN</span></a>
                    </li>
                    <li class="<?= $this->uri->segment(2) == 'pemilik_alat' ? 'active' : '' ?>">
                        <a href="<?= base_url('sekretariat/pemilik_alat') ?>"><i class="fas fa-truck mr-2"></i><span class="menu-text">PEMILIK ALAT</span></a>
                    </li>
                    <li class="<?= $this->uri->segment(2) == 'data_tender' ? 'active' : '' ?>">
                        <a href="<?= base_url('sekretariat/data_tender') ?>"><i class="fas fa-file-contract mr-2"></i><span class="menu-text">DATA TENDER</span></a>
                    </li>

                    <li class="<?= $this->uri->segment(2) == '' || $this->uri->segment(2) == 'daftar_perusahaan' ? 'active' : '' ?>">
                        <a href="<?= base_url('sekretariat') ?>"><i class="fas fa-address-card mr-2"></i><span class="menu-text">DATA PERUSAHAAN</span></a>
                    </li>
                    <!-- Menu REGULASI sementara dinonaktifkan -->
                <?php endif; ?>
                <!-- Menu REGULASI sementara dinonaktifkan -->
            </ul>

            <div class="mt-auto p-4">
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-light btn-block btn-sm rounded-pill">
                    <i class="fas fa-power-off mr-2"></i> Logout
                </a>
            </div>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-custom">
                <div class="container-fluid">
                    <div class="d-flex align-items-center">
                        <button type="button" id="sidebarToggle" class="navbar-icon-btn mr-3" aria-label="Toggle Sidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h5 class="m-0 text-dark font-weight-bold">SIPETA (Sistem Informasi Personel dan Peralatan)</h5>
                    </div>
                    <div class="ml-auto d-flex align-items-center">
                        <span class="mr-3 font-weight-bold text-muted small">Operator: <?= $this->session->userdata('username') ?></span>
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
                                <img src="<?= $ava_url ?>" class="rounded-circle shadow-sm" width="35" style="object-fit: cover; height: 35px; border: 2px solid #fff;">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in mt-2" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= base_url('sekretariat/edit_profil') ?>">
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
