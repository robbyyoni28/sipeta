<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit User</h1>
    <a href="javascript:history.back()" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">Update User Credentials</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/update_user_process') ?>" method="POST">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="id" value="<?= $user->id ?>">
                    
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?= $user->username ?>" required>
                    </div>

                    <div class="form-group">
                        <label>New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••">
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="admin" <?= $user->role == 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="pokja" <?= $user->role == 'pokja' ? 'selected' : '' ?>>Pokja</option>
                            <option value="sekretariat" <?= $user->role == 'sekretariat' ? 'selected' : '' ?>>Sekretariat</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Account Status</label>
                        <select name="status_aktif" class="form-control" required>
                            <option value="1" <?= $user->status_aktif == 1 ? 'selected' : '' ?>>Active / Verified</option>
                            <option value="0" <?= $user->status_aktif == 0 ? 'selected' : '' ?>>Inactive / Pending</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
