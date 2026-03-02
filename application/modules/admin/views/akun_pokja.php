<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white font-weight-bold">Tambah Akun Pokja</div>
            <div class="card-body">
                <form action="<?= base_url('admin/create_pokja_process') ?>" method="POST">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Simpan Akun</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white font-weight-bold">Daftar Akun Pokja</div>
            <div class="card-body">
                <table class="table table-bordered datatable">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pokja as $p): ?>
                        <tr>
                            <td><?= $p->username ?></td>
                            <td><?= strtoupper($p->role) ?></td>
                            <td><span class="badge badge-success">Aktif</span></td>
                            <td>
                                <a href="<?= base_url('admin/edit_user/'.$p->id) ?>" class="btn btn-sm btn-info btn-block">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
