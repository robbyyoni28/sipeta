<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pokja extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $role = $this->session->userdata('role');
        if ($role !== 'pokja' && $role !== 'admin') {
            redirect('auth');
        }
        $this->load->model('Pokja_model');
        $this->load->model('sekretariat/Sekretariat_model');
        $this->load->model('penyedia/Penyedia_model');
        $this->load->model('sekretariat/Personel_lapangan_model');
        $this->load->model('sekretariat/Personel_k3_model');
        $this->load->model('sekretariat/Pemilik_alat_model');
        $this->load->model('sekretariat/Peralatan_model');
        $this->load->model('sekretariat/Regulasi_model');
    }

    public function index() {
        $keyword = $this->input->get('keyword');
        $tahun = $this->input->get('tahun');
        
        $data['tenders'] = $this->Pokja_model->search_tender($keyword, $tahun);
        $data['years'] = $this->Pokja_model->get_available_years();
        
        $this->load->view('layout/header');
        $this->load->view('pemeriksaan', $data);
        $this->load->view('layout/footer');
    }

    public function detail($tender_id) {
        $this->load->model('admin/M_Tender');
        $detail = $this->M_Tender->get_detail_tender($tender_id);
        
        if (!$detail) {
            $this->session->set_flashdata('error', 'Data tidak ditemukan!');
            redirect('pokja/data_tender');
        }

        $data['tender'] = $detail['tender'];
        $data['manajer_teknik'] = $detail['manajer_teknik'];
        $data['manajer_keuangan'] = $detail['manajer_keuangan'];
        $data['personel_lapangan'] = $detail['personel_lapangan'];
        $data['personel_k3'] = $detail['personel_k3'];
        $data['peralatan'] = $detail['peralatan'];

        $this->load->view('layout/header');
        $this->load->view('detail_tender', $data);
        $this->load->view('layout/footer');
    }

    // DATA ENTRY FOR POKJA
    public function daftar_perusahaan() {
        $data['companies'] = $this->Sekretariat_model->get_all_companies();
        $this->load->view('layout/header');
        $this->load->view('sekretariat/index', $data);
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
        redirect('pokja/daftar_perusahaan');
    }

    public function manage($penyedia_id) {
        $data['company'] = $this->Sekretariat_model->get_company_by_id($penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('sekretariat/manage_company', $data);
        $this->load->view('layout/footer');
    }

    public function personel($penyedia_id) {
        $data['company'] = $this->Sekretariat_model->get_company_by_id($penyedia_id);
        $data['personel'] = $this->Penyedia_model->get_personel($penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('sekretariat/personel_managed', $data);
        $this->load->view('layout/footer');
    }

    public function personel_add($penyedia_id) {
        $config['upload_path'] = './assets/uploads/skk/';
        $config['allowed_types'] = 'pdf|jpg|png';
        $config['max_size'] = 2048;
        $this->load->library('upload', $config);
        if (!is_dir($config['upload_path'])) @mkdir($config['upload_path'], 0777, true);

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
        redirect('pokja/personel/'.$penyedia_id);
    }

    public function peralatan($penyedia_id = null) {
        if ($penyedia_id) {
            $data['company'] = $this->Sekretariat_model->get_company_by_id($penyedia_id);
            $data['peralatan'] = $this->Penyedia_model->get_peralatan($penyedia_id);
            $this->load->view('layout/header');
            $this->load->view('sekretariat/peralatan_managed', $data);
            $this->load->view('layout/footer');
        } else {
            $data['peralatan'] = $this->Peralatan_model->get_all();
            $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
            $data['pemilik_list'] = $this->Pemilik_alat_model->get_all();
            
            $this->load->view('layout/header');
            $this->load->view('sekretariat/peralatan', $data);
            $this->load->view('layout/footer');
        }
    }

    public function peralatan_delete($id) {
        $this->Peralatan_model->delete($id);
        $this->session->set_flashdata('success', 'Peralatan berhasil dihapus.');
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
            $this->session->set_flashdata('success', 'Peralatan berhasil ditambahkan.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan peralatan.');
        }

        redirect($this->uri->segment(1).'/peralatan/'.$penyedia_id);
    }

    public function data_tender() {
        $data['tenders'] = $this->Sekretariat_model->get_all_tenders();
        $this->load->view('layout/header');
        $this->load->view('sekretariat/data_tender', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Endpoint untuk mengisi dropdown filter (dipakai oleh view `sekretariat/data_tender`)
     * Saat view diakses dari route `pokja/data_tender`, AJAX akan memanggil `pokja/get_filter_data`.
     */
    public function get_filter_data() {
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

    public function tender($penyedia_id) {
        $data['company'] = $this->Sekretariat_model->get_company_by_id($penyedia_id);
        $data['tender'] = $this->Penyedia_model->get_tenders($penyedia_id);
        $data['personel'] = $this->Penyedia_model->get_personel($penyedia_id);
        $data['peralatan'] = $this->Penyedia_model->get_peralatan($penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('sekretariat/tender_managed', $data);
        $this->load->view('layout/footer');
    }

    public function tender_add($penyedia_id) {
        $tender_data = [
            'penyedia_id' => $penyedia_id,
            'kode_tender' => $this->input->post('kode_tender'),
            'nama_tender' => $this->input->post('nama_tender'),
            'tahun_anggaran' => $this->input->post('tahun_anggaran')
        ];
        $this->Penyedia_model->add_tender($tender_data, $this->input->post('personel_ids'), $this->input->post('peralatan_ids'));
        redirect('pokja/tender/'.$penyedia_id);
    }
    public function input_pemenang() {
        $this->load->view('layout/header');
        $this->load->view('sekretariat/input_pemenang'); // Reusing view
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
                    redirect('pokja/edit_profil');
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
                    redirect('pokja/edit_profil');
                    return;
                }
            }

            if (empty($update_data)) {
                $this->session->set_flashdata('error', 'Tidak ada perubahan yang disimpan.');
                redirect('pokja/edit_profil');
                return;
            }

            // 3. Proses Ke DB
            $this->db->where('username', $username)->update('users', $update_data);
            
            $this->session->set_flashdata('success', 'Profil Berhasil diubah');
            redirect('pokja/edit_profil');
            return;
        }

        $this->load->view('layout/header');
        $this->load->view('pokja/edit_profil', $data);
        $this->load->view('layout/footer');
    }

    public function get_resource_history() {
        $id = $this->input->get('id');
        $type = $this->input->get('type');
        
        $history = [];
        if ($type == 'personel_lapangan') {
            $history = $this->Sekretariat_model->get_personel_history($id);
        } elseif ($type == 'personel_k3') {
            $history = $this->Sekretariat_model->get_personel_k3_history($id);
        } elseif ($type == 'peralatan') {
            $history = $this->Sekretariat_model->get_peralatan_history($id);
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($history));
    }

    public function check_bulk_duplicates() {
        $personel_lapangan = $this->input->post('personel_lapangan') ?: [];
        $personel_k3 = $this->input->post('personel_k3') ?: [];
        $peralatan = $this->input->post('peralatan') ?: [];
        $personel_input = $this->input->post('personel');
        if (!empty($personel_input)) $personel_lapangan = $personel_input;

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

    public function simpan_pemenang() {
        $jenis_tender = $this->input->post('jenis_tender');
        $hps_input = $this->input->post('hps');

        $tender_data = [
            'nama_penyedia' => $this->input->post('nama_penyedia'),
            'kode_tender' => $this->input->post('kode_tender'),
            'satuan_kerja' => $this->input->post('satuan_kerja'),
            'judul_paket' => $this->input->post('judul_paket'),
            'nama_pokmil' => $this->input->post('nama_pokmil'),
            'tanggal_bahp' => $this->input->post('tanggal_bahp'),
            'hps' => str_replace(',', '.', str_replace('.', '', $hps_input)),
            'kualifikasi' => $this->input->post('kualifikasi'),
            'tahun_anggaran' => $this->input->post('tahun_anggaran') ? $this->input->post('tahun_anggaran') : date('Y')
        ];

        // Looping Bersih Personel Lapangan
        $personel_lapangan = [];
        $raw_lapangan = $this->input->post('personel_lapangan');
        if (!empty($raw_lapangan) && is_array($raw_lapangan)) {
            foreach ($raw_lapangan as $p) {
                if (!empty(trim($p['nama'])) && !empty(trim($p['nik']))) {
                    $personel_lapangan[] = $p;
                }
            }
        }

        // Looping Bersih Personel K3
        $personel_k3 = [];
        $raw_k3 = $this->input->post('personel_k3');
        if (!empty($raw_k3) && is_array($raw_k3)) {
            foreach ($raw_k3 as $pk) {
                if (!empty(trim($pk['nama'])) && !empty(trim($pk['nik']))) {
                    $personel_k3[] = $pk;
                }
            }
        }

        // Looping Bersih Peralatan
        $peralatan = [];
        if ($jenis_tender !== 'konsultansi') {
            $raw_alat = $this->input->post('peralatan');
            if (!empty($raw_alat) && is_array($raw_alat)) {
                foreach ($raw_alat as $alat) {
                    if (!empty(trim($alat['jenis_alat'] ?? ''))) {
                        $peralatan[] = $alat;
                    }
                }
            }
        }

        // ── Manajer Teknik & Keuangan dari POST terpisah (bukan dari personel_lapangan)
        $raw_mt = $this->input->post('manajer_teknik');
        $raw_mk = $this->input->post('manajer_keuangan');
        $manajer_teknik  = (!empty($raw_mt['nama']) && !empty($raw_mt['nik'])) ? $raw_mt : null;
        $manajer_keuangan = (!empty($raw_mk['nama']) && !empty($raw_mk['nik'])) ? $raw_mk : null;

        // ── Isi kolom referensi di tabel tender
        $tender_data['manajer_teknik']       = $manajer_teknik['nama']  ?? null;
        $tender_data['nik_manajer_teknik']   = $manajer_teknik['nik']   ?? null;
        $tender_data['manajer_keuangan']     = $manajer_keuangan['nama'] ?? null;
        $tender_data['nik_manajer_keuangan'] = $manajer_keuangan['nik']  ?? null;

        // ── Personel Lapangan dari personel_lapangan[0] disimpan di kolom legacy manajer_proyek
        $tender_data['manajer_proyek']     = $personel_lapangan[0]['nama'] ?? null;
        $tender_data['nik_manajer_proyek'] = $personel_lapangan[0]['nik']  ?? null;
        $tender_data['ahli_k3']     = $personel_k3[0]['nama'] ?? null;
        $tender_data['nik_ahli_k3'] = $personel_k3[0]['nik']  ?? null;

        $force_save = $this->input->post('force_save') === '1';
        if (!$force_save) {
            $duplicates = $this->_get_bulk_duplicates_internal($personel_lapangan, $personel_k3, $peralatan, $tender_data['kode_tender'], $tender_data['tahun_anggaran']);
            if (!empty($duplicates)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'duplicate', 
                        'duplicates' => $duplicates,
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]));
                return;
            }
        }

        if ($this->Sekretariat_model->save_winner_package($tender_data, $personel_lapangan, $personel_k3, $peralatan, $manajer_teknik, $manajer_keuangan)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Paket Pemenang Berhasil Disimpan.']));
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data pemenang.']));
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
            
            if ($res_tender) {
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
    public function cari_personel() {
        $keyword = $this->input->get('keyword');
        $data['personel'] = $this->Pokja_model->search_personel($keyword);
        
        $this->load->view('layout/header');
        $this->load->view('cari_personel', $data);
        $this->load->view('layout/footer');
    }

    public function cari_peralatan() {
        $keyword = $this->input->get('keyword');
        $data['peralatan'] = $this->Pokja_model->search_peralatan($keyword);
        
        $this->load->view('layout/header');
        $this->load->view('cari_peralatan', $data);
        $this->load->view('layout/footer');
    }

    public function detail_personel($id) {
        $data['personel'] = $this->Pokja_model->get_personel_by_id($id);
        $data['history'] = $this->Pokja_model->get_personel_history($id);

        $this->load->view('layout/header');
        $this->load->view('detail_personel', $data);
        $this->load->view('layout/footer');
    }

    public function detail_peralatan($id) {
        $data['peralatan'] = $this->Pokja_model->get_peralatan_by_id($id);
        $data['history'] = $this->Pokja_model->get_peralatan_history($id);

        $this->load->view('layout/header');
        $this->load->view('detail_peralatan', $data);
        $this->load->view('layout/footer');
    }

    // ============================================
    // MANAJER TEKNIK & KEUANGAN METHODS
    // ============================================

    private function _upload_dokumen_pokja($field_name) {
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

    public function manajer_teknik() {
        $this->load->model('admin/M_Tender');
        $penyedia_id = $this->input->get('penyedia_id');
        $data['manajer_list'] = $this->M_Tender->get_all_manajer_teknik($penyedia_id);
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $data['selected_penyedia'] = $penyedia_id;
        $this->load->view('layout/header');
        $this->load->view('sekretariat/manajer_teknik', $data);
        $this->load->view('layout/footer');
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
        $file_ktp = $this->_upload_dokumen_pokja('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen_pokja('file_skk');
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
        $file_ktp = $this->_upload_dokumen_pokja('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen_pokja('file_skk');
        if ($file_skk) $data['file_skk'] = $file_skk;

        if ($this->db->where('id', $id)->update('manajer_teknik', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Manajer Teknik berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui Manajer Teknik.']);
        }
    }

    public function manajer_keuangan() {
        $this->load->model('admin/M_Tender');
        $penyedia_id = $this->input->get('penyedia_id');
        $data['manajer_list'] = $this->M_Tender->get_all_manajer_keuangan($penyedia_id);
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $data['selected_penyedia'] = $penyedia_id;
        $this->load->view('layout/header');
        $this->load->view('sekretariat/manajer_keuangan', $data);
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
        $file_ktp = $this->_upload_dokumen_pokja('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen_pokja('file_skk');
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
        $file_ktp = $this->_upload_dokumen_pokja('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen_pokja('file_skk');
        if ($file_skk) $data['file_skk'] = $file_skk;

        if ($this->db->where('id', $id)->update('manajer_keuangan', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Manajer Keuangan berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui Manajer Keuangan.']);
        }
    }

    // ============================================
    // NEW MANAGEMENT METHODS (PARITY WITH SEKRETARIAT)
    // ============================================
    
    public function personel_lapangan() {
        $penyedia_id = $this->input->get('penyedia_id');
        $data['personel'] = $this->Personel_lapangan_model->get_all($penyedia_id);
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $data['selected_penyedia'] = $penyedia_id;
        
        $this->load->view('layout/header');
        $this->load->view('sekretariat/personel_lapangan', $data);
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
            'pengalaman_tahun' => $this->input->post('pengalaman_tahun')
        ];
        $this->Personel_lapangan_model->create($data);
        $this->session->set_flashdata('success', 'Data Personel Lapangan berhasil ditambahkan.');
        redirect($this->uri->segment(1).'/personel_lapangan');
    }

    public function personel_k3() {
        $penyedia_id = $this->input->get('penyedia_id');
        $data['personel'] = $this->Personel_k3_model->get_with_expiry_check($penyedia_id);
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $data['selected_penyedia'] = $penyedia_id;

        $this->load->view('layout/header');
        $this->load->view('sekretariat/personel_k3', $data);
        $this->load->view('layout/footer');
    }

    public function pemilik_alat() {
        $data['pemilik_alat'] = $this->Pemilik_alat_model->get_all_with_counts();
        $this->load->view('layout/header');
        $this->load->view('sekretariat/pemilik_alat', $data);
        $this->load->view('layout/footer');
    }

    public function regulasi() {
        $tahun = $this->input->get('tahun');
        $data['regulasi'] = $this->Regulasi_model->get_all($tahun);
        $data['available_years'] = $this->Regulasi_model->get_available_years();
        $data['statistics'] = $this->Regulasi_model->get_statistics();
        
        $this->load->view('layout/header');
        $this->load->view('sekretariat/regulasi', $data);
        $this->load->view('layout/footer');
    }

    public function personel_lapangan_update($id) {
        $data = [
            'penyedia_id' => $this->input->post('penyedia_id'),
            'nama' => $this->input->post('nama'),
            'nik' => $this->input->post('nik'),
            'jabatan' => $this->input->post('jabatan'),
            'jenis_skk' => $this->input->post('jenis_skk'),
            'nomor_skk' => $this->input->post('nomor_skk'),
            'pengalaman_tahun' => $this->input->post('pengalaman_tahun')
        ];
        $this->Personel_lapangan_model->update($id, $data);
        $this->session->set_flashdata('success', 'Data Personel Lapangan berhasil diupdate.');
        redirect('pokja/personel_lapangan');
    }

    public function personel_lapangan_delete($id) {
        $this->Personel_lapangan_model->delete($id);
        $this->session->set_flashdata('success', 'Data Personel Lapangan berhasil dihapus.');
        redirect('pokja/personel_lapangan');
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
        $this->Personel_k3_model->create($data);
        $this->session->set_flashdata('success', 'Data Personel K3 berhasil ditambahkan.');
        redirect('pokja/personel_k3');
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
        $this->Personel_k3_model->update($id, $data);
        $this->session->set_flashdata('success', 'Data Personel K3 berhasil diupdate.');
        redirect('pokja/personel_k3');
    }

    public function personel_k3_delete($id) {
        $this->Personel_k3_model->delete($id);
        $this->session->set_flashdata('success', 'Data Personel K3 berhasil dihapus.');
        redirect('pokja/personel_k3');
    }

    public function pemilik_alat_save() {
        $data = [
            'nama_pemilik' => $this->input->post('nama_pemilik'),
            'jenis_pemilik' => $this->input->post('jenis_pemilik'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email')
        ];
        $this->Pemilik_alat_model->create($data);
        $this->session->set_flashdata('success', 'Data Pemilik Alat berhasil ditambahkan.');
        redirect('pokja/pemilik_alat');
    }

    public function pemilik_alat_update($id) {
        $data = [
            'nama_pemilik' => $this->input->post('nama_pemilik'),
            'jenis_pemilik' => $this->input->post('jenis_pemilik'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email')
        ];
        $this->Pemilik_alat_model->update($id, $data);
        $this->session->set_flashdata('success', 'Data Pemilik Alat berhasil diupdate.');
        redirect('pokja/pemilik_alat');
    }

    public function pemilik_alat_delete($id) {
        $this->Pemilik_alat_model->delete($id);
        $this->session->set_flashdata('success', 'Data Pemilik Alat berhasil dihapus.');
        redirect('pokja/pemilik_alat');
    }

    public function regulasi_save() {
        $config['upload_path'] = './uploads/regulasi/';
        $config['allowed_types'] = 'pdf';
        $config['max_size'] = 10240;
        $this->load->library('upload', $config);
        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);

        $file_name = '';
        if ($this->upload->do_upload('file_regulasi')) {
            $file_name = $this->upload->data('file_name');
        }

        $data = [
            'jenis_regulasi' => $this->input->post('jenis_regulasi'),
            'nomor_regulasi' => $this->input->post('nomor_regulasi'),
            'tahun' => $this->input->post('tahun'),
            'judul' => $this->input->post('judul'),
            'tentang' => $this->input->post('tentang'),
            'tanggal_ditetapkan' => $this->input->post('tanggal_ditetapkan'),
            'status' => $this->input->post('status'),
            'file_regulasi' => $file_name
        ];
        $this->Regulasi_model->create($data);
        $this->session->set_flashdata('success', 'Data Regulasi berhasil ditambahkan.');
        redirect('pokja/regulasi');
    }

    public function regulasi_update($id) {
        $data = [
            'jenis_regulasi' => $this->input->post('jenis_regulasi'),
            'nomor_regulasi' => $this->input->post('nomor_regulasi'),
            'tahun' => $this->input->post('tahun'),
            'judul' => $this->input->post('judul'),
            'tentang' => $this->input->post('tentang'),
            'tanggal_ditetapkan' => $this->input->post('tanggal_ditetapkan'),
            'status' => $this->input->post('status')
        ];

        $config['upload_path'] = './uploads/regulasi/';
        $config['allowed_types'] = 'pdf';
        $config['max_size'] = 10240;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file_regulasi')) {
            $file_name = $this->upload->data('file_name');
            $data['file_regulasi'] = $file_name;
        }

        $this->Regulasi_model->update($id, $data);
        $this->session->set_flashdata('success', 'Data Regulasi berhasil diupdate.');
        redirect('pokja/regulasi');
    }

    public function regulasi_delete($id) {
        $this->Regulasi_model->delete($id);
        $this->session->set_flashdata('success', 'Data Regulasi berhasil dihapus.');
        redirect('pokja/regulasi');
    }

    // ==========================================
    // AJAX JSON ENDPOINTS
    // ==========================================

    public function personel_lapangan_json() {
        $penyedia_id = $this->input->get('penyedia_id');
        $keyword = $this->input->get('keyword');
        
        if ($keyword) {
            $data = $this->Personel_lapangan_model->search($keyword, $penyedia_id);
            // Add nama_perusahaan if missing from search results
            foreach($data as $d) {
                if(!isset($d->nama_perusahaan)) {
                    $p = $this->Penyedia_model->get_by_id($d->penyedia_id);
                    $d->nama_perusahaan = $p ? $p->nama_perusahaan : '-';
                }
            }
        } else {
            $data = $this->Personel_lapangan_model->get_all($penyedia_id);
        }
        echo json_encode(['data' => $data]);
    }

    public function personel_k3_json() {
        $penyedia_id = $this->input->get('penyedia_id');
        $keyword = $this->input->get('keyword');
        
        if ($keyword) {
            $data = $this->Personel_k3_model->search($keyword, $penyedia_id);
            // Add nama_perusahaan if missing from search results
            foreach($data as $d) {
                if(!isset($d->nama_perusahaan)) {
                    $p = $this->Penyedia_model->get_by_id($d->penyedia_id);
                    $d->nama_perusahaan = $p ? $p->nama_perusahaan : '-';
                }
            }
        } else {
            $data = $this->Personel_k3_model->get_with_expiry_check($penyedia_id);
        }
        echo json_encode(['data' => $data]);
    }

    public function peralatan_json($penyedia_id = null) {
        $penyedia_id = $penyedia_id ?: $this->input->get('penyedia_id');
        $keyword = $this->input->get('keyword');
        
        if ($keyword) {
            $data = $this->Peralatan_model->search($keyword);
        } else {
            $data = $this->Peralatan_model->get_all($penyedia_id);
        }
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

        // Monitoring pemilik alat untuk pokja berasal dari input pemenang tender,
        // yang tersimpan pada tabel peralatan (nama_pemilik_alat), bukan dari tabel master pemilik_alat.
        $this->db->select("peralatan.nama_pemilik_alat as nama_pemilik, 'Perusahaan' as jenis_pemilik, NULL as alamat, NULL as telepon, NULL as email, COUNT(peralatan.id) as jumlah_alat, GROUP_CONCAT(DISTINCT CONCAT(IFNULL(peralatan.jenis_alat, IFNULL(peralatan.nama_alat, 'Alat')), IF(peralatan.plat_serial IS NULL OR peralatan.plat_serial = '', '', CONCAT(' [', peralatan.plat_serial, ']'))) ORDER BY peralatan.jenis_alat SEPARATOR ', ') as detail", false);
        $this->db->from('peralatan');
        $this->db->where('peralatan.nama_pemilik_alat IS NOT NULL', null, false);
        $this->db->where("peralatan.nama_pemilik_alat != ''", null, false);

        if (!empty($jenis)) {
            // Jenis pemilik tidak disimpan pada peralatan; gunakan placeholder konsisten.
            $this->db->where("'Perusahaan' = ".$this->db->escape($jenis), null, false);
        }

        $this->db->group_by('peralatan.nama_pemilik_alat');
        $this->db->order_by('peralatan.nama_pemilik_alat', 'ASC');
        $data = $this->db->get()->result();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['data' => $data]));
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

    public function regulasi_json() {
        $tahun = $this->input->get('tahun');
        $data = $this->Regulasi_model->get_all($tahun);
        echo json_encode(['data' => $data]);
    }

    // AJAX PROCESSORS
    public function tender_save_ajax() {
        $tender_data = [
            'penyedia_id' => $this->input->post('penyedia_id'),
            'kode_tender' => $this->input->post('kode_tender'),
            'nama_tender' => $this->input->post('nama_tender'),
            'tahun_anggaran' => $this->input->post('tahun_anggaran')
        ];
        
        $personel_ids = $this->input->post('personel_ids');
        $peralatan_ids = $this->input->post('peralatan_ids');

        if ($this->Penyedia_model->add_tender($tender_data, $personel_ids, $peralatan_ids)) {
            echo json_encode(['status' => 'success', 'message' => 'Data Tender berhasil disimpan!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data tender.']);
        }
    }
}
