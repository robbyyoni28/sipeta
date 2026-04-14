<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Controller - SIPETA Application
 * Full CRUD for Tender, Peralatan, Personel, and Profile Management
 * 
 * @author Senior Developer
 * @version 2.0
 */
class Admin extends MX_Controller {

    public function __construct() {
        parent::__construct();
        
        // Security check
        if ($this->session->userdata('role') !== 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak. Anda tidak memiliki izin.');
            redirect('auth');
        }
        
        // Load required libraries and models
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->load->helper('security');
        $this->load->model('M_Admin');
        $this->load->model('M_Tender');
    }

    /**
     * Dashboard - Display statistics and recent activities
     */
    public function index() {
        try {
            // Get statistics from model
            $data['stats'] = $this->M_Admin->get_dashboard_statistics();
            
            // Get recent activities
            $data['recent_activities'] = $this->M_Admin->get_recent_activities(10);
            
            // Get current year for filtering
            $data['current_year'] = date('Y');
            
            $this->load->view('layout/header');
            $this->load->view('admin/dashboard', $data);
            $this->load->view('layout/footer');
            
        } catch (Exception $e) {
            log_message('error', 'Dashboard Error: ' . $e->getMessage());
            show_error('Terjadi kesalahan saat memuat dashboard.');
        }
    }

    /**
     * Data Tender - List all tenders with filtering
     */
    public function data_tender() {
        try {
            // Get filter parameters
            $tahun_anggaran = $this->input->get('tahun_anggaran');
            $keyword = $this->input->get('keyword');
            
            // Get tenders from model
            $data['tenders'] = $this->M_Admin->get_all_tenders($tahun_anggaran, $keyword);
            $data['years'] = $this->M_Admin->get_available_years();
            $data['current_filter'] = $tahun_anggaran;
            
            $this->load->view('layout/header');
            $this->load->view('admin/data_tender', $data);
            $this->load->view('layout/footer');
            
        } catch (Exception $e) {
            log_message('error', 'Data Tender Error: ' . $e->getMessage());
            show_error('Terjadi kesalahan saat memuat data tender.');
        }
    }

    /**
     * Edit Tender - Unified form for tender, peralatan, and personel
     * 
     * @param int $id Tender ID
     */
    public function edit_tender($id = null) {
        try {
            if (!$id) {
                $this->session->set_flashdata('error', 'ID Tender tidak ditemukan.');
                redirect('admin/data_tender');
            }
            
            // Get tender data with all relations
            $tender_data = $this->M_Tender->get_detail_tender($id);
            
            if (!$tender_data) {
                $this->session->set_flashdata('error', 'Data tender tidak ditemukan.');
                redirect('admin/data_tender');
            }
            
            $data['tender'] = $tender_data['tender'];
            $data['peralatan'] = $tender_data['peralatan'];
            $data['personel_lapangan'] = $tender_data['personel_lapangan'];
            $data['personel_k3'] = $tender_data['personel_k3'];
            
            $this->load->view('layout/header');
            $this->load->view('admin/edit_tender', $data);
            $this->load->view('layout/footer');
            
        } catch (Exception $e) {
            log_message('error', 'Edit Tender Error: ' . $e->getMessage());
            show_error('Terjadi kesalahan saat memuat form edit tender.');
        }
    }

    /**
     * Update Tender - Process tender, peralatan, and personel updates
     * 
     * @param int $id Tender ID
     */
    public function update_tender($id = null) {
        try {
            if (!$id) {
                $this->session->set_flashdata('error', 'ID Tender tidak ditemukan.');
                redirect('admin/data_tender');
            }
            
            // Log incoming data for debugging
            log_message('debug', 'Update Tender POST Data: ' . print_r($_POST, true));
            
            // Set validation rules
            $this->set_tender_validation_rules();
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/edit_tender/' . $id);
            }
            
            // Start transaction
            $this->db->trans_start();
            
            // Update tender main data
            $tender_update = $this->prepare_tender_data();
            $this->M_Admin->update_tender($id, $tender_update);
            
            // Update penyedia name if changed
            $this->update_penyedia_name($id);
            
            // Process peralatan (delete-insert logic)
            $peralatan_data = $this->input->post('peralatan');
            $this->M_Admin->delete_peralatan_by_tender($id);
            if (!empty($peralatan_data) && is_array($peralatan_data)) {
                $this->M_Admin->insert_batch_peralatan($id, $peralatan_data);
            }
            
            // Process personel lapangan (delete-insert logic)
            $personel_lapangan_data = $this->input->post('personel_lapangan');
            $this->M_Admin->delete_personel_lapangan_by_tender($id);
            if (!empty($personel_lapangan_data) && is_array($personel_lapangan_data)) {
                $this->M_Admin->insert_batch_personel_lapangan($id, $personel_lapangan_data);
            }
            
            // Process personel K3 (delete-insert logic)
            $personel_k3_data = $this->input->post('personel_k3');
            $this->M_Admin->delete_personel_k3_by_tender($id);
            if (!empty($personel_k3_data) && is_array($personel_k3_data)) {
                $this->M_Admin->insert_batch_personel_k3($id, $personel_k3_data);
            }
            
            // Complete transaction
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Gagal memperbarui data tender. Silakan coba lagi.');
                log_message('error', 'Transaction failed for tender ID: ' . $id);
            } else {
                // Log activity
                $this->M_Admin->log_activity('update_tender', "Update tender ID: {$id}");
                $this->session->set_flashdata('success', 'Data tender berhasil diperbarui.');
            }
            
