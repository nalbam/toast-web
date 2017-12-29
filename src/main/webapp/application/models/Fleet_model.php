<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fleet_model extends CI_Model
{

    private $table = 'fleet';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList($o_no, $u_no = 0)
    {
        $query = "
            select f.*, fs.u_no, ifnull(s.servers, 0) as servers
              from " . TABLE_PREFIX . $this->table . " f
              left join " . TABLE_PREFIX . $this->table . "_star fs on fs.u_no = '$u_no' and fs.f_no = f.no
              left join (select f_no, count(no) as servers from " . TABLE_PREFIX . "server group by f_no) s on s.f_no = f.no
             where f.o_no = '$o_no'
             order by fs.u_no desc, f.phase, f.fleet
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListStar($o_no, $u_no = 0)
    {
        $query = "
            select f.*, fs.u_no, ifnull(s.servers, 0) as servers
              from " . TABLE_PREFIX . $this->table . " f
              join " . TABLE_PREFIX . $this->table . "_star fs on fs.u_no = '$u_no' and fs.f_no = f.no
              left join (select f_no, count(no) as servers from " . TABLE_PREFIX . "server group by f_no) s on s.f_no = f.no
             where f.o_no = '$o_no'
             order by f.phase, f.fleet
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListPhaseCount($o_no)
    {
        $query = "
            select fs.phase, sum(fs.servers) as servers
              from (
                select f.no, f.phase, s.servers
                  from " . TABLE_PREFIX . $this->table . " f
                  left join (select f_no, count(no) as servers from " . TABLE_PREFIX . "server group by f_no) s on s.f_no = f.no
                 where f.o_no = '$o_no'
             ) fs
             group by fs.phase
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListByPhase($o_no, $phase, $u_no = 0)
    {
        $query = "
            select f.*, fs.u_no, ifnull(s.servers, 0) as servers
              from " . TABLE_PREFIX . $this->table . " f
              left join " . TABLE_PREFIX . $this->table . "_star fs on fs.u_no = '$u_no' and fs.f_no = f.no
              left join (select f_no, count(no) as servers from " . TABLE_PREFIX . "server group by f_no) s on s.f_no = f.no
             where f.o_no = '$o_no'
               and f.phase = '$phase'
             order by fs.u_no desc, f.phase, f.fleet
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListSimple($o_no)
    {
        $query = "
            select no, phase, fleet
              from " . TABLE_PREFIX . $this->table . "
             where o_no = '$o_no'
             order by fleet
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListByPhaseSimple($o_no, $phase)
    {
        $query = "
            select no, phase, fleet
              from " . TABLE_PREFIX . $this->table . "
             where o_no = '$o_no'
               and phase = '$phase'
             order by fleet
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListByLB($o_no, $f_no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['o_no' => $o_no, 'lb_f_no' => $f_no]);
        return $query->result();
    }

    public function getOne($o_no, $no)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['no' => $no, 'o_no' => $o_no]);
        return $query->row();
    }

    public function getOneByName($o_no, $phase, $fleet)
    {
        $data = [
            'o_no' => $o_no,
            'phase' => $phase,
            'fleet' => $fleet
        ];
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, $data);
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

    public function toggleStar($no, $u_no)
    {
        $data = ['f_no' => $no, 'u_no' => $u_no];
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
        $this->db->where('f_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table . '_star');
    }

    public function deleteStarByUser($no)
    {
        $this->db->where('u_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table . '_star');
    }

}
