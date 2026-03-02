<?php $module = $this->uri->segment(1); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tools mr-3 text-primary"></i>Audit Pencarian Peralatan</h1>
</div>

<div class="card filter-card-premium border-0 shadow-sm mb-4" style="border-radius: 24px;">
    <div class="card-body p-4">
        <label class="small font-weight-bold text-muted text-uppercase mb-3 d-block"><i class="fas fa-search mr-2 text-primary"></i> Cari Peralatan Secara Global</label>
        <div class="input-group">
            <input type="text" id="globalKeyword" class="form-control" style="border-radius: 12px; height: 52px; font-weight: 500;" placeholder="Ketik Nama Alat, Nomor Seri, Plat, atau Merk..." value="<?= $this->input->get('keyword') ?>">
            <div class="input-group-append ml-2">
                <button id="btnSearch" class="btn btn-primary px-4" style="border-radius: 12px; font-weight: 700;">
                    <i class="fas fa-search mr-2"></i> CARI DATA
                </button>
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
                        <th>Informasi Peralatan</th>
                        <th>Merk & Tipe</th>
                        <th>Nama Penyedia / Perusahaan</th>
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

<script>
$(document).ready(function() {
    let table = $('#ajaxTable').DataTable({
        ...window.dtOptions,
        "ajax": {
            "url": "<?= base_url($module.'/peralatan_json') ?>",
            "data": function(d) {
                d.keyword = $('#globalKeyword').val();
            }
        },
        "columns": [
            { 
                "data": null, 
                "className": "text-center text-muted font-weight-bold small",
                "render": function (data, type, row, meta) { return meta.row + 1; } 
            },
            { 
                "data": "nama_alat",
                "render": function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <div class="avatar-eq mr-3"><i class="fas fa-truck-monster"></i></div>
                            <div>
                                <div class="font-weight-800 text-dark">${data}</div>
                                <div class="badge badge-light border text-muted px-2 py-1 mt-1" style="font-size: 0.7rem; font-family: monospace;">${row.plat_serial || '-'}</div>
                            </div>
                        </div>`;
                }
            },
            {
                "data": "merk",
                "render": function(data, type, row) {
                    return `
                        <div class="font-weight-700 text-primary small mb-1">${data || '-'}</div>
                        <div class="small text-muted font-weight-bold">${row.tipe || '-'}</div>`;
                }
            },
            {
                "data": "nama_perusahaan",
                "render": function(data) {
                    return `<div class="font-weight-700 text-dark" style="font-size: 0.85rem;">${data || '-'}</div>`;
                }
            },
            {
                "data": "id",
                "className": "text-center",
                "render": function(data) {
                    return `
                        <a href="<?= base_url($module.'/detail_peralatan/') ?>${data}" class="btn btn-sm btn-light font-weight-bold" style="border-radius: 8px; border: 1px solid #e2e8f0; color: #4361ee;">
                            <i class="fas fa-history mr-1"></i> Riwayat
                        </a>`;
                }
            }
        ]
    });

    $('#btnSearch').on('click', function() {
        table.ajax.reload();
    });

    $('#globalKeyword').on('keypress', function(e) {
        if(e.which == 13) {
            table.ajax.reload();
        }
    });
});
</script>
