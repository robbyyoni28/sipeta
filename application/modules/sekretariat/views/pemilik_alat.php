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

    .avatar-owner {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #475569 0%, #1e293b 100%);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        box-shadow: 0 4px 6px -1px rgba(71, 85, 105, 0.2);
    }

    .btn-action-view {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f1f5f9;
        color: #475569;
        transition: all 0.2s;
        border: none;
    }

    .btn-action-view:hover {
        background: #475569;
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
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
        transition: all 0.2s;
    }

    .btn-premium-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
        color: white;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-truck mr-3 text-secondary"></i>Monitoring Pemilik Alat</h1>
    <?php if ($module !== 'pokja') : ?>
        <button class="btn-premium-primary" data-toggle="modal" data-target="#modalPemilikAlat">
            <i class="fas fa-plus mr-2"></i> Tambah Pemilik Alat
        </button>
    <?php endif; ?>
</div>

<!-- Premium Filter Section -->
<div class="card filter-card-premium border-0 shadow-sm mb-4" style="border-radius: 24px;">
    <div class="card-body p-4">
        <label class="small font-weight-bold text-muted text-uppercase mb-3 d-block"><i class="fas fa-filter mr-2 text-secondary"></i> Quick Filter Jenis Pemilik</label>
        <div class="row align-items-center">
            <div class="col-md-6">
                <select class="form-control" id="jenisFilter" style="height: 48px; border-radius: 12px; border: 1.5px solid #e2e8f0; font-weight: 600; color: #1e293b;">
                    <option value="">Semua Jenis Pemilik</option>
                    <option value="Perusahaan">Perusahaan</option>
                    <option value="Perorangan">Perorangan</option>
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
                        <th>Identitas Pemilik</th>
                        <th class="text-center">Jenis</th>
                        <th>Alamat & Lokasi</th>
                        <th class="text-center">Jumlah Alat</th>
                        <th>Kontak</th>
                        <?php if ($module !== 'pokja') : ?>
                            <th width="10%" class="text-center">Aksi</th>
                        <?php endif; ?>
                        <th width="8%" class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded by AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($module !== 'pokja') : ?>
    <!-- Modal Form -->
    <div class="modal fade premium-modal" id="modalPemilikAlat" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="<?= base_url($module.'/pemilik_alat_save') ?>" method="POST" id="formPemilikAlat">
                    <div class="modal-header align-items-center">
                        <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="font-size: 1.5rem; color: #94a3b8;">&times;</span>
                        </button>
                        <h5 class="modal-title mx-auto text-dark">Tambah Pemilik Alat Baru</h5>
                        <div class="modal-actions">
                            <button type="button" class="btn-mockup-cancel mr-2" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-mockup-save">Save</button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="owner_id">
                        
                        <div class="row">
                            <div class="col-md-8 mb-4">
                                <label class="form-label-custom">Nama Pemilik (Perusahaan / Perorangan)</label>
                                <input type="text" name="nama_pemilik" id="owner_nama" class="form-control premium-input" placeholder="Contoh: PT. Maju Bersama atau Bp. Ahmad" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label-custom">Jenis Pemilik</label>
                                <select name="jenis_pemilik" id="owner_jenis_modal_input" class="form-control premium-input" style="width: 100%;" required>
                                    <option value="Perusahaan">Perusahaan</option>
                                    <option value="Perorangan">Perorangan</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label-custom">Alamat Lengkap</label>
                            <textarea name="alamat" id="owner_alamat" class="form-control premium-input" placeholder="Alamat kantor atau rumah" rows="3" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label-custom">Nomor Telepon / WhatsApp</label>
                                <input type="text" name="telepon" id="owner_telepon" class="form-control premium-input" placeholder="0812xxxx">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label-custom">Alamat Email</label>
                                <input type="email" name="email" id="owner_email" class="form-control premium-input" placeholder="email@contoh.com">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
