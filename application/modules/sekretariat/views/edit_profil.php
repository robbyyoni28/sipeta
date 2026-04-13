<?php $module = $this->uri->segment(1); ?>
<div class="row">
    <div class="col-md-8 offset-md-2 mt-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="m-0"><i class="fas fa-user-edit mr-2"></i> Edit Profil (<?= ucfirst($module) ?>)</h4>
            </div>
            <div class="card-body">
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success border-left-success shadow-sm alert-dismissible fade show">
                        <?= $this->session->flashdata('success'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger border-left-danger shadow-sm alert-dismissible fade show">
                        <?= $this->session->flashdata('error'); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?= form_open_multipart($module.'/edit_profil'); ?>
                    <div class="form-group text-center">
                        <label class="d-block font-weight-bold">Foto Profil Saat Ini</label>
                        <?php 
                        $foto = !empty($user['foto']) ? $user['foto'] : 'default.png';
                        $foto_url = base_url('assets/img/profile/' . $foto);
                        ?>
                        <img src="<?= $foto_url; ?>" width="150" class="img-thumbnail rounded-circle mb-3 shadow-sm" style="object-fit: cover; height: 150px; border: 3px solid #4e73df;">
                        
                        <div class="custom-file text-left mt-2 px-md-5">
                            <input type="file" name="foto" class="form-control-file border p-2 rounded" accept=".jpg,.png,.jpeg">
                            <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah foto (Maks: 2MB, jpg/png).</small>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label class="font-weight-bold">Username</label>
                        <input type="text" class="form-control bg-light" value="<?= html_escape($user['username']); ?>" readonly>
                        <small class="text-muted">Username tidak dapat diubah dan digunakan untuk login.</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-primary">Nama Lengkap / Instansi</label>
                        <input type="text" name="nama" class="form-control border-primary" placeholder="Kosongkan jika tidak ingin mengubah nama" value="<?= html_escape($user['nama'] ?? ''); ?>" >
                    </div>

                    <hr class="mt-4 mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-lock mr-2"></i> Keamanan Sandi <span class="badge badge-light text-muted" style="font-size: 12px; font-weight: normal;">Opsional</span></h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password Lama</label>
                                <input type="password" name="password_lama" class="form-control" placeholder="Masukkan sandi saat ini">
                                <small class="text-muted text-danger">Wajib diisi jika ingin merubah password.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password Baru</label>
                                <input type="password" name="password_baru" class="form-control" placeholder="Masukkan sandi baru">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-right">
                        <button type="submit" class="btn btn-success shadow-sm px-4 rounded-pill font-weight-bold"><i class="fas fa-save mr-2"></i> Simpan Perubahan Profil</button>
                    </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>
