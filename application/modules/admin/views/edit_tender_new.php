<?php
/**
 * Edit Tender View - SIPETA Application
 * Unified form for Tender, Peralatan, and Personel management
 * 
 * @author Senior Developer
 * @version 2.0
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit mr-2"></i>Edit Tender
        </h1>
        <a href="<?= base_url('admin/data_tender') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </a>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <?= form_open('admin/update_tender/' . $tender->id, ['id' => 'form-edit-tender', 'class' => 'needs-validation', 'novalidate']) ?>
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

    <!-- 1. Data Tender Utama -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-file-contract mr-2"></i>1. Data Tender Utama
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kode_tender" class="form-label font-weight-bold">Kode Tender</label>
                    <input type="text" class="form-control" id="kode_tender" name="kode_tender" 
                           value="<?= html_escape($tender->kode_tender) ?>" required>
                    <div class="invalid-feedback">Kode Tender wajib diisi</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tahun_anggaran" class="form-label font-weight-bold">Tahun Anggaran</label>
                    <input type="number" class="form-control" id="tahun_anggaran" name="tahun_anggaran" 
                           value="<?= html_escape($tender->tahun_anggaran) ?>" required min="2000" max="2100">
                    <div class="invalid-feedback">Tahun Anggaran wajib diisi</div>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="satuan_kerja" class="form-label font-weight-bold">Satuan Kerja</label>
                    <input type="text" class="form-control" id="satuan_kerja" name="satuan_kerja" 
                           value="<?= html_escape($tender->satuan_kerja) ?>" required>
                    <div class="invalid-feedback">Satuan Kerja wajib diisi</div>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="judul_paket" class="form-label font-weight-bold">Judul Paket</label>
                    <input type="text" class="form-control" id="judul_paket" name="judul_paket" 
                           value="<?= html_escape($tender->judul_paket) ?>" required>
                    <div class="invalid-feedback">Judul Paket wajib diisi</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nama_pokmil" class="form-label font-weight-bold">Nama POKMIL</label>
                    <input type="text" class="form-control" id="nama_pokmil" name="nama_pokmil" 
                           value="<?= html_escape($tender->nama_pokmil) ?>" required>
                    <div class="invalid-feedback">Nama POKMIL wajib diisi</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nama_penyedia" class="form-label font-weight-bold">Nama Penyedia</label>
                    <input type="text" class="form-control" id="nama_penyedia" name="nama_penyedia" 
                           value="<?= html_escape($tender->nama_perusahaan ?? '') ?>" required>
                    <div class="invalid-feedback">Nama Penyedia wajib diisi</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="hps" class="form-label font-weight-bold">HPS (Rp)</label>
                    <input type="text" class="form-control rupiah" id="hps" name="hps" 
                           value="<?= number_format($tender->hps, 0, ',', '.') ?>" required 
                           placeholder="Gunakan koma (,) untuk desimal">
                    <div class="invalid-feedback">HPS wajib diisi</div>
                    <small class="text-muted">Format: Gunakan titik (.) untuk ribuan dan koma (,) untuk desimal</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="tanggal_bahp" class="form-label font-weight-bold">Tanggal BAHP</label>
                    <input type="text" class="form-control datepicker" id="tanggal_bahp" name="tanggal_bahp" 
                           value="<?= $tender->tanggal_bahp ? date('d/m/Y', strtotime($tender->tanggal_bahp)) : '' ?>" 
                           placeholder="dd/mm/yyyy">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="kualifikasi" class="form-label font-weight-bold">Kualifikasi</label>
                    <select class="form-control" id="kualifikasi" name="kualifikasi">
                        <option value="Kecil" <?= $tender->segmentasi == 'Kecil' ? 'selected' : '' ?>>Kecil</option>
                        <option value="Menengah" <?= $tender->segmentasi == 'Menengah' ? 'selected' : '' ?>>Menengah</option>
                        <option value="Besar" <?= $tender->segmentasi == 'Besar' ? 'selected' : '' ?>>Besar</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Manajemen Personel -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 border-0" style="background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-users mr-2"></i>2. Manajemen Personel
            </h6>
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
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3" id="personelTabContent">
                <!-- Personel Lapangan Tab -->
                <div class="tab-pane fade show active" id="lapangan" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Lengkap</th>
                                    <th>NIK</th>
                                    <th>Jabatan</th>
                                    <th>Jenis SKK</th>
                                    <th>No. SKK</th>
                                    <th>Masa Berlaku SKK</th>
                                    <th>Masa Berlaku Sertifikat (Tahun)</th>
                                    <th style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="personel-lapangan-tbody">
                                <?php $i = 0; foreach ($personel_lapangan as $p): $i++; ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td>
                                        <input type="text" name="personel_lapangan[<?= $i-1 ?>][nama]" 
                                               class="form-control form-control-sm" 
                                               value="<?= html_escape($p->nama) ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="personel_lapangan[<?= $i-1 ?>][nik]" 
                                               class="form-control form-control-sm" 
                                               value="<?= html_escape($p->nik) ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="personel_lapangan[<?= $i-1 ?>][jabatan]" 
                                               class="form-control form-control-sm" 
                                               value="<?= html_escape($p->jabatan) ?>">
                                    </td>
                                    <td>
                                        <input type="text" name="personel_lapangan[<?= $i-1 ?>][jenis_skk]" 
                                               class="form-control form-control-sm" 
                                               value="<?= html_escape($p->jenis_skk) ?>">
                                    </td>
                                    <td>
                                        <input type="text" name="personel_lapangan[<?= $i-1 ?>][nomor_skk]" 
                                               class="form-control form-control-sm" 
                                               value="<?= html_escape($p->nomor_skk) ?>">
                                    </td>
                                    <td>
                                        <input type="date" name="personel_lapangan[<?= $i-1 ?>][masa_berlaku_skk]" 
                                               class="form-control form-control-sm" 
                                               value="<?= $p->masa_berlaku_skk ? date('Y-m-d', strtotime($p->masa_berlaku_skk)) : '' ?>">
                                    </td>
                                    <td>
                                        <input type="number" name="personel_lapangan[<?= $i-1 ?>][masa_berlaku_skk_sertifikat]" 
                                               class="form-control form-control-sm" 
                                               value="<?= html_escape($p->masa_berlaku_skk_sertifikat ?? '') ?>" 
                                               placeholder="Tahun">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-personel-lapangan">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Lengkap</th>
                                    <th>NIK</th>
                                    <th>Jabatan K3</th>
                                    <th>Jenis Sertifikat</th>
                                    <th>No. Sertifikat</th>
                                    <th>Masa Berlaku Sertifikat</th>
                                    <th style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="personel-k3-tbody">
                                <?php $i = 0; foreach ($personel_k3 as $p): $i++; ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td>
                                        <input type="text" name="personel_k3[<?= $i-1 ?>][nama]" 
                                               class="form-control form-control-sm" 
                                               value="<?= html_escape($p->nama) ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="personel_k3[<?= $i-1 ?>][nik]" 
                                               class="form-control form-control-sm" 
                                               value="<?= html_escape($p->nik) ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="personel_k3[<?= $i-1 ?>][jabatan_k3]" 
                                               class="form-control form-control-sm" 
                                               value="<?= html_escape($p->jabatan_k3) ?>">
                                    </td>
                                    <td>
                                        <input type="text" name="personel_k3[<?= $i-1 ?>][jenis_sertifikat_k3]" 
                                               class="form-control form-control-sm" 
                                               value="<?= html_escape($p->jenis_sertifikat_k3) ?>">
                                    </td>
                                    <td>
                                        <input type="text" name="personel_k3[<?= $i-1 ?>][nomor_sertifikat_k3]" 
                                               class="form-control form-control-sm" 
                                               value="<?= html_escape($p->nomor_sertifikat_k3) ?>">
                                    </td>
                                    <td>
                                        <input type="date" name="personel_k3[<?= $i-1 ?>][masa_berlaku_sertifikat]" 
                                               class="form-control form-control-sm" 
                                               value="<?= $p->masa_berlaku_sertifikat ? date('Y-m-d', strtotime($p->masa_berlaku_sertifikat)) : '' ?>">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger remove-personel-k3">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary mt-2" id="add-personel-k3">
                        <i class="fas fa-plus mr-1"></i> Tambah Personel K3
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Manajemen Peralatan -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 border-0" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-tools mr-2"></i>3. Manajemen Peralatan
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
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
                        <?php $i = 0; foreach ($peralatan as $p): $i++; ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td>
                                <input type="hidden" name="peralatan[<?= $i-1 ?>][id]" value="<?= $p->id ?>">
                                <input type="text" name="peralatan[<?= $i-1 ?>][jenis_alat]" 
                                       class="form-control form-control-sm" 
                                       value="<?= html_escape($p->jenis_alat) ?>" required>
                            </td>
                            <td>
                                <input type="text" name="peralatan[<?= $i-1 ?>][nama_alat]" 
                                       class="form-control form-control-sm" 
                                       value="<?= html_escape($p->nama_alat) ?>">
                            </td>
                            <td>
                                <input type="text" name="peralatan[<?= $i-1 ?>][merk]" 
                                       class="form-control form-control-sm" 
                                       value="<?= html_escape($p->merk) ?>">
                            </td>
                            <td>
                                <input type="text" name="peralatan[<?= $i-1 ?>][tipe]" 
                                       class="form-control form-control-sm" 
                                       value="<?= html_escape($p->tipe) ?>">
                            </td>
                            <td>
                                <input type="text" name="peralatan[<?= $i-1 ?>][kapasitas]" 
                                       class="form-control form-control-sm" 
                                       value="<?= html_escape($p->kapasitas) ?>">
                            </td>
                            <td>
                                <input type="text" name="peralatan[<?= $i-1 ?>][plat_serial]" 
                                       class="form-control form-control-sm" 
                                       value="<?= html_escape($p->plat_serial) ?>">
                            </td>
                            <td>
                                <input type="number" name="peralatan[<?= $i-1 ?>][tahun_pembuatan]" 
                                       class="form-control form-control-sm" 
                                       value="<?= html_escape($p->tahun_pembuatan) ?>">
                            </td>
                            <td>
                                <select name="peralatan[<?= $i-1 ?>][status_kepemilikan]" class="form-control form-control-sm">
                                    <option value="Milik Sendiri" <?= $p->status_kepemilikan == 'Milik Sendiri' ? 'selected' : '' ?>>Milik Sendiri</option>
                                    <option value="Sewa" <?= $p->status_kepemilikan == 'Sewa' ? 'selected' : '' ?>>Sewa</option>
                                    <option value="Kerjasama" <?= $p->status_kepemilikan == 'Kerjasama' ? 'selected' : '' ?>>Kerjasama</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" name="peralatan[<?= $i-1 ?>][jumlah]" 
                                       class="form-control form-control-sm" 
                                       value="<?= html_escape($p->jumlah ?? 1) ?>" min="1">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-peralatan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
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

    <!-- 4. Manajemen Proyek (Optional) -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 border-0" style="background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-user-tie mr-2"></i>4. Manajemen Proyek
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Manajer Proyek</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" name="manajer_proyek" 
                                   value="<?= html_escape($tender->manajer_proyek ?? '') ?>" placeholder="Nama">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" name="nik_manajer_proyek" 
                                   value="<?= html_escape($tender->nik_manajer_proyek ?? '') ?>" placeholder="NIK">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Manajer Teknik</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" name="manajer_teknik" 
                                   value="<?= html_escape($tender->manajer_teknik ?? '') ?>" placeholder="Nama">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" name="nik_manajer_teknik" 
                                   value="<?= html_escape($tender->nik_manajer_teknik ?? '') ?>" placeholder="NIK">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Manajer Keuangan</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" name="manajer_keuangan" 
                                   value="<?= html_escape($tender->manajer_keuangan ?? '') ?>" placeholder="Nama">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" name="nik_manajer_keuangan" 
                                   value="<?= html_escape($tender->nik_manajer_keuangan ?? '') ?>" placeholder="NIK">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold">Ahli K3</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" name="ahli_k3" 
                                   value="<?= html_escape($tender->ahli_k3 ?? '') ?>" placeholder="Nama">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" name="nik_ahli_k3" 
                                   value="<?= html_escape($tender->nik_ahli_k3 ?? '') ?>" placeholder="NIK">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end mb-4">
        <a href="<?= base_url('admin/data_tender') ?>" class="btn btn-secondary mr-2">
            <i class="fas fa-times mr-2"></i>BATAL
        </a>
        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
            <i class="fas fa-save mr-2"></i>SIMPAN PERUBAHAN
        </button>
    </div>
    <?= form_close() ?>
</div>

<!-- JavaScript for Dynamic CRUD -->
<script>
$(document).ready(function() {
    // Initialize counters based on existing data
    let peralatanCounter = <?= isset($peralatan) ? count($peralatan) : 0 ?>;
    let personelLapanganCounter = <?= isset($personel_lapangan) ? count($personel_lapangan) : 0 ?>;
    let personelK3Counter = <?= isset($personel_k3) ? count($personel_k3) : 0 ?>;

    // Format Rupiah - support koma desimal untuk Excel compatibility
    $('.rupiah').on('keyup', function() {
        let val = $(this).val().replace(/[^0-9,]/g, '');
        let parts = val.split(',');
        let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        let decimalPart = parts[1] ? ',' + parts[1] : '';
        $(this).val(integerPart + decimalPart);
    });

    // Datepicker initialization
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            language: 'id'
        });
    }

    // Add new peralatan row
    $('#add-peralatan').click(function() {
        let currentRowCount = $('#peralatan-tbody tr').length;
        
        let newRow = `
            <tr>
                <td>${currentRowCount + 1}</td>
                <td>
                    <input type="hidden" name="peralatan[${currentRowCount}][id]" value="">
                    <input type="text" name="peralatan[${currentRowCount}][jenis_alat]" class="form-control form-control-sm" required>
                </td>
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
                <td><input type="number" name="personel_lapangan[${currentRowCount}][masa_berlaku_skk_sertifikat]" class="form-control form-control-sm" placeholder="Tahun"></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-personel-lapangan"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        $('#personel-lapangan-tbody').append(newRow);
        personelLapanganCounter = currentRowCount + 1;
    });

    // Add new personel K3 row
    $('#add-personel-k3').click(function() {
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
        $(this).closest('tr').remove();
        renumberTable('peralatan-tbody');
    });

    // Remove personel lapangan row
    $(document).on('click', '.remove-personel-lapangan', function() {
        $(this).closest('tr').remove();
        renumberTable('personel-lapangan-tbody');
    });

    // Remove personel K3 row
    $(document).on('click', '.remove-personel-k3', function() {
        $(this).closest('tr').remove();
        renumberTable('personel-k3-tbody');
    });

    // Renumber table rows and update input names
    function renumberTable(tbodyId) {
        $('#' + tbodyId + ' tr').each(function(index) {
            let newIndex = index;
            $(this).find('td:first').text(newIndex + 1);
            
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
        let hpsValue = $('#hps').val();
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

    // Bootstrap form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
});
</script>
