<?php $module = $this->uri->segment(1); ?>
<!-- K3 Detail Modals -->
<?php foreach($personel as $p): 
    $status_class = 'bg-success';
    $status_text = 'Berlaku';
    if($p->expiry_status['status'] == 'expired') {
        $status_class = 'bg-danger';
        $status_text = 'Expired';
    } elseif($p->expiry_status['status'] == 'warning') {
        $status_class = 'bg-warning text-dark';
        $status_text = 'Segera Expired';
    }
?>
<div class="modal fade" id="detailK3Modal<?= $p->id ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);">
                <h5 class="modal-title"><i class="fas fa-briefcase-medical mr-2"></i>Detail Personel K3</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted font-weight-bold">Nama Lengkap</label>
                        <div class="font-weight-bold"><?= $p->nama ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted font-weight-bold">NIK</label>
                        <div><span class="badge badge-light border px-3 py-2"><?= $p->nik ?></span></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted font-weight-bold">Jabatan K3</label>
                        <div class="font-weight-700" style="color: #ff6b35;"><?= $p->jabatan_k3 ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted font-weight-bold">Perusahaan</label>
                        <div class="font-weight-600"><?= $p->nama_perusahaan ?></div>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="small text-muted font-weight-bold">Jenis Sertifikat K3</label>
                        <div><?= $p->jenis_sertifikat_k3 ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="small text-muted font-weight-bold">Status</label>
                        <div><span class="badge <?= $status_class ?> px-3 py-2"><?= $status_text ?></span></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted font-weight-bold">Nomor Sertifikat</label>
                        <div><code class="bg-light px-3 py-2 d-inline-block"><?= $p->nomor_sertifikat_k3 ?></code></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted font-weight-bold">Masa Berlaku</label>
                        <div class="font-weight-bold"><i class="far fa-calendar-alt mr-1"></i> <?= date('d M Y', strtotime($p->masa_berlaku_sertifikat)) ?></div>
                    </div>
                    <?php if($p->file_sertifikat_k3): ?>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted font-weight-bold">File Sertifikat K3</label>
                        <div><a href="<?= base_url('assets/uploads/k3/'.$p->file_sertifikat_k3) ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf mr-1"></i> Lihat File</a></div>
                    </div>
                    <?php endif; ?>
                    <?php if($p->file_ktp): ?>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted font-weight-bold">File KTP</label>
                        <div><a href="<?= base_url('assets/uploads/k3/'.$p->file_ktp) ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-image mr-1"></i> Lihat File</a></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
