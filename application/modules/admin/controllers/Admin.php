<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MX_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('role') !== 'admin') {
            redirect('auth');
        }
        $this->load->model('Admin_model');
    }

    public function index() {
        $data['total_penyedia'] = $this->db->where('role', 'penyedia')->count_all_results('users');
        $data['total_personel'] = $this->db->count_all('personel_lapangan');
        $data['total_peralatan'] = $this->db->count_all('peralatan');
        $data['total_tender'] = $this->db->count_all('tender');
        
        // Riwayat input paket terbaru (sebagai notifikasi aktivitas)
        // nama_tender di DB sudah diganti menjadi satuan_kerja, kita alias agar kompatibel dengan view
        $this->db->select('tender.kode_tender, tender.satuan_kerja AS nama_tender, tender.tahun_anggaran, tender.created_by, tender.tanggal_input, u.role as created_role');
        $this->db->from('tender');
        $this->db->join('users u', 'u.username = tender.created_by', 'left');
        $this->db->order_by('tender.tanggal_input', 'DESC');
        $this->db->limit(10);
        $data['recent_tenders'] = $this->db->get()->result();
        
        $this->load->view('layout/header');
        $this->load->view('dashboard', $data);
        $this->load->view('layout/footer');
    }

    public function verifikasi_penyedia() {
        $data['penyedia'] = $this->Admin_model->get_pending_penyedia();
        $this->load->view('layout/header');
        $this->load->view('verifikasi_penyedia', $data);
        $this->load->view('layout/footer');
    }

    // --- INTEGRATED POKJA & SEKRETARIAT FEATURES ---
    
    public function data_tender() {
        $this->load->model('pokja/Pokja_model');
        $data['years'] = $this->Pokja_model->get_available_years();
        $this->load->view('layout/header');
        $this->load->view('pokja/pemeriksaan', $data); // Using Pokja's audit view
        $this->load->view('layout/footer');
    }

    public function detail($tender_id) {
        $this->load->model('sekretariat/Sekretariat_model');
        $data = $this->Sekretariat_model->get_tender_detail($tender_id);
        
        foreach ($data['personel'] as &$p) {
            $p->history = $this->Sekretariat_model->get_personel_history($p->id);
        }
        foreach ($data['peralatan'] as &$pl) {
            $pl->history = $this->Sekretariat_model->get_peralatan_history($pl->id);
        }

        $this->load->view('layout/header');
        $this->load->view('pokja/detail_tender', $data);
        $this->load->view('layout/footer');
    }

    public function tender_delete($id) {
        $this->db->trans_start();
        $this->db->where('tender_id', $id)->delete('tender_personel_lapangan');
        $this->db->where('tender_id', $id)->delete('tender_personel_k3');
        $this->db->where('tender_id', $id)->delete('tender_peralatan');
        $this->db->where('id', $id)->delete('tender');
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->session->set_flashdata('success', 'Data Tender berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data tender.');
        }
        redirect('admin/data_tender');
    }

    public function edit_tender($id) {
        $this->load->model('sekretariat/Sekretariat_model');
        $tender = $this->db->select('tender.*, penyedia.nama_perusahaan')
                           ->from('tender')
                           ->join('penyedia', 'penyedia.id = tender.penyedia_id', 'left')
                           ->where('tender.id', $id)
                           ->get()->row();

        if (!$tender) {
            $this->session->set_flashdata('error', 'Data tender tidak ditemukan.');
            redirect('admin/data_tender');
            return;
        }

        $data['tender'] = $tender;
        $this->load->view('layout/header');
        $this->load->view('admin/edit_tender', $data);
        $this->load->view('layout/footer');
    }

    public function update_tender($id) {
        $this->load->model('sekretariat/Sekretariat_model');

        // Update penyedia name if changed
        $nama_penyedia = $this->input->post('nama_penyedia');
        $tender_row = $this->db->get_where('tender', ['id' => $id])->row();
        if ($tender_row) {
            $this->db->where('id', $tender_row->penyedia_id)
                     ->update('penyedia', ['nama_perusahaan' => $nama_penyedia]);
        }

        // Parse HPS (remove thousand separators)
        $hps_raw = str_replace(['.', ','], ['', '.'], $this->input->post('hps'));

        // Parse date
        $tanggal_bahp_raw = $this->input->post('tanggal_bahp');
        $tanggal_bahp = null;
        if (!empty($tanggal_bahp_raw)) {
            $dt = DateTime::createFromFormat('d/m/Y', $tanggal_bahp_raw);
            if ($dt) $tanggal_bahp = $dt->format('Y-m-d');
        }

        $update_data = [
            'satuan_kerja'       => $this->input->post('satuan_kerja'),
            'judul_paket'        => $this->input->post('judul_paket'),
            'nama_pokmil'        => $this->input->post('nama_pokmil'),
            'kode_tender'        => $this->input->post('kode_tender'),
            'tanggal_bahp'       => $tanggal_bahp,
            'tahun_anggaran'     => $this->input->post('tahun_anggaran'),
            'hps'                => $hps_raw,
            'segmentasi'         => $this->input->post('kualifikasi'),
            'pemenang_tender'    => $nama_penyedia,
            // Manajer
            'manajer_proyek'     => $this->input->post('manajer_proyek'),
            'nik_manajer_proyek' => $this->input->post('nik_manajer_proyek'),
            'manajer_teknik'     => $this->input->post('manajer_teknik'),
            'nik_manajer_teknik' => $this->input->post('nik_manajer_teknik'),
            'manajer_keuangan'   => $this->input->post('manajer_keuangan'),
            'nik_manajer_keuangan'=> $this->input->post('nik_manajer_keuangan'),
            'ahli_k3'            => $this->input->post('ahli_k3'),
            'nik_ahli_k3'        => $this->input->post('nik_ahli_k3'),
        ];

        $this->db->where('id', $id)->update('tender', $update_data);

        if ($this->db->affected_rows() >= 0) {
            $this->session->set_flashdata('success', 'Data Tender berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data tender.');
        }
        redirect('admin/edit_tender/' . $id);
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

            if (!empty($this->input->post('password_lama'))) {
                $pass_lama = $this->input->post('password_lama');
                $pass_baru = $this->input->post('password_baru');

                if (password_verify($pass_lama, $user_data['password'])) {
                    $update_data['password'] = password_hash($pass_baru, PASSWORD_BCRYPT);
                } else {
                    $this->session->set_flashdata('error', 'Gagal: Password lama salah!');
                    redirect('admin/edit_profil');
                    return;
                }
            }

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
                    redirect('admin/edit_profil');
                    return;
                }
            }

            if (empty($update_data)) {
                $this->session->set_flashdata('error', 'Tidak ada perubahan yang disimpan.');
                redirect('admin/edit_profil');
                return;
            }

            $this->db->where('username', $username)->update('users', $update_data);
            $this->session->set_flashdata('success', 'Profil Berhasil diubah');
            redirect('admin/edit_profil');
            return;
        }

        $this->load->view('layout/header');
        $this->load->view('admin/edit_profil', $data);
        $this->load->view('layout/footer');
    }

    public function input_pemenang_konsultansi() {
        $this->load->view('layout/header');
        $data['jenis_tender'] = 'konsultansi';
        $this->load->view('sekretariat/input_pemenang', $data);
        $this->load->view('layout/footer');
    }

    public function input_pemenang() {
        $this->load->model('sekretariat/Sekretariat_model');
        $data['companies'] = $this->Sekretariat_model->get_all_companies();
        $this->load->view('layout/header');
        $this->load->view('sekretariat/input_pemenang', $data);
        $this->load->view('layout/footer');
    }

    public function daftar_perusahaan() {
        $this->load->model('sekretariat/Sekretariat_model');
        $data['companies'] = $this->Sekretariat_model->get_all_companies();
        $this->load->view('layout/header');
        $this->load->view('sekretariat/index', $data); // Correct view for directory
        $this->load->view('layout/footer');
    }

    public function personel_lapangan() {
        $this->load->model('sekretariat/Personel_lapangan_model');
        $this->load->view('layout/header');
        $this->load->view('sekretariat/personel_lapangan');
        $this->load->view('layout/footer');
    }

    public function personel_k3() {
        $this->load->model('sekretariat/Personel_k3_model');
        $this->load->view('layout/header');
        $this->load->view('sekretariat/personel_k3');
        $this->load->view('layout/footer');
    }

    public function peralatan() {
        $this->load->model('sekretariat/Peralatan_model');
        $this->load->view('layout/header');
        $this->load->view('sekretariat/peralatan');
        $this->load->view('layout/footer');
    }

    public function pemilik_alat() {
        $this->load->model('sekretariat/Pemilik_alat_model');
        $this->load->view('layout/header');
        $this->load->view('sekretariat/pemilik_alat');
        $this->load->view('layout/footer');
    }

    public function regulasi() {
        $this->load->model('sekretariat/Regulasi_model');
        $this->load->view('layout/header');
        $this->load->view('sekretariat/regulasi');
        $this->load->view('layout/footer');
    }

    public function cari_personel() {
        $this->load->view('layout/header');
        $this->load->view('pokja/cari_personel');
        $this->load->view('layout/footer');
    }

    public function cari_peralatan() {
        $this->load->view('layout/header');
        $this->load->view('pokja/cari_peralatan');
        $this->load->view('layout/footer');
    }

    public function detail_personel($id) {
        $this->load->model('pokja/Pokja_model');
        $data['personel'] = $this->Pokja_model->get_personel_by_id($id);
        $data['history'] = $this->Pokja_model->get_personel_history($id);

        $this->load->view('layout/header');
        $this->load->view('pokja/detail_personel', $data);
        $this->load->view('layout/footer');
    }

    public function detail_peralatan($id) {
        $this->load->model('pokja/Pokja_model');
        $data['peralatan'] = $this->Pokja_model->get_peralatan_by_id($id);
        $data['history'] = $this->Pokja_model->get_peralatan_history($id);

        $this->load->view('layout/header');
        $this->load->view('pokja/detail_peralatan', $data);
        $this->load->view('layout/footer');
    }

    // --- JSON & AJAX PROCESSORS ---

    public function get_filter_data() {
        $this->load->model('sekretariat/Sekretariat_model');
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $this->load->model('pokja/Pokja_model');
        $data['available_years'] = $this->Pokja_model->get_available_years();
        echo json_encode($data);
    }

    public function data_tender_json() {
        $this->load->model('sekretariat/Sekretariat_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        $data = $this->Sekretariat_model->get_all_tenders($penyedia_id, $tahun);
        echo json_encode(['data' => $data]);
    }

    public function check_bulk_duplicates() {
        $this->load->model('sekretariat/Sekretariat_model');
        $post = $this->input->post();
        $result = $this->Sekretariat_model->check_bulk_duplicates($post);
        echo json_encode($result);
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

        $tender_data['manajer_proyek'] = $personel_lapangan[0]['nama'] ?? null;
        $tender_data['nik_manajer_proyek'] = $personel_lapangan[0]['nik'] ?? null;
        $tender_data['manajer_teknik'] = $personel_lapangan[1]['nama'] ?? null;
        $tender_data['nik_manajer_teknik'] = $personel_lapangan[1]['nik'] ?? null;
        $tender_data['manajer_keuangan'] = $personel_lapangan[2]['nama'] ?? null;
        $tender_data['nik_manajer_keuangan'] = $personel_lapangan[2]['nik'] ?? null;
        $tender_data['ahli_k3'] = $personel_k3[0]['nama'] ?? null;
        $tender_data['nik_ahli_k3'] = $personel_k3[0]['nik'] ?? null;

        $this->load->model('sekretariat/Sekretariat_model');

        $force_save = $this->input->post('force_save') === '1';
        if (!$force_save && method_exists($this, '_get_bulk_duplicates_internal')) {
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

        if ($this->Sekretariat_model->save_winner_package($tender_data, $personel_lapangan, $personel_k3, $peralatan)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Paket Pemenang Berhasil Disimpan.']));
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data pemenang.']));
    }

    public function personel_lapangan_json() {
        $this->load->model('sekretariat/Personel_lapangan_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        $data = $this->Personel_lapangan_model->get_all($penyedia_id, $tahun);
        echo json_encode(['data' => $data]);
    }

    public function personel_k3_json() {
        $this->load->model('sekretariat/Personel_k3_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        $data = $this->Personel_k3_model->get_all($penyedia_id, $tahun);
        echo json_encode(['data' => $data]);
    }

    public function peralatan_json() {
        $this->load->model('sekretariat/Peralatan_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        $data = $this->Peralatan_model->get_all($penyedia_id, $tahun);
        echo json_encode(['data' => $data]);
    }

    public function pemilik_alat_json() {
        $this->load->model('sekretariat/Pemilik_alat_model');
        $jenis = $this->input->get('jenis');
        $data = $this->Pemilik_alat_model->get_all_with_counts($jenis);
        echo json_encode(['data' => $data]);
    }

    public function regulasi_json() {
        $this->load->model('sekretariat/Regulasi_model');
        $tahun = $this->input->get('tahun');
        $data = $this->Regulasi_model->get_all(['tahun' => $tahun]);
        echo json_encode(['data' => $data]);
    }

    public function get_resource_history() {
        $this->load->model('sekretariat/Sekretariat_model');
        $id = $this->input->get('id');
        $type = $this->input->get('type');
        $history = [];
        if ($type == 'personel_lapangan') {
            $history = $this->Sekretariat_model->get_personel_history($id);
        } elseif ($type == 'personel_k3') {
            $history = $this->Sekretariat_model->get_personel_k3_history($id);
        } else {
            $history = $this->Sekretariat_model->get_peralatan_history($id);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($history));
    }

    // --- DELETE ENDPOINTS ---

    public function personel_lapangan_delete($id) {
        $this->load->model('sekretariat/Personel_lapangan_model');
        if ($this->Personel_lapangan_model->delete($id)) {
            $this->session->set_flashdata('success', 'Personel berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus personel.');
        }
        redirect('admin/personel_lapangan');
    }

    public function personel_k3_delete($id) {
        $this->load->model('sekretariat/Personel_k3_model');
        if ($this->Personel_k3_model->delete($id)) {
            $this->session->set_flashdata('success', 'Personel K3 berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus personel K3.');
        }
        redirect('admin/personel_k3');
    }

    public function peralatan_delete($id) {
        $this->load->model('sekretariat/Peralatan_model');
        if ($this->Peralatan_model->delete($id)) {
            $this->session->set_flashdata('success', 'Peralatan berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus peralatan.');
        }
        redirect('admin/peralatan');
    }

    public function pemilik_alat_delete($id) {
        $this->load->model('sekretariat/Pemilik_alat_model');
        if ($this->Pemilik_alat_model->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Pemilik alat berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus pemilik alat.']);
        }
    }

    public function pemilik_alat_save() {
        $this->load->model('sekretariat/Pemilik_alat_model');
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
        $this->load->model('sekretariat/Pemilik_alat_model');
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

    public function pemilik_alat_detail_json() {
        $id = $this->input->get('id');
        $this->db->select('*');
        $this->db->from('peralatan');
        $this->db->where('pemilik_alat_id', $id);
        $data = $this->db->get()->result();
        echo json_encode(['data' => $data]);
    }

    public function regulasi_delete($id) {
        $this->load->model('sekretariat/Regulasi_model');
        if ($this->Regulasi_model->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Regulasi berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus regulasi.']);
        }
    }

    public function get_regulasi_statistics() {
        $this->load->model('sekretariat/Regulasi_model');
        $data['statistics'] = $this->Regulasi_model->get_statistics();
        $data['available_years'] = $this->Regulasi_model->get_available_years();
        echo json_encode($data);
    }

    public function regulasi_save() {
        $this->load->model('sekretariat/Regulasi_model');
        $data = [
            'instansi' => $this->input->post('instansi'),
            'jenis_regulasi' => $this->input->post('jenis_regulasi'),
            'nomor_regulasi' => $this->input->post('nomor_regulasi'),
            'tahun' => $this->input->post('tahun'),
            'judul' => $this->input->post('judul'),
            'tentang' => $this->input->post('tentang'),
            'status' => $this->input->post('status') ?: 'Berlaku'
        ];

        if (!empty($_FILES['file_regulasi']['name'])) {
            $file_name = $this->Regulasi_model->upload_file('file_regulasi');
            if ($file_name) $data['file_regulasi'] = $file_name;
        }

        if ($this->Regulasi_model->create($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Regulasi berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan regulasi.']);
        }
    }

    public function regulasi_update($id) {
        $this->load->model('sekretariat/Regulasi_model');
        $data = [
            'instansi' => $this->input->post('instansi'),
            'jenis_regulasi' => $this->input->post('jenis_regulasi'),
            'nomor_regulasi' => $this->input->post('nomor_regulasi'),
            'tahun' => $this->input->post('tahun'),
            'judul' => $this->input->post('judul'),
            'tentang' => $this->input->post('tentang'),
            'status' => $this->input->post('status') ?: 'Berlaku'
        ];

        if (!empty($_FILES['file_regulasi']['name'])) {
            $file_name = $this->Regulasi_model->upload_file('file_regulasi');
            if ($file_name) $data['file_regulasi'] = $file_name;
        }

        if ($this->Regulasi_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Regulasi berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate regulasi.']);
        }
    }

    // --- MANAGEMENT PER COMPANY ---

    public function manage($penyedia_id) {
        $this->load->model('sekretariat/Sekretariat_model');
        $data['company'] = $this->Sekretariat_model->get_company_by_id($penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('sekretariat/manage_company', $data);
        $this->load->view('layout/footer');
    }

    public function personel($penyedia_id) {
        $this->load->model('sekretariat/Sekretariat_model');
        $this->load->model('sekretariat/Personel_lapangan_model');
        $data['company'] = $this->Sekretariat_model->get_company_by_id($penyedia_id);
        $data['personel'] = $this->Personel_lapangan_model->get_by_penyedia($penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('sekretariat/personel_managed', $data);
        $this->load->view('layout/footer');
    }

    // --- USER MANAGEMENT ---

    public function toggle_status($user_id, $status) {
        $this->Admin_model->update_user_status($user_id, $status);
        $this->session->set_flashdata('success', 'Status user berhasil diperbarui.');
        redirect('admin/verifikasi_penyedia');
    }

    public function akun_pokja() {
        $data['pokja'] = $this->Admin_model->get_pokja_accounts();
        $this->load->view('layout/header');
        $this->load->view('akun_pokja', $data);
        $this->load->view('layout/footer');
    }

    public function create_pokja_process() {
        $data = [
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password')
        ];
        
        if ($this->Admin_model->create_pokja($data)) {
            $this->session->set_flashdata('success', 'Akun Pokja berhasil dibuat.');
        } else {
            $this->session->set_flashdata('error', 'Gagal membuat akun Pokja.');
        }
        redirect('admin/akun_pokja');
    }

    public function akun_sekretariat() {
        $data['sekretariat'] = $this->Admin_model->get_sekretariat_accounts();
        $this->load->view('layout/header');
        $this->load->view('akun_sekretariat', $data);
        $this->load->view('layout/footer');
    }

    public function create_sekretariat_process() {
        $data = [
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password')
        ];
        
        if ($this->Admin_model->create_sekretariat($data)) {
            $this->session->set_flashdata('success', 'Akun Sekretariat berhasil dibuat.');
        } else {
            $this->session->set_flashdata('error', 'Gagal membuat akun Sekretariat.');
        }
        redirect('admin/akun_sekretariat');
    }
    public function edit_user($user_id) {
        $data['user'] = $this->Admin_model->get_user_by_id($user_id);
        if (!$data['user']) {
            show_404();
        }
        $this->load->view('layout/header');
        $this->load->view('edit_user', $data);
        $this->load->view('layout/footer');
    }

    public function update_user_process() {
        $user_id = $this->input->post('id');
        $data = [
            'username' => $this->input->post('username'),
            'role' => $this->input->post('role'),
            'status_aktif' => $this->input->post('status_aktif')
        ];

        $password = $this->input->post('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->Admin_model->update_user($user_id, $data)) {
            $this->session->set_flashdata('success', 'User updated successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to update user.');
        }

        if ($data['role'] == 'pokja') {
            redirect('admin/akun_pokja');
        } elseif ($data['role'] == 'sekretariat') {
            redirect('admin/akun_sekretariat');
        } else {
            redirect('admin/verifikasi_penyedia');
        }
    }
}
