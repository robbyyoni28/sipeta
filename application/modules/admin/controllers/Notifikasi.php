<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifikasi extends MX_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('role') !== 'admin') {
            redirect('auth');
        }
        // Models not used in the current methods to prevent 500 errors if module paths are incorrect
        // $this->load->model(['Sekretariat_model', 'Penyedia_model']);
    }

    public function get_count() {
        // Count all new activities in last 24 hours
        $count = $this->db->where('tanggal_input >= DATE_SUB(NOW(), INTERVAL 24 HOUR)', null, false)
                         ->count_all_results('tender');
        
        echo json_encode(['count' => $count]);
    }

    public function get_list() {
        $notifications = [];
        
        // Get all recent activities
        $this->db->select('tender.*, penyedia.nama_perusahaan, users.username as created_by_name, users.role as created_role');
        $this->db->from('tender');
        $this->db->join('penyedia', 'penyedia.id = tender.penyedia_id', 'left');
        $this->db->join('users', 'users.username = tender.created_by', 'left');
        $this->db->where('tender.tanggal_input >= DATE_SUB(NOW(), INTERVAL 7 DAY)', null, false);
        $this->db->order_by('tender.tanggal_input', 'DESC');
        $this->db->limit(10);
        
        $tenders = $this->db->get()->result();
        
        foreach ($tenders as $tender) {
            $time_ago = $this->time_ago(strtotime($tender->tanggal_input));
            $role_text = ucfirst($tender->created_role);
            $notifications[] = [
                'title' => 'Aktivitas Tender',
                'message' => "{$role_text} {$tender->created_by_name} menambahkan tender {$tender->satuan_kerja}",
                'time' => $time_ago,
                'icon' => 'fa-clipboard-list',
                'color' => 'info'
            ];
        }
        
        echo json_encode(['notifications' => $notifications]);
    }
    
    private function time_ago($timestamp) {
        $difference = time() - $timestamp;
        $periods = array("detik", "menit", "jam", "hari", "minggu", "bulan", "tahun", "dekade");
        $lengths = array("60","60","24","7","4.35","12","10");

        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);
        
        if ($difference != 1) {
            $periods[$j] .= "";
        }

        return "$difference $periods[$j] yang lalu";
    }
}
