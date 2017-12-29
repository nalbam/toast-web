<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Phase_model extends CI_Model
{

    private $table = 'phase';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList($o_no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['o_no' => $o_no]);
        return $query->result();
    }

    public function getOne($o_no, $no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['no' => $no, 'o_no' => $o_no]);
        return $query->row();
    }

    public function getOneByName($o_no, $phase)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['o_no' => $o_no, 'phase' => $phase]);
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

}
