<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Target_model extends CI_Model
{

    private $table = 'target';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList($f_no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['f_no' => $f_no]);
        return $query->result();
    }

    public function getListByFleet($f_no, $no = null)
    {
        if (empty($no)) {
            $query = "
                select t.*, p.groupId, p.artifactId, p.packaging, v.status
                  from " . TABLE_PREFIX . $this->table . " t
                  join " . TABLE_PREFIX . "project p on p.no = t.p_no
                  join " . TABLE_PREFIX . "fleet f   on f.no = t.f_no
                  left join " . TABLE_PREFIX . "version v on v.p_no = t.p_no and v.version = t.version
                 where t.f_no = '$f_no'
                 order by p.artifactId, t.domain
            ";
        } else {
            $query = "
                select t.*, p.groupId, p.artifactId, p.packaging, v.status
                  from " . TABLE_PREFIX . $this->table . " t
                  join " . TABLE_PREFIX . "project p on p.no = t.p_no
                  join " . TABLE_PREFIX . "fleet f   on f.no = t.f_no
                  left join " . TABLE_PREFIX . "version v on v.p_no = t.p_no and v.version = t.version
                 where t.no = '$no'
                   and t.f_no = '$f_no'
                 order by p.artifactId, t.domain
            ";
        }
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListByServer($s_no)
    {
        $query = "
            select t.*, s.no as s_no, p.groupId, p.artifactId, p.packaging, ifnull(d.deployed, '-') as deployed
              from " . TABLE_PREFIX . $this->table . " t
              join " . TABLE_PREFIX . "server s  on s.f_no = t.f_no
              join " . TABLE_PREFIX . "project p on p.no = t.p_no
              left join " . TABLE_PREFIX . "deploy d on d.s_no = s.no and d.t_no = t.no
             where s.no = '$s_no'
             order by p.artifactId, t.domain
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListByProject($p_no)
    {
        $query = "
            select t.*, f.phase, f.fleet, p.groupId, p.artifactId, p.packaging
              from " . TABLE_PREFIX . $this->table . " t
              join " . TABLE_PREFIX . "project p on p.no = t.p_no
              join " . TABLE_PREFIX . "fleet f   on f.no = t.f_no
             where t.p_no = '$p_no'
             order by f.phase, f.fleet, p.artifactId, t.domain
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getOne($no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['no' => $no]);
        return $query->row();
    }

    public function getOneForDeploy($no)
    {
        $query = "
            select t.*, p.groupId, p.artifactId, p.packaging, f.phase, f.fleet
              from " . TABLE_PREFIX . $this->table . " t
              join " . TABLE_PREFIX . "project p on t.p_no = p.no
              join " . TABLE_PREFIX . "fleet f on t.f_no = f.no
             where t.no = '$no'
        ";
        $query = $this->db->query($query);
        return $query->row();
    }

    public function getOneByServer($s_no, $p_no)
    {
        $query = "
            select t.*
              from " . TABLE_PREFIX . $this->table . " t
              join " . TABLE_PREFIX . "server s  on s.f_no = t.f_no
             where s.no = '$s_no'
               and t.p_no = '$p_no'
        ";
        $query = $this->db->query($query);
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

    public function deleteFleet($no)
    {
        $this->db->where('f_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

    public function deleteProject($no)
    {
        $this->db->where('p_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

}
