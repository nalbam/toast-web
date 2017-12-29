<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ip_model extends CI_Model
{

    private $table = 'ip';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList($phase = null)
    {
        if (empty($phase)) {
            $query = "
                SELECT i.*, f.phase, f.fleet, s.id AS s_id, s.name
                  FROM " . TABLE_PREFIX . $this->table . " i
                  LEFT JOIN " . TABLE_PREFIX . "server s ON s.no = i.s_no
                  LEFT JOIN " . TABLE_PREFIX . "fleet f  ON f.no = i.f_no
                 ORDER BY f.phase, f.fleet, s.name
            ";
        } else {
            $query = "
                SELECT i.*, f.phase, f.fleet, s.id AS s_id, s.name
                  from " . TABLE_PREFIX . $this->table . " i
                  left join " . TABLE_PREFIX . "server s on s.no = i.s_no
                  left join " . TABLE_PREFIX . "fleet f  on f.no = i.f_no
                 where f.phase = '$phase'
                 ORDER BY f.phase, f.fleet, s.name
            ";
        }
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListByFleet($f_no)
    {
        $query = "
            SELECT i.*, f.phase, f.fleet, s.id AS s_id, s.name
              from " . TABLE_PREFIX . $this->table . " i
              left join " . TABLE_PREFIX . "server s on s.no = i.s_no
              left join " . TABLE_PREFIX . "fleet f  on f.no = i.f_no
             where i.f_no = '$f_no'
            ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getOne($no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['no' => $no]);
        return $query->row();
    }

    public function getOneFree($f_no)
    {
        $this->db->limit(1);
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['f_no' => $f_no, 's_no' => 0]);
        return $query->row();
    }

    public function getOneById($id)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['id' => $id]);
        return $query->row();
    }

    public function getOneByServer($s_no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['s_no' => $s_no]);
        return $query->row();
    }

    public function insert($data)
    {
        $this->db->insert(TABLE_PREFIX . $this->table, $data);
        return $this->db->insert_id();
    }

    public function update($no, $data)
    {
        $this->db->set('mod_date', 'NOW()', false);
        $this->db->where('no', $no);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function updateById($id, $data)
    {
        $this->db->set('mod_date', 'NOW()', false);
        $this->db->where('id', $id);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function delete($no)
    {
        $this->db->where('no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

    public function deleteFleet($f_no)
    {
        $this->db->where('f_no', $f_no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

}
