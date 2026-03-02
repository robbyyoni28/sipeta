<?php $module = $this->uri->segment(1); ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-800"><i class="fas fa-address-card mr-2 text-primary"></i>DIREKTORI PERUSAHAAN (PENYEDIA)</h1>
</div>

<div class="card shadow mb-4 border-0 overflow-hidden">
    <div class="card-header bg-gradient-primary py-3">
        <h6 class="m-0 font-weight-bold text-white">Daftar Perusahaan Terdaftar</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 datatable" width="100%" cellspacing="0">
                <thead class="bg-light text-uppercase small font-weight-bold">
                    <tr>
                        <th class="border-0 px-4 py-3">Nama Perusahaan</th>
                        <th class="border-0 py-3">Email</th>
                        <th class="border-0 py-3">Telepon</th>
                        <th class="border-0 py-3">Alamat</th>
                        <th class="border-0 py-3 px-4 text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($companies as $row): ?>
                    <tr class="align-middle">
                        <td class="px-4">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary-light text-white mr-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-weight: 700;">
                                    <?= substr($row->nama_perusahaan, 0, 1) ?>
                                </div>
                                <span class="font-weight-700"><?= $row->nama_perusahaan ?></span>
                            </div>
                        </td>
                        <td><small><?= $row->email ?></small></td>
                        <td><small><?= $row->telepon ?></small></td>
                        <td><small class="text-muted"><?= $row->alamat ?></small></td>
                        <td class="px-4 text-center">
                            <a href="<?= base_url($module.'/manage/'.$row->id) ?>" class="btn btn-sm btn-pill btn-outline-primary shadow-sm px-3">
                                <i class="fas fa-search mr-1"></i> LIHAT DATA
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