            redirect('admin/edit_tender/' . $id);
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Update Tender Exception: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan sistem. Silakan hubungi administrator.');
            redirect('admin/edit_tender/' . $id);
        }
    }

    /**
     * Edit Profile - Display profile edit form
     */
    public function edit_profil() {
        try {
            $username = $this->session->userdata('username');
            $user_data = $this->M_Admin->get_user_by_username($username);
            
            if (!$user_data) {
                $this->session->set_flashdata('error', 'Data user tidak ditemukan.');
                redirect('admin');
            }
            
            $data['user'] = $user_data;
            
            $this->load->view('layout/header');
            $this->load->view('admin/edit_profil', $data);
            $this->load->view('layout/footer');
            
        } catch (Exception $e) {
            log_message('error', 'Edit Profil Error: ' . $e->getMessage());
            show_error('Terjadi kesalahan saat memuat form edit profil.');
        }
    }

    /**
     * Update Profile - Process profile update
     */
    public function update_profil() {
        try {
            $username = $this->session->userdata('username');
            
            // Set validation rules
            $this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|max_length[255]');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|max_length[50]|callback_check_username[' . $username . ']');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/edit_profil');
            }
            
            // Prepare update data
            $update_data = [
                'nama' => $this->input->post('nama', TRUE),
                'username' => $this->input->post('username', TRUE),
            ];
            
            // Handle photo upload
            if (!empty($_FILES['foto']['name'])) {
                $upload_result = $this->upload_profile_photo();
                if (is_array($upload_result) && isset($upload_result['error'])) {
                    $this->session->set_flashdata('error', $upload_result['error']);
                    redirect('admin/edit_profil');
                }
                $update_data['foto'] = $upload_result;
            }
            
            // Update user
            $result = $this->M_Admin->update_user($username, $update_data);
            
            if ($result) {
                // Update session if username changed
                if ($update_data['username'] !== $username) {
                    $this->session->set_userdata('username', $update_data['username']);
                }
                
                // Log activity
                $this->M_Admin->log_activity('update_profil', "Update profil user: {$username}");
                $this->session->set_flashdata('success', 'Profil berhasil diperbarui.');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui profil.');
            }
            
            redirect('admin/edit_profil');
            
        } catch (Exception $e) {
            log_message('error', 'Update Profil Exception: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan sistem.');
            redirect('admin/edit_profil');
        }
    }

    /**
     * Change Password - Process password change
     */
    public function change_password() {
        try {
            $username = $this->session->userdata('username');
            
            // Set validation rules
            $this->form_validation->set_rules('current_password', 'Password Lama', 'required|callback_check_current_password');
            $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]|matches[confirm_password]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required');
            
            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/edit_profil');
            }
            
            // Change password
            $new_password = $this->input->post('new_password', TRUE);
            $result = $this->M_Admin->change_password($username, $new_password);
            
            if ($result) {
                // Log activity
                $this->M_Admin->log_activity('change_password', "Change password user: {$username}");
                $this->session->set_flashdata('success', 'Password berhasil diubah.');
            } else {
                $this->session->set_flashdata('error', 'Gagal mengubah password.');
            }
            
            redirect('admin/edit_profil');
            
        } catch (Exception $e) {
            log_message('error', 'Change Password Exception: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Terjadi kesalahan sistem.');
            redirect('admin/edit_profil');
        }
    }

    /**
     * Upload Profile Photo
     * 
     * @return string|array Filename on success, error array on failure
     */
    private function upload_profile_photo() {
        $config['upload_path'] = './assets/img/profile/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = TRUE;
        $config['file_ext_tolower'] = TRUE;
        
        $this->upload->initialize($config);
        
        if (!$this->upload->do_upload('foto')) {
            return ['error' => $this->upload->display_errors('', '')];
        }
        
        $upload_data = $this->upload->data();
        return $upload_data['file_name'];
    }

    /**
     * Set Tender Validation Rules
     */
    private function set_tender_validation_rules() {
        $this->form_validation->set_rules('satuan_kerja', 'Satuan Kerja', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('judul_paket', 'Judul Paket', 'required|trim|max_length[500]');
        $this->form_validation->set_rules('nama_pokmil', 'Nama POKMIL', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('kode_tender', 'Kode Tender', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('tahun_anggaran', 'Tahun Anggaran', 'required|integer|greater_than[2000]|less_than[2100]');
        $this->form_validation->set_rules('nama_penyedia', 'Nama Penyedia', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('hps', 'HPS', 'required|callback_check_hps_format');
    }

    /**
     * Prepare Tender Data for Update
     * 
     * @return array Prepared data
     */
    private function prepare_tender_data() {
        // Parse HPS with comma decimal separator for Excel compatibility
        $hps_input = $this->input->post('hps');
        $hps_decimal = $this->parse_hps_format($hps_input);
        
        // Parse date
        $tanggal_bahp_raw = $this->input->post('tanggal_bahp');
        $tanggal_bahp = null;
        if (!empty($tanggal_bahp_raw)) {
            $dt = DateTime::createFromFormat('d/m/Y', $tanggal_bahp_raw);
            if ($dt) {
                $tanggal_bahp = $dt->format('Y-m-d');
            }
        }
        
        return [
            'satuan_kerja' => $this->input->post('satuan_kerja', TRUE),
            'judul_paket' => $this->input->post('judul_paket', TRUE),
            'nama_pokmil' => $this->input->post('nama_pokmil', TRUE),
            'kode_tender' => $this->input->post('kode_tender', TRUE),
            'tanggal_bahp' => $tanggal_bahp,
            'tahun_anggaran' => $this->input->post('tahun_anggaran', TRUE),
            'hps' => $hps_decimal,
            'segmentasi' => $this->input->post('kualifikasi', TRUE),
            'pemenang_tender' => $this->input->post('nama_penyedia', TRUE),
            'manajer_proyek' => $this->input->post('manajer_proyek', TRUE),
            'nik_manajer_proyek' => $this->input->post('nik_manajer_proyek', TRUE),
            'manajer_teknik' => $this->input->post('manajer_teknik', TRUE),
            'nik_manajer_teknik' => $this->input->post('nik_manajer_teknik', TRUE),
            'manajer_keuangan' => $this->input->post('manajer_keuangan', TRUE),
            'nik_manajer_keuangan' => $this->input->post('nik_manajer_keuangan', TRUE),
            'ahli_k3' => $this->input->post('ahli_k3', TRUE),
            'nik_ahli_k3' => $this->input->post('nik_ahli_k3', TRUE),
        ];
    }

    /**
     * Update Penyedia Name
     * 
     * @param int $tender_id Tender ID
     */
    private function update_penyedia_name($tender_id) {
        $nama_penyedia = $this->input->post('nama_penyedia', TRUE);
        $tender_row = $this->db->select('penyedia_id')->where('id', $tender_id)->get('tender')->row();
        
        if ($tender_row && $tender_row->penyedia_id) {
            $this->db->where('id', $tender_row->penyedia_id)
                     ->update('penyedia', ['nama_perusahaan' => $nama_penyedia]);
        }
    }

    /**
     * Parse HPS Format - Support comma decimal separator
     * 
     * @param string $hps_input HPS input value
     * @return float Parsed decimal value
     */
    private function parse_hps_format($hps_input) {
        // Remove dots (thousands separator) and 'Rp' prefix
        $hps_raw = str_replace(['.', 'Rp', ' '], ['', '', ''], $hps_input);
        // Convert comma to dot for database
        $hps_raw = str_replace(',', '.', $hps_raw);
        return is_numeric($hps_raw) ? floatval($hps_raw) : 0;
    }

    /**
     * Callback: Check HPS Format
     * 
     * @param string $hps HPS value
     * @return bool
     */
    public function check_hps_format($hps) {
        $hps_decimal = $this->parse_hps_format($hps);
        if ($hps_decimal <= 0) {
            $this->form_validation->set_message('check_hps_format', 'Format HPS tidak valid.');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Callback: Check Current Password
     * 
     * @param string $password Password to check
     * @return bool
     */
    public function check_current_password($password) {
        $username = $this->session->userdata('username');
        $user = $this->M_Admin->get_user_by_username($username);
        
        if (!$user || !password_verify($password, $user->password)) {
            $this->form_validation->set_message('check_current_password', 'Password lama tidak sesuai.');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Callback: Check Username Availability
     * 
     * @param string $username Username to check
     * @param string $current_username Current username (to exclude from check)
     * @return bool
     */
    public function check_username($username, $current_username) {
        if ($username === $current_username) {
            return TRUE;
        }
        
        $exists = $this->M_Admin->check_username_exists($username);
        if ($exists) {
            $this->form_validation->set_message('check_username', 'Username sudah digunakan.');
            return FALSE;
        }
        return TRUE;
    }
}
