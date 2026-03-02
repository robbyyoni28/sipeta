<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pemilik_alat_model extends CI_Model {

    /**
     * Get all pemilik alat
     */
    public function get_all() {
        $this->db->order_by('nama_pemilik', 'ASC');
        return $this->db->get('pemilik_alat')->result();
    }

    /**
     * Get pemilik alat by ID
     */
    public function get_by_id($id) {
        return $this->db->get_where('pemilik_alat', ['id' => $id])->row();
    }

    /**
     * Get pemilik alat by name
     */
    public function get_by_name($nama_pemilik) {
        return $this->db->get_where('pemilik_alat', ['nama_pemilik' => $nama_pemilik])->row();
    }

    /**
     * Create new pemilik alat
     */
    public function create($data) {
        $data['created_by'] = $this->session->userdata('username');
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('pemilik_alat', $data);
    }

    /**
     * Update pemilik alat
     */
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update('pemilik_alat', $data);
    }

    /**
     * Delete pemilik alat
     */
    public function delete($id) {
        return $this->db->where('id', $id)->delete('pemilik_alat');
    }

    /**
     * Get pemilik alat for dropdown
     */
    public function get_dropdown() {
        $this->db->select('id, nama_pemilik, jenis_pemilik');
        $this->db->order_by('nama_pemilik', 'ASC');
        $result = $this->db->get('pemilik_alat')->result();
        
        $dropdown = [];
        foreach ($result as $row) {
            $label = $row->nama_pemilik . ' (' . $row->jenis_pemilik . ')';
            $dropdown[$row->id] = $label;
        }
        
        return $dropdown;
    }

    /**
     * Get pemilik alat by type
     */
    public function get_by_type($jenis_pemilik) {
        $this->db->where('jenis_pemilik', $jenis_pemilik);
        $this->db->order_by('nama_pemilik', 'ASC');
        return $this->db->get('pemilik_alat')->result();
    }

    /**
     * Search pemilik alat
     */
    public function search($keyword) {
        $this->db->like('nama_pemilik', $keyword);
        $this->db->or_like('alamat', $keyword);
        $this->db->or_like('telepon', $keyword);
        $this->db->or_like('email', $keyword);
        $this->db->order_by('nama_pemilik', 'ASC');
        return $this->db->get('pemilik_alat')->result();
    }

    /**
     * Check if name already exists
     */
    public function check_name_exists($nama_pemilik, $exclude_id = null) {
        $this->db->where('nama_pemilik', $nama_pemilik);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        $result = $this->db->get('pemilik_alat')->row();
        return $result ? true : false;
    }

    /**
     * Get pemilik alat with equipment count
     */
    public function get_all_with_counts($jenis = null) {
        $this->db->select("pemilik_alat.*, COUNT(peralatan.id) as jumlah_alat, GROUP_CONCAT(DISTINCT CONCAT(IFNULL(peralatan.jenis_alat, IFNULL(peralatan.nama_alat, 'Alat')), IF(peralatan.plat_serial IS NULL OR peralatan.plat_serial = '', '', CONCAT(' [', peralatan.plat_serial, ']'))) ORDER BY peralatan.jenis_alat SEPARATOR ', ') as detail", false);
        $this->db->from('pemilik_alat');
        $this->db->join('peralatan', 'peralatan.pemilik_alat_id = pemilik_alat.id', 'left');
        
        if ($jenis) {
            $this->db->where('pemilik_alat.jenis_pemilik', $jenis);
        }

        $this->db->group_by('pemilik_alat.id');
        $this->db->order_by('pemilik_alat.nama_pemilik', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get equipment by pemilik
     */
    public function get_equipment($pemilik_id) {
        $this->db->where('pemilik_alat_id', $pemilik_id);
        return $this->db->get('peralatan')->result();
    }

    /**
     * Count total pemilik alat
     */
    public function count_all() {
        return $this->db->count_all('pemilik_alat');
    }

    /**
     * Count by type
     */
    public function count_by_type($jenis_pemilik) {
        return $this->db->where('jenis_pemilik', $jenis_pemilik)->count_all_results('pemilik_alat');
    }
}
