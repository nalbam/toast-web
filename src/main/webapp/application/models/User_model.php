<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model
{

    private $table = 'user';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList($o_no)
    {
        $this->db->order_by('no', 'desc');
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['o_no' => $o_no]);
        return $query->result();
    }

    public function getOne($o_no, $no, $provider = null)
    {
        $data = [
            'no' => $no,
            'o_no' => $o_no
        ];
        if (!empty($provider)) {
            $data['provider'] = $provider;
        }
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, $data);
        return $query->row();
    }

    public function getOneByMemberNo($o_no, $memberNo)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['o_no' => $o_no, 'memberNo' => $memberNo]);
        return $query->row();
    }

    public function getListByFleetStar($f_no)
    {
        $query = "
            select u.*
              from " . TABLE_PREFIX . $this->table . " u
              join " . TABLE_PREFIX . "fleet_star fs on fs.u_no = u.no
             where fs.f_no = '$f_no'
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function insert($data)
    {
        $this->db->insert(TABLE_PREFIX . $this->table, $data);
        return $this->db->insert_id();
    }

    public function update($no, $data)
    {
        $this->db->set('con_date', 'NOW()', false);
        $this->db->where('no', $no);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function delete($no)
    {
        $this->db->where('no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

}
