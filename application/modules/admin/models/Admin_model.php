<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    public function get_pending_penyedia() {
        return $this->db->select('users.id as user_id, users.username, users.status_aktif, penyedia.*')
                        ->from('users')
                        ->join('penyedia', 'users.id = penyedia.user_id')
                        ->where('users.role', 'penyedia')
                        ->get()->result();
    }

    public function update_user_status($user_id, $status) {
        return $this->db->where('id', $user_id)->update('users', ['status_aktif' => $status]);
    }

    public function get_pokja_accounts() {
        return $this->db->get_where('users', ['role' => 'pokja'])->result();
    }

    public function create_pokja($data) {
        $user_data = [
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'pokja',
            'status_aktif' => 1
        ];
        return $this->db->insert('users', $user_data);
    }
    public function get_sekretariat_accounts() {
        return $this->db->get_where('users', ['role' => 'sekretariat'])->result();
    }

    public function create_sekretariat($data) {
        $user_data = [
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'sekretariat',
            'status_aktif' => 1
        ];
        return $this->db->insert('users', $user_data);
    }
    public function get_user_by_id($user_id) {
        return $this->db->get_where('users', ['id' => $user_id])->row();
    }

    public function update_user($user_id, $data) {
        return $this->db->where('id', $user_id)->update('users', $data);
    }
}
