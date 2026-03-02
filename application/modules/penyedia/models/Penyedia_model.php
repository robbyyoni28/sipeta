<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penyedia_model extends CI_Model {

    // Personel Lapangan
    public function get_personel($penyedia_id) {
        return $this->db->get_where('personel_lapangan', ['penyedia_id' => $penyedia_id])->result();
    }

    public function add_personel($data) {
        $data['created_by'] = $this->session->userdata('username');
        return $this->db->insert('personel_lapangan', $data);
    }

    public function update_personel($id, $data) {
        return $this->db->where('id', $id)->update('personel_lapangan', $data);
    }

    public function delete_personel($id) {
        return $this->db->where('id', $id)->delete('personel_lapangan');
    }

    // Peralatan
    public function get_peralatan($penyedia_id) {
        return $this->db->get_where('peralatan', ['penyedia_id' => $penyedia_id])->result();
    }

    public function add_peralatan($data) {
        $data['created_by'] = $this->session->userdata('username');
        return $this->db->insert('peralatan', $data);
    }

    public function update_peralatan($id, $data) {
        return $this->db->where('id', $id)->update('peralatan', $data);
    }

    public function delete_peralatan($id) {
        return $this->db->where('id', $id)->delete('peralatan');
    }

    // Tender
    public function get_tenders($penyedia_id) {
        return $this->db->get_where('tender', ['penyedia_id' => $penyedia_id])->result();
    }

    public function add_tender($tender_data, $personel_ids, $peralatan_ids) {
        $this->db->trans_start();
        
        $tender_data['created_by'] = $this->session->userdata('username');
        $this->db->insert('tender', $tender_data);
        $tender_id = $this->db->insert_id();
        
        foreach ($personel_ids as $p_id) {
            $this->db->insert('tender_personel_lapangan', ['tender_id' => $tender_id, 'personel_lapangan_id' => $p_id]);
        }
        
        foreach ($peralatan_ids as $pl_id) {
            $this->db->insert('tender_peralatan', ['tender_id' => $tender_id, 'peralatan_id' => $pl_id]);
        }
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }
}
