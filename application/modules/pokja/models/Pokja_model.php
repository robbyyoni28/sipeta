<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pokja_model extends CI_Model {

    public function search_tender($keyword = '', $tahun = '') {
        $this->db->select('tender.*, penyedia.nama_perusahaan')
                 ->from('tender')
                 ->join('penyedia', 'tender.penyedia_id = penyedia.id');
        
        if($tahun) {
            $this->db->where('tender.tahun_anggaran', $tahun);
        }

        if($keyword) {
            $this->db->group_start()
                     ->like('tender.kode_tender', $keyword)
                     ->or_like('tender.satuan_kerja', $keyword)
                     ->or_like('penyedia.nama_perusahaan', $keyword)
                     ->group_end();
        }
        return $this->db->get()->result();
    }

    public function get_available_years() {
        $query = $this->db->select('DISTINCT(tahun_anggaran) as tahun')
                          ->from('tender')
                          ->order_by('tahun_anggaran', 'DESC')
                          ->get();
        return $query->result_array();
    }

    public function get_tender_detail($tender_id) {
        $data['tender'] = $this->db->select('tender.*, penyedia.nama_perusahaan')
                                   ->from('tender')
                                   ->join('penyedia', 'tender.penyedia_id = penyedia.id')
                                   ->where('tender.id', $tender_id)
                                   ->get()->row();
        
        $data['personel'] = $this->db->select('personel_lapangan.*')
                                     ->from('tender_personel_lapangan')
                                     ->join('personel_lapangan', 'tender_personel_lapangan.personel_lapangan_id = personel_lapangan.id')
                                     ->where('tender_personel_lapangan.tender_id', $tender_id)
                                     ->get()->result();

        $data['personel_k3'] = $this->db->select('personel_k3.*')
                                     ->from('tender_personel_k3')
                                     ->join('personel_k3', 'tender_personel_k3.personel_k3_id = personel_k3.id')
                                     ->where('tender_personel_k3.tender_id', $tender_id)
                                     ->get()->result();
        
        $data['peralatan'] = $this->db->select('peralatan.*')
                                      ->from('tender_peralatan')
                                      ->join('peralatan', 'tender_peralatan.peralatan_id = peralatan.id')
                                      ->where('tender_peralatan.tender_id', $tender_id)
                                      ->get()->result();
        return $data;
    }

    public function get_personel_history($personel_id) {
        return $this->db->select('tender.kode_tender, tender.satuan_kerja AS nama_tender, penyedia.nama_perusahaan')
                        ->from('tender_personel_lapangan')
                        ->join('tender', 'tender_personel_lapangan.tender_id = tender.id')
                        ->join('penyedia', 'tender.penyedia_id = penyedia.id')
                        ->where('tender_personel_lapangan.personel_lapangan_id', $personel_id)
                        ->get()->result();
    }

    public function get_peralatan_history($peralatan_id) {
        return $this->db->select('tender.kode_tender, tender.satuan_kerja AS nama_tender, penyedia.nama_perusahaan')
                        ->from('tender_peralatan')
                        ->join('tender', 'tender_peralatan.tender_id = tender.id')
                        ->join('penyedia', 'tender.penyedia_id = penyedia.id')
                        ->where('tender_peralatan.peralatan_id', $peralatan_id)
                        ->get()->result();
    }
    public function search_personel($keyword = '') {
        $this->db->select('personel_lapangan.*, penyedia.nama_perusahaan')
                 ->from('personel_lapangan')
                 ->join('penyedia', 'personel_lapangan.penyedia_id = penyedia.id');
        if($keyword) {
            $this->db->group_start()
                     ->like('personel_lapangan.nama', $keyword)
                     ->or_like('personel_lapangan.nik', $keyword)
                     ->or_like('personel_lapangan.nomor_skk', $keyword)
                     ->group_end();
        }
        return $this->db->get()->result();
    }

    public function search_peralatan($keyword = '') {
        $this->db->select('peralatan.*, penyedia.nama_perusahaan')
                 ->from('peralatan')
                 ->join('penyedia', 'peralatan.penyedia_id = penyedia.id');
        if($keyword) {
            $this->db->group_start()
                     ->like('peralatan.nama_alat', $keyword)
                     ->or_like('peralatan.plat_serial', $keyword)
                     ->or_like('peralatan.merk', $keyword)
                     ->group_end();
        }
        return $this->db->get()->result();
    }

    public function get_personel_by_id($id) {
        return $this->db->select('personel_lapangan.*, penyedia.nama_perusahaan')
                        ->from('personel_lapangan')
                        ->join('penyedia', 'personel_lapangan.penyedia_id = penyedia.id')
                        ->where('personel_lapangan.id', $id)
                        ->get()->row();
    }

    public function get_peralatan_by_id($id) {
        return $this->db->select('peralatan.*, penyedia.nama_perusahaan')
                        ->from('peralatan')
                        ->join('penyedia', 'peralatan.penyedia_id = penyedia.id')
                        ->where('peralatan.id', $id)
                        ->get()->row();
    }
}
