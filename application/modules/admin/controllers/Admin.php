<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MX_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('role') !== 'admin') {
            redirect('auth');
        }
        $this->load->model('Admin_model');
    }

    public function index() {
        // Total Penyedia (aktif)
        $data['total_penyedia'] = $this->db->where('role', 'penyedia')
                                           ->where('status_aktif', 1)
                                           ->count_all_results('users');
        
        // Total Personel (Lapangan + K3 + Manajer)
        $data['total_personel_lapangan'] = $this->db->count_all('personel_lapangan');
        $data['total_personel_k3'] = $this->db->count_all('personel_k3');
        $data['total_manajer_teknik'] = $this->db->count_all('manajer_teknik');
        $data['total_manajer_keuangan'] = $this->db->count_all('manajer_keuangan');
        $data['total_personel'] = $data['total_personel_lapangan'] + $data['total_personel_k3']
                                 + $data['total_manajer_teknik'] + $data['total_manajer_keuangan'];
        
        // Total Peralatan
        $data['total_peralatan'] = $this->db->count_all('peralatan');
        
        // Total Tender (berdasarkan tahun anggaran saat ini)
        $current_year = date('Y');
        $data['total_tender'] = $this->db->where('tahun_anggaran', $current_year)
                                          ->count_all_results('tender');
        $data['total_all_tender'] = $this->db->count_all('tender');
        
        // Total HPS (tahun anggaran saat ini)
        $hps_result = $this->db->select_sum('hps')
                              ->where('tahun_anggaran', $current_year)
                              ->get('tender')
                              ->row();
        $data['total_hps'] = $hps_result->hps ?? 0;
        
        // Statistik per tahun
        $data['tender_by_year'] = $this->db->select('tahun_anggaran, COUNT(*) as total, SUM(hps) as total_hps')
                                           ->group_by('tahun_anggaran')
                                           ->order_by('tahun_anggaran', 'DESC')
                                           ->limit(5)
                                           ->get('tender')
                                           ->result();
        
        // Riwayat input paket terbaru (sebagai notifikasi aktivitas)
        $this->db->select('tender.kode_tender, tender.satuan_kerja AS nama_tender, tender.tahun_anggaran, 
                          tender.created_by, tender.tanggal_input, u.role as created_role, 
                          penyedia.nama_perusahaan');
        $this->db->from('tender');
        $this->db->join('users u', 'u.username = tender.created_by', 'left');
        $this->db->join('penyedia', 'penyedia.id = tender.penyedia_id', 'left');
        $this->db->order_by('tender.tanggal_input', 'DESC');
        $this->db->limit(10);
        $data['recent_tenders'] = $this->db->get()->result();
        
        // Statistik Penyedia
        $data['penyedia_stats'] = $this->db->select('COUNT(*) as total, 
                                                   SUM(CASE WHEN status_aktif = 1 THEN 1 ELSE 0 END) as aktif,
                                                   SUM(CASE WHEN status_aktif = 0 THEN 1 ELSE 0 END) as non_aktif')
                                           ->where('role', 'penyedia')
                                           ->get('users')
                                           ->row();
        
        // Statistik Peralatan per jenis
        $data['peralatan_by_type'] = $this->db->select('jenis_alat, COUNT(*) as total')
                                             ->where('jenis_alat IS NOT NULL')
                                             ->group_by('jenis_alat')
                                             ->order_by('total', 'DESC')
                                             ->limit(10)
                                             ->get('peralatan')
                                             ->result();
        
        $this->load->view('layout/header');
        $this->load->view('dashboard', $data);
        $this->load->view('layout/footer');
    }

    public function verifikasi_penyedia() {
        $data['penyedia'] = $this->Admin_model->get_pending_penyedia();
        $this->load->view('layout/header');
        $this->load->view('verifikasi_penyedia', $data);
        $this->load->view('layout/footer');
    }

    // --- INTEGRATED POKJA & SEKRETARIAT FEATURES ---
    
    public function data_tender() {
        $this->load->model('pokja/Pokja_model');
        $data['years'] = $this->Pokja_model->get_available_years();
        $this->load->view('layout/header');
        $this->load->view('pokja/pemeriksaan', $data); // Using Pokja's audit view
        $this->load->view('layout/footer');
    }

    public function detail($tender_id) {
        $this->load->model('M_Tender');
        $detail = $this->M_Tender->get_detail_tender($tender_id);
        
        if (!$detail) {
            $this->session->set_flashdata('error', 'Data tidak ditemukan!');
            redirect('admin/data_tender');
        }

        $data['tender'] = $detail['tender'];
        $data['manajer_teknik'] = $detail['manajer_teknik'];
        $data['manajer_keuangan'] = $detail['manajer_keuangan'];
        $data['personel_lapangan'] = $detail['personel_lapangan'];
        $data['personel_k3'] = $detail['personel_k3'];
        $data['peralatan'] = $detail['peralatan'];

        $this->load->view('layout/header');
        $this->load->view('pokja/detail_tender', $data);
        $this->load->view('layout/footer');
    }

    public function tender_delete($id) {
        $this->db->trans_start();
        $this->db->where('tender_id', $id)->delete('tender_personel_lapangan');
        $this->db->where('tender_id', $id)->delete('tender_personel_k3');
        $this->db->where('tender_id', $id)->delete('tender_peralatan');
        $this->db->where('id', $id)->delete('tender');
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->session->set_flashdata('success', 'Data Tender berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data tender.');
        }
        redirect('admin/data_tender');
    }

    public function edit_tender($id) {
        $this->load->model('M_Tender');
        $detail = $this->M_Tender->get_detail_tender($id);

        if (!$detail || !$detail['tender']) {
            $this->session->set_flashdata('error', 'Data tender tidak ditemukan.');
            redirect('admin/data_tender');
            return;
        }

        $data['tender'] = $detail['tender'];
        $data['manajer_teknik'] = !empty($detail['manajer_teknik']) ? $detail['manajer_teknik'][0] : null;
        $data['manajer_keuangan'] = !empty($detail['manajer_keuangan']) ? $detail['manajer_keuangan'][0] : null;
        $data['peralatan'] = $detail['peralatan'];
        $data['personel_lapangan'] = $detail['personel_lapangan'];
        $data['personel_k3'] = $detail['personel_k3'];
        
        $this->load->view('layout/header');
        $this->load->view('admin/edit_tender', $data);
        $this->load->view('layout/footer');
    }

    public function update_tender($id) {
        $this->load->model('sekretariat/Sekretariat_model');
        $this->load->library('form_validation');

        // Debug log - log all POST data
        error_log('update_tender - POST data: ' . print_r($_POST, true));

        // Set validation rules
        $this->form_validation->set_rules('satuan_kerja', 'Satuan Kerja', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('judul_paket', 'Judul Paket', 'required|trim|max_length[500]');
        $this->form_validation->set_rules('nama_pokmil', 'Nama POKMIL', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('kode_tender', 'Kode Tender', 'required|trim|max_length[50]');
        $this->form_validation->set_rules('tahun_anggaran', 'Tahun Anggaran', 'required|integer|greater_than[2000]|less_than[2100]');
        $this->form_validation->set_rules('nama_penyedia', 'Nama Penyedia', 'required|trim|max_length[255]');

        if ($this->form_validation->run() == FALSE) {
            error_log('update_tender - Validation failed: ' . validation_errors());
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/edit_tender/' . $id);
            return;
        }

        // Update penyedia name if changed
        $nama_penyedia = $this->input->post('nama_penyedia', TRUE);
        $tender_row = $this->db->get_where('tender', ['id' => $id])->row();
        if ($tender_row) {
            $this->db->where('id', $tender_row->penyedia_id)
                     ->update('penyedia', ['nama_perusahaan' => $nama_penyedia]);
        }

        // Parse HPS - gunakan koma sebagai desimal untuk Excel compatibility
        $hps_input = $this->input->post('hps');
        $hps_raw = str_replace(['.', 'Rp', ' '], ['', '', ''], $hps_input);
        $hps_raw = str_replace(',', '.', $hps_raw); // Konversi koma ke titik untuk database
        $hps_decimal = is_numeric($hps_raw) ? floatval($hps_raw) : 0;

        // Parse date
        $tanggal_bahp_raw = $this->input->post('tanggal_bahp');
        $tanggal_bahp = null;
        if (!empty($tanggal_bahp_raw)) {
            $dt = DateTime::createFromFormat('d/m/Y', $tanggal_bahp_raw);
            if ($dt) $tanggal_bahp = $dt->format('Y-m-d');
        }

        $update_data = [
            'satuan_kerja'       => $this->input->post('satuan_kerja', TRUE),
            'judul_paket'        => $this->input->post('judul_paket', TRUE),
            'nama_pokmil'        => $this->input->post('nama_pokmil', TRUE),
            'kode_tender'        => $this->input->post('kode_tender', TRUE),
            'tanggal_bahp'       => $tanggal_bahp,
            'tahun_anggaran'     => $this->input->post('tahun_anggaran', TRUE),
            'hps'                => $hps_decimal,
            'segmentasi'         => $this->input->post('kualifikasi', TRUE),
            'pemenang_tender'    => $nama_penyedia,
            // Manajer
            'manajer_proyek'     => $this->input->post('manajer_proyek', TRUE),
            'nik_manajer_proyek' => $this->input->post('nik_manajer_proyek', TRUE),
            'manajer_teknik'     => $this->input->post('manajer_teknik', TRUE),
            'nik_manajer_teknik' => $this->input->post('nik_manajer_teknik', TRUE),
            'manajer_keuangan'   => $this->input->post('manajer_keuangan', TRUE),
            'nik_manajer_keuangan'=> $this->input->post('nik_manajer_keuangan', TRUE),
            'ahli_k3'            => $this->input->post('ahli_k3', TRUE),
            'nik_ahli_k3'        => $this->input->post('nik_ahli_k3', TRUE),
        ];

        // Update tender data
        $this->db->where('id', $id)->update('tender', $update_data);
        error_log('update_tender - Affected rows for tender update: ' . $this->db->affected_rows());

        // File upload setup
        $config['upload_path']   = './uploads/dokumen/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png';
        $config['max_size']      = 5120; // 5MB
        $config['encrypt_name']  = TRUE;
        $this->load->library('upload', $config);

        // Update Manajer Teknik Table
        if (!empty($update_data['nik_manajer_teknik'])) {
            $mt_data = [
                'nama' => $update_data['manajer_teknik'],
                'jenis_skk' => $this->input->post('jenis_skk_manajer_teknik', TRUE),
                'nomor_skk' => $this->input->post('nomor_skk_manajer_teknik', TRUE),
            ];
            
            $dt_mt = $this->input->post('masa_berlaku_skk_manajer_teknik', TRUE);
            if (!empty($dt_mt)) {
                $dt = DateTime::createFromFormat('d/m/Y', $dt_mt);
                if ($dt) $mt_data['masa_berlaku_skk'] = $dt->format('Y-m-d');
            }

            if (!empty($_FILES['file_ktp_manajer_teknik']['name'])) {
                if ($this->upload->do_upload('file_ktp_manajer_teknik')) {
                    $mt_data['file_ktp'] = $this->upload->data('file_name');
                }
            }
            if (!empty($_FILES['file_skk_manajer_teknik']['name'])) {
                if ($this->upload->do_upload('file_skk_manajer_teknik')) {
                    $mt_data['file_skk'] = $this->upload->data('file_name');
                }
            }

            $existing_mt = $this->db->get_where('manajer_teknik', ['nik' => $update_data['nik_manajer_teknik']])->row();
            if ($existing_mt) {
                $this->db->where('nik', $update_data['nik_manajer_teknik'])->update('manajer_teknik', $mt_data);
            } else {
                $mt_data['penyedia_id'] = $tender_row->penyedia_id ?? 0;
                $mt_data['nik'] = $update_data['nik_manajer_teknik'];
                $mt_data['created_by'] = $this->session->userdata('username');
                $this->db->insert('manajer_teknik', $mt_data);
            }
        }

        // Update Manajer Keuangan Table
        if (!empty($update_data['nik_manajer_keuangan'])) {
            $mk_data = [
                'nama' => $update_data['manajer_keuangan'],
                'jenis_skk' => $this->input->post('jenis_skk_manajer_keuangan', TRUE),
                'nomor_skk' => $this->input->post('nomor_skk_manajer_keuangan', TRUE),
            ];
            
            $dt_mk = $this->input->post('masa_berlaku_skk_manajer_keuangan', TRUE);
            if (!empty($dt_mk)) {
                $dt = DateTime::createFromFormat('d/m/Y', $dt_mk);
                if ($dt) $mk_data['masa_berlaku_skk'] = $dt->format('Y-m-d');
            }

            if (!empty($_FILES['file_ktp_manajer_keuangan']['name'])) {
                if ($this->upload->do_upload('file_ktp_manajer_keuangan')) {
                    $mk_data['file_ktp'] = $this->upload->data('file_name');
                }
            }
            if (!empty($_FILES['file_skk_manajer_keuangan']['name'])) {
                if ($this->upload->do_upload('file_skk_manajer_keuangan')) {
                    $mk_data['file_skk'] = $this->upload->data('file_name');
                }
            }

            $existing_mk = $this->db->get_where('manajer_keuangan', ['nik' => $update_data['nik_manajer_keuangan']])->row();
            if ($existing_mk) {
                $this->db->where('nik', $update_data['nik_manajer_keuangan'])->update('manajer_keuangan', $mk_data);
            } else {
                $mk_data['penyedia_id'] = $tender_row->penyedia_id ?? 0;
                $mk_data['nik'] = $update_data['nik_manajer_keuangan'];
                $mk_data['created_by'] = $this->session->userdata('username');
                $this->db->insert('manajer_keuangan', $mk_data);
            }
        }

        // Proses update peralatan (selalu proses, termasuk jika kosong)
        $peralatan_data = $this->input->post('peralatan');
        error_log('update_tender - peralatan_data: ' . print_r($peralatan_data, true));
        if (is_array($peralatan_data)) {
            $this->update_tender_peralatan($id, $peralatan_data);
        } else {
            error_log('update_tender - peralatan_data is not array, treating as empty');
            $this->update_tender_peralatan($id, []);
        }

        // Proses update personel lapangan (selalu proses, termasuk jika kosong)
        $personel_lapangan_data = $this->input->post('personel_lapangan');
        error_log('update_tender - personel_lapangan_data: ' . print_r($personel_lapangan_data, true));
        if (is_array($personel_lapangan_data)) {
            $this->update_tender_personel_lapangan($id, $personel_lapangan_data);
        } else {
            error_log('update_tender - personel_lapangan_data is not array, treating as empty');
            $this->update_tender_personel_lapangan($id, []);
        }

        // Proses update personel K3 (selalu proses, termasuk jika kosong)
        $personel_k3_data = $this->input->post('personel_k3');
        error_log('update_tender - personel_k3_data: ' . print_r($personel_k3_data, true));
        if (is_array($personel_k3_data)) {
            $this->update_tender_personel_k3($id, $personel_k3_data);
        } else {
            error_log('update_tender - personel_k3_data is not array, treating as empty');
            $this->update_tender_personel_k3($id, []);
        }

        if ($this->db->affected_rows() >= 0) {
            $this->session->set_flashdata('success', 'Data Tender berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui data tender.');
        }
        redirect('admin/edit_tender/' . $id);
    }

    private function update_tender_peralatan($tender_id, $peralatan_data) {
        // Delete-then-insert: hapus semua peralatan tender secara brutal sebelum meng-insert yang baru
        $this->db->where('tender_id', $tender_id)->delete('tender_peralatan');

        if (empty($peralatan_data)) return;

        // Process submitted peralatan data
        foreach ($peralatan_data as $peralatan) {
            if (!empty(trim($peralatan['jenis_alat'] ?? ''))) {
                // Cek apakah peralatan sudah ada di master berdasar plat/serial
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
                        'penyedia_id' => $this->db->select('penyedia_id')->where('id', $tender_id)->get('tender')->row()->penyedia_id,
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

                // Create new link
                $tender_peralatan = [
                    'tender_id' => $tender_id,
                    'peralatan_id' => $peralatan_id,
                    'jumlah' => $peralatan['jumlah'] ?? 1,
                    'keterangan' => $peralatan['keterangan'] ?? null
                ];
                $this->db->insert('tender_peralatan', $tender_peralatan);
            }
        }
    }

    private function update_tender_personel_lapangan($tender_id, $personel_data) {
        // Get existing personel for this tender
        $existing_tender_personel = $this->db->where('tender_id', $tender_id)
                                             ->get('tender_personel_lapangan')
                                             ->result();
        
        $existing_personel_ids = [];
        foreach ($existing_tender_personel as $existing) {
            $existing_personel_ids[] = $existing->personel_lapangan_id;
        }

        // Check for duplicate NIK within this tender
        $nik_list = [];
        foreach ($personel_data as $personel) {
            if (!empty(trim($personel['nik']))) {
                if (in_array($personel['nik'], $nik_list)) {
                    $this->session->set_flashdata('error', 'NIK ' . $personel['nik'] . ' duplikat dalam tender yang sama!');
                    return false;
                }
                $nik_list[] = $personel['nik'];
            }
        }

        // Process submitted personel data
        $submitted_personel_ids = [];
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
                        'masa_berlaku_skk' => !empty($personel['masa_berlaku_skk']) ? date('Y-m-d', strtotime($personel['masa_berlaku_skk'])) : null,
                        'masa_berlaku_skk_sertifikat' => !empty($personel['masa_berlaku_skk_sertifikat']) ? $personel['masa_berlaku_skk_sertifikat'] : null
                    ]);
                } else {
                    // Create new personel in master
                    $new_personel = [
                        'penyedia_id' => $this->db->select('penyedia_id')->where('id', $tender_id)->get('tender')->row()->penyedia_id,
                        'nama' => $personel['nama'],
                        'nik' => $personel['nik'],
                        'jabatan' => $personel['jabatan'],
                        'jenis_skk' => $personel['jenis_skk'],
                        'nomor_skk' => $personel['nomor_skk'],
                        'masa_berlaku_skk' => !empty($personel['masa_berlaku_skk']) ? date('Y-m-d', strtotime($personel['masa_berlaku_skk'])) : null,
                        'masa_berlaku_skk_sertifikat' => !empty($personel['masa_berlaku_skk_sertifikat']) ? $personel['masa_berlaku_skk_sertifikat'] : null,
                        'created_by' => $this->session->userdata('username')
                    ];
                    $this->db->insert('personel_lapangan', $new_personel);
                    $personel_id = $this->db->insert_id();
                }

                // Check if already linked to tender
                $existing_link = $this->db->where('tender_id', $tender_id)
                                         ->where('personel_lapangan_id', $personel_id)
                                         ->get('tender_personel_lapangan')
                                         ->row();

                if (!$existing_link) {
                    // Create new link
                    $tender_personel = [
                        'tender_id' => $tender_id,
                        'personel_lapangan_id' => $personel_id
                    ];
                    $this->db->insert('tender_personel_lapangan', $tender_personel);
                }

                $submitted_personel_ids[] = $personel_id;
            }
        }

        // Remove personel that are no longer in the submitted data
        $to_remove = array_diff($existing_personel_ids, $submitted_personel_ids);
        if (!empty($to_remove)) {
            $this->db->where('tender_id', $tender_id)
                     ->where_in('personel_lapangan_id', $to_remove)
                     ->delete('tender_personel_lapangan');
        }
        
        return true;
    }

    private function update_tender_personel_k3($tender_id, $personel_data) {
        // Get existing personel K3 for this tender
        $existing_tender_personel = $this->db->where('tender_id', $tender_id)
                                             ->get('tender_personel_k3')
                                             ->result();
        
        $existing_personel_ids = [];
        foreach ($existing_tender_personel as $existing) {
            $existing_personel_ids[] = $existing->personel_k3_id;
        }

        // Check for duplicate NIK within this tender
        $nik_list = [];
        foreach ($personel_data as $personel) {
            if (!empty(trim($personel['nik']))) {
                if (in_array($personel['nik'], $nik_list)) {
                    $this->session->set_flashdata('error', 'NIK ' . $personel['nik'] . ' duplikat dalam tender yang sama!');
                    return false;
                }
                $nik_list[] = $personel['nik'];
            }
        }

        // Process submitted personel K3 data
        $submitted_personel_ids = [];
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
                        'penyedia_id' => $this->db->select('penyedia_id')->where('id', $tender_id)->get('tender')->row()->penyedia_id,
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

                // Check if already linked to tender
                $existing_link = $this->db->where('tender_id', $tender_id)
                                         ->where('personel_k3_id', $personel_id)
                                         ->get('tender_personel_k3')
                                         ->row();

                if (!$existing_link) {
                    // Create new link
                    $tender_personel = [
                        'tender_id' => $tender_id,
                        'personel_k3_id' => $personel_id
                    ];
                    $this->db->insert('tender_personel_k3', $tender_personel);
                }

                $submitted_personel_ids[] = $personel_id;
            }
        }

        // Remove personel K3 that are no longer in the submitted data
        $to_remove = array_diff($existing_personel_ids, $submitted_personel_ids);
        if (!empty($to_remove)) {
            $this->db->where('tender_id', $tender_id)
                     ->where_in('personel_k3_id', $to_remove)
                     ->delete('tender_personel_k3');
        }
        
        return true;
    }

    public function edit_profil() {
        $username = $this->session->userdata('username');
        $user_data = $this->db->get_where('users', ['username' => $username])->row_array();
        $data['user'] = $user_data;

        if ($this->input->post()) {
            $update_data = [];

            // Update nama hanya jika diisi
            $nama = $this->input->post('nama', TRUE);
            if (!empty(trim($nama))) {
                $update_data['nama'] = html_escape($nama);
            }

            if (!empty($this->input->post('password_lama'))) {
                $pass_lama = $this->input->post('password_lama');
                $pass_baru = $this->input->post('password_baru');

                if (password_verify($pass_lama, $user_data['password'])) {
                    $update_data['password'] = password_hash($pass_baru, PASSWORD_BCRYPT);
                } else {
                    $this->session->set_flashdata('error', 'Gagal: Password lama salah!');
                    redirect('admin/edit_profil');
                    return;
                }
            }

            if (!empty($_FILES['foto']['name'])) {
                $upload_path = realpath(APPPATH . '../assets/img/profile') . DIRECTORY_SEPARATOR;
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }

                $config['upload_path']   = $upload_path;
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
                $config['max_size']      = 2048; 
                $config['encrypt_name']  = TRUE; 

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('foto')) {
                    $old_file = $user_data['foto'] ?? '';
                    if ($old_file && $old_file != 'default.png' && file_exists($upload_path . $old_file)) {
                        unlink($upload_path . $old_file);
                    }
                    $update_data['foto'] = $this->upload->data('file_name');
                } else {
                    $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                    redirect('admin/edit_profil');
                    return;
                }
            }

            if (empty($update_data)) {
                $this->session->set_flashdata('error', 'Tidak ada perubahan yang disimpan.');
                redirect('admin/edit_profil');
                return;
            }

            $this->db->where('username', $username)->update('users', $update_data);
            $this->session->set_flashdata('success', 'Profil Berhasil diubah');
            redirect('admin/edit_profil');
            return;
        }

        $this->load->view('layout/header');
        $this->load->view('admin/edit_profil', $data);
        $this->load->view('layout/footer');
    }

    public function input_pemenang_konsultansi() {
        $this->load->view('layout/header');
        $data['jenis_tender'] = 'konsultansi';
        $this->load->view('sekretariat/input_pemenang', $data);
        $this->load->view('layout/footer');
    }

    public function input_pemenang() {
        $this->load->model('sekretariat/Sekretariat_model');
        $data['companies'] = $this->Sekretariat_model->get_all_companies();
        $this->load->view('layout/header');
        $this->load->view('sekretariat/input_pemenang', $data);
        $this->load->view('layout/footer');
    }

    public function daftar_perusahaan() {
        $this->load->model('sekretariat/Sekretariat_model');
        $data['companies'] = $this->Sekretariat_model->get_all_companies();
        $this->load->view('layout/header');
        $this->load->view('sekretariat/index', $data); // Correct view for directory
        $this->load->view('layout/footer');
    }

    public function personel_lapangan() {
        $this->load->model('sekretariat/Personel_lapangan_model');
        $this->load->view('layout/header');
        $this->load->view('sekretariat/personel_lapangan');
        $this->load->view('layout/footer');
    }

    public function personel_k3() {
        $this->load->model('sekretariat/Personel_k3_model');
        $this->load->view('layout/header');
        $this->load->view('sekretariat/personel_k3');
        $this->load->view('layout/footer');
    }

    public function peralatan() {
        $this->load->model('sekretariat/Peralatan_model');
        $this->load->view('layout/header');
        $this->load->view('sekretariat/peralatan');
        $this->load->view('layout/footer');
    }

    public function pemilik_alat() {
        $this->load->model('sekretariat/Pemilik_alat_model');
        $this->load->view('layout/header');
        $this->load->view('sekretariat/pemilik_alat');
        $this->load->view('layout/footer');
    }

    public function regulasi() {
        $this->load->model('sekretariat/Regulasi_model');
        $this->load->view('layout/header');
        $this->load->view('sekretariat/regulasi');
        $this->load->view('layout/footer');
    }

    // ============================================
    // MANAJER TEKNIK & KEUANGAN METHODS
    // ============================================

    private function _upload_dokumen_admin($field_name) {
        if (empty($_FILES[$field_name]['name'])) return null;
        $upload_path = FCPATH . 'uploads/dokumen/';
        if (!is_dir($upload_path)) mkdir($upload_path, 0777, true);
        $config = [
            'upload_path'   => $upload_path,
            'allowed_types' => 'pdf|jpg|jpeg|png',
            'max_size'      => 2048,
            'encrypt_name'  => TRUE
        ];
        $this->load->library('upload', $config);
        if ($this->upload->do_upload($field_name)) {
            return $this->upload->data('file_name');
        }
        return null;
    }

    public function manajer_teknik() {
        $this->load->model('admin/M_Tender');
        $this->load->model('sekretariat/Sekretariat_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $data['manajer_list'] = $this->M_Tender->get_all_manajer_teknik($penyedia_id);
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $data['selected_penyedia'] = $penyedia_id;
        $this->load->view('layout/header');
        $this->load->view('sekretariat/manajer_teknik', $data);
        $this->load->view('layout/footer');
    }

    public function manajer_teknik_save() {
        $normalize = function($v) {
            if (!$v) return null;
            $v = trim((string)$v);
            if (!$v) return null;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;
            $dt = DateTime::createFromFormat('d/m/Y', $v);
            return $dt ? $dt->format('Y-m-d') : null;
        };
        $data = [
            'penyedia_id'     => $this->input->post('penyedia_id'),
            'nama'            => $this->input->post('nama'),
            'nik'             => $this->input->post('nik'),
            'jenis_skk'       => $this->input->post('jenis_skk'),
            'nomor_skk'       => $this->input->post('nomor_skk'),
            'masa_berlaku_skk'=> $normalize($this->input->post('masa_berlaku_skk')),
            'created_by'      => $this->session->userdata('username')
        ];
        $file_ktp = $this->_upload_dokumen_admin('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen_admin('file_skk');
        if ($file_skk) $data['file_skk'] = $file_skk;

        if ($this->db->insert('manajer_teknik', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Manajer Teknik berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan Manajer Teknik.']);
        }
    }

    public function manajer_teknik_update($id) {
        $normalize = function($v) {
            if (!$v) return null;
            $v = trim((string)$v);
            if (!$v) return null;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;
            $dt = DateTime::createFromFormat('d/m/Y', $v);
            return $dt ? $dt->format('Y-m-d') : null;
        };
        $data = [
            'penyedia_id'     => $this->input->post('penyedia_id'),
            'nama'            => $this->input->post('nama'),
            'nik'             => $this->input->post('nik'),
            'jenis_skk'       => $this->input->post('jenis_skk'),
            'nomor_skk'       => $this->input->post('nomor_skk'),
            'masa_berlaku_skk'=> $normalize($this->input->post('masa_berlaku_skk'))
        ];
        $file_ktp = $this->_upload_dokumen_admin('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen_admin('file_skk');
        if ($file_skk) $data['file_skk'] = $file_skk;

        if ($this->db->where('id', $id)->update('manajer_teknik', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Manajer Teknik berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui Manajer Teknik.']);
        }
    }

    public function manajer_keuangan() {
        $this->load->model('admin/M_Tender');
        $this->load->model('sekretariat/Sekretariat_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $data['manajer_list'] = $this->M_Tender->get_all_manajer_keuangan($penyedia_id);
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $data['selected_penyedia'] = $penyedia_id;
        $this->load->view('layout/header');
        $this->load->view('sekretariat/manajer_keuangan', $data);
        $this->load->view('layout/footer');
    }

    public function manajer_keuangan_save() {
        $normalize = function($v) {
            if (!$v) return null;
            $v = trim((string)$v);
            if (!$v) return null;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;
            $dt = DateTime::createFromFormat('d/m/Y', $v);
            return $dt ? $dt->format('Y-m-d') : null;
        };
        $data = [
            'penyedia_id'     => $this->input->post('penyedia_id'),
            'nama'            => $this->input->post('nama'),
            'nik'             => $this->input->post('nik'),
            'jenis_skk'       => $this->input->post('jenis_skk'),
            'nomor_skk'       => $this->input->post('nomor_skk'),
            'masa_berlaku_skk'=> $normalize($this->input->post('masa_berlaku_skk')),
            'created_by'      => $this->session->userdata('username')
        ];
        $file_ktp = $this->_upload_dokumen_admin('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen_admin('file_skk');
        if ($file_skk) $data['file_skk'] = $file_skk;

        if ($this->db->insert('manajer_keuangan', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Manajer Keuangan berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan Manajer Keuangan.']);
        }
    }

    public function manajer_keuangan_update($id) {
        $normalize = function($v) {
            if (!$v) return null;
            $v = trim((string)$v);
            if (!$v) return null;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;
            $dt = DateTime::createFromFormat('d/m/Y', $v);
            return $dt ? $dt->format('Y-m-d') : null;
        };
        $data = [
            'penyedia_id'     => $this->input->post('penyedia_id'),
            'nama'            => $this->input->post('nama'),
            'nik'             => $this->input->post('nik'),
            'jenis_skk'       => $this->input->post('jenis_skk'),
            'nomor_skk'       => $this->input->post('nomor_skk'),
            'masa_berlaku_skk'=> $normalize($this->input->post('masa_berlaku_skk'))
        ];
        $file_ktp = $this->_upload_dokumen_admin('file_ktp');
        if ($file_ktp) $data['file_ktp'] = $file_ktp;
        $file_skk = $this->_upload_dokumen_admin('file_skk');
        if ($file_skk) $data['file_skk'] = $file_skk;

        if ($this->db->where('id', $id)->update('manajer_keuangan', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Manajer Keuangan berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui Manajer Keuangan.']);
        }
    }

    public function cari_personel() {
        $this->load->view('layout/header');
        $this->load->view('pokja/cari_personel');
        $this->load->view('layout/footer');
    }

    public function cari_peralatan() {
        $this->load->view('layout/header');
        $this->load->view('pokja/cari_peralatan');
        $this->load->view('layout/footer');
    }

    public function detail_personel($id) {
        $this->load->model('pokja/Pokja_model');
        $data['personel'] = $this->Pokja_model->get_personel_by_id($id);
        $data['history'] = $this->Pokja_model->get_personel_history($id);

        $this->load->view('layout/header');
        $this->load->view('pokja/detail_personel', $data);
        $this->load->view('layout/footer');
    }

    public function detail_peralatan($id) {
        $this->load->model('pokja/Pokja_model');
        $data['peralatan'] = $this->Pokja_model->get_peralatan_by_id($id);
        $data['history'] = $this->Pokja_model->get_peralatan_history($id);

        $this->load->view('layout/header');
        $this->load->view('pokja/detail_peralatan', $data);
        $this->load->view('layout/footer');
    }

    // --- JSON & AJAX PROCESSORS ---

    public function get_filter_data() {
        $this->load->model('sekretariat/Sekretariat_model');
        $data['penyedia_list'] = $this->Sekretariat_model->get_all_companies();
        $this->load->model('pokja/Pokja_model');
        $data['available_years'] = $this->Pokja_model->get_available_years();
        echo json_encode($data);
    }

    public function data_tender_json() {
        $this->load->model('sekretariat/Sekretariat_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        $data = $this->Sekretariat_model->get_all_tenders($penyedia_id, $tahun);
        echo json_encode(['data' => $data]);
    }

    public function check_bulk_duplicates() {
        $this->load->model('sekretariat/Sekretariat_model');
        $post = $this->input->post();
        $result = $this->Sekretariat_model->check_bulk_duplicates($post);
        echo json_encode($result);
    }

    public function simpan_pemenang() {
        $jenis_tender = $this->input->post('jenis_tender');
        $hps_input = $this->input->post('hps');

        $tender_data = [
            'nama_penyedia' => $this->input->post('nama_penyedia'),
            'kode_tender' => $this->input->post('kode_tender'),
            'satuan_kerja' => $this->input->post('satuan_kerja'),
            'judul_paket' => $this->input->post('judul_paket'),
            'nama_pokmil' => $this->input->post('nama_pokmil'),
            'tanggal_bahp' => $this->input->post('tanggal_bahp'),
            'hps' => str_replace(',', '.', str_replace('.', '', $hps_input)),
            'kualifikasi' => $this->input->post('kualifikasi'),
            'tahun_anggaran' => $this->input->post('tahun_anggaran') ? $this->input->post('tahun_anggaran') : date('Y')
        ];

        // Looping Bersih Personel Lapangan
        $personel_lapangan = [];
        $raw_lapangan = $this->input->post('personel_lapangan');
        if (!empty($raw_lapangan) && is_array($raw_lapangan)) {
            foreach ($raw_lapangan as $p) {
                if (!empty(trim($p['nama'])) && !empty(trim($p['nik']))) {
                    $personel_lapangan[] = $p;
                }
            }
        }

        // Looping Bersih Personel K3
        $personel_k3 = [];
        $raw_k3 = $this->input->post('personel_k3');
        if (!empty($raw_k3) && is_array($raw_k3)) {
            foreach ($raw_k3 as $pk) {
                if (!empty(trim($pk['nama'])) && !empty(trim($pk['nik']))) {
                    $personel_k3[] = $pk;
                }
            }
        }

        // Looping Bersih Peralatan
        $peralatan = [];
        if ($jenis_tender !== 'konsultansi') {
            $raw_alat = $this->input->post('peralatan');
            if (!empty($raw_alat) && is_array($raw_alat)) {
                foreach ($raw_alat as $alat) {
                    if (!empty(trim($alat['jenis_alat'] ?? ''))) {
                        $peralatan[] = $alat;
                    }
                }
            }
        }

        // ── Manajer Teknik & Keuangan dari POST terpisah (bukan dari personel_lapangan)
        $raw_mt = $this->input->post('manajer_teknik');
        $raw_mk = $this->input->post('manajer_keuangan');
        $manajer_teknik   = (!empty($raw_mt['nama']) && !empty($raw_mt['nik'])) ? $raw_mt : null;
        $manajer_keuangan = (!empty($raw_mk['nama']) && !empty($raw_mk['nik'])) ? $raw_mk : null;

        // ── Isi kolom referensi di tabel tender
        $tender_data['manajer_teknik']       = $manajer_teknik['nama']   ?? null;
        $tender_data['nik_manajer_teknik']   = $manajer_teknik['nik']    ?? null;
        $tender_data['manajer_keuangan']     = $manajer_keuangan['nama'] ?? null;
        $tender_data['nik_manajer_keuangan'] = $manajer_keuangan['nik']  ?? null;

        // ── Personel Lapangan dari personel_lapangan[0] disimpan di kolom legacy manajer_proyek
        $tender_data['manajer_proyek']       = $personel_lapangan[0]['nama'] ?? null;
        $tender_data['nik_manajer_proyek']   = $personel_lapangan[0]['nik']  ?? null;
        $tender_data['ahli_k3']     = $personel_k3[0]['nama'] ?? null;
        $tender_data['nik_ahli_k3'] = $personel_k3[0]['nik']  ?? null;

        $this->load->model('sekretariat/Sekretariat_model');

        $force_save = $this->input->post('force_save') === '1';
        if (!$force_save && method_exists($this, '_get_bulk_duplicates_internal')) {
            $duplicates = $this->_get_bulk_duplicates_internal($personel_lapangan, $personel_k3, $peralatan, $tender_data['kode_tender'], $tender_data['tahun_anggaran']);
            if (!empty($duplicates)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'duplicate', 
                        'duplicates' => $duplicates,
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]));
                return;
            }
        }

        if ($this->Sekretariat_model->save_winner_package($tender_data, $personel_lapangan, $personel_k3, $peralatan, $manajer_teknik, $manajer_keuangan)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Paket Pemenang Berhasil Disimpan.']));
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data pemenang.']));
    }

    public function personel_lapangan_json() {
        $this->load->model('sekretariat/Personel_lapangan_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        $data = $this->Personel_lapangan_model->get_all($penyedia_id, $tahun);
        echo json_encode(['data' => $data]);
    }

    public function personel_k3_json() {
        $this->load->model('sekretariat/Personel_k3_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        $data = $this->Personel_k3_model->get_all($penyedia_id, $tahun);
        echo json_encode(['data' => $data]);
    }

    public function peralatan_json() {
        $this->load->model('sekretariat/Peralatan_model');
        $penyedia_id = $this->input->get('penyedia_id');
        $tahun = $this->input->get('tahun');
        $data = $this->Peralatan_model->get_all($penyedia_id, $tahun);
        echo json_encode(['data' => $data]);
    }

    public function pemilik_alat_json() {
        $this->load->model('sekretariat/Pemilik_alat_model');
        $jenis = $this->input->get('jenis');
        $data = $this->Pemilik_alat_model->get_all_with_counts($jenis);
        echo json_encode(['data' => $data]);
    }

    public function regulasi_json() {
        $this->load->model('sekretariat/Regulasi_model');
        $tahun = $this->input->get('tahun');
        $data = $this->Regulasi_model->get_all(['tahun' => $tahun]);
        echo json_encode(['data' => $data]);
    }

    public function get_resource_history() {
        $this->load->model('sekretariat/Sekretariat_model');
        $id = $this->input->get('id');
        $type = $this->input->get('type');
        $history = [];
        if ($type == 'personel_lapangan') {
            $history = $this->Sekretariat_model->get_personel_history($id);
        } elseif ($type == 'personel_k3') {
            $history = $this->Sekretariat_model->get_personel_k3_history($id);
        } else {
            $history = $this->Sekretariat_model->get_peralatan_history($id);
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($history));
    }

    // --- DELETE ENDPOINTS ---

    public function personel_lapangan_delete($id) {
        $this->load->model('sekretariat/Personel_lapangan_model');
        if ($this->Personel_lapangan_model->delete($id)) {
            $this->session->set_flashdata('success', 'Personel berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus personel.');
        }
        redirect('admin/personel_lapangan');
    }

    public function personel_k3_delete($id) {
        $this->load->model('sekretariat/Personel_k3_model');
        if ($this->Personel_k3_model->delete($id)) {
            $this->session->set_flashdata('success', 'Personel K3 berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus personel K3.');
        }
        redirect('admin/personel_k3');
    }

    public function peralatan_delete($id) {
        $this->load->model('sekretariat/Peralatan_model');
        if ($this->Peralatan_model->delete($id)) {
            $this->session->set_flashdata('success', 'Peralatan berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus peralatan.');
        }
        redirect('admin/peralatan');
    }

    public function pemilik_alat_delete($id) {
        $this->load->model('sekretariat/Pemilik_alat_model');
        if ($this->Pemilik_alat_model->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Pemilik alat berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus pemilik alat.']);
        }
    }

    public function pemilik_alat_save() {
        $this->load->model('sekretariat/Pemilik_alat_model');
        $data = [
            'nama_pemilik' => $this->input->post('nama_pemilik'),
            'jenis_pemilik' => $this->input->post('jenis_pemilik'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email')
        ];
        if ($this->Pemilik_alat_model->create($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Pemilik alat berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan pemilik alat.']);
        }
    }

    public function pemilik_alat_update($id) {
        $this->load->model('sekretariat/Pemilik_alat_model');
        $data = [
            'nama_pemilik' => $this->input->post('nama_pemilik'),
            'jenis_pemilik' => $this->input->post('jenis_pemilik'),
            'alamat' => $this->input->post('alamat'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email')
        ];
        if ($this->Pemilik_alat_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Pemilik alat berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate pemilik alat.']);
        }
    }

    public function pemilik_alat_detail_json() {
        $id = $this->input->get('id');
        $this->db->select('*');
        $this->db->from('peralatan');
        $this->db->where('pemilik_alat_id', $id);
        $data = $this->db->get()->result();
        echo json_encode(['data' => $data]);
    }

    public function regulasi_delete($id) {
        $this->load->model('sekretariat/Regulasi_model');
        if ($this->Regulasi_model->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Regulasi berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus regulasi.']);
        }
    }

    public function get_regulasi_statistics() {
        $this->load->model('sekretariat/Regulasi_model');
        $data['statistics'] = $this->Regulasi_model->get_statistics();
        $data['available_years'] = $this->Regulasi_model->get_available_years();
        echo json_encode($data);
    }

    public function regulasi_save() {
        $this->load->model('sekretariat/Regulasi_model');
        $data = [
            'instansi' => $this->input->post('instansi'),
            'jenis_regulasi' => $this->input->post('jenis_regulasi'),
            'nomor_regulasi' => $this->input->post('nomor_regulasi'),
            'tahun' => $this->input->post('tahun'),
            'judul' => $this->input->post('judul'),
            'tentang' => $this->input->post('tentang'),
            'status' => $this->input->post('status') ?: 'Berlaku'
        ];

        if (!empty($_FILES['file_regulasi']['name'])) {
            $file_name = $this->Regulasi_model->upload_file('file_regulasi');
            if ($file_name) $data['file_regulasi'] = $file_name;
        }

        if ($this->Regulasi_model->create($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Regulasi berhasil ditambahkan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan regulasi.']);
        }
    }

    public function regulasi_update($id) {
        $this->load->model('sekretariat/Regulasi_model');
        $data = [
            'instansi' => $this->input->post('instansi'),
            'jenis_regulasi' => $this->input->post('jenis_regulasi'),
            'nomor_regulasi' => $this->input->post('nomor_regulasi'),
            'tahun' => $this->input->post('tahun'),
            'judul' => $this->input->post('judul'),
            'tentang' => $this->input->post('tentang'),
            'status' => $this->input->post('status') ?: 'Berlaku'
        ];

        if (!empty($_FILES['file_regulasi']['name'])) {
            $file_name = $this->Regulasi_model->upload_file('file_regulasi');
            if ($file_name) $data['file_regulasi'] = $file_name;
        }

        if ($this->Regulasi_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Regulasi berhasil diupdate.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate regulasi.']);
        }
    }

    // --- MANAGEMENT PER COMPANY ---

    public function manage($penyedia_id) {
        $this->load->model('sekretariat/Sekretariat_model');
        $data['company'] = $this->Sekretariat_model->get_company_by_id($penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('sekretariat/manage_company', $data);
        $this->load->view('layout/footer');
    }

    public function personel($penyedia_id) {
        $this->load->model('sekretariat/Sekretariat_model');
        $this->load->model('sekretariat/Personel_lapangan_model');
        $data['company'] = $this->Sekretariat_model->get_company_by_id($penyedia_id);
        $data['personel'] = $this->Personel_lapangan_model->get_by_penyedia($penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('sekretariat/personel_managed', $data);
        $this->load->view('layout/footer');
    }

    // --- USER MANAGEMENT ---

    public function toggle_status($user_id, $status) {
        $this->Admin_model->update_user_status($user_id, $status);
        $this->session->set_flashdata('success', 'Status user berhasil diperbarui.');
        redirect('admin/verifikasi_penyedia');
    }

    public function akun_pokja() {
        $data['pokja'] = $this->Admin_model->get_pokja_accounts();
        $this->load->view('layout/header');
        $this->load->view('akun_pokja', $data);
        $this->load->view('layout/footer');
    }

    public function create_pokja_process() {
        $data = [
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password')
        ];
        
        if ($this->Admin_model->create_pokja($data)) {
            $this->session->set_flashdata('success', 'Akun Pokja berhasil dibuat.');
        } else {
            $this->session->set_flashdata('error', 'Gagal membuat akun Pokja.');
        }
        redirect('admin/akun_pokja');
    }

    public function akun_sekretariat() {
        $data['sekretariat'] = $this->Admin_model->get_sekretariat_accounts();
        $this->load->view('layout/header');
        $this->load->view('akun_sekretariat', $data);
        $this->load->view('layout/footer');
    }

    public function create_sekretariat_process() {
        $data = [
            'username' => $this->input->post('username'),
            'password' => $this->input->post('password')
        ];
        
        if ($this->Admin_model->create_sekretariat($data)) {
            $this->session->set_flashdata('success', 'Akun Sekretariat berhasil dibuat.');
        } else {
            $this->session->set_flashdata('error', 'Gagal membuat akun Sekretariat.');
        }
        redirect('admin/akun_sekretariat');
    }
    public function edit_user($user_id) {
        $data['user'] = $this->Admin_model->get_user_by_id($user_id);
        if (!$data['user']) {
            show_404();
        }
        $this->load->view('layout/header');
        $this->load->view('edit_user', $data);
        $this->load->view('layout/footer');
    }

    public function update_user_process() {
        $user_id = $this->input->post('id');
        $data = [
            'username' => $this->input->post('username'),
            'role' => $this->input->post('role'),
            'status_aktif' => $this->input->post('status_aktif')
        ];

        $password = $this->input->post('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->Admin_model->update_user($user_id, $data)) {
            $this->session->set_flashdata('success', 'User updated successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to update user.');
        }

        if ($data['role'] == 'pokja') {
            redirect('admin/akun_pokja');
        } elseif ($data['role'] == 'sekretariat') {
            redirect('admin/akun_sekretariat');
        } else {
            redirect('admin/verifikasi_penyedia');
        }
    }
}
