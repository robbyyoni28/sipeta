<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Penyedia - SIPETA PPU</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo_ukpbj.png') ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f4c75;
            --secondary: #3282b8;
            --accent: #ffcc00;
            --dark: #1b262c;
        }
        body { 
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(rgba(15, 76, 117, 0.8), rgba(27, 38, 44, 0.8)), url('https://images.unsplash.com/photo-1541888946425-d81bb19480c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 0;
            margin: 0;
        }
        .register-container {
            width: 100%;
            max-width: 700px;
            padding: 15px;
        }
        .card-register {
            border: none;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header-auth {
            background: var(--primary);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .card-body { padding: 40px; }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e3e6f0;
            background: #f8f9fc;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(15, 76, 117, 0.25);
            border-color: var(--primary);
        }
        .btn-auth {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }
        .btn-auth:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .brand-text {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            z-index: 10;
        }
        .back-home:hover { color: var(--accent); }
        label { font-size: 0.85rem; font-weight: 700; color: #4e73df; }
    </style>
</head>
<body>
    <a href="<?= base_url() ?>" class="back-home"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda</a>
    
    <div class="register-container">
        <div class="card card-register">
            <div class="card-header-auth text-center">
                <i class="fas fa-id-card fa-3x text-warning mb-3"></i>
                <h4 class="brand-text m-0 text-uppercase">Registrasi Penyedia</h4>
                <p class="small m-0" style="opacity: 0.8;">Lengkapi data perusahaan anda untuk bergabung</p>
            </div>
            <div class="card-body">
                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger small"><?= $this->session->flashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('auth/register_process') ?>" method="POST">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    
                    <h6 class="text-uppercase font-weight-bold mb-3 border-bottom pb-2" style="letter-spacing: 1px; color: var(--primary);">Data Akun</h6>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>USERNAME <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-user text-muted"></i></span>
                                </div>
                                <input type="text" name="username" class="form-control border-left-0" required placeholder="pilih username">
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>PASSWORD <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock text-muted"></i></span>
                                </div>
                                <input type="password" name="password" class="form-control border-left-0" required minlength="6" placeholder="minimal 6 karakter">
                            </div>
                        </div>
                    </div>

                    <h6 class="text-uppercase font-weight-bold mb-3 mt-4 border-bottom pb-2" style="letter-spacing: 1px; color: var(--primary);">Data Perusahaan</h6>
                    <div class="form-group">
                        <label>NAMA PERUSAHAAN <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-building text-muted"></i></span>
                            </div>
                            <input type="text" name="nama_perusahaan" class="form-control border-left-0" required placeholder="PT / CV / Firma">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>ALAMAT</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="alamat lengkap perusahaan"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>EMAIL</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-envelope text-muted"></i></span>
                                </div>
                                <input type="email" name="email" class="form-control border-left-0" placeholder="kontak@perusahaan.com">
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>NOMOR TELEPON</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fas fa-phone text-muted"></i></span>
                                </div>
                                <input type="text" name="telepon" class="form-control border-left-0" placeholder="021-xxxxxx">
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info small mt-3">
                        <i class="fas fa-info-circle mr-2"></i> Akun anda akan diverifikasi oleh Admin terlebih dahulu sebelum dapat digunakan.
                    </div>

                    <button type="submit" class="btn btn-auth btn-block mt-4 shadow-lg">Daftar Sebagai Penyedia</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="small text-muted mb-0">Sudah memiliki akun?</p>
                    <a href="<?= base_url('auth') ?>" class="font-weight-bold text-primary">Login Kembali</a>
                </div>
            </div>
        </div>
        <p class="mt-4 text-center text-white small" style="opacity: 0.7;">&copy; 2026 Pemerintah Kabupaten Penajam Paser Utara</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</body>
</html>
