<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_model');
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $role = $this->session->userdata('role');
            if (!empty($role)) {
                 $this->redirect_by_role();
            }
        }
        $this->load->view('login');
    }

    public function captcha() {
        $this->load->helper('string');
        $word = random_string('numeric', 4);
        $this->session->set_userdata('captcha_word', $word);

        $width = 100;
        $height = 40;
        $image = imagecreate($width, $height);
        $bg = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 15, 76, 117);
        
        // Add some noise
        for($i=0; $i<10; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), imagecolorallocate($image, 200, 200, 200));
        }

        imagestring($image, 5, 30, 12, $word, $text_color);
        
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    public function login_process() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $captcha = $this->input->post('captcha');
        $saved_captcha = $this->session->userdata('captcha_word');

        if ($captcha !== $saved_captcha) {
            $this->session->set_flashdata('error', 'Wrong captcha code.');
            redirect('auth');
        }
        
        $result = $this->Auth_model->login($username, $password);
        
        if ($result['status']) {
            $this->session->unset_userdata('captcha_word');
            $this->redirect_by_role();
        } else {
            $this->session->set_flashdata('error', $result['message']);
            redirect('auth');
        }
    }

    public function register() {
        show_404();
    }

    public function register_process() {
        show_404();
    }

    public function seed_admin() {
        $username = 'admin';
        $password = 'admin123';
        
        $exists = $this->db->get_where('users', ['username' => $username])->row();
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        
        if ($exists) {
            $this->db->where('username', $username)->update('users', ['password' => $new_hash, 'status_aktif' => 1]);
        } else {
            $data = [
                'username' => $username,
                'password' => $password,
                'role' => 'admin',
                'status_aktif' => 1
            ];
            $this->Auth_model->register($data);
        }
        redirect('auth');
    }

    public function seed_pokja() {
        $username = 'pokja';
        $password = 'pokja123';
        
        $exists = $this->db->get_where('users', ['username' => $username])->row();
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        
        if ($exists) {
            $this->db->where('username', $username)->update('users', ['password' => $new_hash, 'status_aktif' => 1]);
        } else {
            $data = [
                'username' => $username,
                'password' => $password,
                'role' => 'pokja',
                'status_aktif' => 1
            ];
            $this->Auth_model->register($data);
        }
        redirect('auth');
    }

    public function seed_dummy() {
        if (!$this->db->table_exists('users')) {
            die("Error: Table 'users' belum ada.");
        }

        $this->db->trans_start();

        $perusahaan = [
            ['name' => 'PT. Kalimantan Konstruksi Jaya', 'user' => 'penyedia_kaltim', 'email' => 'contact@kaltimjaya.id'],
            ['name' => 'CV. Penajam Mandiri Teknik', 'user' => 'penyedia_penajam', 'email' => 'info@penajammandiri.com'],
            ['name' => 'PT. Paser Abadi Bangun', 'user' => 'penyedia_paser', 'email' => 'admin@paserabadi.co.id']
        ];

        foreach ($perusahaan as $p) {
            $user = $this->db->get_where('users', ['username' => $p['user']])->row();
            
            if (!$user) {
                // Create User
                $this->db->insert('users', [
                    'username' => $p['user'],
                    'password' => password_hash('penyedia123', PASSWORD_DEFAULT),
                    'role' => 'penyedia',
                    'status_aktif' => 1
                ]);
                $user_id = $this->db->insert_id();
                
                // Create Penyedia Details
                $this->db->insert('penyedia', [
                    'user_id' => $user_id,
                    'nama_perusahaan' => $p['name'],
                    'alamat' => 'Jl. Kawasan Industri No. ' . rand(1, 100),
                    'email' => $p['email'],
                    'telepon' => '0542-' . rand(111111, 999999)
                ]);
                $penyedia_id = $this->db->insert_id();
            } else {
                $penyedia = $this->db->get_where('penyedia', ['user_id' => $user->id])->row();
                $penyedia_id = $penyedia->id;

                // Clean up old associations for this penyedia
                $tenders = $this->db->get_where('tender', ['penyedia_id' => $penyedia_id])->result();
                foreach($tenders as $t) {
                    $this->db->where('tender_id', $t->id)->delete('tender_personel');
                    $this->db->where('tender_id', $t->id)->delete('tender_peralatan');
                }
                $this->db->where('penyedia_id', $penyedia_id)->delete('tender');
                $this->db->where('penyedia_id', $penyedia_id)->delete('personel_lapangan');
                $this->db->where('penyedia_id', $penyedia_id)->delete('peralatan');
            }

            // Create 5 Personel
            $pers_ids = [];
            $jabatan_list = ['Site Manager', 'Tenaga K3', 'Teknisi Elektro', 'Surveyor', 'Logistik'];
            for ($i=1; $i <= 5; $i++) { 
                $this->db->insert('personel_lapangan', [
                    'penyedia_id' => $penyedia_id,
                    'nama' => 'Ahli ' . $i . ' (' . $p['name'] . ')',
                    'nik' => '6471' . rand(10000000, 99999999),
                    'jenis_skk' => 'SKK Ahli Madya',
                    'nomor_skk' => 'REG-' . rand(100, 999) . '/PPU/2026',
                    'jabatan' => $jabatan_list[$i-1],
                    'pengalaman_tahun' => rand(3, 15)
                ]);
                $pers_ids[] = $this->db->insert_id();
            }

            // Create 5 Peralatan
            $alat_ids = [];
            $alat_list = [
                ['n' => 'Excavator PC200', 'm' => 'Komatsu'],
                ['n' => 'Dump Truck 24T', 'm' => 'Mitsubishi'],
                ['n' => 'Concrete Pump', 'm' => 'Sany'],
                ['n' => 'Bulldozer D65', 'm' => 'Komatsu'],
                ['n' => 'Crane 25 Ton', 'm' => 'Kato']
            ];
            foreach ($alat_list as $a) {
                $this->db->insert('peralatan', [
                    'penyedia_id' => $penyedia_id,
                    'nama_alat' => $a['n'],
                    'merk' => $a['m'],
                    'tipe' => 'Standard Edition',
                    'kapasitas' => rand(10, 100) . ' Ton/m3',
                    'plat_serial' => 'SN-' . rand(1000, 9999) . '-PPU',
                    'bukti_kepemilikan' => 'Faktur / BPKB'
                ]);
                $alat_ids[] = $this->db->insert_id();
            }

            // Create 3 Tenders and associate them
            for ($j=1; $j <= 3; $j++) { 
                $kode = 'TDR-' . date('Y') . '-' . rand(100, 999);
                $this->db->insert('tender', [
                    'penyedia_id' => $penyedia_id,
                    'kode_tender' => $kode,
                    'nama_tender' => 'Proyek Strategis ' . $j . ' Daerah ' . ucfirst(substr($p['user'], 9)),
                ]);
                $tender_id = $this->db->insert_id();

                // Cross-association logic:
                // Tender 1 uses Personel 1, 2, 3
                // Tender 2 uses Personel 1, 4, 5  <-- Personel 1 overlaps
                // Tender 3 uses Personel 1, 3, 5  <-- Personel 1 and 3 overlap
                
                $this->db->insert('tender_personel', ['tender_id' => $tender_id, 'personel_id' => $pers_ids[0]]); // Overlap
                if ($j == 1) {
                    $this->db->insert('tender_personel', ['tender_id' => $tender_id, 'personel_id' => $pers_ids[1]]);
                    $this->db->insert('tender_personel', ['tender_id' => $tender_id, 'personel_id' => $pers_ids[2]]);
                    $this->db->insert('tender_peralatan', ['tender_id' => $tender_id, 'peralatan_id' => $alat_ids[0]]);
                    $this->db->insert('tender_peralatan', ['tender_id' => $tender_id, 'peralatan_id' => $alat_ids[1]]);
                } elseif ($j == 2) {
                    $this->db->insert('tender_personel', ['tender_id' => $tender_id, 'personel_id' => $pers_ids[3]]);
                    $this->db->insert('tender_personel', ['tender_id' => $tender_id, 'personel_id' => $pers_ids[4]]);
                    $this->db->insert('tender_peralatan', ['tender_id' => $tender_id, 'peralatan_id' => $alat_ids[0]]);
                    $this->db->insert('tender_peralatan', ['tender_id' => $tender_id, 'peralatan_id' => $alat_ids[2]]);
                } else {
                    $this->db->insert('tender_personel', ['tender_id' => $tender_id, 'personel_id' => $pers_ids[2]]); // Overlap
                    $this->db->insert('tender_personel', ['tender_id' => $tender_id, 'personel_id' => $pers_ids[4]]);
                    $this->db->insert('tender_peralatan', ['tender_id' => $tender_id, 'peralatan_id' => $alat_ids[0]]);
                    $this->db->insert('tender_peralatan', ['tender_id' => $tender_id, 'peralatan_id' => $alat_ids[3]]);
                }
            }
        }

        $this->db->trans_complete();
        echo "<div style='font-family: Arial; padding: 30px; line-height: 1.6;'>";
        echo "<h2 style='color: #2e7d32;'>✔️ DATA DUMMY BERHASIL DIPERBARUI!</h2>";
        echo "<p>Saya telah membersihkan data lama dan membuat data baru dengan <b>keterkaitan (overlap)</b> yang lebih kompleks:</p>";
        echo "<ul>";
        echo "<li><b>3 Perusahaan</b> utama telah dipopulasi ulang.</li>";
        echo "<li>Setiap perusahaan memiliki <b>3 Tender</b> aktif.</li>";
        echo "<li><b>Personel Ahli Utama</b> di setiap perusahaan muncul di <b>KETIGA</b> tender tersebut.</li>";
        echo "<li><b>Personel Ahli Kedua</b> muncul di <b>DUA</b> tender berbeda.</li>";
        echo "</ul>";
        echo "<p>Gunakan akun <b>pokja / pokja123</b> untuk melakukan simulasi pemeriksaan.</p>";
        echo "<br><a href='".base_url('auth')."' style='background: #0f4c75; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Masuk Sebagai POKJA</a>";
        echo "</div>";
    }

    public function seed_penyedia() {
        if (!$this->db->table_exists('users') || !$this->db->table_exists('penyedia')) {
            die("Error: Table 'users' atau 'penyedia' belum ada. Pastikan sudah import <b>schema.sql</b> ke database anda.");
        }

        $username = 'penyedia';
        $password = 'penyedia123';
        
        $exists = $this->db->get_where('users', ['username' => $username])->row();
        
        if ($exists) {
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $this->db->where('username', $username)->update('users', ['password' => $new_hash, 'status_aktif' => 1]);
            echo "Penyedia account found. <b>Password has been reset</b> to: <code>penyedia123</code><br>";
        } else {
            $data = [
                'username' => $username,
                'password' => $password,
                'role' => 'penyedia',
                'status_aktif' => 1,
                'nama_perusahaan' => 'PT. Contoh Penyedia',
                'alamat' => 'Jl. Penajam No. 123',
                'email' => 'penyedia@example.com',
                'telepon' => '08123456789'
            ];
            if ($this->Auth_model->register($data)) {
                echo "Penyedia account <b>created successfully</b>!<br>";
                echo "Username: <code>penyedia</code><br>";
                echo "Password: <code>penyedia123</code>";
            } else {
                echo "Failed to create Penyedia account.";
            }
        }
        echo "<br><br><a href='".base_url('auth')."'>Go to Login</a>";
    }

    public function seed_sekretariat() {
        $username = 'sekretariat';
        $password = 'sekretariat123';
        
        $exists = $this->db->get_where('users', ['username' => $username])->row();
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        
        if ($exists) {
            $this->db->where('username', $username)->update('users', ['password' => $new_hash, 'status_aktif' => 1]);
        } else {
            $data = [
                'username' => $username,
                'password' => $password,
                'role' => 'sekretariat',
                'status_aktif' => 1
            ];
            $this->Auth_model->register($data);
        }
        redirect('auth');
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth');
    }


    private function redirect_by_role() {
        $role = $this->session->userdata('role');
        session_write_close();
        
        if ($role == 'admin') {
            redirect('admin');
        } elseif ($role == 'penyedia') {
            redirect('penyedia');
        } elseif ($role == 'pokja') {
            redirect('pokja');
        } elseif ($role == 'sekretariat') {
            redirect('sekretariat/input_pemenang');
        } else {
            $this->session->set_flashdata('error', "Unrecognized role: '$role'");
            redirect('auth');
        }
    }
}
