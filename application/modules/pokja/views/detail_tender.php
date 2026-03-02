<?php $module = $this->uri->segment(1); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-contract mr-2"></i>Detail Tender: <?= $tender->kode_tender ?></h1>
    <a href="<?= base_url($module.'/data_tender') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar</a>
</div>

<div class="card mb-4 shadow-sm border-left-primary">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td width="150">Kode Tender</td><td>: <strong><?= $tender->kode_tender ?></strong></td></tr>
                    <tr><td>Nama Satker</td><td>: <strong><?= $tender->satuan_kerja ?></strong></td></tr>
                    <tr><td>Judul Paket</td><td>: <?= $tender->judul_paket ?></td></tr>
                </table>
            </div>
            <div class="col-md-6 border-left">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td width="150">Penyedia</td><td>: <span class="badge badge-info"><?= $tender->nama_perusahaan ?></span></td></tr>
                    <tr><td>Tahun Anggaran</td><td>: <?= $tender->tahun_anggaran ?></td></tr>
                    <tr><td>HPS</td><td>: <strong class="text-success">Rp <?= number_format($tender->hps, 0, ',', '.') ?></strong></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs custom-tabs" id="detailTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="personel-tab" data-toggle="tab" href="#personel" role="tab"><i class="fas fa-users mr-1"></i> Personel Lapangan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="k3-tab" data-toggle="tab" href="#k3" role="tab"><i class="fas fa-briefcase-medical mr-1"></i> Personel K3</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="peralatan-tab" data-toggle="tab" href="#peralatan" role="tab"><i class="fas fa-truck-pickup mr-1"></i> Peralatan</a>
            </li>
        </ul>
        <div class="tab-content border border-top-0 bg-white p-4 shadow-sm rounded-bottom" id="detailTabContent">
            <!-- PERSONEL LAPANGAN -->
            <div class="tab-pane fade show active" id="personel" role="tabpanel">
                <?php if(empty($personel)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-light mb-3"></i>
                        <p class="text-muted">Tidak ada personel lapangan yang ditugaskan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($personel as $p): ?>
                    <div class="mb-4 border-bottom pb-3">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-primary font-weight-bold"><?= $p->nama ?> <small class="text-muted">(<?= $p->jabatan ?>)</small></h5>
                        </div>
                        <p class="mb-1 small">NIK: <?= $p->nik ?> | No SKK: <?= $p->nomor_skk ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- PERSONEL K3 -->
            <div class="tab-pane fade" id="k3" role="tabpanel">
                <?php if(empty($personel_k3)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-hand-holding-medical fa-3x text-light mb-3"></i>
                        <p class="text-muted">Tidak ada ahli K3 yang ditugaskan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($personel_k3 as $pk): ?>
                    <div class="mb-4 border-bottom pb-3">
                        <h5 class="text-info font-weight-bold"><?= $pk->nama ?> <small class="text-muted">(<?= $pk->jabatan_k3 ?>)</small></h5>
                        <p class="mb-1 small">NIK: <?= $pk->nik ?> | Sertifikat: <?= $pk->jenis_sertifikat_k3 ?> (<?= $pk->nomor_sertifikat_k3 ?>)</p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- PERALATAN -->
            <div class="tab-pane fade" id="peralatan" role="tabpanel">
                <?php if(empty($peralatan)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-truck-monster fa-3x text-light mb-3"></i>
                        <p class="text-muted">Tidak ada peralatan yang ditugaskan.</p>
                    </div>
                <?php else: ?>
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light">
                            <tr><th>Nama Alat</th><th>Merk/Tipe</th><th>Kapasitas</th><th>No. Seri/Plat</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($peralatan as $pl): ?>
                            <tr>
                                <td><?= $pl->nama_alat ?></td>
                                <td><?= $pl->merk ?> / <?= $pl->tipe ?></td>
                                <td><?= $pl->kapasitas ?></td>
                                <td><?= $pl->plat_serial ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.custom-tabs .nav-link { border-radius: 0; color: #4e73df; font-weight: 600; padding: 12px 20px; }
.custom-tabs .nav-link.active { border-top: 3px solid #4e73df; background: #fff; color: #333; }
</style>
