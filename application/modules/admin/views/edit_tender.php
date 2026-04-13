<?php $t = $tender; ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit mr-2 text-primary"></i>Edit Data Tender</h1>
    <a href="<?= base_url('admin/data_tender') ?>" class="btn btn-sm btn-outline-secondary shadow-sm rounded-pill px-4">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<?php if($this->session->flashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show shadow-sm rounded-lg">
    <i class="fas fa-check-circle mr-2"></i><?= $this->session->flashdata('success') ?>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-lg">
    <i class="fas fa-exclamation-circle mr-2"></i><?= $this->session->flashdata('error') ?>
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
<?php endif; ?>

<?php echo form_error('satuan_kerja', '<div class="alert alert-danger">', '</div>'); ?>
<?php echo form_error('judul_paket', '<div class="alert alert-danger">', '</div>'); ?>
<?php echo form_error('kode_tender', '<div class="alert alert-danger">', '</div>'); ?>
<?php echo form_error('tahun_anggaran', '<div class="alert alert-danger">', '</div>'); ?>

<form action="<?= base_url('admin/update_tender/'.$t->id) ?>" method="POST" id="form-edit-tender">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

    <!-- 1. Informasi Paket -->
    <div class="card shadow mb-4 border-0 overflow-hidden">
        <div class="card-header py-3 border-0" style="background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-file-contract mr-2"></i>1. Informasi Paket & Tender</h6>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Nama Satker</label>
                        <input type="text" name="satuan_kerja" class="form-control form-control-lg bg-light border-0 shadow-none" required value="<?= html_escape($t->satuan_kerja) ?>" style="border-radius: 12px;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Judul Paket Pekerjaan</label>
                        <textarea name="judul_paket" class="form-control bg-light border-0 shadow-none" rows="1" required style="border-radius: 12px;"><?= html_escape($t->judul_paket) ?></textarea>
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Nama POKMIL</label>
                        <input type="text" name="nama_pokmil" class="form-control bg-light border-0" value="<?= html_escape($t->nama_pokmil) ?>" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Kode Tender</label>
                        <input type="text" name="kode_tender" class="form-control bg-light border-0" required value="<?= html_escape($t->kode_tender) ?>" style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Tanggal BAHP</label>
                        <input type="text" name="tanggal_bahp" class="form-control bg-light border-0 datepicker" value="<?= $t->tanggal_bahp ? date('d/m/Y', strtotime($t->tanggal_bahp)) : '' ?>" required style="border-radius: 10px;" placeholder="dd/mm/yyyy">
                    </div>
                </div>
                <div class="col-md-3 mt-2">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Tahun Anggaran</label>
                        <input type="number" name="tahun_anggaran" class="form-control bg-light border-0 font-weight-bold text-primary" value="<?= html_escape($t->tahun_anggaran) ?>" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="col-md-5 mt-3">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-primary">Nama Penyedia (Pemenang)</label>
                        <input type="text" name="nama_penyedia" class="form-control border-primary shadow-sm" required value="<?= html_escape($t->nama_perusahaan ?? $t->pemenang_tender) ?>" style="border-radius: 12px; border-width: 2px;">
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">HPS (Nilai Paket)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-0 text-primary font-weight-bold" style="border-radius: 12px 0 0 12px;">Rp</span>
                            </div>
                            <input type="text" name="hps" id="hps_input" class="form-control bg-light border-0 rupiah font-weight-bold" required value="<?= number_format($t->hps, 0, ',', '.') ?>" style="border-radius: 0 12px 12px 0;">
                        </div>
                        <small class="text-muted">Gunakan titik (.) sebagai ribuan dan koma (,) untuk desimal</small>
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group">
                        <label class="small font-weight-bold text-uppercase text-muted">Kualifikasi Usaha</label>
                        <select name="kualifikasi" class="form-control bg-light border-0" style="border-radius: 10px;">
                            <option value="Non Kecil" <?= ($t->segmentasi ?? '') === 'Non Kecil' ? 'selected' : '' ?>>Menengah / Besar</option>
                            <option value="Kecil" <?= ($t->segmentasi ?? '') === 'Kecil' ? 'selected' : '' ?>>Usaha Kecil</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Manajer / Personel Kunci -->
    <div class="card shadow mb-4 border-0 overflow-hidden">
        <div class="card-header py-3 border-0" style="background: linear-gradient(135deg, #7209b7 0%, #560bad 100%);">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-users mr-2"></i>2. Personel Kunci (Manajer)</h6>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 14px; background: linear-gradient(135deg, #f0f4ff, #fff);">
                        <h6 class="font-weight-bold text-primary small text-uppercase mb-3"><i class="fas fa-user-tie mr-2"></i>Manajer Proyek</h6>
                        <div class="form-group mb-2">
                            <label class="small text-muted">Nama Lengkap</label>
                            <input type="text" name="manajer_proyek" class="form-control form-control-sm bg-light border-0" value="<?= html_escape($t->manajer_proyek) ?>" style="border-radius: 8px;">
                        </div>
                        <div class="form-group mb-0">
                            <label class="small text-muted">NIK</label>
                            <input type="text" name="nik_manajer_proyek" class="form-control form-control-sm bg-light border-0" value="<?= html_escape($t->nik_manajer_proyek) ?>" style="border-radius: 8px;">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 14px; background: linear-gradient(135deg, #f0f4ff, #fff);">
                        <h6 class="font-weight-bold text-info small text-uppercase mb-3 row-mt"><i class="fas fa-cogs mr-2"></i>Manajer Teknik</h6>
                        <div class="form-group mb-2">
                            <label class="small text-muted">Nama Lengkap</label>
                            <input type="text" name="manajer_teknik" class="form-control form-control-sm bg-light border-0" value="<?= html_escape($t->manajer_teknik) ?>" style="border-radius: 8px;">
                        </div>
                        <div class="form-group mb-0">
                            <label class="small text-muted">NIK</label>
                            <input type="text" name="nik_manajer_teknik" class="form-control form-control-sm bg-light border-0" value="<?= html_escape($t->nik_manajer_teknik) ?>" style="border-radius: 8px;">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 14px; background: linear-gradient(135deg, #f0f4ff, #fff);">
                        <h6 class="font-weight-bold text-warning small text-uppercase mb-3 row-mk"><i class="fas fa-wallet mr-2"></i>Manajer Keuangan</h6>
                        <div class="form-group mb-2">
                            <label class="small text-muted">Nama Lengkap</label>
                            <input type="text" name="manajer_keuangan" class="form-control form-control-sm bg-light border-0" value="<?= html_escape($t->manajer_keuangan) ?>" style="border-radius: 8px;">
                        </div>
                        <div class="form-group mb-0">
                            <label class="small text-muted">NIK</label>
                            <input type="text" name="nik_manajer_keuangan" class="form-control form-control-sm bg-light border-0" value="<?= html_escape($t->nik_manajer_keuangan) ?>" style="border-radius: 8px;">
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mt-3">
                    <div class="card border-0 shadow-sm p-3" style="border-radius: 14px; background: linear-gradient(135deg, #fff0f3, #fff);">
                        <h6 class="font-weight-bold text-danger small text-uppercase mb-3"><i class="fas fa-briefcase-medical mr-2"></i>Ahli K3</h6>
                        <div class="form-group mb-2">
                            <label class="small text-muted">Nama Lengkap</label>
                            <input type="text" name="ahli_k3" class="form-control form-control-sm bg-light border-0" value="<?= html_escape($t->ahli_k3) ?>" style="border-radius: 8px;">
                        </div>
                        <div class="form-group mb-0">
                            <label class="small text-muted">NIK</label>
                            <input type="text" name="nik_ahli_k3" class="form-control form-control-sm bg-light border-0" value="<?= html_escape($t->nik_ahli_k3) ?>" style="border-radius: 8px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Personel Management -->
    <div class="card shadow mb-4 border-0 overflow-hidden">
        <div class="card-header py-3 border-0" style="background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);">
            <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-users mr-2"></i>3. Personel Management</h6>
        </div>
        <div class="card-body p-4">
            <!-- Tabs -->
            <ul class="nav nav-tabs" id="personelTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="lapangan-tab" data-toggle="tab" href="#lapangan" role="tab">
                        <i class="fas fa-hard-hat mr-2"></i>Personel Lapangan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="k3-tab" data-toggle="tab" href="#k3" role="tab">
                        <i class="fas fa-briefcase-medical mr-2"></i>Personel K3
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="peralatan-tab" data-toggle="tab" href="#peralatan" role="tab">
                        <i class="fas fa-tools mr-2"></i>Peralatan
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="personelTabContent">
                <!-- Personel Lapangan Tab -->
                <div class="tab-pane fade show active" id="lapangan" role="tabpanel">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="personel-lapangan-table">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th>Jabatan</th>
                                    <th>Jenis SKK</th>
                                    <th>No. SKK</th>
                                    <th>Masa Berlaku SKK</th>
                                    <th style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="personel-lapangan-tbody">
                                <?php $no = 1; foreach ($personel_lapangan as $p): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <input type="hidden" name="personel_lapangan[<?= $no-1 ?>][id]" value="<?= $p->id ?>">
                                        <input type="text" name="personel_lapangan[<?= $no-1 ?>][nama]" class="form-control form-control-sm" value="<?= html_escape($p->nama) ?>" required>
                                    </td>
                                    <td><input type="text" name="personel_lapangan[<?= $no-1 ?>][nik]" class="form-control form-control-sm" value="<?= html_escape($p->nik) ?>" required></td>
                                    <td><input type="text" name="personel_lapangan[<?= $no-1 ?>][jabatan]" class="form-control form-control-sm" value="<?= html_escape($p->jabatan) ?>"></td>
                                    <td><input type="text" name="personel_lapangan[<?= $no-1 ?>][jenis_skk]" class="form-control form-control-sm" value="<?= html_escape($p->jenis_skk) ?>"></td>
                                    <td><input type="text" name="personel_lapangan[<?= $no-1 ?>][nomor_skk]" class="form-control form-control-sm" value="<?= html_escape($p->nomor_skk) ?>"></td>
                                    <td><input type="date" name="personel_lapangan[<?= $no-1 ?>][masa_berlaku_skk]" class="form-control form-control-sm" value="<?= $p->masa_berlaku_skk ? date('Y-m-d', strtotime($p->masa_berlaku_skk)) : '' ?>"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-personel-lapangan"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary mt-2" id="add-personel-lapangan">
                        <i class="fas fa-plus mr-1"></i> Tambah Personel Lapangan
                    </button>
                </div>

                <!-- Personel K3 Tab -->
                <div class="tab-pane fade" id="k3" role="tabpanel">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="personel-k3-table">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th>Jabatan K3</th>
                                    <th>Jenis Sertifikat</th>
                                    <th>No. Sertifikat</th>
                                    <th>Masa Berlaku</th>
                                    <th style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="personel-k3-tbody">
                                <?php $no = 1; foreach ($personel_k3 as $p): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <input type="hidden" name="personel_k3[<?= $no-1 ?>][id]" value="<?= $p->id ?>">
                                        <input type="text" name="personel_k3[<?= $no-1 ?>][nama]" class="form-control form-control-sm" value="<?= html_escape($p->nama) ?>" required>
                                    </td>
                                    <td><input type="text" name="personel_k3[<?= $no-1 ?>][nik]" class="form-control form-control-sm" value="<?= html_escape($p->nik) ?>" required></td>
                                    <td><input type="text" name="personel_k3[<?= $no-1 ?>][jabatan_k3]" class="form-control form-control-sm" value="<?= html_escape($p->jabatan_k3) ?>"></td>
                                    <td><input type="text" name="personel_k3[<?= $no-1 ?>][jenis_sertifikat_k3]" class="form-control form-control-sm" value="<?= html_escape($p->jenis_sertifikat_k3) ?>"></td>
                                    <td><input type="text" name="personel_k3[<?= $no-1 ?>][nomor_sertifikat_k3]" class="form-control form-control-sm" value="<?= html_escape($p->nomor_sertifikat_k3) ?>"></td>
                                    <td><input type="date" name="personel_k3[<?= $no-1 ?>][masa_berlaku_sertifikat]" class="form-control form-control-sm" value="<?= $p->masa_berlaku_sertifikat ? date('Y-m-d', strtotime($p->masa_berlaku_sertifikat)) : '' ?>"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-personel-k3"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary mt-2" id="add-personel-k3">
                        <i class="fas fa-plus mr-1"></i> Tambah Personel K3
                    </button>
                </div>

                <!-- Peralatan Tab -->
                <div class="tab-pane fade" id="peralatan" role="tabpanel">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="peralatan-table">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Jenis Alat</th>
                                    <th>Nama Alat</th>
                                    <th>Merk</th>
                                    <th>Tipe</th>
                                    <th>Kapasitas</th>
                                    <th>No. Seri/Plat</th>
                                    <th>Tahun</th>
                                    <th>Status Kepemilikan</th>
                                    <th>Jumlah</th>
                                    <th style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="peralatan-tbody">
                                <?php $no = 1; foreach ($peralatan as $p): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <input type="hidden" name="peralatan[<?= $no-1 ?>][id]" value="<?= $p->id ?>">
                                        <input type="text" name="peralatan[<?= $no-1 ?>][jenis_alat]" class="form-control form-control-sm" value="<?= html_escape($p->jenis_alat) ?>" required>
                                    </td>
                                    <td><input type="text" name="peralatan[<?= $no-1 ?>][nama_alat]" class="form-control form-control-sm" value="<?= html_escape($p->nama_alat) ?>"></td>
                                    <td><input type="text" name="peralatan[<?= $no-1 ?>][merk]" class="form-control form-control-sm" value="<?= html_escape($p->merk) ?>"></td>
                                    <td><input type="text" name="peralatan[<?= $no-1 ?>][tipe]" class="form-control form-control-sm" value="<?= html_escape($p->tipe) ?>"></td>
                                    <td><input type="text" name="peralatan[<?= $no-1 ?>][kapasitas]" class="form-control form-control-sm" value="<?= html_escape($p->kapasitas) ?>"></td>
                                    <td><input type="text" name="peralatan[<?= $no-1 ?>][plat_serial]" class="form-control form-control-sm" value="<?= html_escape($p->plat_serial) ?>"></td>
                                    <td><input type="number" name="peralatan[<?= $no-1 ?>][tahun_pembuatan]" class="form-control form-control-sm" value="<?= html_escape($p->tahun_pembuatan) ?>"></td>
                                    <td>
                                        <select name="peralatan[<?= $no-1 ?>][status_kepemilikan]" class="form-control form-control-sm">
                                            <option value="Milik Sendiri" <?= $p->status_kepemilikan == 'Milik Sendiri' ? 'selected' : '' ?>>Milik Sendiri</option>
                                            <option value="Sewa" <?= $p->status_kepemilikan == 'Sewa' ? 'selected' : '' ?>>Sewa</option>
                                            <option value="Kerjasama" <?= $p->status_kepemilikan == 'Kerjasama' ? 'selected' : '' ?>>Kerjasama</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="peralatan[<?= $no-1 ?>][jumlah]" class="form-control form-control-sm" value="<?= html_escape($p->jumlah ?? 1) ?>" min="1"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-peralatan"><i class="fas fa-trash"></i></button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary mt-2" id="add-peralatan">
                        <i class="fas fa-plus mr-1"></i> Tambah Peralatan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="text-center mb-5 mt-4">
        <a href="<?= base_url('admin/data_tender') ?>" class="btn-mockup-cancel mr-3">
            <i class="fas fa-times mr-2"></i>BATAL
        </a>
        <button type="submit" class="btn-mockup-save">
            <i class="fas fa-save mr-2"></i>SIMPAN PERUBAHAN
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Initialize counters based on existing data
    let peralatanCounter = <?= count($peralatan) ?>;
    let personelLapanganCounter = <?= count($personel_lapangan) ?>;
    let personelK3Counter = <?= count($personel_k3) ?>;

    // Format Rupiah - support koma desimal untuk Excel
    $('.rupiah').on('keyup', function() {
        let val = $(this).val().replace(/[^0-9,]/g, '');
        let parts = val.split(',');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        let decimalPart = parts[1] ? ',' + parts[1] : '';
        $(this).val(integerPart + decimalPart);
    });

    // Datepicker
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            language: 'id'
        });
    }

    // Add new peralatan row
    $('#add-peralatan').click(function() {
        // Get current row count to avoid conflicts
        let currentRowCount = $('#peralatan-tbody tr').length;
        
        let newRow = `
            <tr>
                <td>${currentRowCount + 1}</td>
                <td><input type="text" name="peralatan[${currentRowCount}][jenis_alat]" class="form-control form-control-sm" required></td>
                <td><input type="text" name="peralatan[${currentRowCount}][nama_alat]" class="form-control form-control-sm"></td>
                <td><input type="text" name="peralatan[${currentRowCount}][merk]" class="form-control form-control-sm"></td>
                <td><input type="text" name="peralatan[${currentRowCount}][tipe]" class="form-control form-control-sm"></td>
                <td><input type="text" name="peralatan[${currentRowCount}][kapasitas]" class="form-control form-control-sm"></td>
                <td><input type="text" name="peralatan[${currentRowCount}][plat_serial]" class="form-control form-control-sm"></td>
                <td><input type="number" name="peralatan[${currentRowCount}][tahun_pembuatan]" class="form-control form-control-sm"></td>
                <td>
                    <select name="peralatan[${currentRowCount}][status_kepemilikan]" class="form-control form-control-sm">
                        <option value="Milik Sendiri">Milik Sendiri</option>
                        <option value="Sewa">Sewa</option>
                        <option value="Kerjasama">Kerjasama</option>
                    </select>
                </td>
                <td><input type="number" name="peralatan[${currentRowCount}][jumlah]" class="form-control form-control-sm" value="1" min="1"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-peralatan"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        $('#peralatan-tbody').append(newRow);
        peralatanCounter = currentRowCount + 1;
    });

    // Add new personel lapangan row
    $('#add-personel-lapangan').click(function() {
        // Get current row count to avoid conflicts
        let currentRowCount = $('#personel-lapangan-tbody tr').length;
        
        let newRow = `
            <tr>
                <td>${currentRowCount + 1}</td>
                <td><input type="text" name="personel_lapangan[${currentRowCount}][nama]" class="form-control form-control-sm" required></td>
                <td><input type="text" name="personel_lapangan[${currentRowCount}][nik]" class="form-control form-control-sm" required></td>
                <td><input type="text" name="personel_lapangan[${currentRowCount}][jabatan]" class="form-control form-control-sm"></td>
                <td><input type="text" name="personel_lapangan[${currentRowCount}][jenis_skk]" class="form-control form-control-sm"></td>
                <td><input type="text" name="personel_lapangan[${currentRowCount}][nomor_skk]" class="form-control form-control-sm"></td>
                <td><input type="date" name="personel_lapangan[${currentRowCount}][masa_berlaku_skk]" class="form-control form-control-sm"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-personel-lapangan"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        $('#personel-lapangan-tbody').append(newRow);
        personelLapanganCounter = currentRowCount + 1;
    });

    // Add new personel K3 row
    $('#add-personel-k3').click(function() {
        // Get current row count to avoid conflicts
        let currentRowCount = $('#personel-k3-tbody tr').length;
        
        let newRow = `
            <tr>
                <td>${currentRowCount + 1}</td>
                <td><input type="text" name="personel_k3[${currentRowCount}][nama]" class="form-control form-control-sm" required></td>
                <td><input type="text" name="personel_k3[${currentRowCount}][nik]" class="form-control form-control-sm" required></td>
                <td><input type="text" name="personel_k3[${currentRowCount}][jabatan_k3]" class="form-control form-control-sm"></td>
                <td><input type="text" name="personel_k3[${currentRowCount}][jenis_sertifikat_k3]" class="form-control form-control-sm"></td>
                <td><input type="text" name="personel_k3[${currentRowCount}][nomor_sertifikat_k3]" class="form-control form-control-sm"></td>
                <td><input type="date" name="personel_k3[${currentRowCount}][masa_berlaku_sertifikat]" class="form-control form-control-sm"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-personel-k3"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        $('#personel-k3-tbody').append(newRow);
        personelK3Counter = currentRowCount + 1;
    });

    // Remove peralatan row
    $(document).on('click', '.remove-peralatan', function() {
        if ($('#peralatan-tbody tr').length > 1) {
            $(this).closest('tr').remove();
            renumberTable('peralatan-tbody');
        } else {
            alert('Minimal harus ada satu peralatan!');
        }
    });

    // Remove personel lapangan row
    $(document).on('click', '.remove-personel-lapangan', function() {
        if ($('#personel-lapangan-tbody tr').length > 1) {
            $(this).closest('tr').remove();
            renumberTable('personel-lapangan-tbody');
        } else {
            alert('Minimal harus ada satu personel lapangan!');
        }
    });

    // Remove personel K3 row
    $(document).on('click', '.remove-personel-k3', function() {
        if ($('#personel-k3-tbody tr').length > 1) {
            $(this).closest('tr').remove();
            renumberTable('personel-k3-tbody');
        } else {
            alert('Minimal harus ada satu personel K3!');
        }
    });

    // Renumber table rows and update input names
    function renumberTable(tbodyId) {
        $('#' + tbodyId + ' tr').each(function(index) {
            let newIndex = index;
            $(this).find('td:first').text(newIndex + 1);
            
            // Update input names based on table type
            if (tbodyId === 'peralatan-tbody') {
                $(this).find('input[name^="peralatan["]').each(function() {
                    let fieldName = $(this).attr('name').replace(/peralatan\[\d+\]/, 'peralatan[' + newIndex + ']');
                    $(this).attr('name', fieldName);
                });
                $(this).find('select[name^="peralatan["]').each(function() {
                    let fieldName = $(this).attr('name').replace(/peralatan\[\d+\]/, 'peralatan[' + newIndex + ']');
                    $(this).attr('name', fieldName);
                });
            } else if (tbodyId === 'personel-lapangan-tbody') {
                $(this).find('input[name^="personel_lapangan["]').each(function() {
                    let fieldName = $(this).attr('name').replace(/personel_lapangan\[\d+\]/, 'personel_lapangan[' + newIndex + ']');
                    $(this).attr('name', fieldName);
                });
            } else if (tbodyId === 'personel-k3-tbody') {
                $(this).find('input[name^="personel_k3["]').each(function() {
                    let fieldName = $(this).attr('name').replace(/personel_k3\[\d+\]/, 'personel_k3[' + newIndex + ']');
                    $(this).attr('name', fieldName);
                });
            }
        });
        
        // Update counters
        if (tbodyId === 'peralatan-tbody') {
            peralatanCounter = $('#' + tbodyId + ' tr').length;
        } else if (tbodyId === 'personel-lapangan-tbody') {
            personelLapanganCounter = $('#' + tbodyId + ' tr').length;
        } else if (tbodyId === 'personel-k3-tbody') {
            personelK3Counter = $('#' + tbodyId + ' tr').length;
        }
    }

    // Form validation
    $('#form-edit-tender').on('submit', function(e) {
        // Validate HPS format
        let hpsValue = $('#hps_input').val();
        let hpsClean = hpsValue.replace(/\./g, '').replace(/,/g, '.');
        if (isNaN(hpsClean) || hpsClean <= 0) {
            e.preventDefault();
            alert('Format HPS tidak valid. Gunakan titik (.) sebagai ribuan dan koma (,) untuk desimal.');
            return false;
        }

        // Validate NIK uniqueness for personel lapangan
        let nikLapangan = [];
        $('#personel-lapangan-tbody input[name*="[nik]"]').each(function() {
            let nik = $(this).val().trim();
            if (nik) {
                if (nikLapangan.includes(nik)) {
                    e.preventDefault();
                    alert('NIK ' + nik + ' duplikat pada Personel Lapangan!');
                    $(this).focus();
                    return false;
                }
                nikLapangan.push(nik);
            }
        });

        // Validate NIK uniqueness for personel K3
        let nikK3 = [];
        $('#personel-k3-tbody input[name*="[nik]"]').each(function() {
            let nik = $(this).val().trim();
            if (nik) {
                if (nikK3.includes(nik)) {
                    e.preventDefault();
                    alert('NIK ' + nik + ' duplikat pada Personel K3!');
                    $(this).focus();
                    return false;
                }
                nikK3.push(nik);
            }
        });
    });
});
</script>
