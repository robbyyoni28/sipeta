<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Model - SIPETA Application
 * Handles all database operations for Admin module
 * 
 * @author Senior Developer
 * @version 2.0
 */
class M_Admin extends CI_Model {

    /**
     * Get Dashboard Statistics
     * 
     * @return array Statistics data
     */
    public function get_dashboard_statistics() {
        $stats = [];
        
        // Total Penyedia (aktif)
        $stats['total_penyedia'] = $this->db->where('role', 'penyedia')
                                           ->where('status_aktif', 1)
                                           ->count_all_results('users');
        
        // Total Personel (Lapangan + K3)
        $stats['total_personel_lapangan'] = $this->db->count_all('personel_lapangan');
        $stats['total_personel_k3'] = $this->db->count_all('personel_k3');
        $stats['total_personel'] = $stats['total_personel_lapangan'] + $stats['total_personel_k3'];
        
        // Total Peralatan
        $stats['total_peralatan'] = $this->db->count_all('peralatan');
        
        // Total Tender (berdasarkan tahun anggaran saat ini)
        $current_year = date('Y');
        $stats['total_tender'] = $this->db->where('tahun_anggaran', $current_year)
                                          ->count_all_results('tender');
        $stats['total_all_tender'] = $this->db->count_all('tender');
        
        // Total HPS (tahun anggaran saat ini)
        $hps_result = $this->db->select_sum('hps')
                              ->where('tahun_anggaran', $current_year)
                              ->get('tender')
                              ->row();
        $stats['total_hps'] = $hps_result->hps ?? 0;
        
        // Statistik per tahun
        $stats['tender_by_year'] = $this->db->select('tahun_anggaran, COUNT(*) as total, SUM(hps) as total_hps')
                                           ->group_by('tahun_anggaran')
                                           ->order_by('tahun_anggaran', 'DESC')
                                           ->limit(5)
                                           ->get('tender')
                                           ->result();
        
        return $stats;
    }

    /**
     * Get Recent Activities
     * 
     * @param int $limit Number of activities to retrieve
     * @return array Activities
     */
    public function get_recent_activities($limit = 10) {
        return $this->db->select('*')
                       ->from('admin_activity_logs')
                       ->order_by('created_at', 'DESC')
                       ->limit($limit)
                       ->get()
                       ->result();
    }

    /**
     * Get All Tenders with Filtering
     * 
     * @param string $tahun_anggaran Filter by year
     * @param string $keyword Search keyword
     * @return array Tenders
     */
    public function get_all_tenders($tahun_anggaran = null, $keyword = null) {
        $this->db->select('t.*, p.nama_perusahaan')
                 ->from('tender t')
                 ->join('penyedia p', 'p.id = t.penyedia_id', 'left')
                 ->order_by('t.tahun_anggaran', 'DESC')
                 ->order_by('t.created_at', 'DESC');
        
        if ($tahun_anggaran) {
            $this->db->where('t.tahun_anggaran', $tahun_anggaran);
        }
        
        if ($keyword) {
            $this->db->group_start()
                     ->like('t.kode_tender', $keyword)
                     ->or_like('t.judul_paket', $keyword)
                     ->or_like('p.nama_perusahaan', $keyword)
                     ->group_end();
        }
        
        return $this->db->get()->result();
    }

    /**
     * Get Available Years for Filtering
     * 
     * @return array Years
     */
    public function get_available_years() {
        return $this->db->select('DISTINCT tahun_anggaran')
                       ->from('tender')
                       ->order_by('tahun_anggaran', 'DESC')
                       ->get()
                       ->result_array();
    }

    /**
     * Update Tender Data
     * 
     * @param int $tender_id Tender ID
     * @param array $data Data to update
     * @return bool Success status
     */
    public function update_tender($tender_id, $data) {
        return $this->db->where('id', $tender_id)->update('tender', $data);
    }

