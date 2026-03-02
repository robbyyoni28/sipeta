<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Personel_lapangan_model extends CI_Model {

    private function normalize_date($value) {
        if ($value === null) return null;
        $value = trim((string) $value);
        if ($value === '') return null;
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }
        $dt = DateTime::createFromFormat('d/m/Y', $value);
        if ($dt instanceof DateTime) {
            return $dt->format('Y-m-d');
        }
        return null;
    }

    /**
     * Get all personel lapangan by penyedia
     */
    public function get_all($penyedia_id = null, $tahun = null) {
        $this->db->select('personel_lapangan.*, penyedia.nama_perusahaan');
        $this->db->from('personel_lapangan');
        $this->db->join('penyedia', 'penyedia.id = personel_lapangan.penyedia_id', 'left');
        if ($penyedia_id) {
            $this->db->where('personel_lapangan.penyedia_id', $penyedia_id);
        }
        if ($tahun) {
            $this->db->where('YEAR(personel_lapangan.created_at)', $tahun);
        }
        $this->db->order_by('personel_lapangan.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_available_years() {
        $query = $this->db->query("SELECT DISTINCT YEAR(created_at) as tahun FROM personel_lapangan WHERE created_at IS NOT NULL AND created_at != '0000-00-00 00:00:00' HAVING tahun > 0 ORDER BY tahun DESC");
        return array_column($query->result_array(), 'tahun');
    }

    /**
     * Get personel lapangan by ID
     */
    public function get_by_id($id) {
        return $this->db->get_where('personel_lapangan', ['id' => $id])->row();
    }

    /**
     * Get personel lapangan by NIK
     */
    public function get_by_nik($nik) {
        return $this->db->get_where('personel_lapangan', ['nik' => $nik])->row();
    }

    /**
     * Create new personel lapangan
     */
    public function create($data) {
        $data['created_by'] = $this->session->userdata('username');
        $data['created_at'] = date('Y-m-d H:i:s');
        if (array_key_exists('masa_berlaku_skk', $data)) {
            $data['masa_berlaku_skk'] = $this->normalize_date($data['masa_berlaku_skk']);
        }
        return $this->db->insert('personel_lapangan', $data);
    }

    /**
     * Update personel lapangan
     */
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        if (array_key_exists('masa_berlaku_skk', $data)) {
            $data['masa_berlaku_skk'] = $this->normalize_date($data['masa_berlaku_skk']);
        }
        return $this->db->where('id', $id)->update('personel_lapangan', $data);
    }

    /**
     * Delete personel lapangan
     */
    public function delete($id) {
        return $this->db->where('id', $id)->delete('personel_lapangan');
    }

    /**
     * Check if NIK already exists (for validation)
     */
    public function check_nik_exists($nik, $exclude_id = null) {
        $this->db->where('nik', $nik);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        $result = $this->db->get('personel_lapangan')->row();
        return $result ? true : false;
    }

    /**
     * Get personel lapangan with penyedia info
     */
    public function get_with_penyedia($id) {
        $this->db->select('personel_lapangan.*, penyedia.nama_perusahaan');
        $this->db->from('personel_lapangan');
        $this->db->join('penyedia', 'penyedia.id = personel_lapangan.penyedia_id', 'left');
        $this->db->where('personel_lapangan.id', $id);
        return $this->db->get()->row();
    }

    /**
     * Get personel lapangan for dropdown
     */
    public function get_dropdown($penyedia_id = null) {
        $this->db->select('id, nama, nik, jabatan');
        if ($penyedia_id) {
            $this->db->where('penyedia_id', $penyedia_id);
        }
        $this->db->order_by('nama', 'ASC');
        return $this->db->get('personel_lapangan')->result();
    }

    /**
     * Search personel lapangan
     */
    public function search($keyword, $penyedia_id = null) {
        $this->db->like('nama', $keyword);
        $this->db->or_like('nik', $keyword);
        $this->db->or_like('jabatan', $keyword);
        $this->db->or_like('nomor_skk', $keyword);
        
        if ($penyedia_id) {
            $this->db->where('penyedia_id', $penyedia_id);
        }
        
        return $this->db->get('personel_lapangan')->result();
    }

    /**
     * Count personel lapangan by penyedia
     */
    public function count_by_penyedia($penyedia_id) {
        return $this->db->where('penyedia_id', $penyedia_id)->count_all_results('personel_lapangan');
    }
}
