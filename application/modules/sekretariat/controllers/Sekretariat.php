<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sekretariat extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $role = $this->session->userdata('role');
        if ($role !== 'sekretariat' && $role !== 'admin' && $role !== 'pokja') {
            redirect('auth');
        }

        if ($role === 'sekretariat') {
            $method = $this->router->fetch_method();
            $allowed_methods = [
                'input_pemenang',
                'input_pemenang_konsultansi',
                'simpan_pemenang',
                'check_bulk_duplicates',
                'edit_profil',
                'manajer_teknik',
                'manajer_teknik_save',
                'manajer_teknik_update',
                'manajer_keuangan',
                'manajer_keuangan_save',
                'manajer_keuangan_update'
            ];

            if (!in_array($method, $allowed_methods, true)) {
                if ($this->input->is_ajax_request()) {
                    $this->output->set_status_header(403);
                    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Sekretariat hanya dapat melakukan input.']);
                    exit;
                }
                redirect('sekretariat/input_pemenang');
            }
        }
        $this->load->model('Sekretariat_model');
        $this->load->model('admin/Tender_model', 'M_tender');
        $this->load->model('penyedia/Penyedia_model');
        
        // Load new models
        $this->load->model('Personel_lapangan_model');
        $this->load->model('Personel_k3_model');
        $this->load->model('Pemilik_alat_model');
        $this->load->model('Peralatan_model');
        $this->load->model('Regulasi_model');
    }

    public function index() {
        $data['companies'] = $this->Sekretariat_model->get_all_companies();
        $this->load->view('layout/header');
        $this->load->view('index', $data);
        $this->load->view('layout/footer');
    }

    public function create_company_process() {
        $data = [
            'nama_perusahaan' => $this->input->post('nama_perusahaan'),
            'alamat' => $this->input->post('alamat'),
            'email' => $this->input->post('email'),
            'telepon' => $this->input->post('telepon')
        ];
        $this->Sekretariat_model->create_company($data);
        $this->session->set_flashdata('success', 'Perusahaan berhasil ditambahkan.');
        redirect('sekretariat');
    }

    public function manage($penyedia_id) {
        $data['company'] = $this->Sekretariat_model->get_company_by_id($penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('manage_company', $data);
        $this->load->view('layout/footer');
    }

    // PERSONEL
    public function personel($penyedia_id) {
        $data['company'] = $this->Sekretariat_model->get_company_by_id($penyedia_id);
        $data['personel'] = $this->Penyedia_model->get_personel($penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('personel_managed', $data);
        $this->load->view('layout/footer');
    }

    public function personel_add($penyedia_id) {
        $config['upload_path'] = './assets/uploads/skk/';
        $config['allowed_types'] = 'pdf|jpg|png';
        $config['max_size'] = 2048;
        $this->load->library('upload', $config);
        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);

        $file_skk = '';
        if ($this->upload->do_upload('file_skk')) $file_skk = $this->upload->data('file_name');
        
        $file_sp = '';
        if ($this->upload->do_upload('file_surat_pernyataan')) $file_sp = $this->upload->data('file_name');

        $data = [
            'penyedia_id' => $penyedia_id,
            'nama' => $this->input->post('nama'),
            'nik' => $this->input->post('nik'),
            'jenis_skk' => $this->input->post('jenis_skk'),
            'nomor_skk' => $this->input->post('nomor_skk'),
            'jabatan' => $this->input->post('jabatan'),
            'pengalaman_tahun' => $this->input->post('pengalaman_tahun'),
            'file_skk' => $file_skk,
            'file_surat_pernyataan' => $file_sp
        ];

        $this->Penyedia_model->add_personel($data);
        redirect('sekretariat/personel/'.$penyedia_id);
    }

    // PERALATAN
    public function peralatan() {
        $penyedia_id = $this->input->get('penyedia_id');
        
        $data['peralatan'] = $this->Peralatan_model->get_all($penyedia_id);
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $data['pemilik_list'] = $this->Pemilik_alat_model->get_all();
        $data['selected_penyedia'] = $penyedia_id;
        
        $this->load->view('layout/header');
        $this->load->view('peralatan', $data);
        $this->load->view('layout/footer');
    }

    public function peralatan_delete($id) {
        $ok = $this->Peralatan_model->delete($id);

        if ($this->input->is_ajax_request()) {
            if ($ok) {
                echo json_encode(['status' => 'success', 'message' => 'Peralatan berhasil dihapus.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus peralatan.']);
            }
            return;
        }

        if ($ok) {
            $this->session->set_flashdata('success', 'Peralatan berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus peralatan.');
        }
        redirect($this->uri->segment(1).'/peralatan');
    }

    public function peralatan_add($penyedia_id) {
        // If coming from general list, penyedia_id is in POST
        if ($penyedia_id == 0) {
            $penyedia_id = $this->input->post('penyedia_id');
        }

        $config['upload_path'] = './assets/uploads/peralatan/';
        $config['allowed_types'] = 'pdf|jpg|png';
        $config['max_size'] = 2048;
        $this->load->library('upload', $config);
        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);

        $file_bukti = '';
        if ($this->upload->do_upload('file_bukti')) $file_bukti = $this->upload->data('file_name');
        
        $file_dok = '';
        if ($this->upload->do_upload('file_dokumentasi')) $file_dok = $this->upload->data('file_name');

        $data = [
            'penyedia_id' => $penyedia_id,
            'nama_alat' => $this->input->post('nama_alat'),
            'merk' => $this->input->post('merk'),
            'tipe' => $this->input->post('tipe'),
            'kapasitas' => $this->input->post('kapasitas'),
            'plat_serial' => $this->input->post('plat_serial'),
            'status_kepemilikan' => $this->input->post('status_kepemilikan') ?: 'Milik Sendiri',
            'bukti_kepemilikan' => $this->input->post('bukti_kepemilikan'),
            'file_bukti' => $file_bukti,
            'file_dokumentasi' => $file_dok
        ];

        if ($this->Penyedia_model->add_peralatan($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Peralatan berhasil ditambahkan.', 'penyedia_id' => $penyedia_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan peralatan.']);
        }
    }

    public function peralatan_update($id) {
        $file_bukti = $this->input->post('old_file_bukti');
        if ($this->upload->do_upload('file_bukti')) $file_bukti = $this->upload->data('file_name');
        
        $file_dok = $this->input->post('old_file_dokumentasi');
        if ($this->upload->do_upload('file_dokumentasi')) $file_dok = $this->upload->data('file_name');

        $data = [
            'nama_alat' => $this->input->post('nama_alat'),
            'merk' => $this->input->post('merk'),
            'tipe' => $this->input->post('tipe'),
            'kapasitas' => $this->input->post('kapasitas'),
            'plat_serial' => $this->input->post('plat_serial'),
            'status_kepemilikan' => $this->input->post('status_kepemilikan'),
            'file_bukti' => $file_bukti,
            'file_dokumentasi' => $file_dok
        ];

        if ($this->Peralatan_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Peralatan berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui peralatan.']);
        }
    }

    // TENDER
    public function data_tender() {
        $data['module'] = 'sekretariat';
        $data['user'] = $this->db->get_where('users', ['username' => $this->session->userdata('username')])->row_array();
        $this->load->view('layout/header');
        $this->load->view('data_tender', $data);
        $this->load->view('layout/footer');
    }

    public function detail($tender_id) {
        $this->load->model('admin/Tender_model');
        $detail = $this->Tender_model->get_detail_tender($tender_id);
        
        if (!$detail) {
            $this->session->set_flashdata('error', 'Data tidak ditemukan!');
            redirect('sekretariat/data_tender');
        }

        $data['tender'] = $detail['tender'];
        $data['manajer_teknik'] = $detail['manajer_teknik'];
        $data['manajer_keuangan'] = $detail['manajer_keuangan'];
        $data['personel_lapangan'] = $detail['personel_lapangan'];
        $data['personel_k3'] = $detail['personel_k3'];
        $data['peralatan'] = $detail['peralatan'];

        $this->load->view('layout/header');
        $this->load->view('pokja/detail_tender', $data);
        $this->load->view('layout/footer');
    }

    public function tender($penyedia_id) {
        $data['company'] = $this->Sekretariat_model->get_company_by_id($penyedia_id);
        $data['tender'] = $this->Penyedia_model->get_tenders($penyedia_id);
        $data['personel'] = $this->Penyedia_model->get_personel($penyedia_id);
        $data['peralatan'] = $this->Penyedia_model->get_peralatan($penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('tender_managed', $data);
        $this->load->view('layout/footer');
    }

    public function tender_add($penyedia_id) {
        $tender_data = [
            'penyedia_id' => $penyedia_id,
            'kode_tender' => $this->input->post('kode_tender'),
            'nama_tender' => $this->input->post('nama_tender')
        ];
        if ($this->Penyedia_model->add_tender($tender_data, $this->input->post('personel_ids'), $this->input->post('peralatan_ids'))) {
            echo json_encode(['status' => 'success', 'message' => 'Tender berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan tender.']);
        }
    }
    public function input_pemenang() {
        $this->load->view('layout/header');
        $this->load->view('sekretariat/input_pemenang');
        $this->load->view('layout/footer');
    }

    public function input_pemenang_konsultansi() {
        $this->load->view('layout/header');
        $data['jenis_tender'] = 'konsultansi';
        // Note: Gunakan duplicate/copy dari input_pemenang, tapi hapus elemen HTML Form Peralatan
        $this->load->view('sekretariat/input_pemenang', $data);
        $this->load->view('layout/footer');
    }

    public function edit_profil() {
        $username = $this->session->userdata('username');
        $user_data = $this->db->get_where('users', ['username' => $username])->row_array();
        $data['user'] = $user_data;

        if ($this->input->post()) {
            $update_data = [];

            // Update nama hanya jika diisi
            $nama = $this->input->post('nama', TRUE);
            if (!empty(trim($nama))) {
                $update_data['nama'] = html_escape($nama);
            }

            // 1. Eksekusi Password Checking
            if (!empty($this->input->post('password_lama'))) {
                $pass_lama = $this->input->post('password_lama');
                $pass_baru = $this->input->post('password_baru');

                if (password_verify($pass_lama, $user_data['password'])) {
                    $update_data['password'] = password_hash($pass_baru, PASSWORD_BCRYPT);
                } else {
                    $this->session->set_flashdata('error', 'Gagal: Password lama salah!');
                    redirect('sekretariat/edit_profil');
                    return;
                }
            }

            // 2. Eksekusi Upload Foto CI3
            if (!empty($_FILES['foto']['name'])) {
                $upload_path = realpath(APPPATH . '../assets/img/profile') . DIRECTORY_SEPARATOR;
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }

                $config['upload_path']   = $upload_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size']      = 2048; 
                $config['encrypt_name']  = TRUE; 

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('foto')) {
                    $old_file = $user_data['foto'] ?? '';
                    if ($old_file && $old_file != 'default.png' && file_exists($upload_path . $old_file)) {
                        unlink($upload_path . $old_file);
                    }
                    $update_data['foto'] = $this->upload->data('file_name');
                } else {
                    $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                    redirect('sekretariat/edit_profil');
                    return;
                }
            }

            // Jika tidak ada yang berubah, langsung redirect
            if (empty($update_data)) {
                $this->session->set_flashdata('error', 'Tidak ada perubahan yang disimpan.');
                redirect('sekretariat/edit_profil');
                return;
            }

            // 3. Proses Ke DB
            $this->db->where('username', $username)->update('users', $update_data);
            
            $this->session->set_flashdata('success', 'Profil Berhasil diubah');
            redirect('sekretariat/edit_profil');
            return;
        }

        $this->load->view('layout/header');
        $this->load->view('sekretariat/edit_profil', $data);
        $this->load->view('layout/footer');
    }

    public function simpan_pemenang() {
        $jenis_tender = $this->input->post('jenis_tender');
        $hps_input = $this->input->post('hps');
        $hps_val = str_replace(',', '.', str_replace('.', '', $hps_input));

        // 1. Find or Create Penyedia
        $nama_penyedia = $this->input->post('nama_penyedia');
        $penyedia = $this->db->get_where('penyedia', ['nama_perusahaan' => $nama_penyedia])->row();
        if ($penyedia) {
            $penyedia_id = $penyedia->id;
        } else {
            $this->db->insert('penyedia', [
                'nama_perusahaan' => $nama_penyedia,
                'created_by' => $this->session->userdata('username')
            ]);
            $penyedia_id = $this->db->insert_id();
        }

        $tender_data = [
            'penyedia_id' => $penyedia_id,
            'pemenang_tender' => $nama_penyedia,
            'kode_tender' => $this->input->post('kode_tender'),
            'satuan_kerja' => $this->input->post('satuan_kerja'),
            'judul_paket' => $this->input->post('judul_paket'),
            'nama_pokmil' => $this->input->post('nama_pokmil'),
            'tanggal_bahp' => $this->_normalize_date_internal($this->input->post('tanggal_bahp')),
            'hps' => $hps_val,
            'segmentasi' => $this->input->post('kualifikasi') ?: 'Kecil',
            'is_konsultansi' => ($this->input->post('jenis_tender') == 'konsultansi' ? 1 : 0),
            'tahun_anggaran' => $this->input->post('tahun_anggaran') ?: date('Y')
        ];

        // Looping Bersih Personel Lapangan
        $personel_lapangan = [];
        $raw_lapangan = $this->input->post('personel_lapangan');
        if (!empty($raw_lapangan) && is_array($raw_lapangan)) {
            foreach ($raw_lapangan as $p) {
                if (!empty(trim($p['nama'] ?? '')) && !empty(trim($p['nik'] ?? ''))) {
                    $personel_lapangan[] = $p;
                }
            }
        }

        // Looping Bersih Personel K3
        $personel_k3 = [];
        $raw_k3 = $this->input->post('personel_k3');
        if (!empty($raw_k3) && is_array($raw_k3)) {
            foreach ($raw_k3 as $pk) {
                if (!empty(trim($pk['nama'] ?? '')) && !empty(trim($pk['nik'] ?? ''))) {
                    $personel_k3[] = $pk;
                }
            }
        }

        // Looping Bersih Peralatan - NEW FLAT FORMAT
        // Format baru: peralatan[N][jenis_alat], peralatan[N][plat_serial], dll.
        // Setiap row = 1 unit alat, tidak ada lagi nested units.
        $peralatan = [];
        if ($jenis_tender !== 'konsultansi') {
            $raw_alat = $this->input->post('peralatan');
            if (!empty($raw_alat) && is_array($raw_alat)) {
                foreach ($raw_alat as $alat) {
                    $jenis = trim((string)($alat['jenis_alat'] ?? ''));
                    if ($jenis === '') continue; // Skip baris kosong
                    // Pastikan nama_alat selalu terisi (sinkron dengan jenis_alat)
                    $alat['nama_alat'] = $jenis;
                    $peralatan[] = $alat;
                }
            }
        }

        // Manajer Data
        $raw_mt = $this->input->post('manajer_teknik');
        $raw_mk = $this->input->post('manajer_keuangan');
        $manajer_teknik  = (!empty($raw_mt['nama']) && !empty($raw_mt['nik'])) ? $raw_mt : null;
        $manajer_keuangan = (!empty($raw_mk['nama']) && !empty($raw_mk['nik'])) ? $raw_mk : null;

        // Duplicate Check
        if ($this->input->post('force_save') !== '1') {
            $duplicates = $this->_get_bulk_duplicates_internal($personel_lapangan, $personel_k3, $peralatan, $tender_data['kode_tender'], $tender_data['tahun_anggaran']);
            if (!empty($duplicates)) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => 'duplicate', 
                    'duplicates' => $duplicates,
                    'csrfHash' => $this->security->get_csrf_hash()
                ]));
                return;
            }
        }

        // ── INSERT TENDER (wrapped dalam transaction)
        $this->db->trans_start();

        $tender_id = $this->M_tender->insert_tender($tender_data);

        if ($tender_id) {
            // ── SAVE MANAGERS
            // 1. Manajer Proyek (From first personnel in array)
            if (!empty($personel_lapangan)) {
                $mp = $personel_lapangan[0];
                $this->db->insert('manajer_proyek', [
                    'tender_id' => $tender_id,
                    'penyedia_id' => $penyedia_id,
                    'nama' => $mp['nama'],
                    'nik' => $mp['nik'],
                    'jenis_skk' => $mp['jenis_skk'] ?? null,
                    'nomor_skk' => $mp['nomor_skk'] ?? null,
                    'masa_berlaku_skk' => $this->_normalize_date_internal($mp['masa_berlaku_skk'] ?? null),
                    'created_by' => $this->session->userdata('username')
                ]);
                
                // Update legacy columns in tender table
                $this->db->where('id', $tender_id)->update('tender', [
                    'manajer_proyek' => $mp['nama'],
                    'nik_manajer_proyek' => $mp['nik']
                ]);
            }

            // 2. Manajer Teknik
            if ($manajer_teknik) {
                $this->db->insert('manajer_teknik', [
                    'tender_id' => $tender_id,
                    'penyedia_id' => $penyedia_id,
                    'nama' => $manajer_teknik['nama'],
                    'nik' => $manajer_teknik['nik'],
                    'jenis_skk' => $manajer_teknik['jenis_skk'] ?? null,
                    'nomor_skk' => $manajer_teknik['nomor_skk'] ?? null,
                    'masa_berlaku_skk' => $this->_normalize_date_internal($manajer_teknik['masa_berlaku_skk'] ?? null),
                    'created_by' => $this->session->userdata('username')
                ]);
                
                $this->db->where('id', $tender_id)->update('tender', [
                    'manajer_teknik' => $manajer_teknik['nama'],
                    'nik_manajer_teknik' => $manajer_teknik['nik']
                ]);
            }

            // 3. Manajer Keuangan
            if ($manajer_keuangan) {
                $this->db->insert('manajer_keuangan', [
                    'tender_id' => $tender_id,
                    'penyedia_id' => $penyedia_id,
                    'nama' => $manajer_keuangan['nama'],
                    'nik' => $manajer_keuangan['nik'],
                    'jenis_skk' => $manajer_keuangan['jenis_skk'] ?? null,
                    'nomor_skk' => $manajer_keuangan['nomor_skk'] ?? null,
                    'masa_berlaku_skk' => $this->_normalize_date_internal($manajer_keuangan['masa_berlaku_skk'] ?? null),
                    'created_by' => $this->session->userdata('username')
                ]);

                $this->db->where('id', $tender_id)->update('tender', [
                    'manajer_keuangan' => $manajer_keuangan['nama'],
                    'nik_manajer_keuangan' => $manajer_keuangan['nik']
                ]);
            }

            // 4. Personel K3
            if (!empty($personel_k3)) {
                foreach ($personel_k3 as $pk) {
                    $existing = $this->db->get_where('personel_k3', ['nik' => $pk['nik']])->row();
                    if ($existing) {
                        $p_id = $existing->id;
                    } else {
                        $this->db->insert('personel_k3', [
                            'penyedia_id' => $penyedia_id,
                            'nama' => $pk['nama'],
                            'nik' => $pk['nik'],
                            'jabatan_k3' => $pk['jabatan_k3'] ?? 'Ahli K3 Konstruksi',
                            'jenis_sertifikat_k3' => $pk['jenis_sertifikat_k3'] ?? null,
                            'nomor_sertifikat_k3' => $pk['nomor_sertifikat_k3'] ?? null,
                            'masa_berlaku_sertifikat' => $this->_normalize_date_internal($pk['masa_berlaku_sertifikat'] ?? null),
                            'created_by' => $this->session->userdata('username')
                        ]);
                        $p_id = $this->db->insert_id();
                    }
                    $this->db->insert('tender_personel_k3', ['tender_id' => $tender_id, 'personel_k3_id' => $p_id]);
                }
                
                // Update legacy columns
                $this->db->where('id', $tender_id)->update('tender', [
                    'ahli_k3' => $personel_k3[0]['nama'],
                    'nik_ahli_k3' => $personel_k3[0]['nik']
                ]);
            }

            // 5. Personel Lapangan (Pelaksana Lapangan)
            if (!empty($personel_lapangan)) {
                foreach ($personel_lapangan as $idx => $pl) {
                    $existing = $this->db->get_where('personel_lapangan', ['nik' => $pl['nik']])->row();
                    if ($existing) {
                        $p_id = $existing->id;
                    } else {
                        $jabatan_val = !empty($pl['jabatan']) ? $pl['jabatan'] : 'Pelaksana Lapangan';
                        $this->db->insert('personel_lapangan', [
                            'penyedia_id' => $penyedia_id,
                            'nama' => $pl['nama'],
                            'nik' => $pl['nik'],
                            'jabatan' => $jabatan_val,
                            'jenis_skk' => $pl['jenis_skk'] ?? null,
                            'nomor_skk' => $pl['nomor_skk'] ?? null,
                            'masa_berlaku_skk' => $this->_normalize_date_internal($pl['masa_berlaku_skk'] ?? null),
                            'created_by' => $this->session->userdata('username')
                        ]);
                        $p_id = $this->db->insert_id();
                    }
                    $this->db->insert('tender_personel_lapangan', ['tender_id' => $tender_id, 'personel_lapangan_id' => $p_id]);
                }
            }

            // 6. Peralatan - FLAT FORMAT (1 row = 1 unit alat)
            // Setiap elemen $peralatan sudah merupakan 1 unit alat dengan field langsung.
            if (!empty($peralatan)) {
                foreach ($peralatan as $alat) {
                    $jenis  = trim((string)($alat['jenis_alat'] ?? ''));
                    $nama   = trim((string)($alat['nama_alat'] ?? $jenis));
                    $plat   = trim((string)($alat['plat_serial'] ?? ''));

                    if ($jenis === '' && $nama === '') continue; // Baris benar-benar kosong

                    $peralatan_data = [
                        'penyedia_id'        => $penyedia_id,
                        'jenis_alat'         => $jenis ?: $nama,
                        'nama_alat'          => $nama ?: $jenis,
                        'merk'               => $alat['merk'] ?? null,
                        'tipe'               => $alat['tipe'] ?? null,
                        'kapasitas'          => $alat['kapasitas'] ?? null,
                        'plat_serial'        => $plat ?: null,
                        'status_kepemilikan' => $alat['status_kepemilikan'] ?? 'Milik Sendiri',
                        'nama_pemilik_alat'  => $alat['nama_pemilik_alat'] ?? null,
                        'bukti_kepemilikan'  => $alat['bukti_kepemilikan'] ?? null,
                        'created_by'         => $this->session->userdata('username')
                    ];

                    // Cek duplikasi berdasarkan plat_serial (jika ada)
                    if ($plat !== '') {
                        $existing_pl = $this->db->get_where('peralatan', ['plat_serial' => $plat])->row();
                        if ($existing_pl) {
                            $peralatan_id = $existing_pl->id;
                            // Update data yang ada
                            $this->db->where('id', $peralatan_id)->update('peralatan', $peralatan_data);
                        } else {
                            $this->db->insert('peralatan', $peralatan_data);
                            $peralatan_id = $this->db->insert_id();
                        }
                    } else {
                        // Tanpa plat_serial → selalu buat baru
                        $this->db->insert('peralatan', $peralatan_data);
                        $peralatan_id = $this->db->insert_id();
                    }

                    // Hubungkan ke tender (jumlah=1 per unit/row)
                    $this->db->insert('tender_peralatan', [
                        'tender_id'    => $tender_id,
                        'peralatan_id' => $peralatan_id,
                        'jumlah'       => 1
                    ]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan database. Data tidak tersimpan.',
                    'csrfHash' => $this->security->get_csrf_hash()
                ]));
                return;
            }

            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'Data Pemenang Tender Berhasil Disimpan.']));
            return;
        }

        $this->db->trans_rollback();
        $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data tender.']));
    }

    private function _normalize_date_internal($value) {
        if (!$value) return null;
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) return $value;
        $dt = DateTime::createFromFormat('d/m/Y', $value);
        return $dt ? $dt->format('Y-m-d') : null;
    }

    // ============================================
    // PERSONEL LAPANGAN METHODS
    // ============================================
    

    // ============================================
    // MANAJER TEKNIK METHODS
    // ============================================

    public function manajer_teknik() {
        $this->load->model('admin/Tender_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $data['manajer_list'] = $this->Tender_model->get_all_manajer_teknik($penyedia_id);
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $data['selected_penyedia'] = $penyedia_id;
        $data['module'] = 'sekretariat';
        $this->load->view('layout/header');
        $this->load->view('manajer_teknik', $data);
        $this->load->view('layout/footer');
    }

    private function _upload_dokumen($field_name) {
        if (empty($_FILES[$field_name]['name'])) return null;
        $upload_path = FCPATH . 'uploads/dokumen/';
        if (!is_dir($upload_path)) mkdir($upload_path, 0777, true);
        $config = [
            'upload_path'   => $upload_path,
            'allowed_types' => 'pdf|jpg|jpeg|png',
            'max_size'      => 2048,
            'encrypt_name'  => TRUE
        ];
        $this->load->library('upload', $config);
        if ($this->upload->do_upload($field_name)) {
            return $this->upload->data('file_name');
        }
        return null;
    }

    public function manajer_teknik_save() {
        $normalize = function($v) {
            if (!$v) return null;
            $v = trim((string)$v);
            if (!$v) return null;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;
            $dt = DateTime::createFromFormat('d/m/Y', $v);
            return $dt ? $dt->format('Y-m-d') : null;
        };
        $data = [
            'penyedia_id'     => $this->input->post('penyedia_id'),
            'nama'            => $this->input->post('nama'),
            'nik'             => $this->input->post('nik'),
            'jenis_skk'       => $this->input->post('jenis_skk'),
            'nomor_skk'       => $this->input->post('nomor_skk'),
            'masa_berlaku_skk'=> $normalize($this->input->post('masa_berlaku_skk')),
            'created_by'      => $this->session->userdata('username')
        ];
        $file_ktp = $this->_upload_dokumen('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen('file_skk');
        if ($file_skk) $data['file_skk'] = $file_skk;

        if ($this->db->insert('manajer_teknik', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Manajer Teknik berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan Manajer Teknik.']);
        }
    }

    public function manajer_teknik_update($id) {
        $normalize = function($v) {
            if (!$v) return null;
            $v = trim((string)$v);
            if (!$v) return null;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;
            $dt = DateTime::createFromFormat('d/m/Y', $v);
            return $dt ? $dt->format('Y-m-d') : null;
        };
        $data = [
            'penyedia_id'     => $this->input->post('penyedia_id'),
            'nama'            => $this->input->post('nama'),
            'nik'             => $this->input->post('nik'),
            'jenis_skk'       => $this->input->post('jenis_skk'),
            'nomor_skk'       => $this->input->post('nomor_skk'),
            'masa_berlaku_skk'=> $normalize($this->input->post('masa_berlaku_skk'))
        ];
        $file_ktp = $this->_upload_dokumen('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen('file_skk');
        if ($file_skk) $data['file_skk'] = $file_skk;

        if ($this->db->where('id', $id)->update('manajer_teknik', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Manajer Teknik berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui Manajer Teknik.']);
        }
    }

    // ============================================
    // MANAJER KEUANGAN METHODS
    // ============================================

    public function manajer_keuangan() {
        $this->load->model('admin/Tender_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $data['manajer_list'] = $this->Tender_model->get_all_manajer_keuangan($penyedia_id);
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $data['selected_penyedia'] = $penyedia_id;
        $data['module'] = 'sekretariat';
        $this->load->view('layout/header');
        $this->load->view('manajer_keuangan', $data);
        $this->load->view('layout/footer');
    }

    public function manajer_keuangan_save() {
        $normalize = function($v) {
            if (!$v) return null;
            $v = trim((string)$v);
            if (!$v) return null;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;
            $dt = DateTime::createFromFormat('d/m/Y', $v);
            return $dt ? $dt->format('Y-m-d') : null;
        };
        $data = [
            'penyedia_id'     => $this->input->post('penyedia_id'),
            'nama'            => $this->input->post('nama'),
            'nik'             => $this->input->post('nik'),
            'jenis_skk'       => $this->input->post('jenis_skk'),
            'nomor_skk'       => $this->input->post('nomor_skk'),
            'masa_berlaku_skk'=> $normalize($this->input->post('masa_berlaku_skk')),
            'created_by'      => $this->session->userdata('username')
        ];
        $file_ktp = $this->_upload_dokumen('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen('file_skk');
        if ($file_skk) $data['file_skk'] = $file_skk;

        if ($this->db->insert('manajer_keuangan', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Manajer Keuangan berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan Manajer Keuangan.']);
        }
    }

    public function manajer_keuangan_update($id) {
        $normalize = function($v) {
            if (!$v) return null;
            $v = trim((string)$v);
            if (!$v) return null;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;
            $dt = DateTime::createFromFormat('d/m/Y', $v);
            return $dt ? $dt->format('Y-m-d') : null;
        };
        $data = [
            'penyedia_id'     => $this->input->post('penyedia_id'),
            'nama'            => $this->input->post('nama'),
            'nik'             => $this->input->post('nik'),
            'jenis_skk'       => $this->input->post('jenis_skk'),
            'nomor_skk'       => $this->input->post('nomor_skk'),
            'masa_berlaku_skk'=> $normalize($this->input->post('masa_berlaku_skk'))
        ];
        $file_ktp = $this->_upload_dokumen('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen('file_skk');
        if ($file_skk) $data['file_skk'] = $file_skk;

        if ($this->db->where('id', $id)->update('manajer_keuangan', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Manajer Keuangan berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui Manajer Keuangan.']);
        }
    }

    // ============================================
    // AJAX FILTER DATA
    // ============================================

    public function get_filter_data() {
        // Tahun filter seharusnya mengikuti field `tender.tahun_anggaran`.
        // Fallback ke YEAR(tanggal_input) jika data lama belum punya tahun_anggaran konsisten.
        $years = $this->Sekretariat_model->get_available_years();
        if (empty($years)) {
            $years = $this->Sekretariat_model->get_all_years();
        }

        $data = [
            'penyedia_list' => $this->Sekretariat_model->get_all_companies(),
            'available_years' => $years
        ];
        echo json_encode($data);
    }
    
    // ============================================
    // PERSONEL LAPANGAN METHODS
    // ============================================
    
    public function personel_lapangan() {
        $data['module'] = 'sekretariat';
        $data['user'] = $this->db->get_where('users', ['username' => $this->session->userdata('username')])->row_array();
        $this->load->view('layout/header');
        $this->load->view('personel_lapangan', $data);
        $this->load->view('layout/footer');
    }

    public function personel_lapangan_save() {
        $data = [
            'penyedia_id' => $this->input->post('penyedia_id'),
            'nama' => $this->input->post('nama'),
            'nik' => $this->input->post('nik'),
            'jabatan' => $this->input->post('jabatan'),
            'jenis_skk' => $this->input->post('jenis_skk'),
            'nomor_skk' => $this->input->post('nomor_skk'),
            'masa_berlaku_skk' => $this->input->post('masa_berlaku_skk')
        ];

        $nik = trim((string) ($data['nik'] ?? ''));
        if ($nik !== '' && $this->Personel_lapangan_model->check_nik_exists($nik)) {
            echo json_encode(['status' => 'error', 'message' => 'Duplikasi data: NIK sudah terdaftar.']);
            return;
        }

        if ($this->Personel_lapangan_model->create($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Personel lapangan berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan personel lapangan.']);
        }
    }

    public function personel_lapangan_update($id) {
        $data = [
            'penyedia_id' => $this->input->post('penyedia_id'),
            'nama' => $this->input->post('nama'),
            'nik' => $this->input->post('nik'),
            'jabatan' => $this->input->post('jabatan'),
            'jenis_skk' => $this->input->post('jenis_skk'),
            'nomor_skk' => $this->input->post('nomor_skk'),
            'masa_berlaku_skk' => $this->input->post('masa_berlaku_skk')
        ];

        if ($this->Personel_lapangan_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Personel lapangan berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate personel lapangan.']);
        }
    }

    public function personel_lapangan_delete($id) {
        if ($this->Personel_lapangan_model->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Personel lapangan berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus personel lapangan.']);
        }
    }

    // ============================================
    // PERSONEL K3 METHODS
    // ============================================
    
    public function personel_k3() {
        $penyedia_id = $this->input->get('penyedia_id');
        $data['personel'] = $this->Personel_k3_model->get_with_expiry_check($penyedia_id);
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $data['selected_penyedia'] = $penyedia_id;

        $this->load->view('layout/header');
        $this->load->view('personel_k3', $data);
        $this->load->view('layout/footer');
    }

    public function personel_k3_save() {
        $data = [
            'penyedia_id' => $this->input->post('penyedia_id'),
            'nama' => $this->input->post('nama'),
            'nik' => $this->input->post('nik'),
            'jabatan_k3' => $this->input->post('jabatan_k3'),
            'jenis_sertifikat_k3' => $this->input->post('jenis_sertifikat_k3'),
            'nomor_sertifikat_k3' => $this->input->post('nomor_sertifikat_k3'),
            'masa_berlaku_sertifikat' => $this->input->post('masa_berlaku_sertifikat')
        ];

        $nik = trim((string) ($data['nik'] ?? ''));
        if ($nik !== '' && $this->Personel_k3_model->get_by_nik($nik)) {
            echo json_encode(['status' => 'error', 'message' => 'Duplikasi data: NIK sudah terdaftar.']);
            return;
        }

        if ($this->Personel_k3_model->create($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Personel K3 berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan personel K3.']);
        }
    }

    public function personel_k3_update($id) {
        $data = [
            'penyedia_id' => $this->input->post('penyedia_id'),
            'nama' => $this->input->post('nama'),
            'nik' => $this->input->post('nik'),
            'jabatan_k3' => $this->input->post('jabatan_k3'),
            'jenis_sertifikat_k3' => $this->input->post('jenis_sertifikat_k3'),
            'nomor_sertifikat_k3' => $this->input->post('nomor_sertifikat_k3'),
            'masa_berlaku_sertifikat' => $this->input->post('masa_berlaku_sertifikat')
        ];

        if ($this->Personel_k3_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Personel K3 berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate personel K3.']);
        }
    }

    public function personel_k3_delete($id) {
        if ($this->Personel_k3_model->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Personel K3 berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus personel K3.']);
        }
    }

    // ============================================
    // PEMILIK ALAT METHODS
    // ============================================
    
    public function pemilik_alat() {
        $data['module'] = 'sekretariat';
        $data['user'] = $this->db->get_where('users', ['username' => $this->session->userdata('username')])->row_array();
        $this->load->view('layout/header');
        $this->load->view('pemilik_alat', $data);
        $this->load->view('layout/footer');
    }

    public function pemilik_alat_save() {
        $data = [
            'nama_pemilik' => $this->input->post('nama_pemilik'),
            'jenis_pemilik' => $this->input->post('jenis_pemilik'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email')
        ];

        if ($this->Pemilik_alat_model->create($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Pemilik alat berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan pemilik alat.']);
        }
    }

    public function pemilik_alat_update($id) {
        $data = [
            'nama_pemilik' => $this->input->post('nama_pemilik'),
            'jenis_pemilik' => $this->input->post('jenis_pemilik'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email')
        ];

        if ($this->Pemilik_alat_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Pemilik alat berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate pemilik alat.']);
        }
    }

    public function pemilik_alat_delete($id) {
        if ($this->Pemilik_alat_model->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Pemilik alat berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus pemilik alat.']);
        }
    }

    public function pemilik_alat_detail_json() {
        $id = $this->input->get('id');
        $nama = $this->input->get('nama');

        $this->db->select('peralatan.jenis_alat, peralatan.nama_alat, peralatan.plat_serial, peralatan.merk, peralatan.tipe, peralatan.kapasitas');
        $this->db->from('peralatan');

        if (!empty($id)) {
            $this->db->where('peralatan.pemilik_alat_id', $id);
        } else {
            $nama = trim((string) $nama);
            if ($nama !== '') {
                $this->db->where('peralatan.nama_pemilik_alat', $nama);
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(['data' => []]));
                return;
            }
        }

        $this->db->order_by('peralatan.jenis_alat', 'ASC');
        $data = $this->db->get()->result();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['data' => $data]));
    }

    // ============================================
    // REGULASI METHODS
    // ============================================
    
    public function regulasi() {
        $data['module'] = 'sekretariat';
        $data['user'] = $this->db->get_where('users', ['username' => $this->session->userdata('username')])->row_array();
        $this->load->view('layout/header');
        $this->load->view('regulasi', $data);
        $this->load->view('layout/footer');
    }

    public function regulasi_save() {
        $data = [
            'instansi' => $this->input->post('instansi'),
            'jenis_regulasi' => $this->input->post('jenis_regulasi'),
            'nomor_regulasi' => $this->input->post('nomor_regulasi'),
            'tahun' => $this->input->post('tahun'),
            'judul' => $this->input->post('judul'),
            'tentang' => $this->input->post('tentang'),
            'status' => $this->input->post('status') ?: 'Berlaku'
        ];

        // Handle file upload
        if (!empty($_FILES['file_regulasi']['name'])) {
            $file_name = $this->Regulasi_model->upload_file('file_regulasi');
            if ($file_name) {
                $data['file_regulasi'] = $file_name;
            }
        }

        if ($this->Regulasi_model->create($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Regulasi berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan regulasi.']);
        }
    }

    public function regulasi_update($id) {
        $data = [
            'instansi' => $this->input->post('instansi'),
            'jenis_regulasi' => $this->input->post('jenis_regulasi'),
            'nomor_regulasi' => $this->input->post('nomor_regulasi'),
            'tahun' => $this->input->post('tahun'),
            'judul' => $this->input->post('judul'),
            'tentang' => $this->input->post('tentang'),
            'status' => $this->input->post('status') ?: 'Berlaku'
        ];

        // Handle file upload
        if (!empty($_FILES['file_regulasi']['name'])) {
            $file_name = $this->Regulasi_model->upload_file('file_regulasi');
            if ($file_name) {
                $data['file_regulasi'] = $file_name;
            }
        }

        if ($this->Regulasi_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Regulasi berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate regulasi.']);
        }
    }

    public function regulasi_delete($id) {
        if ($this->Regulasi_model->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Regulasi berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus regulasi.']);
        }
    }

    // AJAX DUPLICATE CHECKS
    public function check_duplicate_personel() {
        $nik = $this->input->post('nik');
        $tahun = $this->input->post('tahun');
        
        // Check in personel_lapangan
        $check_lapangan = $this->db->select('personel_lapangan.*, tender.kode_tender, tender.judul_paket, tender.tahun_anggaran, penyedia.nama_perusahaan, tender.satuan_kerja AS nama_tender')
                          ->from('tender_personel_lapangan')
                          ->join('personel_lapangan', 'personel_lapangan.id = tender_personel_lapangan.personel_lapangan_id')
                          ->join('tender', 'tender.id = tender_personel_lapangan.tender_id')
                          ->join('penyedia', 'penyedia.id = tender.penyedia_id')
                          ->where('personel_lapangan.nik', $nik)
                          ->where('tender.tahun_anggaran', $tahun)
                          ->get()->row();

        if ($check_lapangan) {
            echo json_encode([
                'status' => 'duplicate', 
                'message' => "NIK ini sudah terdaftar pada Paket: {$check_lapangan->nama_tender} (Nama: {$check_lapangan->nama})",
                'data' => $check_lapangan
            ]);
            return;
        }

        // Check in personel_k3
        $check_k3 = $this->db->select('personel_k3.*, tender.kode_tender, tender.judul_paket, tender.tahun_anggaran, penyedia.nama_perusahaan, tender.satuan_kerja AS nama_tender')
                          ->from('tender_personel_k3')
                          ->join('personel_k3', 'personel_k3.id = tender_personel_k3.personel_k3_id')
                          ->join('tender', 'tender.id = tender_personel_k3.tender_id')
                          ->join('penyedia', 'penyedia.id = tender.penyedia_id')
                          ->where('personel_k3.nik', $nik)
                          ->where('tender.tahun_anggaran', $tahun)
                          ->get()->row();

        if ($check_k3) {
            echo json_encode([
                'status' => 'duplicate', 
                'message' => "NIK ini sudah terdaftar pada Paket: {$check_k3->nama_tender} (Nama: {$check_k3->nama})",
                'data' => $check_k3
            ]);
        } else {
            echo json_encode(['status' => 'success']);
        }
    }

    public function get_regulasi_statistics() {
        $data = [
            'statistics' => $this->Regulasi_model->get_statistics(),
            'available_years' => $this->Regulasi_model->get_available_years()
        ];
        echo json_encode($data);
    }

    public function check_duplicate_equipment() {
        $plat = $this->input->post('plat');
        $tahun = $this->input->post('tahun');
        
        $check = $this->db->select('peralatan.*, tender.kode_tender, tender.judul_paket, tender.tahun_anggaran, penyedia.nama_perusahaan, tender.satuan_kerja AS nama_tender')
                          ->from('tender_peralatan')
                          ->join('peralatan', 'peralatan.id = tender_peralatan.peralatan_id')
                          ->join('tender', 'tender.id = tender_peralatan.tender_id')
                          ->join('penyedia', 'penyedia.id = tender.penyedia_id')
                          ->where('peralatan.plat_serial', $plat)
                          ->where('tender.tahun_anggaran', $tahun)
                          ->get()->row();

        if ($check) {
            echo json_encode([
                'status' => 'duplicate', 
                'message' => "Alat dengan No. Seri/Plat ini sudah terpakai di Paket: {$check->nama_tender}",
                'data' => $check
            ]);
        } else {
            echo json_encode(['status' => 'success']);
        }
    }


    public function check_bulk_duplicates() {
        $personel_lapangan = $this->input->post('personel_lapangan') ?: [];
        $personel_k3 = $this->input->post('personel_k3') ?: [];
        $peralatan = $this->input->post('peralatan') ?: [];
        
        // Handle flattened 'personel' from AJAX if needed, or just follow the same structure
        $personel_input = $this->input->post('personel');
        if (!empty($personel_input)) {
            // Mapping for real-time check which might send simple array
            $personel_lapangan = $personel_input;
        }

        $kode_tender = trim((string) $this->input->post('kode_tender'));
        $tahun = trim((string) $this->input->post('tahun'));
        
        $duplicates = $this->_get_bulk_duplicates_internal($personel_lapangan, $personel_k3, $peralatan, $kode_tender, $tahun);

        $payload = [
            'status' => !empty($duplicates) ? 'duplicate' : 'success',
            'duplicates' => $duplicates,
            'csrfHash' => $this->security->get_csrf_hash()
        ];

        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }

    private function _get_bulk_duplicates_internal($personel_lapangan, $personel_k3, $peralatan, $kode_tender, $tahun) {
        $duplicates = [];
        if (!$tahun) $tahun = date('Y');
        $kode_tender = trim((string)$kode_tender);

        // 0. Check Kode Tender Duplicate (Only if not empty)
        if ($kode_tender !== '') {
            $sql_tender = "SELECT t.*, p.nama_perusahaan 
                           FROM tender t 
                           JOIN penyedia p ON p.id = t.penyedia_id 
                           WHERE TRIM(t.kode_tender) = ? AND t.tahun_anggaran = ? LIMIT 1";
            $res_tender = $this->db->query($sql_tender, [$kode_tender, $tahun])->row();
            
            // Note: If we are UPDATING an existing tender, we'd need its ID to exclude it. 
            // But since this is primarily for 'input_pemenang' (creating new), we flag if it exists at all.
            if ($res_tender) {
                // If the user is on the same tender, it's not a "duplicate" in a blocking sense for the same record,
                // but usually this UI is for NEW entries. 
                $duplicates[] = ['type' => 'Kode Tender', 'identifier' => $kode_tender, 'detail' => $res_tender];
            }
        }

        // 1. Check Personel Lapangan (Check against BOTH Lapangan and K3 tables)
        if (!empty($personel_lapangan)) {
            foreach ($personel_lapangan as $p) {
                $nik = trim((string)($p['nik'] ?? ''));
                $skk = trim((string)($p['nomor_skk'] ?? ($p['no_skk'] ?? '')));
                if ($nik === '' && $skk === '') continue;

                // Check in Lapangan Table
                $sql = "SELECT pl.*, t.kode_tender, t.judul_paket, t.tahun_anggaran, p.nama_perusahaan,
                        (CASE WHEN (pl.nik = ? AND pl.nik != '') THEN 'NIK' ELSE 'SKK' END) as matched_by
                        FROM tender_personel_lapangan tpl
                        JOIN personel_lapangan pl ON pl.id = tpl.personel_lapangan_id
                        JOIN tender t ON t.id = tpl.tender_id
                        JOIN penyedia p ON p.id = t.penyedia_id
                        WHERE t.tahun_anggaran = ? ";
                $params = [$nik, $tahun];
                if ($kode_tender !== '') { $sql .= " AND t.kode_tender != ? "; $params[] = $kode_tender; }
                $sql .= " AND ( (TRIM(pl.nik) = ? AND pl.nik != '') OR (TRIM(pl.nomor_skk) = ? AND pl.nomor_skk != '') ) LIMIT 1";
                $params[] = $nik; $params[] = $skk;
                
                $query = $this->db->query($sql, $params);
                $res = $query->row();
                
                if ($res) {
                    $duplicates[] = [
                        'type' => 'Personel Lapangan', 
                        'identifier' => ($res->matched_by == 'NIK') ? $nik : $skk, 
                        'matched_by' => $res->matched_by,
                        'detail' => $res
                    ];
                    continue;
                }

                // Also check in K3 Table (Cross-check)
                $sql_k3 = "SELECT pk3.*, t.kode_tender, t.judul_paket, t.tahun_anggaran, p.nama_perusahaan,
                           (CASE WHEN (pk3.nik = ? AND pk3.nik != '') THEN 'NIK' ELSE 'Sertifikat' END) as matched_by
                           FROM tender_personel_k3 tpk
                           JOIN personel_k3 pk3 ON pk3.id = tpk.personel_k3_id
                           JOIN tender t ON t.id = tpk.tender_id
                           JOIN penyedia p ON p.id = t.penyedia_id
                           WHERE t.tahun_anggaran = ? ";
                $params_k3 = [$nik, $tahun];
                if ($kode_tender !== '') { $sql_k3 .= " AND t.kode_tender != ? "; $params_k3[] = $kode_tender; }
                $sql_k3 .= " AND ( (TRIM(pk3.nik) = ? AND pk3.nik != '') OR (TRIM(pk3.nomor_sertifikat_k3) = ? AND pk3.nomor_sertifikat_k3 != '') ) LIMIT 1";
                $params_k3[] = $nik; $params_k3[] = $skk;
                
                $query_k3 = $this->db->query($sql_k3, $params_k3);
                $res_k3 = $query_k3->row();

                if ($res_k3) {
                    $duplicates[] = [
                        'type' => 'Personel (Terdaftar sebagai K3)', 
                        'identifier' => ($res_k3->matched_by == 'NIK') ? $nik : $skk, 
                        'matched_by' => $res_k3->matched_by,
                        'detail' => $res_k3
                    ];
                }
            }
        }

        // 2. Check Personel K3 (Check against BOTH K3 and Lapangan tables)
        if (!empty($personel_k3)) {
            foreach ($personel_k3 as $pk) {
                $nik = trim((string)($pk['nik'] ?? ''));
                $skk = trim((string)($pk['nomor_sertifikat_k3'] ?? ($pk['no_skk'] ?? '')));
                if ($nik === '' && $skk === '') continue;

                // Check in K3 Table
                $sql = "SELECT pk3.*, t.kode_tender, t.judul_paket, t.tahun_anggaran, p.nama_perusahaan,
                        (CASE WHEN (pk3.nik = ? AND pk3.nik != '') THEN 'NIK' ELSE 'Sertifikat' END) as matched_by
                        FROM tender_personel_k3 tpk
                        JOIN personel_k3 pk3 ON pk3.id = tpk.personel_k3_id
                        JOIN tender t ON t.id = tpk.tender_id
                        JOIN penyedia p ON p.id = t.penyedia_id
                        WHERE t.tahun_anggaran = ? ";
                $params = [$nik, $tahun];
                if ($kode_tender !== '') { $sql .= " AND t.kode_tender != ? "; $params[] = $kode_tender; }
                $sql .= " AND ( (TRIM(pk3.nik) = ? AND pk3.nik != '') OR (TRIM(pk3.nomor_sertifikat_k3) = ? AND pk3.nomor_sertifikat_k3 != '') ) LIMIT 1";
                $params[] = $nik; $params[] = $skk;
                
                $query = $this->db->query($sql, $params);
                $res = $query->row();
                
                if ($res) {
                    $duplicates[] = [
                        'type' => 'Personel K3', 
                        'identifier' => ($res->matched_by == 'NIK') ? $nik : $skk, 
                        'matched_by' => $res->matched_by,
                        'detail' => $res
                    ];
                    continue;
                }

                // Also check in Lapangan Table (Cross-check)
                $sql_pl = "SELECT pl.*, t.kode_tender, t.judul_paket, t.tahun_anggaran, p.nama_perusahaan,
                           (CASE WHEN (pl.nik = ? AND pl.nik != '') THEN 'NIK' ELSE 'SKK' END) as matched_by
                           FROM tender_personel_lapangan tpl
                           JOIN personel_lapangan pl ON pl.id = tpl.personel_lapangan_id
                           JOIN tender t ON t.id = tpl.tender_id
                           JOIN penyedia p ON p.id = t.penyedia_id
                           WHERE t.tahun_anggaran = ? ";
                $params_pl = [$nik, $tahun];
                if ($kode_tender !== '') { $sql_pl .= " AND t.kode_tender != ? "; $params_pl[] = $kode_tender; }
                $sql_pl .= " AND ( (TRIM(pl.nik) = ? AND pl.nik != '') OR (TRIM(pl.nomor_skk) = ? AND pl.nomor_skk != '') ) LIMIT 1";
                $params_pl[] = $nik; $params_pl[] = $skk;
                
                $query_pl = $this->db->query($sql_pl, $params_pl);
                $res_pl = $query_pl->row();

                if ($res_pl) {
                    $duplicates[] = [
                        'type' => 'Personel (Terdaftar sebagai Lapangan)', 
                        'identifier' => ($res_pl->matched_by == 'NIK') ? $nik : $skk, 
                        'matched_by' => $res_pl->matched_by,
                        'detail' => $res_pl
                    ];
                }
            }
        }

        // 3. Check Peralatan
        if (!empty($peralatan)) {
            foreach ($peralatan as $al) {
                $units = $al['units'] ?? [$al];
                foreach ($units as $u) {
                    $plat = trim((string)($u['plat'] ?? ($u['plat_serial'] ?? '')));
                    if ($plat === '') continue;

                    $sql = "SELECT alt.plat_serial as plat, alt.*, t.kode_tender, t.judul_paket, t.tahun_anggaran, p.nama_perusahaan 
                            FROM tender_peralatan ta
                            JOIN peralatan alt ON alt.id = ta.peralatan_id
                            JOIN tender t ON t.id = ta.tender_id
                            JOIN penyedia p ON p.id = t.penyedia_id
                            WHERE t.tahun_anggaran = ? AND TRIM(alt.plat_serial) = ? ";
                    $params = [$tahun, $plat];

                    if ($kode_tender !== '') {
                        $sql .= " AND t.kode_tender != ? ";
                        $params[] = $kode_tender;
                    }
                    $sql .= " LIMIT 1";

                    $res = $this->db->query($sql, $params)->row();
                    if ($res) {
                        $duplicates[] = ['type' => 'Peralatan', 'identifier' => $plat, 'detail' => $res];
                    }
                }
            }
        }

        return $duplicates;
    }
    // ==========================================
    // AJAX JSON ENDPOINTS
    // ==========================================

    public function personel_lapangan_json() {
        $penyedia_id = $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        $data = $this->Personel_lapangan_model->get_all($penyedia_id, $tahun);
        echo json_encode(['data' => $data]);
    }

    public function personel_k3_json() {
        $penyedia_id = $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        // Note: checking expiry is separate logic, but for filtering main table created_at is used
        $data = $this->Personel_k3_model->get_all($penyedia_id, $tahun); 
        echo json_encode(['data' => $data]);
    }

    public function regulasi_json() {
        $tahun = $this->input->get('tahun');
        $data = $this->Regulasi_model->get_all($tahun);
        echo json_encode(['data' => $data]);
    }

    public function peralatan_json($penyedia_id = null) {
        $penyedia_id = $penyedia_id ?: $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        $data = $this->Peralatan_model->get_all($penyedia_id, $tahun);
        echo json_encode(['data' => $data]);
    }

    public function data_tender_json() {
        $penyedia_id = $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        $data = $this->Sekretariat_model->get_all_tenders($penyedia_id, $tahun);
        echo json_encode(['data' => $data]);
    }

    public function pemilik_alat_json() {
        $jenis = $this->input->get('jenis');
        $data = $this->Pemilik_alat_model->get_all_with_counts($jenis);
        echo json_encode(['data' => $data]);
    }

    public function get_resource_history() {
        $id = $this->input->get('id');
        $type = $this->input->get('type');
        
        $history = [];
        if ($type == 'personel_lapangan') {
            $history = $this->Sekretariat_model->get_personel_history($id);
        } elseif ($type == 'personel_k3') {
            $history = $this->db->select('t.kode_tender, t.satuan_kerja AS nama_tender, p.nama_perusahaan, t.tahun_anggaran, t.judul_paket')
                            ->from('tender_personel_k3 tpk')
                            ->join('tender t', 'tpk.tender_id = t.id')
                            ->join('penyedia p', 't.penyedia_id = p.id')
                            ->where('tpk.personel_k3_id', $id)
                            ->get()->result();
        } elseif ($type == 'peralatan') {
            $history = $this->Sekretariat_model->get_peralatan_history($id);
        }

        echo json_encode($history);
    }
}
