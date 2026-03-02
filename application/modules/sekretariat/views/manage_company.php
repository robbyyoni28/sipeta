<?php $module = $this->uri->segment(1); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelola Data: <?= $company->nama_perusahaan ?></h1>
    <a href="<?= base_url($module.'/daftar_perusahaan' . ($module == 'sekretariat' ? '' : '')) ?>" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar
    </a>
</div>

<div class="row">
    <!-- Personel Card -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Monitoring Personel</div>
                        <div class="h5 mb-2 font-weight-bold text-gray-800">Tenaga Ahli & Teknis</div>
                        <div class="btn-group w-100">
                            <a href="<?= base_url($module.'/personel_lapangan?penyedia_id='.$company->id) ?>" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search mr-1"></i> Personel Lapangan</a>
                            <a href="<?= base_url($module.'/personel_k3?penyedia_id='.$company->id) ?>" class="btn btn-sm btn-warning shadow-sm"><i class="fas fa-search mr-1"></i> Personel K3</a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Peralatan Card -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Monitoring Peralatan</div>
                        <div class="h5 mb-2 font-weight-bold text-gray-800">Inventaris Alat Berat</div>
                        <a href="<?= base_url($module.'/peralatan/'.$company->id) ?>" class="btn btn-sm btn-success btn-block shadow-sm"><i class="fas fa-search mr-1"></i> Lihat Peralatan</a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tender Card -->
    <div class="col-xl-12 col-md-12 mb-4">
        <div class="card border-left-info shadow h-100 py-3">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Status Partisipasi</div>
                        <div class="h5 mb-2 font-weight-bold text-gray-800">Histori Tender & Pemenang</div>
                        <a href="<?= base_url($module.'/tender/'.$company->id) ?>" class="btn btn-sm btn-info px-4">Buka Histori Tender</a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Informasi Dasar Perusahaan</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Alamat:</strong><br><?= $company->alamat ?: '-' ?></p>
            </div>
            <div class="col-md-3">
                <p><strong>Email:</strong><br><?= $company->email ?: '-' ?></p>
            </div>
            <div class="col-md-3">
                <p><strong>Telepon:</strong><br><?= $company->telepon ?: '-' ?></p>
            </div>
        </div>
    </div>
</div>
