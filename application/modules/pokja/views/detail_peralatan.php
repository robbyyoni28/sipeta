<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Riwayat Alat: <?= $peralatan->nama_alat ?></h1>
    <a href="<?= base_url('pokja/cari_peralatan') ?>" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali</a>
</div>

<div class="card mb-4 border-left-success">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr><td width="150">Nama Alat</td><td>: <strong><?= $peralatan->nama_alat ?></strong></td></tr>
                    <tr><td>No. Seri / Plat</td><td>: <strong><?= $peralatan->plat_serial ?></strong></td></tr>
                    <tr><td>Merk / Tipe</td><td>: <strong><?= $peralatan->merk ?> / <?= $peralatan->tipe ?></strong></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr><td width="150">Kapasitas</td><td>: <strong><?= $peralatan->kapasitas ?></strong></td></tr>
                    <tr><td>Bukti Kepemilikan</td><td>: <strong><?= $peralatan->bukti_kepemilikan ?></strong></td></tr>
                    <tr><td>Penyedia Terakhir</td><td>: <strong><?= $peralatan->nama_perusahaan ?></strong></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Daftar Paket Pekerjaan (Tender) Yang Pernah Diikuti</h6>
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
