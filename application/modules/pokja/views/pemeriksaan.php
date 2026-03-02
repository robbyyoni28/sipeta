<?php $module = $this->uri->segment(1); ?>
<style>
    .premium-alert {
        background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
        border: none;
        border-radius: 20px;
        color: white;
        box-shadow: 0 10px 15px -3px rgba(67, 97, 238, 0.3);
    }
    .premium-alert h5 { font-weight: 800; letter-spacing: -0.02em; }
    .premium-alert p { opacity: 0.9; font-weight: 500; }


    .table-premium thead th {
        background: #f8fafc;
        border: none;
        color: #64748b;
        font-size: 0.75rem;
        padding: 1.25rem 1rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table-premium tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
    }

    .btn-periksa {
        background: #f1f5f9;
        color: #4361ee;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        padding: 8px 16px;
        transition: all 0.2s;
    }

    .btn-periksa:hover {
        background: #4361ee;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(67, 97, 238, 0.2);
    }

    .operator-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
    }
</style>

<div class="alert premium-alert py-4 mb-4">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="icon-circle bg-white text-primary" style="width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-shield-alt fa-lg"></i>
            </div>
        </div>
        <div class="col">
            <h5 class="m-0">Audit & Pemeriksaan SIPETA</h5>
            <p class="m-0 small">Verifikasi penggunaan aset personel and peralatan secara transparan antar paket tender.</p>
        </div>
    </div>
</div>

<!-- Premium Filter Section -->
<div class="card filter-card-premium border-0 shadow-sm mb-4" style="border-radius: 24px;">
    <div class="card-body p-4">
        <label class="small font-weight-bold text-muted text-uppercase mb-3 d-block"><i class="fas fa-calendar-alt mr-2 text-primary"></i> Quick Filter Tahun Anggaran</label>
        <div class="quick-filter-wrapper" id="yearFilter">
            <a href="javascript:void(0)" class="filter-chip active" data-id="">
                <i class="fas fa-history text-primary"></i> Semua Tahun
            </a>
            <?php foreach($years as $yr): ?>
            <a href="javascript:void(0)" class="filter-chip" data-id="<?= $yr['tahun'] ?>">
                <i class="fas fa-calendar-check text-primary"></i> <?= $yr['tahun'] ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius: 24px; overflow: hidden;">
    <div class="card-header bg-white py-4 border-0">
        <div class="d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-800 text-dark"><i class="fas fa-list-ul mr-2 text-primary"></i>HASIL PEMERIKSAAN TENDER</h6>
            <div id="tenderCountBadge" class="badge badge-light px-3 py-2 rounded-pill font-weight-bold text-primary border">Memuat...</div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 table-premium" id="ajaxTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="15%">Kode Tender</th>
                        <th>Informasi Nama Tender</th>
                        <th width="8%" class="text-center">Tahun</th>
                        <th width="20%">Nama Penyedia</th>
                        <th width="15%">Admin Input</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded by AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let table = $('#ajaxTable').DataTable({
        ...window.dtOptions,
        "ajax": {
            "url": "<?= base_url($module.'/data_tender_json') ?>",
            "data": function(d) {
                d.tahun = $('.filter-chip.active').data('id');
            },
            "dataSrc": function(json) {
                $('#tenderCountBadge').text('Total: ' + json.data.length + ' Paket');
                return json.data;
            }
        },
        "columns": [
            { 
                "data": "kode_tender",
                "render": function(data) {
                    return `<code class="text-primary font-weight-bold" style="font-size: 0.9rem; background: #eef2ff; padding: 4px 8px; border-radius: 6px;">${data}</code>`;
                }
            },
            {
                "data": "nama_tender",
                "render": function(data, type, row) {
                    let date = row.tanggal_input ? new Date(row.tanggal_input).toLocaleDateString('id-ID') : '-';
                    return `
                        <div class="font-weight-800 text-dark mb-1" style="font-size: 0.95rem; line-height: 1.4;">${data}</div>
                        <div class="small text-muted font-weight-bold mt-1">
                            <i class="far fa-calendar-check mr-1 opacity-50"></i> Terimput: ${date}
                        </div>`;
                }
            },
            {
                "data": "tahun_anggaran",
                "className": "text-center",
                "render": function(data) {
                    return `<span class="badge badge-warning px-3 py-2 rounded-pill font-weight-bold shadow-sm" style="font-size: 0.75rem;">${data || '-'}</span>`;
                }
            },
            {
                "data": "nama_perusahaan",
                "render": function(data) {
                    return `<div class="font-weight-700 text-dark">${data}</div>`;
                }
            },
            {
                "data": "created_by",
                "render": function(data, type, row) {
                    let role = row.created_role ? row.created_role.toUpperCase() : '-';
                    return `
                        <div class="operator-badge">
                            <i class="fas fa-user-circle mr-2 opacity-50"></i>${data || '-'}
                            <span class="ml-2 badge badge-light border">${role}</span>
                        </div>`;
                }
            },
            {
                "data": "id",
                "className": "text-center",
                "render": function(data, type, row) {
                    let btnDetail = `
                        <a href="<?= base_url($module.'/detail/') ?>${data}" class="btn-periksa d-inline-flex align-items-center btn-sm mr-1">
                            <i class="fas fa-search mr-1"></i> DETAIL
                        </a>`;
                    
                    let btnDelete = '';
                    if ("<?= $this->session->userdata('role') ?>" === "admin" && "<?= $module ?>" === "admin") {
                        btnDelete = `
                            <button class="btn btn-sm btn-outline-danger btn-delete-tender" data-id="${data}" style="border-radius: 12px; font-weight: 700;">
                                <i class="fas fa-trash"></i>
                            </button>`;
                    }
                    
                    return `<div class="d-flex justify-content-center">${btnDetail}${btnDelete}</div>`;
                }
            }
        ]
    });

    // Handle Delete Tender
    $(document).on('click', '.btn-delete-tender', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Tender?',
            text: "Data tender dan semua keterkaitan personel/alat akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('admin/tender_delete/') ?>" + id;
            }
        });
    });

    // Chip Click Handler
    $('.filter-chip').on('click', function() {
        $('.filter-chip').removeClass('active');
        $(this).addClass('active');
        table.ajax.reload();
    });
});
</script>
