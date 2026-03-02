<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Riwayat Personel: <?= $personel->nama ?></h1>
    <a href="<?= base_url('pokja/cari_personel') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali</a>
</div>

<div class="card mb-4 border-left-primary">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr><td width="150">Nama Lengkap</td><td>: <strong><?= $personel->nama ?></strong></td></tr>
                    <tr><td>NIK</td><td>: <strong><?= $personel->nik ?></strong></td></tr>
                    <tr><td>Jabatan</td><td>: <strong><?= $personel->jabatan ?></strong></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr><td width="150">Nomor Sertifikat/SKK</td><td>: <strong><?= $personel->nomor_skk ?></strong></td></tr>
                    <tr><td>Jenis SKK</td><td>: <strong><?= $personel->jenis_skk ?></strong></td></tr>
                    <tr><td>Penyedia Terakhir</td><td>: <strong><?= $personel->nama_perusahaan ?></strong></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Paket Pekerjaan (Tender) Yang Pernah Diikuti</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Kode Tender</th>
                        <th>Nama Tender</th>
                        <th>Penyedia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($history)): ?>
                    <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada riwayat kemenangan pada tender.</td></tr>
                    <?php else: ?>
                        <?php foreach($history as $h): ?>
                        <tr>
                            <td><strong><?= $h->kode_tender ?></strong></td>
                            <td><?= $h->nama_tender ?></td>
                            <td><?= $h->nama_perusahaan ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
