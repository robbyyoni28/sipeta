<?php $module = $this->uri->segment(1); ?>
<style>
    :root {
        --reg-primary: #6366f1;
        --reg-success: #10b981;
        --reg-warning: #f59e0b;
        --reg-danger: #ef4444;
        --reg-info: #06b6d4;
    }

    .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    /* Premium Stats Cards */
    .stat-card {
        border: none;
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .stat-card .card-body {
        padding: 1.5rem;
        z-index: 1;
        position: relative;
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .icon-shape {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    /* Custom Gradients */
    .bg-grad-purple { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); }
    .bg-grad-emerald { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-grad-amber { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-grad-rose { background: linear-gradient(135deg, #ef4444 0%, #be123c 100%); }

    /* Modal Styling - Premium Form */
    .premium-modal .modal-content {
        border-radius: 24px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .premium-modal .modal-header {
        border-bottom: 1px solid #f1f5f9;
        padding: 1.5rem 2rem;
    }

    .premium-modal .modal-body {
        padding: 2rem;
    }

    .form-label-custom {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 0.5rem;
        display: block;
    }

    .premium-input {
        height: 54px !important;
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 0 1rem !important;
        font-weight: 500 !important;
        color: #1e293b !important;
        transition: all 0.2s ease !important;
        background-color: #f8fafc !important;
    }

    .premium-input:focus {
        border-color: var(--reg-info) !important;
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.1) !important;
    }

    /* Table Enhancements */

    /* Table Enhancements */
    .table-premium thead th {
        background: #f8fafc;
        border: none;
        color: #64748b;
        font-size: 0.75rem;
        padding: 1.25rem 1rem;
    }

    .table-premium tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
    }

    .btn-action {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
    }

    /* Year Spinner Container */
    .year-spinner {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 4px;
    }

    .year-spinner input {
        border: none !important;
        background: transparent !important;
        text-align: center;
        font-weight: 700;
        width: 100%;
        height: 44px;
        font-size: 1.1rem;
    }

    .year-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: white;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        transition: all 0.2s;
    }

    .year-btn:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .btn-premium-primary {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        transition: all 0.2s;
    }

    .btn-premium-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        color: white;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-gavel mr-3 text-primary"></i>Monitoring Regulasi</h1>
    <button class="btn-premium-primary" data-toggle="modal" data-target="#modalRegulasi">
        <i class="fas fa-plus mr-2"></i> Tambah Regulasi
    </button>
</div>

<!-- Premium Stats Widgets -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-grad-purple text-white">
            <div class="card-body">
                <div class="icon-shape">
                    <i class="fas fa-file-alt fa-lg text-white"></i>
                </div>
                <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-80">Total Regulasi</div>
                <div class="h3 mb-0 font-weight-900" id="stat-total">0</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-grad-emerald text-white">
            <div class="card-body">
                <div class="icon-shape">
                    <i class="fas fa-check-circle fa-lg text-white"></i>
                </div>
                <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-80">Berlaku</div>
                <div class="h3 mb-0 font-weight-900" id="stat-berlaku">0</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-grad-amber text-white">
            <div class="card-body">
                <div class="icon-shape">
                    <i class="fas fa-sync fa-lg text-white"></i>
                </div>
                <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-80">Direvisi</div>
                <div class="h3 mb-0 font-weight-900" id="stat-direvisi">0</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card bg-grad-rose text-white">
            <div class="card-body">
                <div class="icon-shape">
                    <i class="fas fa-times-circle fa-lg text-white"></i>
                </div>
                <div class="text-xs font-weight-bold text-uppercase mb-1 opacity-80">Dicabut</div>
                <div class="h3 mb-0 font-weight-900" id="stat-dicabut">0</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Filter Section -->
<div class="card filter-card-premium border-0 shadow-sm mb-4" style="border-radius: 20px;">
    <div class="card-body p-4">
        <label class="small font-weight-bold text-muted text-uppercase mb-3 d-block"><i class="fas fa-filter mr-2 text-primary"></i> Quick Filter Tahun Terbit</label>
        <div class="row align-items-center">
            <div class="col-md-6">
                <select class="form-control" id="yearFilter" style="height: 48px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-weight: 600; color: #1e293b;">
                    <option value="">Semua Tahun</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 20px; overflow: hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 table-premium" id="ajaxTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="15%">Instansi</th>
                        <th width="20%">Identitas Regulasi</th>
                        <th>Judul / Substansi</th>
                        <th width="10%" class="text-center">Status</th>
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

<!-- Premium Modal Form -->
<div class="modal fade premium-modal" id="modalRegulasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= base_url($module.'/regulasi_save') ?>" method="POST" enctype="multipart/form-data" id="formRegulasi">
                <div class="modal-header align-items-center">
                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 1.5rem; color: #94a3b8;">&times;</span>
                    </button>
                    <h5 class="modal-title mx-auto text-dark">Tambah Regulasi Baru</h5>
                    <div class="modal-actions">
                        <button type="button" class="btn-mockup-cancel mr-2" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-mockup-save">Save</button>
                    </div>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="reg_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label-custom">Instansi Pembuat</label>
                            <select name="instansi" id="reg_instansi" class="form-control premium-input select2-tags">
                                <option value="">Add or search</option>
                                <option value="PUPR">PUPR</option>
                                <option value="LKPP">LKPP</option>
                                <option value="PRESIDEN">PRESIDEN</option>
                                <option value="KEMENDAGRI">KEMENDAGRI</option>
                                <option value="POKJA">POKJA</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label-custom">Jenis Regulasi</label>
                            <input type="text" name="jenis_regulasi" id="reg_jenis" class="form-control premium-input" placeholder="Contoh: Peraturan Menteri">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label-custom">Judul Regulasi</label>
                        <textarea name="judul" id="reg_judul" class="form-control premium-input pt-3" style="height: 100px !important; resize: none;" placeholder="Masukkan judul lengkap regulasi..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label-custom">Tahun Terbit <span class="text-danger">*</span></label>
                            <div class="year-spinner">
                                <button type="button" class="year-btn" onclick="decrementYear()"><i class="fas fa-minus"></i></button>
                                <input type="number" name="tahun" id="reg_tahun" value="<?= date('Y') ?>">
                                <button type="button" class="year-btn" onclick="incrementYear()"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label-custom">Upload File <span class="text-danger">*</span></label>
                            <div class="custom-file" style="height: 54px;">
                                <input type="file" name="file_regulasi" class="custom-file-input" id="reg_file">
                                <label class="custom-file-label d-flex align-items-center" for="reg_file" style="height: 54px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <span class="text-muted">Pilih Berkas...</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Supplemental Fields -->
                    <input type="hidden" name="nomor_regulasi" id="reg_nomor" value="-">
                    <input type="hidden" name="tentang" id="reg_tentang" value="-">
                    <input type="hidden" name="status" id="reg_status" value="Berlaku">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function incrementYear() {
    let input = document.getElementById('reg_tahun');
    input.value = parseInt(input.value) + 1;
}
function decrementYear() {
    let input = document.getElementById('reg_tahun');
    input.value = parseInt(input.value) - 1;
}

$(document).ready(function() {
    // Load Stats and Filters via AJAX
    $.getJSON("<?= base_url($module.'/get_regulasi_statistics') ?>", function(data) {
        // Stats
        $('#stat-total').text(data.statistics.total);
        $('#stat-berlaku').text(data.statistics.berlaku);
        $('#stat-direvisi').text(data.statistics.direvisi);
        $('#stat-dicabut').text(data.statistics.dicabut);

        // Years
        let yearSelect = $('#yearFilter');
        data.available_years.forEach(function(year) {
            yearSelect.append(new Option(year, year));
        });
    });

    let table = $('#ajaxTable').DataTable({
        ...window.dtOptions,
        "ajax": {
            "url": "<?= base_url($module.'/regulasi_json') ?>",
            "data": function(d) {
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
                "data": "instansi",
                "render": function(data) {
                    return `<div class="badge badge-light p-2 border text-dark font-weight-bold w-100" style="border-radius: 8px; letter-spacing: 0.5px;">${data || '-'}</div>`;
                }
            },
            {
                "data": "id",
                "render": function(data, type, row) {
                    return `
                        <div class="small font-weight-800 text-indigo mb-1">${row.jenis_regulasi}</div>
                        <div class="font-weight-600 text-dark">${row.nomor_regulasi || '-'}</div>
                        <div class="small text-muted font-weight-bold mt-1">
                            <i class="fas fa-clock mr-1 opacity-50"></i> Tahun ${row.tahun}
                        </div>`;
                }
            },
            {
                "data": "judul",
                "render": function(data, type, row) {
                    return `
                        <div class="font-weight-800 text-dark mb-1" style="font-size: 0.95rem; line-height: 1.4;">${data}</div>
                        <div class="small text-muted" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${row.tentang || '-'}</div>`;
                }
            },
            {
                "data": "status",
                "className": "text-center",
                "render": function(data) {
                    let badge = 'badge-success';
                    if(data == 'Dicabut') badge = 'badge-danger';
                    if(data == 'Direvisi') badge = 'badge-warning';
                    return `<span class="badge ${badge} px-3 py-2 rounded-pill shadow-sm small font-weight-bold">${data}</span>`;
                }
            },
            {
                "data": "id",
                "className": "text-center",
                "render": function(data, type, row) {
                    let downloadBtn = row.file_regulasi ? `
                        <a href="<?= base_url('uploads/regulasi/') ?>${row.file_regulasi}" class="btn-action bg-light text-danger mr-1" target="_blank" title="Download/View">
                            <i class="fas fa-file-pdf"></i>
                        </a>` : '';
                    
                    return `
                        <div class="d-flex justify-content-center">
                            ${downloadBtn}
                            <button class="btn-action bg-light text-primary btn-edit mr-1" 
                                data-id="${row.id}"
                                data-instansi="${row.instansi}"
                                data-jenis="${row.jenis_regulasi}"
                                data-nomor="${row.nomor_regulasi}"
                                data-tahun="${row.tahun}"
                                data-judul="${row.judul}"
                                data-tentang="${row.tentang}"
                                data-status="${row.status}"
                                title="Ubah Data">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action bg-light text-danger btn-delete" data-id="${row.id}" data-name="${row.nomor_regulasi}" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>`;
                }
            }
        ]
    });

    // Select Change Handler
    $('#yearFilter').on('change', function() {
        table.ajax.reload();
    });

    $('.select2-tags').select2({
        tags: true,
        placeholder: "Pilih atau Ketik Baru",
        dropdownParent: $('#modalRegulasi'),
        width: '100%'
    });

    // Form Submit
    $('#formRegulasi').on('submit', function(e) {
        e.preventDefault();
        ajaxFormSubmit(this, function(response) {
            if(response.status == 'success') {
                $('#modalRegulasi').modal('hide');
                table.ajax.reload();
            }
        });
    });

    $(document).on('click', '.btn-edit', function() {
        let d = $(this).data();
        $('#reg_id').val(d.id);
        $('#reg_instansi').val(d.instansi).trigger('change');
        $('#reg_jenis').val(d.jenis);
        $('#reg_nomor').val(d.nomor);
        $('#reg_tahun').val(d.tahun);
        $('#reg_judul').val(d.judul);
        $('#reg_tentang').val(d.tentang);
        $('#reg_status').val(d.status);
        
        $('#formRegulasi').attr('action', '<?= base_url($module."/regulasi_update/") ?>' + d.id);
        $('.modal-title').text('Perbarui Data Regulasi');
        $('#modalRegulasi').modal('show');
    });

    $('#modalRegulasi').on('hidden.bs.modal', function() {
        $('#formRegulasi')[0].reset();
        $('#reg_id').val('');
        $('#reg_instansi').val('').trigger('change');
        $('#formRegulasi').attr('action', '<?= base_url($module."/regulasi_save") ?>');
        $('.modal-title').text('Tambah Regulasi Baru');
    });

    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        if(fileName.length > 20) fileName = fileName.substring(0, 17) + "...";
        $(this).next('.custom-file-label').find('span').text(fileName).removeClass('text-muted').addClass('text-dark font-weight-bold');
    });

    // Delete Handler
    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        
        Swal.fire({
            title: 'Hapus Regulasi?',
            text: "Apakah Anda yakin ingin menghapus " + name + "? Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn-mockup-danger ml-2',
                cancelButton: 'btn-mockup-cancel'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url($module."/regulasi_delete/") ?>' + id,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: res.message
                            });
                        }
                    }
                });
            }
        });
    });
});
</script>
