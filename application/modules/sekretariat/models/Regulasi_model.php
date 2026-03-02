<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Regulasi_model extends CI_Model {

    /**
     * Get all regulasi with optional filters
     */
    public function get_all($filters = []) {
        if (!empty($filters['jenis_regulasi'])) {
            $this->db->where('jenis_regulasi', $filters['jenis_regulasi']);
        }

        if (!empty($filters['instansi'])) {
            $this->db->where('instansi', $filters['instansi']);
        }
        
        if (!empty($filters['tahun'])) {
            $this->db->where('tahun', $filters['tahun']);
        }
        
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        $this->db->order_by('tahun', 'DESC');
        $this->db->order_by('nomor_regulasi', 'ASC');
        return $this->db->get('regulasi')->result();
    }

    /**
     * Get regulasi by ID
     */
    public function get_by_id($id) {
        return $this->db->get_where('regulasi', ['id' => $id])->row();
    }

    /**
     * Create new regulasi
     */
    public function create($data) {
        $data['created_by'] = $this->session->userdata('username');
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('regulasi', $data);
    }

    /**
     * Update regulasi
     */
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update('regulasi', $data);
    }

    /**
     * Delete regulasi
     */
    public function delete($id) {
        // Get file path before delete
        $regulasi = $this->get_by_id($id);
        
        // Delete from database
        $deleted = $this->db->where('id', $id)->delete('regulasi');
        
        // Delete file if exists
        if ($deleted && $regulasi && $regulasi->file_regulasi) {
            $file_path = './uploads/regulasi/' . $regulasi->file_regulasi;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        return $deleted;
    }

    /**
     * Upload file regulasi
     */
    public function upload_file($file_field = 'file_regulasi') {
        $config['upload_path']   = './uploads/regulasi/';
        $config['allowed_types'] = 'pdf|doc|docx';
        $config['max_size']      = 10240; // 10MB
        $config['encrypt_name']  = true;
        
        // Create directory if not exists
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }
        
        $this->load->library('upload', $config);
        
        if ($this->upload->do_upload($file_field)) {
            return $this->upload->data('file_name');
        } else {
            return false;
        }
    }

    /**
     * Get regulasi by type
     */
    public function get_by_type($jenis_regulasi) {
        $this->db->where('jenis_regulasi', $jenis_regulasi);
        $this->db->order_by('tahun', 'DESC');
        return $this->db->get('regulasi')->result();
    }

    /**
     * Get regulasi by year
     */
    public function get_by_year($tahun) {
        $this->db->where('tahun', $tahun);
        $this->db->order_by('jenis_regulasi', 'ASC');
        $this->db->order_by('nomor_regulasi', 'ASC');
        return $this->db->get('regulasi')->result();
    }

    /**
     * Get regulasi by status
     */
    public function get_by_status($status) {
        $this->db->where('status', $status);
        $this->db->order_by('tahun', 'DESC');
        return $this->db->get('regulasi')->result();
    }

    /**
     * Search regulasi
     */
    public function search($keyword) {
        $this->db->like('nomor_regulasi', $keyword);
        $this->db->or_like('judul', $keyword);
        $this->db->or_like('tentang', $keyword);
        $this->db->order_by('tahun', 'DESC');
        return $this->db->get('regulasi')->result();
    }

    /**
     * Get available years for filter
     */
    public function get_available_years() {
        $this->db->select('DISTINCT(tahun) as tahun');
        $this->db->order_by('tahun', 'DESC');
        $result = $this->db->get('regulasi')->result();
        
        $years = [];
        foreach ($result as $row) {
            $years[] = $row->tahun;
        }
        
        return $years;
    }

    /**
     * Count regulasi by type
     */
    public function count_by_type($jenis_regulasi) {
        return $this->db->where('jenis_regulasi', $jenis_regulasi)->count_all_results('regulasi');
    }

    /**
     * Count regulasi by status
     */
    public function count_by_status($status) {
        return $this->db->where('status', $status)->count_all_results('regulasi');
    }

    /**
     * Get latest regulasi
     */
    public function get_latest($limit = 10) {
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('regulasi')->result();
    }

    /**
     * Check if nomor regulasi exists
     */
    public function check_nomor_exists($nomor_regulasi, $tahun, $exclude_id = null) {
        $this->db->where('nomor_regulasi', $nomor_regulasi);
        $this->db->where('tahun', $tahun);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        $result = $this->db->get('regulasi')->row();
        return $result ? true : false;
    }

    /**
     * Get statistics
     */
    public function get_statistics() {
        $stats = [];
        
        // Total regulasi
        $stats['total'] = $this->db->count_all('regulasi');
        
        // By status
        $stats['berlaku'] = $this->count_by_status('Berlaku');
        $stats['dicabut'] = $this->count_by_status('Dicabut');
        $stats['direvisi'] = $this->count_by_status('Direvisi');
        
        // By type
        $this->db->select('jenis_regulasi, COUNT(*) as jumlah');
        $this->db->group_by('jenis_regulasi');
        $stats['by_type'] = $this->db->get('regulasi')->result();

        // By instansi
        $this->db->select('instansi, COUNT(*) as jumlah');
        $this->db->group_by('instansi');
        $stats['by_instansi'] = $this->db->get('regulasi')->result();
        
        return $stats;
    }
}
