<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peralatan_model extends CI_Model {

    /**
     * Get all equipment with owner and provider info
     */
    public function get_all($penyedia_id = null, $tahun = null) {
        $this->db->select('peralatan.*, penyedia.nama_perusahaan, pemilik_alat.nama_pemilik as nama_pemilik_katalog');
        $this->db->from('peralatan');
        $this->db->join('penyedia', 'penyedia.id = peralatan.penyedia_id', 'left');
        $this->db->join('pemilik_alat', 'pemilik_alat.id = peralatan.pemilik_alat_id', 'left');
        
        if ($penyedia_id) {
            $this->db->where('peralatan.penyedia_id', $penyedia_id);
        }

        if ($tahun) {
            $this->db->where('YEAR(peralatan.created_at)', $tahun);
        }
        
        $this->db->order_by('peralatan.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_available_years() {
        $query = $this->db->query("SELECT DISTINCT YEAR(created_at) as tahun FROM peralatan WHERE created_at IS NOT NULL AND created_at != '0000-00-00 00:00:00' HAVING tahun > 0 ORDER BY tahun DESC");
        return array_column($query->result_array(), 'tahun');
    }

    /**
     * Get by ID
     */
    public function get_by_id($id) {
        return $this->db->get_where('peralatan', ['id' => $id])->row();
    }

    /**
     * Create
     */
    public function create($data) {
        $data['created_by'] = $this->session->userdata('username');
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('peralatan', $data);
    }

    /**
     * Update
     */
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update('peralatan', $data);
    }

    /**
     * Delete
     */
    public function delete($id) {
        return $this->db->where('id', $id)->delete('peralatan');
    }

    public function search($keyword) {
        $this->db->select('peralatan.*, penyedia.nama_perusahaan, pemilik_alat.nama_pemilik as nama_pemilik_katalog');
        $this->db->from('peralatan');
        $this->db->join('penyedia', 'penyedia.id = peralatan.penyedia_id', 'left');
        $this->db->join('pemilik_alat', 'pemilik_alat.id = peralatan.pemilik_alat_id', 'left');
        
        $this->db->group_start();
        $this->db->like('peralatan.nama_alat', $keyword);
        $this->db->or_like('peralatan.merk', $keyword);
        $this->db->or_like('peralatan.plat_serial', $keyword);
        $this->db->or_like('peralatan.tipe', $keyword);
        $this->db->group_end();
        
        $this->db->order_by('peralatan.nama_alat', 'ASC');
        return $this->db->get()->result();
    }
}