    /**
     * Delete Peralatan by Tender ID (Delete-Insert Logic)
     * 
     * @param int $tender_id Tender ID
     * @return bool Success status
     */
    public function delete_peralatan_by_tender($tender_id) {
        // Get peralatan IDs before deletion
        $peralatan_ids = $this->db->select('peralatan_id')
                                  ->where('tender_id', $tender_id)
                                  ->get('tender_peralatan')
                                  ->result_array();
        
        // Delete from junction table
        $this->db->where('tender_id', $tender_id)->delete('tender_peralatan');
        
        // Optional: Clean up orphaned peralatan records
        // Uncomment if you want to delete peralatan that are no longer linked to any tender
        /*
        foreach ($peralatan_ids as $p_id) {
            $is_used = $this->db->where('peralatan_id', $p_id['peralatan_id'])
                               ->count_all_results('tender_peralatan');
            if ($is_used == 0) {
                $this->db->where('id', $p_id['peralatan_id'])->delete('peralatan');
            }
        }
        */
        
        return TRUE;
    }

    private function merge_peralatan_units_row(array $p) {
        if (empty($p['units']) || !is_array($p['units']) || !isset($p['units'][0]) || !is_array($p['units'][0])) {
            return $p;
        }
        $u = $p['units'][0];
        foreach (['plat_serial', 'merk', 'tipe', 'kapasitas', 'status_kepemilikan', 'tahun_pembuatan'] as $f) {
            $top = isset($p[$f]) ? trim((string) $p[$f]) : '';
            if ($top === '' && isset($u[$f]) && trim((string) $u[$f]) !== '') {
                $p[$f] = $u[$f];
            }
        }
        return $p;
    }

