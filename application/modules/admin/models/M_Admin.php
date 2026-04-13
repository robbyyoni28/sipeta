<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Admin extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get user by ID
     */
    public function get_user_by_id($user_id) {
        return $this->db->where('id', $user_id)
                       ->get('users')
                       ->row();
    }

    /**
     * Update user data
     */
    public function update_user($user_id, $data) {
        $this->db->where('id', $user_id);
        return $this->db->update('users', $data);
    }

    /**
     * Update admin profile
     */
    public function update_profile($username, $data) {
        $this->db->where('username', $username);
        return $this->db->update('users', $data);
    }

    /**
     * Update password dengan validasi
     */
    public function update_password($username, $old_password, $new_password) {
        // Get current user data
        $user = $this->db->where('username', $username)
                        ->get('users')
                        ->row();

        if (!$user) {
            return ['status' => 'error', 'message' => 'User tidak ditemukan'];
        }

        // Verify old password
        if (!password_verify($old_password, $user->password)) {
            return ['status' => 'error', 'message' => 'Password lama salah'];
        }

        // Update with new password
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $this->db->where('username', $username)
                 ->update('users', ['password' => $password_hash]);

        return ['status' => 'success', 'message' => 'Password berhasil diperbarui'];
    }

    /**
     * Upload foto profil
     */
    public function upload_profile_photo($username, $file_field = 'foto') {
        $user = $this->db->where('username', $username)
                        ->get('users')
                        ->row();

        if (!$user) {
            return ['status' => 'error', 'message' => 'User tidak ditemukan'];
        }

        // Upload configuration
        $upload_path = realpath(APPPATH . '../assets/img/profile') . DIRECTORY_SEPARATOR;
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size'] = 2048; // 2MB
        $config['encrypt_name'] = TRUE;
        $config['file_ext_tolower'] = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($file_field)) {
            return ['status' => 'error', 'message' => $this->upload->display_errors('', '')];
        }

        $upload_data = $this->upload->data();

        // Delete old photo if exists
        if (!empty($user->foto) && $user->foto != 'default.png') {
            $old_file_path = $upload_path . $user->foto;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        }

        // Update database
        $this->db->where('username', $username)
                 ->update('users', ['foto' => $upload_data['file_name']]);

        return [
            'status' => 'success', 
            'message' => 'Foto profil berhasil diupload',
            'file_name' => $upload_data['file_name']
        ];
    }

    /**
     * Get admin statistics
     */
    public function get_admin_statistics() {
        $stats = [];

        // Total users by role
        $stats['users_by_role'] = $this->db->select('role, COUNT(*) as total')
                                          ->group_by('role')
                                          ->get('users')
                                          ->result();

        // Active vs inactive users
        $stats['user_status'] = $this->db->select('status_aktif, COUNT(*) as total')
                                         ->group_by('status_aktif')
                                         ->get('users')
                                         ->result();

        // Recent registrations
        $stats['recent_users'] = $this->db->select('username, role, status_aktif, created_at')
                                          ->order_by('created_at', 'DESC')
                                          ->limit(10)
                                          ->get('users')
                                          ->result();

        // Login activity (if you have login_logs table)
        // This is optional - implement if you have login tracking
        $stats['recent_logins'] = $this->get_recent_logins();

        return $stats;
    }

    /**
     * Get recent login activity
     */
    private function get_recent_logins() {
        // Check if login_logs table exists
        if (!$this->db->table_exists('login_logs')) {
            return [];
        }

        return $this->db->select('username, login_time, ip_address, user_agent')
                        ->order_by('login_time', 'DESC')
                        ->limit(20)
                        ->get('login_logs')
                        ->result();
    }

    /**
     * Create user dengan validasi lengkap
     */
    public function create_user($data) {
        // Validate required fields
        $required_fields = ['username', 'password', 'role'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                return ['status' => 'error', 'message' => 'Field ' . $field . ' wajib diisi'];
            }
        }

        // Check if username already exists
        if ($this->db->where('username', $data['username'])->count_all_results('users') > 0) {
            return ['status' => 'error', 'message' => 'Username sudah digunakan'];
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // Set default values
        $data['created_at'] = date('Y-m-d H:i:s');
        if (!isset($data['status_aktif'])) {
            $data['status_aktif'] = 0; // Default inactive
        }

        // Insert user
        $this->db->insert('users', $data);
        $user_id = $this->db->insert_id();

        if ($user_id) {
            return ['status' => 'success', 'message' => 'User berhasil dibuat', 'user_id' => $user_id];
        } else {
            return ['status' => 'error', 'message' => 'Gagal membuat user'];
        }
    }

    /**
     * Delete user dengan validasi
     */
    public function delete_user($user_id) {
        // Check if user exists
        $user = $this->db->where('id', $user_id)->get('users')->row();
        if (!$user) {
            return ['status' => 'error', 'message' => 'User tidak ditemukan'];
        }

        // Prevent deleting admin users (optional security measure)
        if ($user->role === 'admin') {
            return ['status' => 'error', 'message' => 'Tidak dapat menghapus user admin'];
        }

        // Check if user has related data (tender, etc.)
        $has_related_data = $this->check_user_related_data($user_id);
        if ($has_related_data) {
            return ['status' => 'error', 'message' => 'User tidak dapat dihapus karena memiliki data terkait'];
        }

        // Delete user profile photo if exists
        if (!empty($user->foto) && $user->foto != 'default.png') {
            $photo_path = realpath(APPPATH . '../assets/img/profile') . DIRECTORY_SEPARATOR . $user->foto;
            if (file_exists($photo_path)) {
                unlink($photo_path);
            }
        }

        // Delete user
        $this->db->where('id', $user_id)->delete('users');

        return ['status' => 'success', 'message' => 'User berhasil dihapus'];
    }

    /**
     * Check if user has related data
     */
    private function check_user_related_data($user_id) {
        // Check if user created any tenders
        $tender_count = $this->db->where('created_by', $user_id)
                                 ->count_all_results('tender');

        // Check if user is a penyedia with related data
        $user = $this->db->where('id', $user_id)->get('users')->row();
        if ($user && $user->role === 'penyedia') {
            $penyedia_count = $this->db->where('user_id', $user_id)
                                    ->count_all_results('penyedia');
            if ($penyedia_count > 0) {
                return true;
            }
        }

        return $tender_count > 0;
    }

    /**
     * Get all users with filtering
     */
    public function get_all_users($filters = []) {
        $this->db->select('id, username, role, status_aktif, created_at, nama, foto');

        // Apply filters
        if (!empty($filters['role'])) {
            $this->db->where('role', $filters['role']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('status_aktif', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $search = $this->db->escape_like_str($filters['search']);
            $this->db->group_start()
                     ->like('username', $search)
                     ->or_like('nama', $search)
                     ->group_end();
        }

        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('users')->result();
    }

    /**
     * Toggle user status
     */
    public function toggle_user_status($user_id, $status) {
        $this->db->where('id', $user_id)
                 ->update('users', ['status_aktif' => $status]);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Log user activity
     */
    public function log_activity($username, $activity, $details = []) {
        if (!$this->db->table_exists('admin_activity_logs')) {
            return false;
        }

        $log_data = [
            'username' => $username,
            'activity' => $activity,
            'details' => json_encode($details),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('admin_activity_logs', $log_data);
    }
}
