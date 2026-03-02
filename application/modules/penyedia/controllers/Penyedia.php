<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penyedia extends MX_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('role') !== 'penyedia') {
            redirect('auth');
        }
        $this->load->model('Penyedia_model');
        $this->penyedia_id = $this->session->userdata('penyedia_id');
    }

    public function index() {
        $data['total_personel'] = $this->db->where('penyedia_id', $this->penyedia_id)->count_all_results('personel_lapangan');
        $data['total_peralatan'] = $this->db->where('penyedia_id', $this->penyedia_id)->count_all_results('peralatan');
        $data['total_tender'] = $this->db->where('penyedia_id', $this->penyedia_id)->count_all_results('tender');
        
        $this->load->view('layout/header');
        $this->load->view('dashboard', $data);
        $this->load->view('layout/footer');
    }

    // PERSONEL
    public function personel() {
        $data['personel'] = $this->Penyedia_model->get_personel($this->penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('personel', $data);
        $this->load->view('layout/footer');
    }

    public function personel_add() {
        $config['upload_path'] = './assets/uploads/skk/';
        $config['allowed_types'] = 'pdf|jpg|png';
        $config['max_size'] = 2048;
        $this->load->library('upload', $config);

        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);

        $file_skk = '';
        if ($this->upload->do_upload('file_skk')) {
            $file_skk = $this->upload->data('file_name');
        }

        $file_sp = '';
        if ($this->upload->do_upload('file_surat_pernyataan')) {
            $file_sp = $this->upload->data('file_name');
        }

        $data = [
            'penyedia_id' => $this->penyedia_id,
            'nama' => $this->input->post('nama'),
            'nik' => $this->input->post('nik'),
            'jenis_skk' => $this->input->post('jenis_skk'),
            'nomor_skk' => $this->input->post('nomor_skk'),
            'jabatan' => $this->input->post('jabatan'),
            'masa_berlaku_skk' => $this->input->post('masa_berlaku_skk'),
            'file_skk' => $file_skk,
            'file_surat_pernyataan' => $file_sp
        ];

        $this->Penyedia_model->add_personel($data);
        redirect('penyedia/personel');
    }

    // PERALATAN
    public function peralatan() {
        $data['peralatan'] = $this->Penyedia_model->get_peralatan($this->penyedia_id);
        $this->load->view('layout/header');
        $this->load->view('peralatan', $data);
        $this->load->view('layout/footer');
    }

    public function peralatan_add() {
        $config['upload_path'] = './assets/uploads/peralatan/';
        $config['allowed_types'] = 'pdf|jpg|png';
        $config['max_size'] = 2048;
        $this->load->library('upload', $config);
        
        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);

        $file_bukti = '';
        if ($this->upload->do_upload('file_bukti')) {
            $file_bukti = $this->upload->data('file_name');
        }

        $file_dok = '';
        if ($this->upload->do_upload('file_dokumentasi')) {
            $file_dok = $this->upload->data('file_name');
        }

        $data = [
            'penyedia_id' => $this->penyedia_id,
            'nama_alat' => $this->input->post('nama_alat'),
            'merk' => $this->input->post('merk'),
            'tipe' => $this->input->post('tipe'),
            'kapasitas' => $this->input->post('kapasitas'),
            'plat_serial' => $this->input->post('plat_serial'),
            'bukti_kepemilikan' => $this->input->post('bukti_kepemilikan'),
            'file_bukti' => $file_bukti,
            'file_dokumentasi' => $file_dok
        ];

        $this->Penyedia_model->add_peralatan($data);
        redirect('penyedia/peralatan');
    }

    // TENDER
    public function tender() {
        $data['tender'] = $this->Penyedia_model->get_tenders($this->penyedia_id);
        $data['personel'] = $this->Penyedia_model->get_personel($this->penyedia_id);
        $data['peralatan'] = $this->Penyedia_model->get_peralatan($this->penyedia_id);
        
        $this->load->view('layout/header');
        $this->load->view('tender', $data);
        $this->load->view('layout/footer');
    }

    public function tender_add() {
        $tender_data = [
            'penyedia_id' => $this->penyedia_id,
            'kode_tender' => $this->input->post('kode_tender'),
            'nama_tender' => $this->input->post('nama_tender')
        ];
        
        $personel_ids = $this->input->post('personel_ids');
        $peralatan_ids = $this->input->post('peralatan_ids');
        
        $this->Penyedia_model->add_tender($tender_data, $personel_ids, $peralatan_ids);
        redirect('penyedia/tender');
    }
}
