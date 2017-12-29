<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Build_model extends CI_Model
{

    private $table = 'build';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList($p_no = null)
    {
        $data = [];
        if (!empty($p_no)) {
            $data['p_no'] = $p_no;
        }
        $this->db->order_by('build_date', 'desc');
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, $data);
        return $query->result();
    }

    public function getOne($no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['no' => $no]);
        return $query->row();
    }

    public function insert($data)
    {
        $this->db->insert(TABLE_PREFIX . $this->table, $data);
        return $this->db->insert_id();
    }

    public function update($no, $data)
    {
        $this->db->set('build_date', 'NOW()', false);
        $this->db->where('no', $no);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function delete($no)
    {
        $this->db->where('no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

}
