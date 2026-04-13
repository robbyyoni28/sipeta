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
    // Format Rupiah
    $('.rupiah').on('keyup', function() {
        let val = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(new Intl.NumberFormat('id-ID').format(val));
    });

    // Datepicker
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            language: 'id'
        });
    }
});
</script>
