<div class="card">
    <div class="card-header bg-white font-weight-bold">
        Daftar Penyedia & Status Verifikasi
    </div>
    <div class="card-body">
        <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>

        <table class="table table-bordered datatable">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Nama Perusahaan</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($penyedia as $p): ?>
                <tr>
                    <td><?= $p->username ?></td>
                    <td><?= $p->nama_perusahaan ?></td>
                    <td><?= $p->email ?></td>
                    <td><?= $p->telepon ?></td>
                    <td>
                        <?= $p->status_aktif == 1 ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Non-aktif</span>' ?>
                    </td>
                    <td>
                        <?php if($p->status_aktif == 0): ?>
                            <a href="<?= base_url('admin/toggle_status/'.$p->user_id.'/1') ?>" class="btn btn-sm btn-success">Aktifkan</a>
                        <?php else: ?>
                            <a href="<?= base_url('admin/toggle_status/'.$p->user_id.'/0') ?>" class="btn btn-sm btn-danger">Matikan</a>
                        <?php endif; ?>
                        <a href="<?= base_url('admin/edit_user/'.$p->user_id) ?>" class="btn btn-sm btn-info ml-1">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
