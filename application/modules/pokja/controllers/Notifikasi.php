<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifikasi extends MX_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('role') !== 'pokja' && $this->session->userdata('role') !== 'admin') {
            redirect('auth');
        }
    }

    public function get_count() {
        $count = 0;
        $role = $this->session->userdata('role');
        $since = (int) $this->input->get('since');

        if (!$this->db->table_exists('tender') || !$this->db->field_exists('tanggal_input', 'tender')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['count' => 0]));
            return;
        }
        
        if ($role === 'pokja') {
            // Count new tenders added by sekretariat
            $this->db->where('DATE(tanggal_input) = CURDATE()', null, false);
            if ($since > 0) {
                $this->db->where('tanggal_input > FROM_UNIXTIME(' . $since . ')', null, false);
            }
            $count = $this->db->count_all_results('tender');
        } elseif ($role === 'admin') {
            // Count all new activities in last 24 hours
            $this->db->where('DATE(tanggal_input) = CURDATE()', null, false);
            if ($since > 0) {
                $this->db->where('tanggal_input > FROM_UNIXTIME(' . $since . ')', null, false);
            }
            $count = $this->db->count_all_results('tender');
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['count' => $count]));
    }

    public function get_list() {
        $notifications = [];
        $role = $this->session->userdata('role');

        if (!$this->db->table_exists('tender') || !$this->db->field_exists('tanggal_input', 'tender')) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['notifications' => []]));
            return;
        }
        
        if ($role === 'pokja') {
            // Get recent tenders added by sekretariat
            $this->db->select('tender.*, penyedia.nama_perusahaan, users.username as created_by_name');
            $this->db->from('tender');
            $this->db->join('penyedia', 'penyedia.id = tender.penyedia_id', 'left');
            $this->db->join('users', 'users.username = tender.created_by', 'left');
            $this->db->where('DATE(tender.tanggal_input) = CURDATE()', null, false);
            $this->db->order_by('tender.tanggal_input', 'DESC');
            $this->db->limit(10);
            
            $tenders = $this->db->get()->result();
            
            foreach ($tenders as $tender) {
                $time_ago = $this->time_ago(strtotime($tender->tanggal_input));
                $created_by_name = $tender->created_by_name ? $tender->created_by_name : '-';
                $notifications[] = [
                    'title' => 'Tender Baru Ditambahkan',
                    'message' => "{$created_by_name} menambahkan tender {$tender->satuan_kerja}",
                    'time' => $time_ago,
                    'icon' => 'fa-star',
                    'color' => 'success'
                ];
            }
        } elseif ($role === 'admin') {
            // Get all recent activities
            $this->db->select('tender.*, penyedia.nama_perusahaan, users.username as created_by_name, users.role as created_role');
            $this->db->from('tender');
            $this->db->join('penyedia', 'penyedia.id = tender.penyedia_id', 'left');
            $this->db->join('users', 'users.username = tender.created_by', 'left');
            $this->db->where('DATE(tender.tanggal_input) = CURDATE()', null, false);
            $this->db->order_by('tender.tanggal_input', 'DESC');
            $this->db->limit(10);
            
            $tenders = $this->db->get()->result();
            
            foreach ($tenders as $tender) {
                $time_ago = $this->time_ago(strtotime($tender->tanggal_input));
                $role_text = $tender->created_role ? ucfirst($tender->created_role) : 'User';
                $created_by_name = $tender->created_by_name ? $tender->created_by_name : '-';
                $notifications[] = [
                    'title' => 'Aktivitas Tender',
                    'message' => "{$role_text} {$created_by_name} menambahkan tender {$tender->satuan_kerja}",
                    'time' => $time_ago,
                    'icon' => 'fa-clipboard-list',
                    'color' => 'info'
                ];
            }
        }
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['notifications' => $notifications]));
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
