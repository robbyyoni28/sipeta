<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tender_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get detail tender dengan semua relasi
     */
    public function get_detail_tender($tender_id) {
        // Get basic tender info with LEFT JOIN to managers
        $tender = $this->db->select('t.*, p.nama_perusahaan, 
                                   mp.nama as mp_nama, mp.nik as mp_nik, mp.jenis_skk as mp_jenis_skk, mp.nomor_skk as mp_nomor_skk, mp.masa_berlaku_skk as mp_masa_berlaku_skk,
                                   mt.nama as mt_nama, mt.nik as mt_nik, mt.jenis_skk as mt_jenis_skk, mt.nomor_skk as mt_nomor_skk, mt.masa_berlaku_skk as mt_masa_berlaku_skk,
                                   mk.nama as mk_nama, mk.nik as mk_nik, mk.jenis_skk as mk_jenis_skk, mk.nomor_skk as mk_nomor_skk, mk.masa_berlaku_skk as mk_masa_berlaku_skk')
                          ->from('tender t')
                          ->join('penyedia p', 'p.id = t.penyedia_id', 'left')
                          ->join('manajer_proyek mp', 'mp.tender_id = t.id', 'left')
                          ->join('manajer_teknik mt', 'mt.tender_id = t.id', 'left')
                          ->join('manajer_keuangan mk', 'mk.tender_id = t.id', 'left')
                          ->where('t.id', $tender_id)
                          ->get()
                          ->row();

        if (!$tender) {
            return null;
        }

        // Get personel lapangan
        $personel_lapangan = $this->db->select('pl.*, tpl.tender_id')
                                      ->from('tender_personel_lapangan tpl')
                                      ->join('personel_lapangan pl', 'pl.id = tpl.personel_lapangan_id')
                                      ->where('tpl.tender_id', $tender_id)
                                      ->get()
                                      ->result();

        // Get personel K3
        $personel_k3 = $this->db->select('pk.*, tpk.tender_id')
                                 ->from('tender_personel_k3 tpk')
                                 ->join('personel_k3 pk', 'pk.id = tpk.personel_k3_id')
                                 ->where('tpk.tender_id', $tender_id)
                                 ->get()
                                 ->result();

        // Get peralatan
        $peralatan = $this->db->select('p.*, tp.tender_id, tp.jumlah, tp.keterangan')
                              ->from('tender_peralatan tp')
                              ->join('peralatan p', 'p.id = tp.peralatan_id')
                              ->where('tp.tender_id', $tender_id)
                              ->get()
                              ->result();

        // Use data from join
        $manajer_proyek = $tender->mp_nama ? [(object)[
            'nama' => $tender->mp_nama,
            'nik' => $tender->mp_nik,
            'jenis_skk' => $tender->mp_jenis_skk,
            'nomor_skk' => $tender->mp_nomor_skk,
            'masa_berlaku_skk' => $tender->mp_masa_berlaku_skk
        ]] : [];

        $manajer_teknik = $tender->mt_nama ? [(object)[
            'nama' => $tender->mt_nama,
            'nik' => $tender->mt_nik,
            'jenis_skk' => $tender->mt_jenis_skk,
            'nomor_skk' => $tender->mt_nomor_skk,
            'masa_berlaku_skk' => $tender->mt_masa_berlaku_skk
        ]] : [];

        $manajer_keuangan = $tender->mk_nama ? [(object)[
            'nama' => $tender->mk_nama,
            'nik' => $tender->mk_nik,
            'jenis_skk' => $tender->mk_jenis_skk,
            'nomor_skk' => $tender->mk_nomor_skk,
            'masa_berlaku_skk' => $tender->mk_masa_berlaku_skk
        ]] : [];

        return [
            'tender' => $tender,
            'manajer_proyek' => $manajer_proyek,
            'manajer_teknik' => $manajer_teknik,
            'manajer_keuangan' => $manajer_keuangan,
            'personel_lapangan' => $personel_lapangan,
            'personel_k3' => $personel_k3,
            'peralatan' => $peralatan
        ];
    }


    /**
     * Save batch peralatan dengan logika delete-then-insert
     */
    public function save_batch_peralatan($tender_id, $peralatan_data) {
        $this->db->trans_start();

        // Delete existing peralatan for this tender
        $this->db->where('tender_id', $tender_id)->delete('tender_peralatan');

        // Insert new peralatan
        foreach ($peralatan_data as $peralatan) {
            if (!empty(trim($peralatan['jenis_alat']))) {
                // Cek apakah peralatan sudah ada di master
                $existing_peralatan = $this->db->where('plat_serial', $peralatan['plat_serial'])
                                               ->get('peralatan')
                                               ->row();

                if ($existing_peralatan) {
                    $peralatan_id = $existing_peralatan->id;
                    // Update master peralatan jika ada perubahan
                    $this->db->where('id', $peralatan_id)->update('peralatan', [
                        'nama_alat' => $peralatan['nama_alat'],
                        'merk' => $peralatan['merk'],
                        'tipe' => $peralatan['tipe'],
                        'kapasitas' => $peralatan['kapasitas'],
                        'jenis_alat' => $peralatan['jenis_alat'],
                        'tahun_pembuatan' => $peralatan['tahun_pembuatan'],
                        'status_kepemilikan' => $peralatan['status_kepemilikan'] ?? 'Milik Sendiri'
                    ]);
                } else {
                    // Create new peralatan in master
                    $new_peralatan = [
                        'penyedia_id' => $this->get_penyedia_id_by_tender($tender_id),
                        'nama_alat' => $peralatan['nama_alat'],
                        'merk' => $peralatan['merk'],
                        'tipe' => $peralatan['tipe'],
                        'kapasitas' => $peralatan['kapasitas'],
                        'plat_serial' => $peralatan['plat_serial'],
                        'jenis_alat' => $peralatan['jenis_alat'],
                        'tahun_pembuatan' => $peralatan['tahun_pembuatan'],
                        'status_kepemilikan' => $peralatan['status_kepemilikan'] ?? 'Milik Sendiri',
                        'created_by' => $this->session->userdata('username')
                    ];
                    $this->db->insert('peralatan', $new_peralatan);
                    $peralatan_id = $this->db->insert_id();
                }

                // Link to tender
                $tender_peralatan = [
                    'tender_id' => $tender_id,
                    'peralatan_id' => $peralatan_id,
                    'jumlah' => $peralatan['jumlah'] ?? 1,
                    'keterangan' => $peralatan['keterangan'] ?? null
                ];
                $this->db->insert('tender_peralatan', $tender_peralatan);
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Save batch personel lapangan dengan validasi NIK
     */
    public function save_batch_personel_lapangan($tender_id, $personel_data) {
        $this->db->trans_start();

        // Delete existing personel for this tender
        $this->db->where('tender_id', $tender_id)->delete('tender_personel_lapangan');

        // Check for duplicate NIK within this tender
        $nik_list = [];
        foreach ($personel_data as $personel) {
            if (!empty(trim($personel['nik']))) {
                if (in_array($personel['nik'], $nik_list)) {
                    $this->db->trans_rollback();
                    return ['status' => 'error', 'message' => 'NIK ' . $personel['nik'] . ' duplikat dalam tender yang sama!'];
                }
                $nik_list[] = $personel['nik'];
            }
        }

        // Insert new personel
        foreach ($personel_data as $personel) {
            if (!empty(trim($personel['nama'])) && !empty(trim($personel['nik']))) {
                // Cek apakah personel sudah ada di master
                $existing_personel = $this->db->where('nik', $personel['nik'])
                                              ->get('personel_lapangan')
                                              ->row();

                if ($existing_personel) {
                    $personel_id = $existing_personel->id;
                    // Update master personel jika ada perubahan
                    $this->db->where('id', $personel_id)->update('personel_lapangan', [
                        'nama' => $personel['nama'],
                        'jabatan' => $personel['jabatan'],
                        'jenis_skk' => $personel['jenis_skk'],
                        'nomor_skk' => $personel['nomor_skk'],
                        'masa_berlaku_skk' => !empty($personel['masa_berlaku_skk']) ? date('Y-m-d', strtotime($personel['masa_berlaku_skk'])) : null
                    ]);
                } else {
                    // Create new personel in master
                    $new_personel = [
                        'penyedia_id' => $this->get_penyedia_id_by_tender($tender_id),
                        'nama' => $personel['nama'],
                        'nik' => $personel['nik'],
                        'jabatan' => $personel['jabatan'],
                        'jenis_skk' => $personel['jenis_skk'],
                        'nomor_skk' => $personel['nomor_skk'],
                        'masa_berlaku_skk' => !empty($personel['masa_berlaku_skk']) ? date('Y-m-d', strtotime($personel['masa_berlaku_skk'])) : null,
                        'created_by' => $this->session->userdata('username')
                    ];
                    $this->db->insert('personel_lapangan', $new_personel);
                    $personel_id = $this->db->insert_id();
                }

                // Link to tender
                $tender_personel = [
                    'tender_id' => $tender_id,
                    'personel_lapangan_id' => $personel_id
                ];
                $this->db->insert('tender_personel_lapangan', $tender_personel);
            }
        }

        $this->db->trans_complete();
        return ['status' => $this->db->trans_status() ? 'success' : 'error', 'message' => ''];
    }

    /**
     * Save batch personel K3 dengan validasi NIK
     */
    public function save_batch_personel_k3($tender_id, $personel_data) {
        $this->db->trans_start();

        // Delete existing personel K3 for this tender
        $this->db->where('tender_id', $tender_id)->delete('tender_personel_k3');

        // Check for duplicate NIK within this tender
        $nik_list = [];
        foreach ($personel_data as $personel) {
            if (!empty(trim($personel['nik']))) {
                if (in_array($personel['nik'], $nik_list)) {
                    $this->db->trans_rollback();
                    return ['status' => 'error', 'message' => 'NIK ' . $personel['nik'] . ' duplikat dalam tender yang sama!'];
                }
                $nik_list[] = $personel['nik'];
            }
        }

        // Insert new personel K3
        foreach ($personel_data as $personel) {
            if (!empty(trim($personel['nama'])) && !empty(trim($personel['nik']))) {
                // Cek apakah personel K3 sudah ada di master
                $existing_personel = $this->db->where('nik', $personel['nik'])
                                              ->get('personel_k3')
                                              ->row();

                if ($existing_personel) {
                    $personel_id = $existing_personel->id;
                    // Update master personel K3 jika ada perubahan
                    $this->db->where('id', $personel_id)->update('personel_k3', [
                        'nama' => $personel['nama'],
                        'jabatan_k3' => $personel['jabatan_k3'],
                        'jenis_sertifikat_k3' => $personel['jenis_sertifikat_k3'],
                        'nomor_sertifikat_k3' => $personel['nomor_sertifikat_k3'],
                        'masa_berlaku_sertifikat' => !empty($personel['masa_berlaku_sertifikat']) ? date('Y-m-d', strtotime($personel['masa_berlaku_sertifikat'])) : null
                    ]);
                } else {
                    // Create new personel K3 in master
                    $new_personel = [
                        'penyedia_id' => $this->get_penyedia_id_by_tender($tender_id),
                        'nama' => $personel['nama'],
                        'nik' => $personel['nik'],
                        'jabatan_k3' => $personel['jabatan_k3'],
                        'jenis_sertifikat_k3' => $personel['jenis_sertifikat_k3'],
                        'nomor_sertifikat_k3' => $personel['nomor_sertifikat_k3'],
                        'masa_berlaku_sertifikat' => !empty($personel['masa_berlaku_sertifikat']) ? date('Y-m-d', strtotime($personel['masa_berlaku_sertifikat'])) : null,
                        'created_by' => $this->session->userdata('username')
                    ];
                    $this->db->insert('personel_k3', $new_personel);
                    $personel_id = $this->db->insert_id();
                }

                // Link to tender
                $tender_personel = [
                    'tender_id' => $tender_id,
                    'personel_k3_id' => $personel_id
                ];
                $this->db->insert('tender_personel_k3', $tender_personel);
            }
        }

        $this->db->trans_complete();
        return ['status' => $this->db->trans_status() ? 'success' : 'error', 'message' => ''];
    }

    /**
     * Get penyedia_id by tender_id
     */
    private function get_penyedia_id_by_tender($tender_id) {
        $result = $this->db->select('penyedia_id')
                           ->where('id', $tender_id)
                           ->get('tender')
                           ->row();
        return $result ? $result->penyedia_id : null;
    }

    public function insert_tender($data) {
        $data['created_by'] = $this->session->userdata('username');
        $this->db->insert('tender', $data);
        return $this->db->insert_id();
    }

    /**
     * Update tender dengan validasi
     */
    public function update_tender($tender_id, $data) {
        $this->db->where('id', $tender_id);
        return $this->db->update('tender', $data);
    }

    /**
     * Get tender statistics untuk dashboard
     */
    /**
     * Get all manajer teknik dengan filter penyedia
     */
    public function get_all_manajer_teknik($penyedia_id = null) {
        $this->db->select('mt.*, p.nama_perusahaan');
        $this->db->from('manajer_teknik mt');
        $this->db->join('penyedia p', 'p.id = mt.penyedia_id', 'left');
        if ($penyedia_id) {
            $this->db->where('mt.penyedia_id', $penyedia_id);
        }
        $this->db->order_by('mt.id', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get all manajer keuangan dengan filter penyedia
     */
    public function get_all_manajer_keuangan($penyedia_id = null) {
        $this->db->select('mk.*, p.nama_perusahaan');
        $this->db->from('manajer_keuangan mk');
        $this->db->join('penyedia p', 'p.id = mk.penyedia_id', 'left');
        if ($penyedia_id) {
            $this->db->where('mk.penyedia_id', $penyedia_id);
        }
        $this->db->order_by('mk.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_tender_statistics() {
        $current_year = date('Y');
        
        // Total tender tahun ini
        $total_tender = $this->db->where('tahun_anggaran', $current_year)
                                 ->count_all_results('tender');

        // Total HPS tahun ini
        $hps_result = $this->db->select_sum('hps')
                              ->where('tahun_anggaran', $current_year)
                              ->get('tender')
                              ->row();
        $total_hps = $hps_result->hps ?? 0;

        // Statistik per bulan tahun ini
        $monthly_stats = $this->db->select('MONTH(tanggal_input) as month, COUNT(*) as total')
                                  ->where('tahun_anggaran', $current_year)
                                  ->group_by('MONTH(tanggal_input)')
                                  ->order_by('month')
                                  ->get('tender')
                                  ->result();

        // Statistik per penyedia
        $penyedia_stats = $this->db->select('p.nama_perusahaan, COUNT(t.id) as total_tender, SUM(t.hps) as total_hps')
                                   ->from('tender t')
                                   ->join('penyedia p', 'p.id = t.penyedia_id')
                                   ->where('t.tahun_anggaran', $current_year)
                                   ->group_by('t.penyedia_id')
                                   ->order_by('total_tender', 'DESC')
                                   ->limit(10)
                                   ->get()
                                   ->result();

        return [
            'total_tender' => $total_tender,
            'total_hps' => $total_hps,
            'monthly_stats' => $monthly_stats,
            'penyedia_stats' => $penyedia_stats
        ];
    }
}