    /**
     * Insert Batch Peralatan for Tender
     * 
     * @param int $tender_id Tender ID
     * @param array $peralatan_data Array of peralatan data
     * @return bool Success status
     */
    public function insert_batch_peralatan($tender_id, $peralatan_data) {
        $penyedia_id = $this->db->select('penyedia_id')
                               ->where('id', $tender_id)
                               ->get('tender')
                               ->row()
                               ->penyedia_id;
        
        $batch_insert = [];
        
        foreach ($peralatan_data as $peralatan) {
            $peralatan = $this->merge_peralatan_units_row((array) $peralatan);
            // Skip if required fields are empty
            if (empty(trim($peralatan['jenis_alat'] ?? ''))) {
                continue;
            }

            $nama_alat = trim((string)($peralatan['nama_alat'] ?? ''));
            if ($nama_alat === '') {
                $nama_alat = trim((string)($peralatan['jenis_alat'] ?? ''));
            }

            $plat_key = isset($peralatan['plat_serial']) ? trim((string) $peralatan['plat_serial']) : '';
            $plat_key = ($plat_key === '') ? null : $plat_key;
            $peralatan['plat_serial'] = $plat_key;
            
            $existing = null;
            if ($plat_key !== null) {
                $existing = $this->db->where('plat_serial', $plat_key)
                                    ->get('peralatan')
                                    ->row();
            }
            
            if ($existing) {
                $peralatan_id = $existing->id;
                // Update existing peralatan
                $this->db->where('id', $peralatan_id)->update('peralatan', [
                    'nama_alat' => $nama_alat,
                    'merk' => $peralatan['merk'],
                    'tipe' => $peralatan['tipe'],
                    'kapasitas' => $peralatan['kapasitas'],
                    'jenis_alat' => $peralatan['jenis_alat'],
                    'tahun_pembuatan' => $peralatan['tahun_pembuatan'],
                    'status_kepemilikan' => $peralatan['status_kepemilikan'] ?? 'Milik Sendiri',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Insert new peralatan to master
                $peralatan_master = [
                    'penyedia_id' => $penyedia_id,
                    'nama_alat' => $nama_alat,
                    'merk' => $peralatan['merk'],
                    'tipe' => $peralatan['tipe'],
                    'kapasitas' => $peralatan['kapasitas'],
                    'plat_serial' => $peralatan['plat_serial'],
                    'jenis_alat' => $peralatan['jenis_alat'],
                    'tahun_pembuatan' => $peralatan['tahun_pembuatan'],
                    'status_kepemilikan' => $peralatan['status_kepemilikan'] ?? 'Milik Sendiri',
                    'created_by' => $this->session->userdata('username'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('peralatan', $peralatan_master);
                $peralatan_id = $this->db->insert_id();
            }
            
            // Prepare junction table data
            $batch_insert[] = [
                'tender_id' => $tender_id,
                'peralatan_id' => $peralatan_id,
                'jumlah' => $peralatan['jumlah'] ?? 1,
                'keterangan' => $peralatan['keterangan'] ?? null
            ];
        }
        
        if (!empty($batch_insert)) {
            return $this->db->insert_batch('tender_peralatan', $batch_insert);
        }
        
        return TRUE;
    }

    /**
     * Delete Personel Lapangan by Tender ID
     * 
     * @param int $tender_id Tender ID
     * @return bool Success status
     */
    public function delete_personel_lapangan_by_tender($tender_id) {
        return $this->db->where('tender_id', $tender_id)->delete('tender_personel_lapangan');
    }

    /**
     * Insert Batch Personel Lapangan for Tender
     * 
     * @param int $tender_id Tender ID
     * @param array $personel_data Array of personel data
     * @return bool Success status
     */
    public function insert_batch_personel_lapangan($tender_id, $personel_data) {
        $penyedia_id = $this->db->select('penyedia_id')
                               ->where('id', $tender_id)
                               ->get('tender')
                               ->row()
                               ->penyedia_id;
        
        $batch_insert = [];
        $nik_list = [];
        
        foreach ($personel_data as $personel) {
            // Skip if NIK is empty (validation requirement)
            if (empty(trim($personel['nik']))) {
                continue;
            }
            
            // Check for duplicate NIK within this tender
            if (in_array($personel['nik'], $nik_list)) {
                log_message('error', "Duplicate NIK found in personel lapangan: {$personel['nik']}");
                continue;
            }
            $nik_list[] = $personel['nik'];
            
            // Check if personel already exists in master (by NIK)
            $existing = $this->db->where('nik', $personel['nik'])
                                ->get('personel_lapangan')
                                ->row();
            
            if ($existing) {
                $personel_id = $existing->id;
                // Update existing personel
                $this->db->where('id', $personel_id)->update('personel_lapangan', [
                    'nama' => $personel['nama'],
                    'jabatan' => $personel['jabatan'],
                    'jenis_skk' => $personel['jenis_skk'],
                    'nomor_skk' => $personel['nomor_skk'],
                    'masa_berlaku_skk' => !empty($personel['masa_berlaku_skk']) ? date('Y-m-d', strtotime($personel['masa_berlaku_skk'])) : null,
                    'masa_berlaku_skk_sertifikat' => !empty($personel['masa_berlaku_skk_sertifikat']) ? $personel['masa_berlaku_skk_sertifikat'] : null,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Insert new personel to master
                $personel_master = [
                    'penyedia_id' => $penyedia_id,
                    'nama' => $personel['nama'],
                    'nik' => $personel['nik'],
                    'jabatan' => $personel['jabatan'],
                    'jenis_skk' => $personel['jenis_skk'],
                    'nomor_skk' => $personel['nomor_skk'],
                    'masa_berlaku_skk' => !empty($personel['masa_berlaku_skk']) ? date('Y-m-d', strtotime($personel['masa_berlaku_skk'])) : null,
                    'masa_berlaku_skk_sertifikat' => !empty($personel['masa_berlaku_skk_sertifikat']) ? $personel['masa_berlaku_skk_sertifikat'] : null,
                    'created_by' => $this->session->userdata('username'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('personel_lapangan', $personel_master);
                $personel_id = $this->db->insert_id();
            }
            
            // Prepare junction table data
            $batch_insert[] = [
                'tender_id' => $tender_id,
                'personel_lapangan_id' => $personel_id
            ];
        }
        
        if (!empty($batch_insert)) {
            return $this->db->insert_batch('tender_personel_lapangan', $batch_insert);
        }
        
        return TRUE;
    }

    /**
     * Delete Personel K3 by Tender ID
     * 
     * @param int $tender_id Tender ID
     * @return bool Success status
     */
    public function delete_personel_k3_by_tender($tender_id) {
        return $this->db->where('tender_id', $tender_id)->delete('tender_personel_k3');
    }

    /**
     * Insert Batch Personel K3 for Tender
     * 
     * @param int $tender_id Tender ID
     * @param array $personel_data Array of personel K3 data
     * @return bool Success status
     */
    public function insert_batch_personel_k3($tender_id, $personel_data) {
        $penyedia_id = $this->db->select('penyedia_id')
                               ->where('id', $tender_id)
                               ->get('tender')
                               ->row()
                               ->penyedia_id;
        
        $batch_insert = [];
        $nik_list = [];
        
        foreach ($personel_data as $personel) {
            // Skip if NIK is empty (validation requirement)
            if (empty(trim($personel['nik']))) {
                continue;
            }
            
            // Check for duplicate NIK within this tender
            if (in_array($personel['nik'], $nik_list)) {
                log_message('error', "Duplicate NIK found in personel K3: {$personel['nik']}");
                continue;
            }
            $nik_list[] = $personel['nik'];
            
            // Check if personel already exists in master (by NIK)
            $existing = $this->db->where('nik', $personel['nik'])
                                ->get('personel_k3')
                                ->row();
            
            if ($existing) {
                $personel_id = $existing->id;
                // Update existing personel
                $this->db->where('id', $personel_id)->update('personel_k3', [
                    'nama' => $personel['nama'],
                    'jabatan_k3' => $personel['jabatan_k3'],
                    'jenis_sertifikat_k3' => $personel['jenis_sertifikat_k3'],
                    'nomor_sertifikat_k3' => $personel['nomor_sertifikat_k3'],
                    'masa_berlaku_sertifikat' => !empty($personel['masa_berlaku_sertifikat']) ? date('Y-m-d', strtotime($personel['masa_berlaku_sertifikat'])) : null,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Insert new personel to master
                $personel_master = [
                    'penyedia_id' => $penyedia_id,
                    'nama' => $personel['nama'],
                    'nik' => $personel['nik'],
                    'jabatan_k3' => $personel['jabatan_k3'],
                    'jenis_sertifikat_k3' => $personel['jenis_sertifikat_k3'],
                    'nomor_sertifikat_k3' => $personel['nomor_sertifikat_k3'],
                    'masa_berlaku_sertifikat' => !empty($personel['masa_berlaku_sertifikat']) ? date('Y-m-d', strtotime($personel['masa_berlaku_sertifikat'])) : null,
                    'created_by' => $this->session->userdata('username'),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('personel_k3', $personel_master);
                $personel_id = $this->db->insert_id();
            }
            
            // Prepare junction table data
            $batch_insert[] = [
                'tender_id' => $tender_id,
                'personel_k3_id' => $personel_id
            ];
        }
        
        if (!empty($batch_insert)) {
            return $this->db->insert_batch('tender_personel_k3', $batch_insert);
        }
        
        return TRUE;
    }

    /**
     * Get User by Username
     * 
     * @param string $username Username
     * @return object User data
     */
    public function get_user_by_username($username) {
        return $this->db->where('username', $username)->get('users')->row();
    }

    /**
     * Update User Data
     * 
     * @param string $username Current username
     * @param array $data Data to update
     * @return bool Success status
     */
    public function update_user($username, $data) {
        return $this->db->where('username', $username)->update('users', $data);
    }

    /**
     * Change User Password
     * 
     * @param string $username Username
     * @param string $new_password New password (plain text)
     * @return bool Success status
     */
    public function change_password($username, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        return $this->db->where('username', $username)
                       ->update('users', ['password' => $hashed_password]);
    }

    /**
     * Check Username Existence
     * 
     * @param string $username Username to check
     * @return bool True if exists, false otherwise
     */
    public function check_username_exists($username) {
        $count = $this->db->where('username', $username)
                        ->count_all_results('users');
        return $count > 0;
    }

    /**
     * Log User Activity
     * 
     * @param string $activity Activity description
     * @param string $details Additional details
     * @return bool Success status
     */
    public function log_activity($activity, $details = null) {
        $log_data = [
            'username' => $this->session->userdata('username'),
            'activity' => $activity,
            'details' => $details,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('admin_activity_logs', $log_data);
    }

    /**
     * Auth User - Verify user credentials
     * 
     * @param string $username Username
     * @param string $password Password
     * @return object|false User data on success, false on failure
     */
    public function auth_user($username, $password) {
        $user = $this->db->where('username', $username)->get('users')->row();
        
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        
        return FALSE;
    }
}
