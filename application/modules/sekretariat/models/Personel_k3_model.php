<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Personel_k3_model extends CI_Model {

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
     * Get all personel K3 by penyedia
     */
    public function get_all($penyedia_id = null, $tahun = null) {
        $this->db->select('personel_k3.*, penyedia.nama_perusahaan');
        $this->db->from('personel_k3');
        $this->db->join('penyedia', 'penyedia.id = personel_k3.penyedia_id', 'left');
        if ($penyedia_id) {
            $this->db->where('personel_k3.penyedia_id', $penyedia_id);
        }
        if ($tahun) {
            $this->db->where('YEAR(personel_k3.created_at)', $tahun);
        }
        $this->db->order_by('personel_k3.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_available_years() {
        $query = $this->db->query("SELECT DISTINCT YEAR(created_at) as tahun FROM personel_k3 WHERE created_at IS NOT NULL AND created_at != '0000-00-00 00:00:00' HAVING tahun > 0 ORDER BY tahun DESC");
        return array_column($query->result_array(), 'tahun');
    }

    /**
     * Get personel K3 by ID
     */
    public function get_by_id($id) {
        return $this->db->get_where('personel_k3', ['id' => $id])->row();
    }

    /**
     * Get personel K3 by NIK
     */
    public function get_by_nik($nik) {
        return $this->db->get_where('personel_k3', ['nik' => $nik])->row();
    }

    /**
     * Create new personel K3
     */
    public function create($data) {
        $data['created_by'] = $this->session->userdata('username');
        $data['created_at'] = date('Y-m-d H:i:s');
        if (array_key_exists('masa_berlaku_sertifikat', $data)) {
            $data['masa_berlaku_sertifikat'] = $this->normalize_date($data['masa_berlaku_sertifikat']);
        }
        return $this->db->insert('personel_k3', $data);
    }

    /**
     * Update personel K3
     */
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        if (array_key_exists('masa_berlaku_sertifikat', $data)) {
            $data['masa_berlaku_sertifikat'] = $this->normalize_date($data['masa_berlaku_sertifikat']);
        }
        return $this->db->where('id', $id)->update('personel_k3', $data);
    }

    /**
     * Delete personel K3
     */
    public function delete($id) {
        return $this->db->where('id', $id)->delete('personel_k3');
    }

    /**
     * Check if NIK already exists (for validation)
     */
    public function check_nik_exists($nik, $exclude_id = null) {
        $this->db->where('nik', $nik);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        $result = $this->db->get('personel_k3')->row();
        return $result ? true : false;
    }

    /**
     * Check if sertifikat is expired or near expiry
     */
    public function check_sertifikat_expired($personel_k3_id) {
        $personel = $this->get_by_id($personel_k3_id);
        
        if (!$personel || !$personel->masa_berlaku_sertifikat) {
            return ['status' => 'unknown', 'message' => 'Masa berlaku tidak diketahui'];
        }

        $masa_berlaku = strtotime($personel->masa_berlaku_sertifikat);
        $today = strtotime(date('Y-m-d'));
        $diff_days = floor(($masa_berlaku - $today) / (60 * 60 * 24));

        if ($diff_days < 0) {
            return ['status' => 'expired', 'message' => 'Sertifikat sudah expired', 'days' => abs($diff_days)];
        } elseif ($diff_days <= 30) {
            return ['status' => 'warning', 'message' => 'Sertifikat akan expired dalam ' . $diff_days . ' hari', 'days' => $diff_days];
        } else {
            return ['status' => 'valid', 'message' => 'Sertifikat masih berlaku', 'days' => $diff_days];
        }
    }

    /**
     * Get personel K3 with expiry warning
     */
    public function get_with_expiry_check($penyedia_id = null) {
        $personel_list = $this->get_all($penyedia_id);
        
        foreach ($personel_list as $personel) {
            $personel->expiry_status = $this->check_sertifikat_expired($personel->id);
        }
        
        return $personel_list;
    }

    /**
     * Get personel K3 with penyedia info
     */
    public function get_with_penyedia($id) {
        $this->db->select('personel_k3.*, penyedia.nama_perusahaan');
        $this->db->from('personel_k3');
        $this->db->join('penyedia', 'penyedia.id = personel_k3.penyedia_id', 'left');
        $this->db->where('personel_k3.id', $id);
        return $this->db->get()->row();
    }

    /**
     * Get personel K3 for dropdown
     */
    public function get_dropdown($penyedia_id = null) {
        $this->db->select('id, nama, nik, jabatan_k3');
        if ($penyedia_id) {
            $this->db->where('penyedia_id', $penyedia_id);
        }
        $this->db->order_by('nama', 'ASC');
        return $this->db->get('personel_k3')->result();
    }

    /**
     * Search personel K3
     */
    public function search($keyword, $penyedia_id = null) {
        $this->db->like('nama', $keyword);
        $this->db->or_like('nik', $keyword);
        $this->db->or_like('jabatan_k3', $keyword);
        $this->db->or_like('nomor_sertifikat_k3', $keyword);
        
        if ($penyedia_id) {
            $this->db->where('penyedia_id', $penyedia_id);
        }
        
        return $this->db->get('personel_k3')->result();
    }

    /**
     * Count personel K3 by penyedia
     */
    public function count_by_penyedia($penyedia_id) {
        return $this->db->where('penyedia_id', $penyedia_id)->count_all_results('personel_k3');
    }

    /**
     * Get expired certificates count
     */
    public function count_expired_certificates() {
        $this->db->where('masa_berlaku_sertifikat <', date('Y-m-d'));
        return $this->db->count_all_results('personel_k3');
    }

    /**
     * Get certificates expiring soon (within 30 days)
     */
    public function get_expiring_soon($days = 30) {
        $date_limit = date('Y-m-d', strtotime("+{$days} days"));
        $this->db->where('masa_berlaku_sertifikat <=', $date_limit);
        $this->db->where('masa_berlaku_sertifikat >=', date('Y-m-d'));
        $this->db->order_by('masa_berlaku_sertifikat', 'ASC');
        return $this->db->get('personel_k3')->result();
    }
}
