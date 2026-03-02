<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Penyedia</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $total_penyedia ?></div>
                        <small class="text-muted">Perusahaan Terdaftar</small>
                    </div>
                    <div class="col-auto">
                        <div class="icon-box bg-light text-primary">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm h-100 py-2" style="border-left-color: #1cc88a;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Personel</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $total_personel ?></div>
                        <small class="text-muted">Tenaga Ahli Terverifikasi</small>
                    </div>
                    <div class="col-auto">
                        <div class="icon-box bg-light text-success">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm h-100 py-2" style="border-left-color: #36b9cc;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Peralatan</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $total_peralatan ?></div>
                        <small class="text-muted">Aset Terdaftar</small>
                    </div>
                    <div class="col-auto">
                        <div class="icon-box bg-light text-info">
                            <i class="fas fa-tools"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats shadow-sm h-100 py-2" style="border-left-color: #f6c23e;">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Paket Tender</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800"><?= $total_tender ?></div>
                        <small class="text-muted">History Partisipasi</small>
                    </div>
                    <div class="col-auto">
                        <div class="icon-box bg-light text-warning">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="font-weight-bold"><i class="fas fa-bell mr-2"></i>Riwayat Input Paket Terakhir</span>
        <span class="badge badge-pill badge-primary"><?= count($recent_tenders) ?> Aktivitas</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Kode Tender</th>
                        <th>Nama Tender</th>
                        <th>Tahun</th>
                        <th>Diinput Oleh</th>
                        <th>Role</th>
                        <th>Tanggal Input</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_tenders)): ?>
                        <?php foreach ($recent_tenders as $rt): ?>
                            <tr>
                                <td><code><?= $rt->kode_tender ?></code></td>
                                <td><?= $rt->nama_tender ?></td>
                                <td><span class="badge badge-soft-success"><?= $rt->tahun_anggaran ?></span></td>
                                <td><?= $rt->created_by ?: '-' ?></td>
                                <td><span class="badge badge-soft-danger"><?= strtoupper($rt->created_role ?: '-') ?></span></td>
                                <td><?= $rt->tanggal_input ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted py-3">Belum ada aktivitas paket.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
