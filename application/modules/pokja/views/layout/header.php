<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokja Panel - SIPETA (Sistem Informasi Personel dan Peralatan) PPU</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo_ukpbj.png') ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
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
            background: linear-gradient(to bottom, #0ea5e9, #0284c7);
            color: #fff;
        }
    </style>
    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-active: #0ea5e9;
            --content-bg: #f1f5f9;
            --primary: #0f172a;
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
            border-color: #0ea5e9;
            color: #0ea5e9;
            background: #f0f9ff;
            transform: translateY(-1px);
        }
        .filter-chip.active {
            background: #0ea5e9;
            border-color: #0ea5e9;
            color: #fff;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
        }

        body { 
            font-family: 'Roboto', sans-serif; 
            background: var(--content-bg);
            color: #1e293b;
        }

        h1, h2, h3, h4, h5, .sidebar-brand { 
            font-family: 'Montserrat', sans-serif; 
            font-weight: 700;
        }

        #wrapper { display: flex; width: 100%; align-items: stretch; }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: var(--sidebar-bg);
            color: #fff;
            height: 100vh;
            position: sticky;
            top: 0;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        #wrapper.sidebar-collapsed #sidebar {
            min-width: 88px;
            max-width: 88px;
        }
        #wrapper.sidebar-collapsed .sidebar-header {
            padding: 28px 16px;
            text-align: center;
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

        .sidebar-header { padding: 40px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar-brand { color: #0ea5e9; font-size: 1.1rem; text-transform: uppercase; }

        #sidebar ul li a {
            padding: 14px 18px;
            margin: 6px 14px;
            border-radius: 14px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 48px;
            color: #94a3b8;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 600;
        }
        #sidebar ul li a i {
            width: 22px;
            font-size: 1.05rem;
            margin-right: 0 !important;
            opacity: 0.9;
        }
        #sidebar ul li a:hover { color: #fff; background: rgba(255,255,255,0.08); transform: translateX(4px); }
        #sidebar ul li.active > a {
            color: #fff;
            background: var(--sidebar-active);
            font-weight: 600;
        }

        #content { flex: 1; min-width: 0; }
        
        .navbar-custom {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-header { background: #fff; font-weight: 700; border-bottom: 1px solid #f1f5f9; padding: 20px 25px; }

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

        .btn-info { background-color: #0ea5e9; border-color: #0ea5e9; border-radius: 8px; font-weight: 600; }
        .btn-info:hover { background-color: #0284c7; border-color: #0284c7; }

        .table thead th { border: none; background: #f8fafc; font-size: 0.75rem; text-transform: uppercase; color: #64748b; padding: 15px; }
        .table td { vertical-align: middle; padding: 15px; border-top: 1px solid #f1f5f9; }

        .history-list { font-size: 0.85rem; padding-left: 1.2rem; }
        .history-list li { margin-bottom: 5px; color: #475569; }

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
                    <i class="fas fa-search-dollar mr-2"></i> POKJA PANEL
                </div>
            </div>

            <ul class="list-unstyled components mt-3">
                <li class="<?= $this->uri->segment(2) == 'input_pemenang' ? 'active' : '' ?>">
                    <a href="<?= base_url('pokja/input_pemenang') ?>"><i class="far fa-star mr-2"></i> PEMENANG TENDER</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'personel_lapangan' ? 'active' : '' ?>">
                    <a href="<?= base_url('pokja/personel_lapangan') ?>"><i class="fas fa-users mr-2"></i> PERSONEL LAPANGAN</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'personel_k3' ? 'active' : '' ?>">
                    <a href="<?= base_url('pokja/personel_k3') ?>"><i class="fas fa-briefcase-medical mr-2"></i> PERSONEL K3</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'peralatan' ? 'active' : '' ?>">
                    <a href="<?= base_url('pokja/peralatan') ?>"><i class="fas fa-truck-pickup mr-2"></i> PERALATAN</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'pemilik_alat' ? 'active' : '' ?>">
                    <a href="<?= base_url('pokja/pemilik_alat') ?>"><i class="fas fa-truck mr-2"></i> PEMILIK ALAT</a>
                </li>
                <li class="<?= $this->uri->segment(2) == 'data_tender' ? 'active' : '' ?>">
                    <a href="<?= base_url('pokja/data_tender') ?>"><i class="fas fa-file-contract mr-2"></i> DATA TENDER</a>
                </li>

                <li class="<?= $this->uri->segment(2) == 'daftar_perusahaan' || $this->uri->segment(2) == 'manage' ? 'active' : '' ?>">
                    <a href="<?= base_url('pokja/daftar_perusahaan') ?>"><i class="fas fa-address-card mr-2"></i> DATA PERUSAHAAN</a>
                </li>
                <!-- Menu REGULASI sementara dinonaktifkan -->
            </ul>

            <div class="mt-auto p-4">
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-info btn-block btn-sm rounded-pill">
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
                        <div class="notif-wrap" style="position: relative;">
                            <a href="#" class="navbar-icon-btn" id="notifBell" aria-label="Notifikasi">
                                <i class="far fa-bell"></i>
                                <span class="badge badge-danger notif-badge" id="notifBadge">0</span>
                            </a>
                            <div id="notifDropdown" class="dropdown-menu" style="display:none; position:absolute; right:0; left:auto; top:calc(100% + 10px); width:360px; max-width:calc(100vw - 24px); max-height:420px; overflow-y:auto; padding:0; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); z-index:1050;">
                                <div class="p-3 border-bottom bg-light">
                                    <strong class="small">Notifikasi</strong>
                                </div>
                                <div id="notifList"></div>
                            </div>
                        </div>
                        <span class="mr-3 font-weight-bold text-muted small">Welcome, <?= $this->session->userdata('username') ?></span>
                        <img src="https://ui-avatars.com/api/?name=Pokja&background=0ea5e9&color=fff" class="rounded-circle" width="35">
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