$(document).ready(function() {
    const baseDtOptions = (window.dtOptions) ? window.dtOptions : {};
    let table = $('#ajaxTable').DataTable($.extend(true, {}, baseDtOptions, {
        "ajax": {
            "url": "<?= base_url($module.'/pemilik_alat_json') ?>",
            "dataSrc": "data",
            "data": function(d) {
                d.jenis = $('#jenisFilter').val();
            }
        },
        "columns": [
            { 
                "data": null, 
                "className": "text-center text-muted font-weight-bold small",
                "render": function (data, type, row, meta) { return meta.row + 1; } 
            },
            { 
                "data": "nama_pemilik",
                "render": function(data) {
                    return `
                        <div class="d-flex align-items-center">
                            <div class="avatar-owner mr-3">${data.charAt(0).toUpperCase()}</div>
                            <div class="font-weight-800 text-dark">${data}</div>
                        </div>`;
                }
            },
            {
                "data": "jenis_pemilik",
                "className": "text-center",
                "render": function(data) {
                    let badge = data == 'Perusahaan' ? 'badge-primary' : 'badge-secondary';
                    return `<span class="badge badge-pill ${badge} px-3 py-1" style="font-size: 0.7rem; font-weight: 700;">${data}</span>`;
                }
            },
            {
                "data": "alamat",
                "render": function(data) {
                    return `<div class="small text-muted font-weight-bold" style="max-width: 250px; line-height: 1.4;">${data || '-'}</div>`;
                }
            },
            {
                "data": "jumlah_alat",
                "className": "text-center",
                "render": function(data) {
                    return `
                        <div class="h5 font-weight-800 text-info m-0">${data}</div>
                        <small class="text-muted font-weight-bold text-uppercase" style="font-size: 0.6rem;">Unit Alat</small>`;
                }
            },
            {
                "data": null,
                "render": function(data, type, row) {
                    return `
                        <div>
                            <div class="small font-weight-bold text-dark"><i class="fas fa-phone mr-1 text-success"></i> ${row.telepon || '-'}</div>
                            <div class="small text-muted"><i class="fas fa-envelope mr-1 text-primary"></i> ${row.email || '-'}</div>
                        </div>`;
                }
            },
            <?php if ($module !== 'pokja') : ?>
                {
                    "data": "id",
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
                            <div class="d-flex justify-content-center">
                                <button class="btn-action-view text-primary btn-edit mr-1" 
                                    data-id="${row.id}"
                                    data-nama="${row.nama_pemilik}"
                                    data-jenis="${row.jenis_pemilik}"
                                    data-alamat="${row.alamat}"
                                    data-telepon="${row.telepon}"
                                    data-email="${row.email}"
                                    title="Ubah Data">
                                    <i class="fas fa-edit text-info"></i>
                                </button>
                                <button class="btn-action-view text-danger btn-delete" data-id="${data}" data-name="${row.nama_pemilik}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>`;
                    }
                }
            <?php endif; ?>
            {
                "data": null,
                "className": "text-center",
                "orderable": false,
                "render": function(data, type, row) {
                    const ownerId = row.id ? row.id : '';
                    const ownerName = row.nama_pemilik ? row.nama_pemilik : '';
                    return `
                        <button type="button" class="btn-action-view btn-detail" data-id="${ownerId}" data-nama="${ownerName}" title="Lihat Detail">
                            <i class="fas fa-list"></i>
                        </button>`;
                }
            }
        ]
    }));

    // Select Change Handler
    $('#jenisFilter').on('change', function() {
        table.ajax.reload();
    });

    <?php if ($module !== 'pokja') : ?>
        // Select2 for modal input
        if ($('#owner_jenis_modal_input').hasClass("select2-hidden-accessible")) {
            $('#owner_jenis_modal_input').select2('destroy');
        }

        $('#owner_jenis_modal_input').select2({
            dropdownParent: $('#modalPemilikAlat'),
            width: '100%',
            minimumResultsForSearch: Infinity
        });

        // Form Submit
        $('#formPemilikAlat').on('submit', function(e) {
            e.preventDefault();
            ajaxFormSubmit(this, function(response) {
                if(response.status == 'success') {
                    $('#modalPemilikAlat').modal('hide');
                    table.ajax.reload();
                }
            });
        });

        // Edit Handler
        $(document).on('click', '.btn-edit', function() {
            let d = $(this).data();
            $('#owner_id').val(d.id);
            $('#owner_nama').val(d.nama);
            $('#owner_jenis_modal_input').val(d.jenis).trigger('change');
            $('#owner_alamat').val(d.alamat);
            $('#owner_telepon').val(d.telepon);
            $('#owner_email').val(d.email);
            
            $('#formPemilikAlat').attr('action', '<?= base_url($module."/pemilik_alat_update/") ?>' + d.id);
            $('.modal-title').text('Perbarui Data Pemilik Alat');
            $('#modalPemilikAlat').modal('show');
        });

        $('#modalPemilikAlat').on('hidden.bs.modal', function() {
            $('#formPemilikAlat')[0].reset();
            $('#owner_id').val('');
            $('#owner_jenis_modal_input').val('Perusahaan').trigger('change');
            $('#formPemilikAlat').attr('action', '<?= base_url($module."/pemilik_alat_save") ?>');
            $('.modal-title').text('Tambah Pemilik Alat Baru');
        });

        // Delete Handler
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            
            Swal.fire({
                title: 'Hapus Pemilik Alat?',
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
                        url: '<?= base_url($module."/pemilik_alat_delete/") ?>' + id,
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
    <?php endif; ?>

    // Detail modal handler (read-only)
    $(document).on('click', '.btn-detail', function() {
        const ownerId = $(this).data('id');
        const ownerName = $(this).data('nama');

        $('#ownerDetailTitle').text(ownerName || '-');
        $('#ownerDetailList').html('<tr><td colspan="6" class="text-center text-muted">Memuat...</td></tr>');
        $('#modalOwnerDetail').modal('show');

        $.ajax({
            url: '<?= base_url($module.'/pemilik_alat_detail_json') ?>',
            method: 'GET',
            dataType: 'json',
            data: {
                id: ownerId,
                nama: ownerName
            }
        }).done(function(res) {
            const items = (res && res.data) ? res.data : [];
            if (!items.length) {
                $('#ownerDetailList').html('<tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>');
                return;
            }

            let rows = '';
            items.forEach(function(it, idx) {
                rows += `
                    <tr>
                        <td class="text-center">${idx + 1}</td>
                        <td>${it.jenis_alat || it.nama_alat || '-'}</td>
                        <td>${it.plat_serial || '-'}</td>
                        <td>${it.merk || '-'}</td>
                        <td>${it.tipe || '-'}</td>
                        <td>${it.kapasitas || '-'}</td>
                    </tr>`;
            });
            $('#ownerDetailList').html(rows);
        }).fail(function() {
            $('#ownerDetailList').html('<tr><td colspan="6" class="text-center text-muted">Gagal memuat detail</td></tr>');
        });
    });
});
</script>

<div class="modal fade premium-modal" id="modalOwnerDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="font-size: 1.5rem; color: #94a3b8;">&times;</span>
                </button>
                <h5 class="modal-title mx-auto text-dark">Detail Peralatan - <span id="ownerDetailTitle">-</span></h5>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Jenis Alat</th>
                                <th>Plat/Serial</th>
                                <th>Merk</th>
                                <th>Tipe</th>
                                <th>Kapasitas</th>
                            </tr>
                        </thead>
                        <tbody id="ownerDetailList"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
