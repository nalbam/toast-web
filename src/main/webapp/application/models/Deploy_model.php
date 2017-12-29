<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Deploy_model extends CI_Model
{

    private $table = 'deploy';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList($s_no, $t_no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['s_no' => $s_no, 't_no' => $t_no]);
        return $query->result();
    }

    public function getOne($s_no, $t_no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['s_no' => $s_no, 't_no' => $t_no]);
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

    public function delete($no)
    {
        $this->db->where('no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

    public function deleteServer($no)
    {
        $this->db->where('s_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

    public function deleteTarget($no)
    {
        $this->db->where('t_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

}
