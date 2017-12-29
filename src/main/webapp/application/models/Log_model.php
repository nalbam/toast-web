<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Log_model extends CI_Model
{

    private $table = 'log';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList($s_no = null)
    {
        if (!empty($s_no)) {
            $this->db->where('s_no', $s_no);
        }
        $this->db->order_by('reg_date', 'desc');
        $query = $this->db->get(TABLE_PREFIX . $this->table, 0, 20);
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
        $this->db->where('no', $no);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function delete($no)
    {
        $this->db->where('no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

}
