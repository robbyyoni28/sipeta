<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend extends MX_Controller {

    public function index() {
        $data['total_penyedia'] = $this->db->count_all('penyedia');
        $data['total_personel'] = $this->db->count_all('personel_lapangan');
        $data['total_peralatan'] = $this->db->count_all('peralatan');
        $data['total_tender'] = $this->db->count_all('tender');
        
        $this->load->view('home', $data);
    }
}
