<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sekretariat_model extends CI_Model {

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

    public function get_all_companies() {
        return $this->db->get('penyedia')->result();
    }

    public function get_company_by_id($id) {
        return $this->db->get_where('penyedia', ['id' => $id])->row();
    }

    public function create_company($data) {
        $data['created_by'] = $this->session->userdata('username');
        return $this->db->insert('penyedia', $data);
    }

    public function update_company($id, $data) {
        return $this->db->where('id', $id)->update('penyedia', $data);
    }

    public function delete_company($id) {
        return $this->db->where('id', $id)->delete('penyedia');
    }

    public function get_all_years() {
        $query = $this->db->query("SELECT DISTINCT YEAR(tanggal_input) as tahun FROM tender WHERE tanggal_input IS NOT NULL AND tanggal_input != '0000-00-00 00:00:00' HAVING tahun > 0 ORDER BY tahun DESC");
        return array_column($query->result_array(), 'tahun');
    }
    /**
     * Save winner package with complete data
     * Updated to support new schema with all fields
     */
    public function save_winner_package($tender_data, $personel_lapangan = [], $personel_k3 = [], $peralatan = [], $manajer_teknik = null, $manajer_keuangan = null) {
        $this->db->trans_start();

        // 1. Find or Create Penyedia
        $penyedia = $this->db->get_where('penyedia', ['nama_perusahaan' => $tender_data['nama_penyedia']])->row();
        if ($penyedia) {
            $penyedia_id = $penyedia->id;
        } else {
            $this->db->insert('penyedia', [
                'nama_perusahaan' => $tender_data['nama_penyedia'],
                'created_by' => $this->session->userdata('username')
            ]);
            $penyedia_id = $this->db->insert_id();
        }

        // 2. Create Tender with ALL new fields
        $tender_insert = [
            'penyedia_id' => $penyedia_id,
            'kode_tender' => $tender_data['kode_tender'],
            // kolom di DB: satuan_kerja (konsep: nama satker / nama tender)
            'satuan_kerja' => $tender_data['satuan_kerja'] ?? ($tender_data['nama_tender'] ?? $tender_data['judul_paket']),
            'tahun_anggaran' => $tender_data['tahun_anggaran'] ?? date('Y'),
            'tanggal_input' => date('Y-m-d H:i:s'),
            
            // New fields
            'nama_pokmil' => $tender_data['nama_pokmil'] ?? null,
            'judul_paket' => $tender_data['judul_paket'] ?? null,
            'tanggal_bahp' => $this->normalize_date($tender_data['tanggal_bahp'] ?? null),
            'hps' => $tender_data['hps'] ?? null,
            'pemenang_tender' => $tender_data['pemenang_tender'] ?? $tender_data['nama_penyedia'],
            'segmentasi' => $tender_data['kualifikasi'] ?? ($tender_data['segmentasi'] ?? 'Non Kecil'),
            
            // Manajer data
            'manajer_proyek' => $tender_data['manajer_proyek'] ?? null,
            'nik_manajer_proyek' => $tender_data['nik_manajer_proyek'] ?? null,
            'manajer_teknik' => $tender_data['manajer_teknik'] ?? null,
            'nik_manajer_teknik' => $tender_data['nik_manajer_teknik'] ?? null,
            'manajer_keuangan' => $tender_data['manajer_keuangan'] ?? null,
            'nik_manajer_keuangan' => $tender_data['nik_manajer_keuangan'] ?? null,
            
            // Ahli K3
            'ahli_k3' => $tender_data['ahli_k3'] ?? null,
            'nik_ahli_k3' => $tender_data['nik_ahli_k3'] ?? null,
            
            'created_by' => $this->session->userdata('username')
        ];
        
        $this->db->insert('tender', $tender_insert);
        $tender_id = $this->db->insert_id();

        // 3a. Save Manajer Teknik langsung ke tabel manajer_teknik
        if (!empty($manajer_teknik) && !empty($manajer_teknik['nama']) && !empty($manajer_teknik['nik'])) {
            $p = $manajer_teknik;
            $existing_mt = $this->db->get_where('manajer_teknik', ['nik' => $p['nik']])->row();
            if ($existing_mt) {
                $this->db->where('id', $existing_mt->id)->update('manajer_teknik', [
                    'penyedia_id'     => $penyedia_id,
                    'nama'            => $p['nama'],
                    'jenis_skk'       => $p['jenis_skk'] ?? null,
                    'nomor_skk'       => $p['nomor_skk'] ?? null,
                    'masa_berlaku_skk'=> $this->normalize_date($p['masa_berlaku_skk'] ?? null)
                ]);
            } else {
                $this->db->insert('manajer_teknik', [
                    'penyedia_id'     => $penyedia_id,
                    'nama'            => $p['nama'],
                    'nik'             => $p['nik'],
                    'jenis_skk'       => $p['jenis_skk'] ?? null,
                    'nomor_skk'       => $p['nomor_skk'] ?? null,
                    'masa_berlaku_skk'=> $this->normalize_date($p['masa_berlaku_skk'] ?? null),
                    'created_by'      => $this->session->userdata('username')
                ]);
            }
        }

        // 3b. Save Manajer Keuangan langsung ke tabel manajer_keuangan
        if (!empty($manajer_keuangan) && !empty($manajer_keuangan['nama']) && !empty($manajer_keuangan['nik'])) {
            $p = $manajer_keuangan;
            $existing_mk = $this->db->get_where('manajer_keuangan', ['nik' => $p['nik']])->row();
            if ($existing_mk) {
                $this->db->where('id', $existing_mk->id)->update('manajer_keuangan', [
                    'penyedia_id'     => $penyedia_id,
                    'nama'            => $p['nama'],
                    'jenis_skk'       => $p['jenis_skk'] ?? null,
                    'nomor_skk'       => $p['nomor_skk'] ?? null,
                    'masa_berlaku_skk'=> $this->normalize_date($p['masa_berlaku_skk'] ?? null)
                ]);
            } else {
                $this->db->insert('manajer_keuangan', [
                    'penyedia_id'     => $penyedia_id,
                    'nama'            => $p['nama'],
                    'nik'             => $p['nik'],
                    'jenis_skk'       => $p['jenis_skk'] ?? null,
                    'nomor_skk'       => $p['nomor_skk'] ?? null,
                    'masa_berlaku_skk'=> $this->normalize_date($p['masa_berlaku_skk'] ?? null),
                    'created_by'      => $this->session->userdata('username')
                ]);
            }
        }

        // 3c. Save Personel Lapangan (Manajer Proyek, Pelaksana, dll)
        if (!empty($personel_lapangan)) {
            foreach ($personel_lapangan as $p) {
                if (empty($p['nama']) || empty($p['nik'])) continue;
                
                $existing = $this->db->get_where('personel_lapangan', ['nik' => $p['nik']])->row();
                if ($existing) {
                    $personel_id = $existing->id;
                    $this->db->where('id', $personel_id)->update('personel_lapangan', [
                        'nama' => $p['nama'],
                        'jenis_skk' => $p['jenis_skk'] ?? null,
                        'nomor_skk' => $p['nomor_skk'] ?? null,
                        'jabatan' => $p['jabatan'] ?? null,
                        'masa_berlaku_skk' => $this->normalize_date($p['masa_berlaku_skk'] ?? null)
                    ]);
                } else {
                    $this->db->insert('personel_lapangan', [
                        'penyedia_id' => $penyedia_id,
                        'nama' => $p['nama'],
                        'nik' => $p['nik'],
                        'jenis_skk' => $p['jenis_skk'] ?? null,
                        'nomor_skk' => $p['nomor_skk'] ?? null,
                        'jabatan' => $p['jabatan'] ?? null,
                        'masa_berlaku_skk' => $this->normalize_date($p['masa_berlaku_skk'] ?? null),
                        'created_by' => $this->session->userdata('username')
                    ]);
                    $personel_id = $this->db->insert_id();
                }
                
                $this->db->insert('tender_personel_lapangan', [
                    'tender_id' => $tender_id,
                    'personel_lapangan_id' => $personel_id
                ]);
            }
        }

        // 4. Process Personel K3
        if (!empty($personel_k3)) {
            foreach ($personel_k3 as $pk) {
                if (empty($pk['nama']) || empty($pk['nik'])) continue;
                
                // Check if personel K3 exists by NIK
                $existing_k3 = $this->db->get_where('personel_k3', ['nik' => $pk['nik']])->row();
                if ($existing_k3) {
                    $personel_k3_id = $existing_k3->id;
                    // Update details
                    $this->db->where('id', $personel_k3_id)->update('personel_k3', [
                        'nama' => $pk['nama'],
                        'jabatan_k3' => $pk['jabatan_k3'] ?? null,
                        'jenis_sertifikat_k3' => $pk['jenis_sertifikat_k3'] ?? null,
                        'nomor_sertifikat_k3' => $pk['nomor_sertifikat_k3'] ?? null,
                        'masa_berlaku_sertifikat' => $this->normalize_date($pk['masa_berlaku_sertifikat'] ?? null)
                    ]);
                } else {
                    $this->db->insert('personel_k3', [
                        'penyedia_id' => $penyedia_id,
                        'nama' => $pk['nama'],
                        'nik' => $pk['nik'],
                        'jabatan_k3' => $pk['jabatan_k3'] ?? null,
                        'jenis_sertifikat_k3' => $pk['jenis_sertifikat_k3'] ?? null,
                        'nomor_sertifikat_k3' => $pk['nomor_sertifikat_k3'] ?? null,
                        'masa_berlaku_sertifikat' => $this->normalize_date($pk['masa_berlaku_sertifikat'] ?? null),
                        'created_by' => $this->session->userdata('username')
                    ]);
                    $personel_k3_id = $this->db->insert_id();
                }
                
                // Link to tender
                $this->db->insert('tender_personel_k3', [
                    'tender_id' => $tender_id,
                    'personel_k3_id' => $personel_k3_id
                ]);
            }
        }

        // 5. Process Peralatan with new fields
        if (!empty($peralatan)) {
            foreach ($peralatan as $pl) {
                $ja_pl = trim((string)($pl['jenis_alat'] ?? ''));
                $na_pl = trim((string)($pl['nama_alat'] ?? ''));
                if ($ja_pl === '' && $na_pl === '') {
                    continue;
                }

                // Support new payload format: peralatan[][units][]
                $units = [];
                if (isset($pl['units']) && is_array($pl['units'])) {
                    $units = $pl['units'];
                }
                if (empty($units)) {
                    $units = [ $pl ];
                }

                foreach ($units as $u) {
                    $jenis = trim((string)($pl['jenis_alat'] ?? ($u['jenis_alat'] ?? '')));
                    $nama = trim((string)($pl['nama_alat'] ?? ($u['nama_alat'] ?? '')));
                    if ($nama === '' && $jenis !== '') {
                        $nama = $jenis;
                    } elseif ($nama === '' && $jenis === '') {
                        $nama = trim((string)($u['jenis_alat'] ?? ''));
                    }
                    if ($jenis === '' && $nama !== '') {
                        $jenis = $nama;
                    }
                    if ($nama === '') {
                        continue;
                    }

                    $peralatan_data = [
                        'penyedia_id' => $penyedia_id,
                        'jenis_alat' => $jenis !== '' ? $jenis : $nama,
                        'nama_alat' => $nama,
                        'merk' => $u['merk'] ?? ($pl['merk'] ?? null),
                        'tipe' => $u['tipe'] ?? ($pl['tipe'] ?? null),
                        'kapasitas' => $u['kapasitas'] ?? ($pl['kapasitas'] ?? null),
                        'plat_serial' => $u['plat_serial'] ?? ($pl['plat_serial'] ?? null),
                        'tahun_pembuatan' => $u['tahun_pembuatan'] ?? ($pl['tahun_pembuatan'] ?? null),
                        'status_kepemilikan' => $u['status_kepemilikan'] ?? ($pl['status_kepemilikan'] ?? 'Milik Sendiri'),
                        'nama_pemilik_alat' => $u['nama_pemilik_alat'] ?? ($pl['nama_pemilik_alat'] ?? null),
                        'bukti_kepemilikan' => $u['bukti_kepemilikan'] ?? ($pl['bukti_kepemilikan'] ?? null),
                        'created_by' => $this->session->userdata('username')
                    ];

                    // Check if equipment exists by plat_serial (if provided)
                    if (!empty($peralatan_data['plat_serial'])) {
                        $existing_pl = $this->db->get_where('peralatan', ['plat_serial' => $peralatan_data['plat_serial']])->row();
                        if ($existing_pl) {
                            $peralatan_id = $existing_pl->id;
                            $this->db->where('id', $peralatan_id)->update('peralatan', $peralatan_data);
                        } else {
                            $this->db->insert('peralatan', $peralatan_data);
                            $peralatan_id = $this->db->insert_id();
                        }
                    } else {
                        // No plat_serial, always create new
                        $this->db->insert('peralatan', $peralatan_data);
                        $peralatan_id = $this->db->insert_id();
                    }

                    // Link to tender per unit (jumlah=1)
                    $this->db->insert('tender_peralatan', [
                        'tender_id' => $tender_id,
                        'peralatan_id' => $peralatan_id,
                        'jumlah' => 1,
                        'keterangan' => $pl['keterangan'] ?? null
                    ]);
                }
            }
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_all_tenders($penyedia_id = null, $tahun = null, $jenis_tender = null) {
        $this->db->select('tender.*, tender.satuan_kerja as nama_tender, penyedia.nama_perusahaan, u.role as created_role,
                          (SELECT COUNT(*) FROM tender_personel_lapangan WHERE tender_id = tender.id) as jumlah_personel,
                          (SELECT COUNT(*) FROM tender_peralatan WHERE tender_id = tender.id) as jumlah_alat');
        $this->db->from('tender');
        $this->db->join('penyedia', 'penyedia.id = tender.penyedia_id', 'left');
        $this->db->join('users u', 'u.username = tender.created_by', 'left');
        
        if ($penyedia_id) {
            $this->db->where('tender.penyedia_id', $penyedia_id);
        }
        if ($tahun) {
            $this->db->where('tender.tahun_anggaran', $tahun);
        }
        if ($jenis_tender) {
            if ($jenis_tender == 'konsultansi') {
                $this->db->where('is_konsultansi', 1);
            } else {
                $this->db->where('is_konsultansi', 0);
            }
        }

        $this->db->order_by('tender.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_tender_detail($tender_id) {
        $data['tender'] = $this->db->select('tender.*, penyedia.nama_perusahaan')
                                   ->from('tender')
                                   ->join('penyedia', 'tender.penyedia_id = penyedia.id', 'left')
                                   ->where('tender.id', $tender_id)
                                   ->get()->row();
        
        $data['manajer_proyek'] = $this->db->get_where('manajer_proyek', ['tender_id' => $tender_id])->result();
        $data['manajer_teknik'] = $this->db->get_where('manajer_teknik', ['tender_id' => $tender_id])->result();
        $data['manajer_keuangan'] = $this->db->get_where('manajer_keuangan', ['tender_id' => $tender_id])->result();

        $data['personel'] = $this->db->select('personel_lapangan.*')
                                     ->from('tender_personel_lapangan')
                                     ->join('personel_lapangan', 'tender_personel_lapangan.personel_lapangan_id = personel_lapangan.id', 'left')
                                     ->where('tender_personel_lapangan.tender_id', $tender_id)
                                     ->get()->result();

        $data['personel_k3'] = $this->db->select('personel_k3.*')
                                     ->from('tender_personel_k3')
                                     ->join('personel_k3', 'tender_personel_k3.personel_k3_id = personel_k3.id', 'left')
                                     ->where('tender_personel_k3.tender_id', $tender_id)
                                     ->get()->result();
        
        $data['peralatan'] = $this->db->select('peralatan.*')
                                      ->from('tender_peralatan')
                                      ->join('peralatan', 'tender_peralatan.peralatan_id = peralatan.id', 'left')
                                      ->where('tender_peralatan.tender_id', $tender_id)
                                      ->get()->result();
        return $data;
    }

    public function get_personel_history($personel_id) {
        return $this->db->select('tender.kode_tender, tender.satuan_kerja AS nama_tender, penyedia.nama_perusahaan')
                        ->from('tender_personel_lapangan')
                        ->join('tender', 'tender_personel_lapangan.tender_id = tender.id', 'left')
                        ->join('penyedia', 'tender.penyedia_id = penyedia.id', 'left')
                        ->where('tender_personel_lapangan.personel_lapangan_id', $personel_id)
                        ->get()->result();
    }

    public function get_peralatan_history($peralatan_id) {
        return $this->db->select('tender.kode_tender, tender.satuan_kerja AS nama_tender, penyedia.nama_perusahaan')
                        ->from('tender_peralatan')
                        ->join('tender', 'tender_peralatan.tender_id = tender.id', 'left')
                        ->join('penyedia', 'tender.penyedia_id = penyedia.id', 'left')
                        ->where('tender_peralatan.peralatan_id', $peralatan_id)
                        ->get()->result();
    }

    public function get_personel_k3_history($id) {
        return $this->db->select('t.kode_tender, t.satuan_kerja AS nama_tender, p.nama_perusahaan, t.tahun_anggaran, t.judul_paket')
                        ->from('tender_personel_k3 tpk')
                        ->join('tender t', 'tpk.tender_id = t.id', 'left')
                        ->join('penyedia p', 't.penyedia_id = p.id', 'left')
                        ->where('tpk.personel_k3_id', $id)
                        ->get()->result();
    }

    public function get_available_years() {
        $result = $this->db->select('tahun_anggaran')
                        ->from('tender')
                        ->group_by('tahun_anggaran')
                        ->order_by('tahun_anggaran', 'DESC')
                        ->get()->result();
        
        $years = [];
        foreach ($result as $row) {
            $years[] = $row->tahun_anggaran;
        }
        return $years;
    }

    /**
     * Check for duplicate data in bulk input
     * 
     * @param array $post POST data
     * @return array Result with duplicates info
     */
    public function check_bulk_duplicates($post) {
        $duplicates = [];
        
        // Check duplicate kode_tender
        if (isset($post['kode_tender'])) {
            $existing = $this->db->where('kode_tender', $post['kode_tender'])->get('tender')->row();
            if ($existing) {
                $duplicates['kode_tender'] = 'Kode Tender sudah ada';
            }
        }
        
        // Check duplicate NIK personel lapangan
        if (isset($post['personel_lapangan']) && is_array($post['personel_lapangan'])) {
            $nik_list = [];
            foreach ($post['personel_lapangan'] as $p) {
                if (!empty($p['nik'])) {
                    if (in_array($p['nik'], $nik_list)) {
                        $duplicates['personel_lapangan'][] = "NIK {$p['nik']} duplikat dalam input";
                    }
                    $nik_list[] = $p['nik'];
                }
            }
        }
        
        // Check duplicate NIK personel K3
        if (isset($post['personel_k3']) && is_array($post['personel_k3'])) {
            $nik_list = [];
            foreach ($post['personel_k3'] as $p) {
                if (!empty($p['nik'])) {
                    if (in_array($p['nik'], $nik_list)) {
                        $duplicates['personel_k3'][] = "NIK {$p['nik']} duplikat dalam input";
                    }
                    $nik_list[] = $p['nik'];
                }
            }
        }
        
        // Check duplicate plat_serial peralatan
        if (isset($post['peralatan']) && is_array($post['peralatan'])) {
            $plat_list = [];
            foreach ($post['peralatan'] as $p) {
                if (!empty($p['plat_serial'])) {
                    if (in_array($p['plat_serial'], $plat_list)) {
                        $duplicates['peralatan'][] = "No Seri {$p['plat_serial']} duplikat dalam input";
                    }
                    $plat_list[] = $p['plat_serial'];
                }
            }
        }
        
        return [
            'has_duplicates' => !empty($duplicates),
            'duplicates' => $duplicates
        ];
    }
}
