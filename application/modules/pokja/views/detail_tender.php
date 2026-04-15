<?php $module = $this->uri->segment(1); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-contract mr-2"></i>Detail Tender: <?= $tender->kode_tender ?? 'Tanpa Kode' ?></h1>
    <div>
        <a href="<?= base_url($module.'/report/cetak_pdf/'.$tender->id) ?>" target="_blank" class="btn btn-sm btn-info shadow-sm mr-2"><i class="fas fa-print fa-sm text-white-50"></i> Cetak Detil Personel</a>
        <a href="<?= base_url($module.'/data_tender') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar</a>
    </div>
</div>

<div class="card mb-4 shadow-sm border-left-primary">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mb-3 pb-3 border-bottom">
                <label class="small text-muted text-uppercase font-weight-bold mb-1 d-block">Judul Paket Pekerjaan</label>
                <h4 class="font-weight-bold text-dark mb-0"><?= $tender->judul_paket ?? '-' ?></h4>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0 mt-2">
                    <tr><td width="180">Kode Tender</td><td>: <strong><?= $tender->kode_tender ?? '-' ?></strong></td></tr>
                    <tr><td>Satuan Kerja</td><td>: <?= $tender->satuan_kerja ?? '-' ?></td></tr>
                    <tr><td>Sifat / Segmentasi</td><td>: <span class="badge badge-light border"><?= $tender->segmentasi ?? '-' ?></span></td></tr>
                    <tr><td>Jenis Pengadaan</td><td>: <span class="badge badge-primary"><?= (isset($tender->is_konsultansi) && $tender->is_konsultansi == 1) ? 'Jasa Konsultansi' : 'Pekerjaan Konstruksi' ?></span></td></tr>
                </table>
            </div>
            <div class="col-md-6 border-left">
                <table class="table table-sm table-borderless mb-0 mt-2">
                    <tr><td width="180">Penyedia Terpilih</td><td>: <strong class="text-primary"><?= $tender->pemenang_tender ?? ($tender->nama_perusahaan ?? '-') ?></strong></td></tr>
                    <tr><td>Nama Pokmil</td><td>: <?= $tender->nama_pokmil ?? '-' ?></td></tr>
                    <tr><td>Tahun Anggaran</td><td>: <?= $tender->tahun_anggaran ?? date('Y') ?></td></tr>
                    <tr><td>Tanggal BAHP</td><td>: <?= !empty($tender->tanggal_bahp) ? date('d/m/Y', strtotime($tender->tanggal_bahp)) : '-' ?></td></tr>
                    <tr><td>Nilai HPS/Penawaran</td><td>: <strong class="text-success">Rp <?= number_format($tender->hps ?? 0, 0, ',', '.') ?></strong></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs custom-tabs" id="detailTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="mp-tab" data-toggle="tab" href="#manajer_proyek" role="tab"><i class="fas fa-user-tie mr-1"></i> Manajer Proyek</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="mt-tab" data-toggle="tab" href="#manajer_teknik" role="tab"><i class="fas fa-cogs mr-1"></i> Manajer Teknik</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="mk-tab" data-toggle="tab" href="#manajer_keuangan" role="tab"><i class="fas fa-file-invoice-dollar mr-1"></i> Manajer Keuangan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="personel-tab" data-toggle="tab" href="#personel_lapangan" role="tab"><i class="fas fa-users mr-1"></i> Personel Lapangan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="k3-tab" data-toggle="tab" href="#k3" role="tab"><i class="fas fa-hard-hat mr-1"></i> Personel K3</a>
            </li>
            <?php if(!isset($tender->is_konsultansi) || $tender->is_konsultansi != 1): ?>
            <li class="nav-item">
                <a class="nav-link" id="peralatan-tab" data-toggle="tab" href="#peralatan" role="tab"><i class="fas fa-truck-pickup mr-1"></i> Peralatan</a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="tab-content border border-top-0 bg-white p-4 shadow-sm rounded-bottom" id="detailTabContent">
            
            <div class="tab-pane fade show active" id="manajer_proyek" role="tabpanel">
                <?php if(empty($manajer_proyek)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-light mb-3"></i>
                        <p class="text-muted">Manajer Proyek belum ditentukan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($manajer_proyek as $mp): ?>
                    <div class="mb-4 border-bottom pb-3">
                        <h5 class="text-primary font-weight-bold"><?= $mp->nama ?? '' ?> <small class="text-muted">(Manajer Proyek)</small></h5>
                        <p class="mb-1 small">NIK: <?= $mp->nik ?? '' ?> | No SKK: <?= $mp->nomor_skk ?? '-' ?> | Masa Berlaku: <?= !empty($mp->masa_berlaku_skk) ? date('d/m/Y', strtotime($mp->masa_berlaku_skk)) : '-' ?></p>
                        <div class="mt-2">
                           <?php if(!empty($mp->file_ktp)) echo "<a href='".base_url('uploads/dokumen/'.$mp->file_ktp)."' class='badge badge-info py-1 px-2 mr-2' target='_blank'><i class='fas fa-id-card'></i> Dokumen KTP</a>"; ?>
                           <?php if(!empty($mp->file_skk)) echo "<a href='".base_url('uploads/dokumen/'.$mp->file_skk)."' class='badge badge-success py-1 px-2' target='_blank'><i class='fas fa-certificate'></i> Dokumen SKK</a>"; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="manajer_teknik" role="tabpanel">
                <?php if(empty($manajer_teknik)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-light mb-3"></i>
                        <p class="text-muted">Manajer Teknik belum ditentukan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($manajer_teknik as $mt): ?>
                    <div class="mb-4 border-bottom pb-3">
                        <h5 class="text-primary font-weight-bold"><?= $mt->nama ?? '' ?> <small class="text-muted">(Manajer Teknik)</small></h5>
                        <p class="mb-1 small">NIK: <?= $mt->nik ?? '' ?> | No SKK: <?= $mt->nomor_skk ?? '-' ?> | Masa Berlaku: <?= $mt->masa_berlaku_skk ?? '-' ?></p>
                        <div class="mt-2">
                           <?php if(!empty($mt->file_ktp)) echo "<a href='".base_url('uploads/dokumen/'.$mt->file_ktp)."' class='badge badge-info py-1 px-2 mr-2' target='_blank'><i class='fas fa-id-card'></i> Dokumen KTP</a>"; ?>
                           <?php if(!empty($mt->file_skk)) echo "<a href='".base_url('uploads/dokumen/'.$mt->file_skk)."' class='badge badge-success py-1 px-2' target='_blank'><i class='fas fa-certificate'></i> Dokumen SKK</a>"; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="manajer_keuangan" role="tabpanel">
                <?php if(empty($manajer_keuangan)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-light mb-3"></i>
                        <p class="text-muted">Manajer Keuangan belum ditentukan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($manajer_keuangan as $mk): ?>
                    <div class="mb-4 border-bottom pb-3">
                        <h5 class="text-primary font-weight-bold"><?= $mk->nama ?? '' ?> <small class="text-muted">(Manajer Keuangan)</small></h5>
                        <p class="mb-1 small">NIK: <?= $mk->nik ?? '' ?></p>
                        <div class="mt-2">
                           <?php if(!empty($mk->file_ktp)) echo "<a href='".base_url('uploads/dokumen/'.$mk->file_ktp)."' class='badge badge-info py-1 px-2' target='_blank'><i class='fas fa-id-card'></i> Dokumen KTP</a>"; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="personel_lapangan" role="tabpanel">
                <?php if(empty($personel_lapangan)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users-slash fa-3x text-light mb-3"></i>
                        <p class="text-muted">Tidak ada personel lapangan ekstra yang ditugaskan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($personel_lapangan as $pl): ?>
                    <div class="mb-4 border-bottom pb-3">
                        <h5 class="text-primary font-weight-bold"><?= $pl->nama ?? '' ?> <small class="text-muted">(Pelaksana Lapangan)</small></h5>
                        <p class="mb-1 small">NIK: <?= $pl->nik ?? '' ?> | No SKK: <?= $pl->nomor_skk ?? '-' ?> | Masa Berlaku: <?= $pl->masa_berlaku_skk ?? '-' ?></p>
                         <div class="mt-2">
                           <?php if(!empty($pl->file_ktp)) echo "<a href='".base_url('uploads/dokumen/'.$pl->file_ktp)."' class='badge badge-info py-1 px-2 mr-2' target='_blank'><i class='fas fa-id-card'></i> Dokumen KTP</a>"; ?>
                           <?php if(!empty($pl->file_skk)) echo "<a href='".base_url('uploads/dokumen/'.$pl->file_skk)."' class='badge badge-success py-1 px-2' target='_blank'><i class='fas fa-certificate'></i> Dokumen SKK</a>"; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="k3" role="tabpanel">
                <?php if(empty($personel_k3)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-hand-holding-medical fa-3x text-light mb-3"></i>
                        <p class="text-muted">Tidak ada ahli K3 yang ditugaskan.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($personel_k3 as $pk): ?>
                    <div class="mb-4 border-bottom pb-3">
                        <h5 class="text-info font-weight-bold"><?= $pk->nama ?? '' ?> <small class="text-muted">(Ahli K3)</small></h5>
                        <p class="mb-1 small">NIK: <?= $pk->nik ?? '' ?> | Sertifikat: <?= $pk->jenis_sertifikat_k3 ?? 'Sertifikat' ?> (<?= $pk->nomor_sertifikat_k3 ?? '-' ?>)</p>
                        <div class="mt-2">
                           <?php if(!empty($pk->file_ktp)) echo "<a href='".base_url('uploads/dokumen/'.$pk->file_ktp)."' class='badge badge-info py-1 px-2 mr-2' target='_blank'><i class='fas fa-id-card'></i> Dokumen KTP</a>"; ?>
                           <?php if(!empty($pk->file_skk)) echo "<a href='".base_url('uploads/dokumen/'.$pk->file_skk)."' class='badge badge-success py-1 px-2' target='_blank'><i class='fas fa-shield-alt'></i> Dokumen K3</a>"; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="peralatan" role="tabpanel">
                <?php if((isset($tender->jenis_tender) && $tender->jenis_tender == 'Konsultansi') || (isset($tender->is_konsultansi) && $tender->is_konsultansi == 1)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-info-circle fa-3x text-light mb-3"></i>
                        <p class="text-muted">Pengadaan Jasa Konsultansi tidak membutuhkan data peralatan.</p>
                    </div>
                <?php elseif(empty($peralatan)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-truck-monster fa-3x text-light mb-3"></i>
                        <p class="text-muted">Tidak ada peralatan yang ditugaskan.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="bg-navy text-white" style="background-color: #001f3f;">
                            <tr><th>Nama Alat</th><th>Merk/Tipe</th><th>Kapasitas</th><th>No. Seri/Plat</th><th>Kuantitas (Unit)</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($peralatan as $pl): ?>
                            <tr>
                                <td><?= $pl->nama_alat ?? '' ?></td>
                                <td><?= $pl->merk ?? '-' ?> / <?= $pl->tipe ?? '-' ?></td>
                                <td><?= $pl->kapasitas ?? '-' ?></td>
                                <td><?= $pl->plat_serial ?? '-' ?></td>
                                <td><strong><?= $pl->jumlah ?? '1' ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</div>

<style>
.custom-tabs .nav-link { border-radius: 0; color: #001f3f; font-weight: 600; padding: 12px 18px; }
.custom-tabs .nav-link:hover { background-color: #f8f9fa; }
.custom-tabs .nav-link.active { border-top: 3px solid #001f3f; background: #fff; color: #333; }
</style>
