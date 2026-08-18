<?php $module = $this->uri->segment(1); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-boxes mr-2 text-primary"></i>Recording Paket Pemenang Tender Non Konstruksi</h1>
    <a href="<?= base_url($module.'/data_tender?jenis=non_konstruksi') ?>" class="btn btn-sm btn-secondary shadow-sm" style="border-radius: 10px;">
        <i class="fas fa-list fa-sm mr-1"></i> Data Monitoring Non Konstruksi
    </a>
</div>

<?php if($this->session->flashdata('success')): ?>
    <div class="alert alert-success border-left-success shadow alert-dismissible fade show" style="border-radius: 12px;">
        <i class="fas fa-check-circle mr-2"></i><?= $this->session->flashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close text-white">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
    <div class="alert alert-danger border-left-danger shadow alert-dismissible fade show" style="border-radius: 12px;">
        <i class="fas fa-exclamation-triangle mr-2"></i><?= $this->session->flashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close text-white">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<form action="<?= base_url($module.'/simpan_pemenang') ?>" method="POST" id="form-pemenang-non-konstruksi" enctype="multipart/form-data">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
    <input type="hidden" name="jenis_tender" value="non_konstruksi">
    
    <!-- 1. Informasi Paket -->
    <div class="card shadow mb-4 border-0 overflow-hidden" style="border-radius: 16px;">
        <div class="card-header py-3 bg-gradient-primary text-white border-0">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-file-contract mr-2"></i>1. Informasi Paket & Tender Non Konstruksi</h5>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Nama Satker</label>
                        <input type="text" name="satuan_kerja" class="form-control form-control-lg bg-light border-0 shadow-none" required placeholder="Contoh: Dinas Komunikasi dan Informatika..." style="border-radius: 12px;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Judul Paket Pekerjaan</label>
                        <textarea name="judul_paket" class="form-control bg-light border-0 shadow-none" rows="1" required placeholder="Masukkan judul paket pekerjaan non-konstruksi..." style="border-radius: 12px;"></textarea>
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Nama POKMIL / POKJA</label>
                        <input type="text" name="nama_pokmil" class="form-control bg-light border-0" placeholder="Contoh: Pokja Pemilihan 1" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Kode Tender</label>
                        <input type="text" name="kode_tender" id="kode_tender" class="form-control kode-check bg-light border-0" required placeholder="1234567" style="border-radius: 10px;">
                        <div class="feedback-kode-inline"></div>
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Tanggal BAHP</label>
                        <input type="text" name="tanggal_bahp" class="form-control bg-light border-0 datepicker" required style="border-radius: 10px;" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Tahun Anggaran</label>
                        <input type="number" name="tahun_anggaran" id="tahun_anggaran" class="form-control bg-light border-0 font-weight-bold text-primary" value="<?= date('Y') ?>" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-primary">Pemenang Tender / Penyedia</label>
                        <input type="text" name="nama_penyedia" class="form-control border-primary shadow-sm" required placeholder="Ketik nama penyedia/perusahaan..." style="border-radius: 12px; border-width: 2px;">
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">HPS / Nilai Paket</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0 text-primary font-weight-bold" style="border-radius: 12px 0 0 12px;">Rp</span>
                            </div>
                            <input type="text" name="hps" class="form-control bg-light border-0 rupiah font-weight-bold" required placeholder="0" style="border-radius: 0 12px 12px 0;">
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mt-3">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Kualifikasi Usaha</label>
                        <select name="kualifikasi" id="kualifikasi" class="form-control bg-light border-0" style="border-radius: 10px;">
                            <option value="Kecil" selected>Usaha Kecil</option>
                            <option value="Non Kecil">Menengah / Besar</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Jenis Pengadaan</label>
                        <select name="jenis_pengadaan" class="form-control bg-light border-0" style="border-radius: 10px;">
                            <option value="Jasa Lainnya" selected>Jasa Lainnya</option>
                            <option value="Pengadaan Barang">Pengadaan Barang</option>
                            <option value="Jasa Konsultansi Non Konstruksi">Jasa Konsultansi Non Konstruksi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Kategori Tender</label>
                        <input type="text" class="form-control bg-light border-0 font-weight-bold text-success" value="NON KONSTRUKSI" readonly style="border-radius: 10px; max-width: 250px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Data Personel (Text Input Manual untuk Jabatan) -->
    <div class="card shadow mb-4 border-0 overflow-hidden" style="border-radius: 16px;">
        <div class="card-header py-3 bg-gradient-info text-white border-0 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-users mr-2"></i>2. Data Personel (Jabatan Input Manual)</h6>
            <button type="button" id="btn-add-personel" class="btn btn-light btn-sm font-weight-bold rounded-pill shadow-sm">
                <i class="fas fa-user-plus mr-1"></i> Tambah Personel
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 small" id="table-personel">
                    <thead class="bg-light text-uppercase font-weight-bold text-muted" style="font-size: 0.75rem;">
                        <tr>
                            <th class="px-3 py-3" width="18%">Jabatan (Manual)</th>
                            <th class="py-3" width="22%">Nama Lengkap</th>
                            <th class="py-3" width="16%">NIK</th>
                            <th class="py-3" width="16%">Jenis Sertifikat / SKK</th>
                            <th class="py-3" width="14%">No. Sertifikat / SKK</th>
                            <th class="py-3" width="10%">Masa Berlaku</th>
                            <th class="py-3 px-3 text-center" width="4%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-personel">
                        <tr class="row-personel">
                            <td class="px-3 py-2 align-middle">
                                <input type="text" name="personel[0][jabatan]" class="form-control form-control-sm bg-light border-0 font-weight-bold text-dark" placeholder="Contoh: Tenaga Administrasi" required style="border-radius: 8px;">
                            </td>
                            <td class="py-2 align-middle">
                                <input type="text" name="personel[0][nama]" class="form-control form-control-sm bg-white border-0" placeholder="Nama Lengkap" required style="border-radius: 8px;">
                            </td>
                            <td class="py-2 align-middle">
                                <input type="text" name="personel[0][nik]" class="form-control form-control-sm nik-check bg-white border-0 font-weight-bold" placeholder="16 Digit NIK" required style="border-radius: 8px;">
                                <div class="feedback-nik-inline"></div>
                            </td>
                            <td class="py-2 align-middle">
                                <input type="text" name="personel[0][jenis_sertifikat]" class="form-control form-control-sm bg-white border-0" placeholder="Jenis Sertifikat" style="border-radius: 8px;">
                            </td>
                            <td class="py-2 align-middle">
                                <input type="text" name="personel[0][nomor_sertifikat]" class="form-control form-control-sm skk-check bg-white border-0" placeholder="No. Sertifikat" style="border-radius: 8px;">
                                <div class="feedback-skk-inline"></div>
                            </td>
                            <td class="py-2 align-middle">
                                <input type="text" name="personel[0][masa_berlaku]" class="form-control form-control-sm bg-white border-0 datepicker" placeholder="dd/mm/yyyy" style="border-radius: 8px;" autocomplete="off">
                            </td>
                            <td class="py-2 px-3 align-middle text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-personel" style="border-radius: 10px;" title="Hapus baris">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. Data Peralatan (Text Input Manual untuk Jenis Alat) -->
    <div class="card shadow mb-4 border-0 overflow-hidden" style="border-radius: 16px;">
        <div class="card-header py-3 bg-gradient-success text-white border-0 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-tools mr-2"></i>3. Data Peralatan Non Konstruksi (Jenis Alat Input Manual)</h6>
            <button type="button" id="btn-add-peralatan" class="btn btn-light btn-sm font-weight-bold rounded-pill shadow-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Alat
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 small" id="table-peralatan">
                    <thead class="bg-light text-uppercase font-weight-bold text-muted" style="font-size: 0.7rem;">
                        <tr>
                            <th class="px-3 py-3" width="14%">Jenis Alat (Manual)</th>
                            <th class="py-3" width="11%">No. Plat / Seri</th>
                            <th class="py-3" width="9%">Merk</th>
                            <th class="py-3" width="9%">Tipe</th>
                            <th class="py-3" width="12%">Spesifikasi / Kapasitas</th>
                            <th class="py-3" width="12%">Status Kepemilikan</th>
                            <th class="py-3" width="14%">Nama Pemilik</th>
                            <th class="py-3" width="13%">Bukti Kepemilikan</th>
                            <th class="py-3 px-3 text-center" width="6%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-peralatan">
                        <tr class="row-peralatan">
                            <td class="px-3 py-2 align-middle">
                                <input type="text" name="peralatan[0][jenis_alat]" class="form-control form-control-sm bg-light border-0 font-weight-bold text-dark" placeholder="Contoh: Laptop / Drone" style="border-radius: 8px;" autocomplete="off">
                            </td>
                            <td class="py-2 align-middle">
                                <input type="text" name="peralatan[0][plat_serial]" class="form-control form-control-sm plat-check bg-white border-0 font-weight-bold" placeholder="No. Plat / Serial" style="border-radius: 8px;">
                                <div class="feedback-plat-inline"></div>
                            </td>
                            <td class="py-2 align-middle">
                                <input type="text" name="peralatan[0][merk]" class="form-control form-control-sm bg-white border-0" placeholder="Merk" style="border-radius: 8px;">
                            </td>
                            <td class="py-2 align-middle">
                                <input type="text" name="peralatan[0][tipe]" class="form-control form-control-sm bg-white border-0" placeholder="Tipe" style="border-radius: 8px;">
                            </td>
                            <td class="py-2 align-middle">
                                <input type="text" name="peralatan[0][kapasitas]" class="form-control form-control-sm bg-white border-0" placeholder="Spesifikasi / Kapasitas" style="border-radius: 8px;">
                            </td>
                            <td class="py-2 align-middle">
                                <select name="peralatan[0][status_kepemilikan]" class="form-control form-control-sm bg-white border-0" style="border-radius: 8px;">
                                    <option value="Milik Sendiri">Milik Sendiri</option>
                                    <option value="Sewa">Sewa</option>
                                    <option value="Sewa Beli">Sewa Beli</option>
                                </select>
                            </td>
                            <td class="py-2 align-middle">
                                <input type="text" name="peralatan[0][nama_pemilik_alat]" class="form-control form-control-sm bg-white border-0" placeholder="Nama Pemilik" style="border-radius: 8px;">
                            </td>
                            <td class="py-2 align-middle">
                                <input type="text" name="peralatan[0][bukti_kepemilikan]" class="form-control form-control-sm bg-white border-0" placeholder="Bukti Kepemilikan" style="border-radius: 8px;">
                            </td>
                            <td class="py-2 px-3 align-middle text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-peralatan" style="border-radius: 10px;" title="Hapus baris">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="text-center mb-5 mt-5">
        <button type="submit" class="btn btn-primary btn-lg shadow-lg px-5 border-0 rounded-pill" style="background: linear-gradient(to right, #4361ee, #3f37c9); padding: 15px 50px; font-weight: 800; letter-spacing: 1px;">
            <i class="fas fa-save mr-2"></i> SIMPAN PAKET NON KONSTRUKSI
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Format Rupiah
    $('.rupiah').on('keyup', function() {
        let val = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(new Intl.NumberFormat('id-ID').format(val));
    });

    // Reindex Personel Table Name Attributes
    function reindexPersonel() {
        $('#tbody-personel tr.row-personel').each(function(idx) {
            $(this).find('input, select').each(function() {
                var name = $(this).attr('name');
                if (!name) return;
                $(this).attr('name', name.replace(/personel\[\d+\]/, 'personel[' + idx + ']'));
            });
        });
    }

    // Template Generator for Personel Rows
    function buatBarisPersonelBaru(idx) {
        return '<tr class="row-personel">' +
            '<td class="px-3 py-2 align-middle">' +
                '<input type="text" name="personel[' + idx + '][jabatan]" class="form-control form-control-sm bg-light border-0 font-weight-bold text-dark" placeholder="Contoh: Operator Komputer" required style="border-radius: 8px;">' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<input type="text" name="personel[' + idx + '][nama]" class="form-control form-control-sm bg-white border-0" placeholder="Nama Lengkap" required style="border-radius: 8px;">' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<input type="text" name="personel[' + idx + '][nik]" class="form-control form-control-sm nik-check bg-white border-0 font-weight-bold" placeholder="16 Digit NIK" required style="border-radius: 8px;">' +
                '<div class="feedback-nik-inline"></div>' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<input type="text" name="personel[' + idx + '][jenis_sertifikat]" class="form-control form-control-sm bg-white border-0" placeholder="Jenis Sertifikat" style="border-radius: 8px;">' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<input type="text" name="personel[' + idx + '][nomor_sertifikat]" class="form-control form-control-sm skk-check bg-white border-0" placeholder="No. Sertifikat" style="border-radius: 8px;">' +
                '<div class="feedback-skk-inline"></div>' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<input type="text" name="personel[' + idx + '][masa_berlaku]" class="form-control form-control-sm bg-white border-0 datepicker" placeholder="dd/mm/yyyy" style="border-radius: 8px;" autocomplete="off">' +
            '</td>' +
            '<td class="py-2 px-3 align-middle text-center">' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-personel" style="border-radius: 10px;" title="Hapus baris">' +
                    '<i class="fas fa-trash"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';
    }

    // Tambah Baris Personel
    $('#btn-add-personel').on('click', function() {
        var $tbody = $('#tbody-personel');
        var rowCount = $tbody.find('tr.row-personel').length;
        var $newRow = $(buatBarisPersonelBaru(rowCount));
        $tbody.append($newRow);
        
        // Init Datepicker for new row if available
        if ($.fn.datepicker) {
            $newRow.find('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true
            });
        }
        $newRow.find('input:first').focus();
    });

    // Hapus Baris Personel
    $(document).on('click', '.btn-remove-personel', function() {
        var $tbody = $('#tbody-personel');
        var $rows = $tbody.find('tr.row-personel');
        if ($rows.length <= 1) {
            $rows.first().find('input').val('');
            return;
        }
        $(this).closest('tr').remove();
        reindexPersonel();
    });

    // Reindex Peralatan Table Name Attributes
    function reindexPeralatan() {
        $('#tbody-peralatan tr.row-peralatan').each(function(idx) {
            $(this).find('input, select').each(function() {
                var name = $(this).attr('name');
                if (!name) return;
                $(this).attr('name', name.replace(/peralatan\[\d+\]/, 'peralatan[' + idx + ']'));
            });
        });
    }

    // Template Generator for Peralatan Rows
    function buatBarisPeralatanBaru(idx) {
        return '<tr class="row-peralatan">' +
            '<td class="px-3 py-2 align-middle">' +
                '<input type="text" name="peralatan[' + idx + '][jenis_alat]" class="form-control form-control-sm bg-light border-0 font-weight-bold text-dark" placeholder="Contoh: Laptop / Drone" style="border-radius: 8px;" autocomplete="off">' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<input type="text" name="peralatan[' + idx + '][plat_serial]" class="form-control form-control-sm plat-check bg-white border-0 font-weight-bold" placeholder="No. Plat / Serial" style="border-radius: 8px;">' +
                '<div class="feedback-plat-inline"></div>' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<input type="text" name="peralatan[' + idx + '][merk]" class="form-control form-control-sm bg-white border-0" placeholder="Merk" style="border-radius: 8px;">' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<input type="text" name="peralatan[' + idx + '][tipe]" class="form-control form-control-sm bg-white border-0" placeholder="Tipe" style="border-radius: 8px;">' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<input type="text" name="peralatan[' + idx + '][kapasitas]" class="form-control form-control-sm bg-white border-0" placeholder="Spesifikasi / Kapasitas" style="border-radius: 8px;">' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<select name="peralatan[' + idx + '][status_kepemilikan]" class="form-control form-control-sm bg-white border-0" style="border-radius: 8px;">' +
                    '<option value="Milik Sendiri">Milik Sendiri</option>' +
                    '<option value="Sewa">Sewa</option>' +
                    '<option value="Sewa Beli">Sewa Beli</option>' +
                '</select>' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<input type="text" name="peralatan[' + idx + '][nama_pemilik_alat]" class="form-control form-control-sm bg-white border-0" placeholder="Nama Pemilik" style="border-radius: 8px;">' +
            '</td>' +
            '<td class="py-2 align-middle">' +
                '<input type="text" name="peralatan[' + idx + '][bukti_kepemilikan]" class="form-control form-control-sm bg-white border-0" placeholder="Bukti Kepemilikan" style="border-radius: 8px;">' +
            '</td>' +
            '<td class="py-2 px-3 align-middle text-center">' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-peralatan" style="border-radius: 10px;" title="Hapus baris">' +
                    '<i class="fas fa-trash"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';
    }

    // Tambah Baris Peralatan
    $('#btn-add-peralatan').on('click', function() {
        var $tbody = $('#tbody-peralatan');
        var rowCount = $tbody.find('tr.row-peralatan').length;
        var $newRow = $(buatBarisPeralatanBaru(rowCount));
        $tbody.append($newRow);
        $newRow.find('input:first').focus();
    });

    // Hapus Baris Peralatan
    $(document).on('click', '.btn-remove-peralatan', function() {
        var $tbody = $('#tbody-peralatan');
        var $rows = $tbody.find('tr.row-peralatan');
        if ($rows.length <= 1) {
            $rows.first().find('input').val('');
            return;
        }
        $(this).closest('tr').remove();
        reindexPeralatan();
    });

    // Form Submit Handler dengan Proteksi Duplicate Submit
    let isSubmitting = false;
    $('#form-pemenang-non-konstruksi').on('submit', function(e) {
        e.preventDefault();
        if (isSubmitting) return;
        isSubmitting = true;
        performAjaxSubmit(this);
    });

    function performAjaxSubmit(form, forceSave = false) {
        let formData = new FormData(form);
        if (forceSave) {
            formData.append('force_save', '1');
        }
        let url = $(form).attr('action');

        const $submitBtn = $(form).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'JSON',
            success: function(res) {
                if (res && res.csrfHash) {
                    $(form).find('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val(res.csrfHash);
                }

                if (res.status === 'duplicate') {
                    $submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> SIMPAN PAKET NON KONSTRUKSI');
                    showBulkDuplicateModal(res.duplicates, function(confirmed) {
                        if (confirmed) {
                            isSubmitting = true;
                            performAjaxSubmit(form, true);
                        } else {
                            isSubmitting = false;
                        }
                    });
                    return;
                }

                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message || 'Data Tender Non-Konstruksi berhasil disimpan.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    setTimeout(() => {
                        window.location.href = '<?= base_url($module."/data_tender?jenis=non_konstruksi") ?>';
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Terjadi kesalahan sistem.'
                    });
                    isSubmitting = false;
                    $submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> SIMPAN PAKET NON KONSTRUKSI');
                }
            },
            error: function(xhr) {
                let message = 'Gagal terhubung ke server.';
                if (xhr && xhr.responseJSON) {
                    message = xhr.responseJSON.message || xhr.responseJSON.error || message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
                isSubmitting = false;
                $submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> SIMPAN PAKET NON KONSTRUKSI');
            }
        });
    }

    // Modal Duplicate Summary
    function showBulkDuplicateModal(duplicates, callback) {
        let rows = '';
        duplicates.forEach(function(d) {
            let detail = d.detail || {};
            let namaOrAlat = detail.nama || detail.jenis_alat || detail.nama_alat || d.identifier;
            let tenderInfo = detail.kode_tender ? ('Tender: ' + detail.kode_tender) : '';
            let matchLabel = d.matched_by ? ('<span class="badge badge-danger ml-1">' + d.matched_by + ' Sama</span>') : '';
            
            rows += '<tr>' +
                '<td><span class="badge badge-primary">' + (d.type || 'Duplikat') + '</span>' + matchLabel + '</td>' +
                '<td><code>' + (d.identifier || '-') + '</code></td>' +
                '<td>' +
                '<div class="small font-weight-bold text-dark">' + namaOrAlat + '</div>' +
                (tenderInfo ? '<div class="small text-danger">' + tenderInfo + '</div>' : '') +
                (detail.judul_paket ? '<div class="small text-muted">' + (detail.judul_paket || '') + '</div>' : '') +
                '</td>' +
                '<td><span class="small font-weight-bold">' + (detail.nama_perusahaan || '-') + '</span></td>' +
                '</tr>';
        });

        let modalHtml = `
            <div class="modal fade" id="bulkDuplicateModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-warning text-white border-0">
                            <h5 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Peringatan Duplikasi Data</h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body p-4">
                            <p class="text-dark">Sistem menemukan bahwa beberapa data yang Anda masukkan telah digunakan di tender lain pada tahun yang sama:</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm table-striped small">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Tipe</th>
                                            <th>Identifier (NIK/SKK/Plat)</th>
                                            <th>Detail Penggunaan Sebelumnya</th>
                                            <th>Penyedia</th>
                                        </tr>
                                    </thead>
                                    <tbody>${rows}</tbody>
                                </table>
                            </div>
                            <div class="alert alert-light border mt-3 mb-0">
                                <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-question-circle mr-2"></i>Tetap Simpan?</h6>
                                <p class="mb-0 small text-muted">Data di atas tercatat sudah digunakan di tender lain. Apakah Anda yakin ingin melanjutkan penyimpanan paket pemenang ini?</p>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0 p-3">
                            <button type="button" class="btn-mockup-cancel mr-2" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i> Batal & Cek Lagi
                            </button>
                            <button type="button" class="btn-mockup-warning" id="confirmBulkSave">
                                <i class="fas fa-check mr-1"></i> Ya, Tetap Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#bulkDuplicateModal').remove();
        $('body').append(modalHtml);
        $('#bulkDuplicateModal').modal('show');

        $('#confirmBulkSave').on('click', function() {
            $('#bulkDuplicateModal').data('confirmed', true);
            $('#bulkDuplicateModal').modal('hide');
            callback(true);
        });

        $('#bulkDuplicateModal').on('hidden.bs.modal', function () {
            if (!$(this).data('confirmed')) {
                isSubmitting = false;
                $('#form-pemenang-non-konstruksi button[type="submit"]').prop('disabled', false).html('<i class="fas fa-save mr-2"></i> SIMPAN PAKET NON KONSTRUKSI');
                callback(false);
            }
            $(this).remove();
        });
    }
});
</script>
