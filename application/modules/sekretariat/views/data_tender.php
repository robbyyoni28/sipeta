<?php $module = $this->uri->segment(1); ?>
<style>
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

    .btn-new-recording {
        background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        transition: all 0.2s;
    }

    .btn-new-recording:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        color: white;
    }

    .tender-stats-box {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .tender-stat-item {
        background: #f1f5f9;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #475569;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice-dollar mr-3 text-primary"></i>Monitoring Data Tender</h1>
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
                        <th>Informasi Paket Tender</th>
                        <th>Riwayat Input</th>
                        <th class="text-center">Tahun</th>
                        <th>Penyedia Menang</th>
                        <th class="text-right">HPS / Penawaran</th>
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
            "url": "<?= base_url($module.'/data_tender_json') ?>",
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
                "data": "tanggal_input",
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
                "data": "nama_tender",
                "render": function(data, type, row) {
                    return `
                        <code class="text-primary font-weight-bold mb-1 d-block" style="font-size: 10px;">${row.kode_tender}</code>
                        <div class="font-weight-800 text-dark mb-1" style="line-height: 1.3;">${data}</div>
                        <div class="tender-stats-box">
                            <div class="tender-stat-item"><i class="fas fa-users mr-1"></i>${row.jumlah_personel} Pers</div>
                            <div class="tender-stat-item"><i class="fas fa-tools mr-1"></i>${row.jumlah_alat} Alat</div>
                        </div>`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    const createdBy = row.created_by ? row.created_by : '-';
                    const createdRole = row.created_role ? row.created_role : '';
                    return `
                        <div class="font-weight-800 text-dark">${createdBy}</div>
                        <small class="text-muted font-weight-bold">${createdRole || '-'}</small>`;
                }
            },
            {
                "data": "tahun_anggaran",
                "className": "text-center",
                "render": function(data) {
                    return `<span class="badge badge-warning px-3 py-2 rounded-pill font-weight-bold shadow-sm small">${data}</span>`;
                }
            },
            {
                "data": "nama_perusahaan",
                "render": function(data, type, row) {
                    return `
                        <div class="font-weight-800 text-dark">${data}</div>
                        <small class="text-muted font-weight-bold">${row.npwp || '-'}</small>`;
                }
            },
            {
                "data": "hps",
                "className": "text-right",
                "render": function(data, type, row) {
                    let hps = new Intl.NumberFormat('id-ID').format(data);
                    return `
                        <div class="font-weight-800 text-dark small">Rp ${hps}</div>
                        <div class="text-success font-weight-bold" style="font-size: 0.75rem;">(Terpilih)</div>`;
                }
            },
            {
                "data": "id",
                "className": "text-center",
                "render": function(data) {
                    return `
                        <a href="<?= base_url('pokja/detail/') ?>${data}" class="btn btn-sm btn-light font-weight-bold" style="border-radius: 8px; border: 1px solid #e2e8f0; color: #4361ee;">
                            <i class="fas fa-search-plus mr-1"></i> Detail
                        </a>`;
                }
            }
        ]
    });

    // Filter Change Handler
    $('#companyFilter, #yearFilter').on('change', function() {
        table.ajax.reload();
    });
});
</script>
