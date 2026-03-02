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
        $this->load->model('sekretariat/Sekretariat_model');
        $res = $this->Sekretariat_model->save_winner_package($this->input->post());
        echo json_encode($res);
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
