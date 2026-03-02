<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - SIPETA (Sistem Informasi Personel dan Peralatan) PPU</title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 15px;
        }
        .card-login {
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
        }
        .back-home:hover { color: var(--accent); }
    </style>
</head>
<body>
    <a href="<?= base_url() ?>" class="back-home"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda</a>
    
    <div class="login-container text-center">
        <div class="card card-login">
            <div class="card-header-auth">
                <i class="fas fa-hard-hat fa-3x text-warning mb-3"></i>
                <h4 class="brand-text m-0 text-uppercase">SIPETA PPU</h4>
                <small style="opacity: 0.8;">Sistem Informasi Personel dan Peralatan</small><br>
                <small style="opacity: 0.6; font-size: 0.7rem;">Kabupaten Penajam Paser Utara</small>
            </div>
            <div class="card-body">
                <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger small"><?= $this->session->flashdata('error') ?></div>
                <?php endif; ?>
                
                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success small"><?= $this->session->flashdata('success') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('auth/login_process') ?>" method="POST">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <div class="form-group text-left">
                        <label class="small font-weight-bold text-muted">USERNAME</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-user text-primary"></i></span>
                            </div>
                            <input type="text" name="username" class="form-control border-left-0" required placeholder="Username anda">
                        </div>
                    </div>
                    <div class="form-group text-left">
                        <label class="small font-weight-bold text-muted">PASSWORD</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-key text-primary"></i></span>
                            </div>
                            <input type="password" name="password" class="form-control border-left-0" required placeholder="Password anda">
                        </div>
                    </div>
                    <div class="form-group text-left">
                        <label class="small font-weight-bold text-muted">CAPTCHA CODE</label>
                        <div class="row no-gutters">
                            <div class="col-7">
                                <input type="text" name="captcha" class="form-control" required placeholder="Type the code">
                            </div>
                            <div class="col-5 pl-2">
                                <img src="<?= base_url('auth/captcha') ?>" class="rounded w-100 h-100" style="cursor: pointer;" id="captcha-img" title="Click to refresh" alt="Captcha">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-auth btn-block mt-4 shadow">Masuk Ke Sistem</button>
                </form>
            </div>
        </div>
        <p class="mt-4 text-white small" style="opacity: 0.7;">&copy; 2026 Pemerintah Kabupaten Penajam Paser Utara</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#captcha-img').click(function() {
                var src = '<?= base_url("auth/captcha") ?>?' + Math.random();
                $(this).attr('src', src);
            });
        });
    </script>
</body>
</html>
