<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Project_model extends CI_Model
{

    private $table = 'project';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList($o_no, $u_no = 0)
    {
        $query = "
            select p.*, ps.u_no
              from " . TABLE_PREFIX . $this->table . " p
              left join " . TABLE_PREFIX . $this->table . "_star ps on ps.u_no = '$u_no' and ps.p_no = p.no
             where p.o_no = '$o_no'
             order by ps.u_no desc, p.name
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListApk($o_no, $u_no = 0)
    {
        $query = "
            select p.*, ps.u_no
              from " . TABLE_PREFIX . $this->table . " p where p.packaging = 'apk'
              left join " . TABLE_PREFIX . $this->table . "_star ps on ps.u_no = '$u_no' and ps.p_no = p.no
             where p.o_no = '$o_no'
             order by ps.u_no desc, p.name
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListStar($o_no, $u_no = 0)
    {
        $query = "
            select p.*, ps.u_no
              from " . TABLE_PREFIX . $this->table . " p
              join " . TABLE_PREFIX . $this->table . "_star ps on ps.u_no = '$u_no' and ps.p_no = p.no
             where p.o_no = '$o_no'
             order by p.name
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getOne($o_no, $no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['no' => $no, 'o_no' => $o_no]);
        return $query->row();
    }

    public function getOneByName($o_no, $artifactId)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['o_no' => $o_no, 'artifactId' => $artifactId]);
        return $query->row();
    }

    public function insert($data)
    {
        $data = array_filter($data, function ($value) {
            return !empty($value);
        });
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

    public function updateVersion($no, $data)
    {
        $this->db->where('no', $no);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function delete($no)
    {
        $this->db->where('no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

    public function toggleStar($no, $u_no)
    {
        $data = ['p_no' => $no, 'u_no' => $u_no];
        $query = $this->db->get_where(TABLE_PREFIX . $this->table . '_star', $data);
        $one = $query->row();

        if (empty($one)) {
            $this->db->insert(TABLE_PREFIX . $this->table . '_star', $data);
            return $this->db->insert_id();

        } else {
            $this->db->where('no', $one->no);
            $this->db->delete(TABLE_PREFIX . $this->table . '_star');
            return 0;
        }
    }

    public function deleteStar($no)
    {
        $this->db->where('p_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table . '_star');
    }

    public function deleteStarByUser($no)
    {
        $this->db->where('u_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table . '_star');
    }

}
