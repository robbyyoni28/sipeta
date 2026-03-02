<?php $module = $this->uri->segment(1); ?>
<style>
    :root {
        --k3-primary: #f59e0b;
        --k3-dark: #d97706;
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

    .avatar-k3 {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2);
    }

    .btn-action-view {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #fff8eb;
        color: var(--k3-primary);
        transition: all 0.2s;
        border: none;
    }

    .btn-action-view:hover {
        background: var(--k3-primary);
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
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        transition: all 0.2s;
    }

    .btn-premium-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        color: white;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-briefcase-medical mr-3 text-warning"></i>Monitoring Personel Ahli K3</h1>
</div>

<!-- Premium Filter Section -->
<div class="card filter-card-premium border-0 shadow-sm mb-4" style="border-radius: 24px;">
    <div class="card-body p-4">
        <label class="small font-weight-bold text-muted text-uppercase mb-3 d-block"><i class="fas fa-filter mr-2 text-warning"></i> Quick Filter</label>
        <div class="row align-items-center">
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
                        <th>Identitas Personel</th>
                        <th>Jabatan K3</th>
                        <th>Sertifikat K3</th>
                        <th class="text-center">Masa Berlaku</th>
                        <th width="10%" class="text-center">Aksi</th>
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
                <h5 class="modal-title font-weight-bold" style="color: var(--k3-dark);"><i class="fas fa-id-card-alt mr-2"></i>Riwayat & Detail Personel K3</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                <div id="detailBasicInfo" class="row mb-4"></div>
                <hr>
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-history mr-2 text-warning"></i>
                    <h6 class="font-weight-bold m-0">Rekam Jejak Tender</h6>
                </div>
                <div id="detailHistory" style="max-height: 45vh; overflow-y: auto;">
                    <div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-warning"></i></div>
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
            "url": "<?= base_url($module.'/personel_k3_json') ?>",
            "data": function(d) {
                d.penyedia_id = $('#companyFilter').val();
                d.tahun = $('#yearFilter').val();
            }
        },
        "columns": [
            { 
                "data": null, 
                "orderable": false,
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
                "data": "nama",
                "render": function(data, type, row) {
                    return `<div class="font-weight-bold text-dark">${data}</div>
                            <div class="small text-muted"><i class="fas fa-id-card mr-1"></i>${row.nik || '-'}</div>
                            <div class="small text-muted mt-1"><i class="fas fa-building mr-1"></i>${row.nama_perusahaan || '-'}</div>`;
                }
            },
            { "data": "jabatan_k3" },
            { 
                "data": "jenis_sertifikat_k3",
                "render": function(data, type, row) {
                     return `<div class="font-weight-bold text-dark small">${data || '-'}</div>
                             <div class="small text-muted text-monospace">${row.nomor_sertifikat_k3 || '-'}</div>`;
                }
            },
            { 
                "data": "masa_berlaku_sertifikat",
                "className": "text-center",
                "render": function(data, type, row) {
                    if (!data) return '-';
                    let cssClass = 'badge-success';
                    let text = 'Valid';
                    
                    if (row.expiry_status) {
                        if (row.expiry_status.status === 'expired') {
                            cssClass = 'badge-danger';
                            text = 'Expired';
                        } else if (row.expiry_status.status === 'warning') {
                            cssClass = 'badge-warning';
                            text = 'Hampir Expired';
                        }
                    }
                    
                    return `<div class="small font-weight-bold">${data}</div><span class="badge ${cssClass} mt-1">${text}</span>`;
                }
            },
            {
                "data": "id",
                "orderable": false,
                "className": "text-center",
                "render": function(data, type, row) {
                    let btnDetail = `
                        <button class="btn-action-view btn-detail-resource mr-1" data-id="${data}" data-type="personel_k3" title="Lihat Detail Riwayat">
                            <i class="fas fa-search-plus"></i>
                        </button>`;
                    
                    let adminBtns = '';
                    if ("<?= $this->session->userdata('role') ?>" === "admin" && "<?= $module ?>" === "admin") {
                        adminBtns = `
                            <button class="btn-action-view btn-edit-personel mr-1 text-warning" data-id="${data}" title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action-view btn-delete-personel text-danger" data-id="${data}" title="Hapus Data">
                                <i class="fas fa-trash"></i>
                            </button>`;
                    }

                    return `<div class="d-flex justify-content-center">${btnDetail}${adminBtns}</div>`;
                }
            }
        ]
    });

    // Admin Handlers
    $(document).on('click', '.btn-delete-personel', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Personel K3?',
            text: "Data personel K3 akan terhapus namun riwayat tender tetap tercatat di sistem.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('admin/personel_k3_delete/') ?>" + id;
            }
        });
    });

    $(document).on('click', '.btn-edit-personel', function() {
        let btn = $(this);
        let id = btn.data('id');
        let row = table.row(btn.closest('tr')).data();
        Swal.fire({
            title: 'Fitur Edit',
            text: 'Mengarahkan ke halaman manajemen personel perusahaan...',
            icon: 'info',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "<?= base_url('admin/personel/') ?>" + row.penyedia_id;
        });
    });

    // Filter Event
    $('#companyFilter, #yearFilter').change(function() {
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
                <small class="text-muted font-weight-bold text-uppercase d-block mb-1">Nama Lengkap</small>
                <div class="font-weight-bold text-dark h6">${row.nama}</div>
            </div>
            <div class="col-md-6 mb-3">
                <small class="text-muted font-weight-bold text-uppercase d-block mb-1">NIK</small>
                <div class="font-weight-bold text-dark h6">${row.nik}</div>
            </div>
            <div class="col-md-6">
                <small class="text-muted font-weight-bold text-uppercase d-block mb-1">Penyedia</small>
                <div class="font-weight-bold text-dark h6">${row.nama_perusahaan || '-'}</div>
            </div>
             <div class="col-md-6">
                <small class="text-muted font-weight-bold text-uppercase d-block mb-1">Jabatan K3</small>
                <div class="font-weight-bold text-warning h6">${row.jabatan_k3 || '-'}</div>
            </div>
        `);

        $('#detailModal').modal('show');
        $('#detailHistory').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x text-warning"></i></div>');

        $.get("<?= base_url($module.'/get_resource_history') ?>", {id: id, type: type}, function(history) {
            let html = '<div class="list-group list-group-flush">';
            if (history.length > 0) {
                history.forEach(h => {
                    html += `
                        <div class="list-group-item border-0 mb-2 shadow-sm rounded-lg p-3" style="background: #fffbeb; border-radius: 12px; border-left: 4px solid #f59e0b !important;">
                            <div class="small text-muted mb-1 text-uppercase font-weight-bold">${h.kode_tender}</div>
                            <div class="font-weight-bold text-dark mb-1">${h.nama_tender}</div>
                            <div class="small text-warning font-weight-bold">${h.nama_perusahaan}</div>
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
