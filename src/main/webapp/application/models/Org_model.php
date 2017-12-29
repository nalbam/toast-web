<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Org_model extends CI_Model
{

    private $table = 'org';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList()
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, []);
        return $query->result();
    }

    public function getListByUno($u_no)
    {
        $query = "
            select o.*, u.no as u_no, u.username
              from " . TABLE_PREFIX . $this->table . " o
              join " . TABLE_PREFIX . "user u on u.no = '$u_no'
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getOne($no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['no' => $no]);
        return $query->row();
    }

    public function getOneById($id)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['id' => $id]);
        return $query->row();
    }

    public function insert($data)
    {
        $this->db->insert(TABLE_PREFIX . $this->table, $data);
        return $this->db->insert_id();
    }

    public function update($no, $data)
    {
        $this->db->where('no', $no);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function delete($no)
    {
        $this->db->where('no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

}
