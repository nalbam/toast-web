<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Version_model extends CI_Model
{

    private $table = 'version';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList($p_no = null)
    {
        if (!empty($p_no)) {
            $this->db->where('p_no', $p_no);
        }
        $this->db->order_by('reg_date', 'desc');
        $query = $this->db->get(TABLE_PREFIX . $this->table);
        return $query->result();
    }

    public function getOldList($p_no)
    {
        $this->db->where('p_no', $p_no);
        $this->db->order_by('mod_date', 'desc');
        $query = $this->db->get(TABLE_PREFIX . $this->table, 20, 20);
        return $query->result();
    }

    public function getLastVersion($p_no, $branch = null)
    {
        $this->db->where('p_no', $p_no);
        if (!empty($branch)) {
            $this->db->where('branch', $branch);
        }
        $this->db->order_by('mod_date', 'desc');
        $query = $this->db->get(TABLE_PREFIX . $this->table, 0, 1);
        return $query->row();
    }

    public function getOne($no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['no' => $no]);
        return $query->row();
    }

    public function getOneByVersion($p_no, $version)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['p_no' => $p_no, 'version' => $version]);
        return $query->row();
    }

    public function insert($data)
    {
        $this->db->set('mod_date', 'NOW()', false);
        $this->db->insert(TABLE_PREFIX . $this->table, $data);
        return $this->db->insert_id();
    }

    public function update($no, $data)
    {
        $data = array_filter($data, function ($value) {
            return !empty($value);
        });
        $this->db->set('mod_date', 'NOW()', false);
        $this->db->where('no', $no);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function updateStatus($no, $status)
    {
        $this->db->set('mod_date', 'NOW()', false);
        $this->db->where('no', $no);
        $this->db->where('status < ' . $status);
        $this->db->update(TABLE_PREFIX . $this->table, ['status' => $status]);
    }

    public function delete($no)
    {
        $this->db->where('no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

    public function deleteProject($no)
    {
        $this->db->where('p_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

}
