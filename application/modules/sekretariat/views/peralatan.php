<?php $module = $this->uri->segment(1); ?>
<style>
    :root {
        --eq-primary: #0d9488;
        --eq-light: #f0fdfa;
    }

    .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
        font-weight: 800;
        letter-spacing: -0.02em;
    }



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

    .avatar-eq {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.2);
    }

    .btn-action-view {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: var(--eq-light);
        color: var(--eq-primary);
        transition: all 0.2s;
        border: none;
    }

    .btn-action-view:hover {
        background: var(--eq-primary);
        color: white;
        transform: translateY(-2px);
    }

    .premium-modal .modal-content {
        border-radius: 24px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-info-item {
        background: #f8fafc;
        padding: 1rem;
        border-radius: 12px;
        height: 100%;
        border: 1px solid #f1f5f9;
    }

    .btn-premium-primary {
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3);
        transition: all 0.2s;
    }

    .btn-premium-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(13, 148, 136, 0.4);
        color: white;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tools mr-3 text-info"></i>Monitoring Peralatan</h1>
</div>

<!-- Premium Filter Section -->
<div class="card filter-card-premium border-0 shadow-sm mb-4" style="border-radius: 24px;">
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-muted text-uppercase mb-2 d-block"><i class="fas fa-industry mr-2 text-primary"></i> Filter Perusahaan</label>
                <select class="form-control" id="companyFilter" style="height: 48px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-weight: 600; color: #1e293b;">
                    <option value="">Semua Perusahaan</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="small font-weight-bold text-muted text-uppercase mb-2 d-block"><i class="fas fa-calendar-alt mr-2 text-primary"></i> Tahun Anggaran</label>
                <select class="form-control" id="yearFilter" style="height: 48px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-weight: 600; color: #1e293b;">
                    <option value="">Semua Tahun</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius: 24px; overflow: hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 table-premium" id="ajaxTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Tanggal Input</th>
                        <th>Informasi Alat</th>
                        <th>Merk & Spesifikasi</th>
                        <th>Nama Perusahaan</th>
                        <th>Lokasi & Status</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded by AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title font-weight-bold text-info"><i class="fas fa-tools mr-2"></i>Spesifikasi & Riwayat Alat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                <div id="detailBasicInfo" class="row mb-4"></div>
                <hr>
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-history mr-2 text-info"></i>
                    <h6 class="font-weight-bold m-0">Rekam Jejak Tender</h6>
                </div>
                <div id="detailHistory">
                    <div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-info"></i></div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light font-weight-bold px-4 py-2" style="border-radius: 12px;" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load Filters via AJAX
    $.getJSON("<?= base_url($module.'/get_filter_data') ?>", function(data) {
        let companySelect = $('#companyFilter');
        data.penyedia_list.forEach(function(item) {
            companySelect.append(new Option(item.nama_perusahaan, item.id));
        });

        let yearSelect = $('#yearFilter');
        data.available_years.forEach(function(year) {
            yearSelect.append(new Option(year, year));
        });
    });

    let table = $('#ajaxTable').DataTable({
        ...window.dtOptions,
        "ajax": {
            "url": "<?= base_url($module.'/peralatan_json') ?>",
            "data": function(d) {
                d.penyedia_id = $('#companyFilter').val();
                d.tahun = $('#yearFilter').val();
            }
        },
        "columns": [
            { 
                "data": null, 
                "className": "text-center text-muted font-weight-bold small",
                "render": function (data, type, row, meta) { return meta.row + 1; } 
            },
            {
                "data": "created_at",
                "render": function(data) {
                    if(!data) return '-';
                    let date = new Date(data);
                    let day = String(date.getDate()).padStart(2, '0');
                    let month = String(date.getMonth() + 1).padStart(2, '0');
                    let year = date.getFullYear();
                    return `<div class="font-weight-bold text-dark small">${day}/${month}/${year}</div>`;
                }
            },
            { 
                "data": "nama_alat",
                "render": function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <div class="avatar-eq mr-3"><i class="fas fa-truck-monster"></i></div>
                            <div>
                                <div class="font-weight-800 text-dark">${data}</div>
                                <div class="badge badge-light border text-muted px-2 py-1 mt-1" style="font-size: 0.7rem; font-family: monospace;">${row.kapasitas || '-'}</div>
                            </div>
                        </div>`;
                }
            },
            {
                "data": "merk",
                "render": function(data, type, row) {
                    return `
                        <div class="font-weight-700 text-info small mb-1">${data || '-'}</div>
                        <code class="text-muted small">${row.nomor_seri || '-'}</code>`;
                }
            },
            {
                "data": "nama_perusahaan",
                "render": function(data) {
                    return `<div class="font-weight-700 text-dark" style="font-size: 0.85rem;">${data || '-'}</div>`;
                }
            },
            {
                "data": "lokasi_sekarang",
                "render": function(data, type, row) {
                    return `
                        <div class="small font-weight-800 text-dark"><i class="fas fa-map-marker-alt mr-2 text-danger opacity-50"></i>${data || '-'}</div>
                        <div class="mt-1"><span class="badge badge-pill badge-primary px-3 py-1" style="font-size: 0.65rem;">${row.status_kepemilikan || '-'}</span></div>`;
                }
            },
            {
                "data": "id",
                "className": "text-center",
                "render": function(data, type, row) {
                    let btnDetail = `
                        <button class="btn-action-view btn-detail-resource mr-1" data-id="${data}" data-type="peralatan" title="Lihat Detail Riwayat">
                            <i class="fas fa-search-plus"></i>
                        </button>`;
                    
                    let adminBtns = '';
                    if ("<?= $this->session->userdata('role') ?>" === "admin" && "<?= $module ?>" === "admin") {
                        adminBtns = `
                            <button class="btn-action-view btn-edit-peralatan mr-1 text-warning" data-id="${data}" title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action-view btn-delete-peralatan text-danger" data-id="${data}" title="Hapus Data">
                                <i class="fas fa-trash"></i>
                            </button>`;
                    }

                    return `<div class="d-flex justify-content-center">${btnDetail}${adminBtns}</div>`;
                }
            }
        ]
    });

    // Admin Handlers
    $(document).on('click', '.btn-delete-peralatan', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Peralatan?',
            text: "Data alat akan terhapus namun riwayat tender tetap tercatat di sistem.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('admin/peralatan_delete/') ?>" + id;
            }
        });
    });

    $(document).on('click', '.btn-edit-peralatan', function() {
        let btn = $(this);
        let id = btn.data('id');
        let row = table.row(btn.closest('tr')).data();
        Swal.fire({
            title: 'Fitur Edit',
            text: 'Mengarahkan ke halaman manajemen peralatan perusahaan...',
            icon: 'info',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "<?= base_url('admin/peralatan/') ?>" + row.penyedia_id;
        });
    });

    // Select Change Handler
    $('#companyFilter, #yearFilter').on('change', function() {
        table.ajax.reload();
    });



    // Modal Handler
    $(document).on('click', '.btn-detail-resource', function() {
        let btn = $(this);
        let id = btn.data('id');
        let type = btn.data('type');
        let row = table.row(btn.closest('tr')).data();

        $('#detailBasicInfo').html(`
            <div class="col-md-6 mb-3">
                <small class="text-muted font-weight-bold text-uppercase d-block mb-1">Nama Alat</small>
                <div class="font-weight-bold text-dark h6">${row.nama_alat}</div>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted font-weight-bold text-uppercase d-block mb-1">Merk / Brand</small>
                <div class="font-weight-bold text-info h6">${row.merk || '-'}</div>
            </div>
            <div class="col-md-6">
                <small class="text-muted font-weight-bold text-uppercase d-block mb-1">Pemilik</small>
                <div class="font-weight-bold text-dark h6">${row.nama_perusahaan || '-'}</div>
            </div>
             <div class="col-md-6">
                <small class="text-muted font-weight-bold text-uppercase d-block mb-1">Kapasitas</small>
                <div class="font-weight-bold text-primary h6">${row.kapasitas || '-'}</div>
            </div>
        `);

        $('#detailModal').modal('show');
        $('#detailHistory').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-info"></i></div>');

        $.get("<?= base_url($module.'/get_resource_history') ?>", {id: id, type: type}, function(history) {
            let html = '<div class="list-group list-group-flush">';
            if (history.length > 0) {
                history.forEach(h => {
                    html += `
                        <div class="list-group-item border-0 mb-2 shadow-sm rounded-lg p-3" style="background: #f0fdfa; border-radius: 12px; border-left: 4px solid #0d9488 !important;">
                            <div class="small text-muted mb-1 text-uppercase font-weight-bold">${h.kode_tender}</div>
                            <div class="font-weight-bold text-dark mb-1">${h.nama_tender}</div>
                            <div class="small text-teal font-weight-bold">${h.nama_perusahaan}</div>
                        </div>`;
                });
            } else {
                html += '<div class="text-center p-4 text-muted">Tidak ada riwayat.</div>';
            }
            html += '</div>';
            $('#detailHistory').html(html);
        });
    });

});
</script>
