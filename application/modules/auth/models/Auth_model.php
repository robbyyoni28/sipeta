<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    public function register($data) {
        $this->db->trans_start();
        
        $user_data = [
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'],
            'status_aktif' => isset($data['status_aktif']) ? $data['status_aktif'] : (($data['role'] == 'penyedia') ? 0 : 1)
        ];
        
        $this->db->insert('users', $user_data);
        $user_id = $this->db->insert_id();
        
        if ($data['role'] == 'penyedia') {
            $penyedia_data = [
                'user_id' => $user_id,
                'nama_perusahaan' => $data['nama_perusahaan'],
                'alamat' => $data['alamat'],
                'email' => $data['email'],
                'telepon' => $data['telepon']
            ];
            $this->db->insert('penyedia', $penyedia_data);
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function login($username, $password) {
        $user = $this->db->get_where('users', ['username' => $username])->row();
        
        if (!$user) {
            return ['status' => false, 'message' => "User '$username' tidak ditemukan di database."];
        }

        if (password_verify($password, $user->password)) {
            if ($user->status_aktif == 0) {
                return ['status' => false, 'message' => 'Akun belum aktif, silakan hubungi admin.'];
            }
            
            $session_data = [
                'user_id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'logged_in' => TRUE
            ];
            
            if ($user->role == 'penyedia') {
                $penyedia = $this->db->get_where('penyedia', ['user_id' => $user->id])->row();
                if ($penyedia) {
                    $session_data['penyedia_id'] = $penyedia->id;
                    $session_data['nama_perusahaan'] = $penyedia->nama_perusahaan;
                }
            }
            
            $this->session->set_userdata($session_data);
            return ['status' => true];
        }
        
        return ['status' => false, 'message' => 'Password yang anda masukkan salah.'];
    }
}
