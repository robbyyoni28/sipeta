<?php
/**
 * Edit Profile View - SIPETA Application
 * Profile management for all user roles (Admin, Sekretariat, Pokja)
 * 
 * @author Senior Developer
 * @version 2.0
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-circle mr-2"></i>Edit Profil
        </h1>
        <a href="<?= base_url('admin') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i>Kembali
        </a>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $this->session->flashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Profile Photo Card -->
        <div class="col-md-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header py-3 border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-camera mr-2"></i>Foto Profil
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php 
                        $photo_path = $user->foto && $user->foto !== 'default.png' 
                            ? base_url('assets/img/profile/' . $user->foto) 
                            : 'https://via.placeholder.com/150?text=' . strtoupper(substr($user->nama ?? 'U', 0, 1));
                        ?>
                        <img src="<?= $photo_path ?>" 
                             alt="Foto Profil" 
                             class="rounded-circle" 
                             style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #667eea;">
                    </div>
                    <h5 class="mb-1"><?= html_escape($user->nama ?? $user->username) ?></h5>
                    <p class="text-muted mb-3">
                        <span class="badge badge-info"><?= ucfirst($user->role) ?></span>
                        <span class="badge <?= $user->status_aktif ? 'badge-success' : 'badge-danger' ?>">
                            <?= $user->status_aktif ? 'Aktif' : 'Non-Aktif' ?>
                        </span>
                    </p>
                    
                    <?= form_open_multipart('admin/update_profil', ['id' => 'form-foto']) ?>
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    
                    <div class="custom-file mb-3">
                        <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                        <label class="custom-file-label" for="foto">Pilih Foto</label>
                        <small class="text-muted d-block">Format: JPG, PNG, GIF. Max: 2MB</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-upload mr-1"></i>Upload Foto
                    </button>
                    <?= form_close() ?>
                </div>
            </div>
        </div>

        <!-- Profile Information Card -->
        <div class="col-md-8 mb-4">
            <div class="card shadow border-0">
                <div class="card-header py-3 border-0" style="background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-user-edit mr-2"></i>Informasi Profil
                    </h6>
                </div>
                <div class="card-body p-4">
                    <?= form_open('admin/update_profil', ['id' => 'form-profil', 'class' => 'needs-validation', 'novalidate']) ?>
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label font-weight-bold">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" 
                                   value="<?= html_escape($user->nama ?? '') ?>" required>
                            <div class="invalid-feedback">Nama Lengkap wajib diisi</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label font-weight-bold">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= html_escape($user->username) ?>" required>
                            <div class="invalid-feedback">Username wajib diisi</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label font-weight-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= html_escape($user->email ?? '') ?>" readonly
                                   style="background-color: #f8f9fa;">
                            <small class="text-muted">Email tidak dapat diubah</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label font-weight-bold">Role</label>
                            <input type="text" class="form-control" id="role" name="role" 
                                   value="<?= ucfirst($user->role) ?>" readonly
                                   style="background-color: #f8f9fa;">
                            <small class="text-muted">Role ditentukan oleh administrator</small>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="created_at" class="form-label font-weight-bold">Tanggal Dibuat</label>
                            <input type="text" class="form-control" id="created_at" name="created_at" 
                                   value="<?= $user->created_at ? date('d/m/Y H:i:s', strtotime($user->created_at)) : '-' ?>" readonly
                                   style="background-color: #f8f9fa;">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #f72585 0%, #b5179e 100%); border: none;">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </div>
                    
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Card -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow border-0">
                <div class="card-header py-3 border-0" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-lock mr-2"></i>Ganti Password
                    </h6>
                </div>
                <div class="card-body p-4">
                    <?= form_open('admin/change_password', ['id' => 'form-password', 'class' => 'needs-validation', 'novalidate']) ?>
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="current_password" class="form-label font-weight-bold">Password Lama</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="invalid-feedback">Password lama wajib diisi</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="new_password" class="form-label font-weight-bold">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password" name="new_password" 
                                       required minlength="6" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}"
                                       title="Minimal 6 karakter, harus mengandung huruf besar, huruf kecil, dan angka">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="invalid-feedback">
                                Minimal 6 karakter, harus mengandung huruf besar, huruf kecil, dan angka
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="confirm_password" class="form-label font-weight-bold">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="invalid-feedback">Konfirmasi password wajib diisi</div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Tips Password:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Minimal 6 karakter</li>
                            <li>Harus mengandung huruf besar (A-Z)</li>
                            <li>Harus mengandung huruf kecil (a-z)</li>
                            <li>Harus mengandung angka (0-9)</li>
                            <li>Disarankan menggunakan karakter khusus (!@#$%^&*)</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none;">
                            <i class="fas fa-key mr-2"></i>Ganti Password
                        </button>
                    </div>
                    
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
$(document).ready(function() {
    // Custom file input label
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Password confirmation validation
    $('#confirm_password').on('input', function() {
        var newPassword = $('#new_password').val();
        var confirmPassword = $(this).val();
        
        if (newPassword !== confirmPassword) {
            $(this).get(0).setCustomValidity('Password tidak cocok');
        } else {
            $(this).get(0).setCustomValidity('');
        }
    });

    // Bootstrap form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
});

// Toggle password visibility
function togglePassword(fieldId) {
    var field = document.getElementById(fieldId);
    var icon = field.nextElementSibling.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
