<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Server_model extends CI_Model
{

    private $table = 'server';

    function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getList($o_no, $u_no = 0)
    {
        $query = "
            select s.*, f.phase, f.fleet, ss.u_no
              from " . TABLE_PREFIX . $this->table . " s
              left join " . TABLE_PREFIX . "fleet f on f.no = s.f_no
              left join " . TABLE_PREFIX . $this->table . "_star ss on ss.u_no = '$u_no' and ss.s_no = s.no
             where s.o_no = '$o_no'
             order by ss.u_no desc, f.phase, f.fleet, s.name
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListStar($o_no, $u_no = 0)
    {
        $query = "
            select s.*, f.phase, f.fleet, ss.u_no
              from " . TABLE_PREFIX . $this->table . " s
              join " . TABLE_PREFIX . $this->table . "_star ss on ss.u_no = '$u_no' and ss.s_no = s.no
              left join " . TABLE_PREFIX . "fleet f on f.no = s.f_no
             where s.o_no = '$o_no'
             order by f.phase, f.fleet, s.name
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListByPhase($o_no, $phase, $u_no = 0)
    {
        $query = "
            select s.*, f.phase, f.fleet, ss.u_no
              from " . TABLE_PREFIX . $this->table . " s
              left join " . TABLE_PREFIX . $this->table . "_star ss on ss.u_no = '$u_no' and ss.s_no = s.no
              left join " . TABLE_PREFIX . "fleet f on f.no = s.f_no
             where s.o_no = '$o_no'
               and f.phase = '$phase'
             order by ss.u_no desc, f.phase, f.fleet, s.name
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getListByFleet($o_no, $f_no)
    {
        $query = "
            select s.*, f.phase, f.fleet
              from " . TABLE_PREFIX . $this->table . " s
              left join " . TABLE_PREFIX . "fleet f on f.no = s.f_no
             where s.o_no = '$o_no'
               and s.f_no = '$f_no'
             order by f.phase, f.fleet, s.name
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getOne($o_no, $no)
    {
        $query = "
            select s.*, f.phase, f.fleet, u.username
              from " . TABLE_PREFIX . $this->table . " s
              left join " . TABLE_PREFIX . "fleet f on f.no = s.f_no
              left join " . TABLE_PREFIX . "user u  on u.no = s.u_no
             where s.o_no = '$o_no'
               and s.no = '$no'
        ";
        $query = $this->db->query($query);
        return $query->row();
    }

    public function getOneById($o_no, $id)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['o_no' => $o_no, 'id' => $id]);
        return $query->row();
    }

    public function getOneByName($o_no, $name)
    {
        $query = $this->db->get_where(TABLE_PREFIX . $this->table, ['o_no' => $o_no, 'name' => $name]);
        return $query->row();
    }

    public function getListMon($no, $h = 0)
    {
        if (empty($h)) {
            $fr = date('Y-m-d H:i:s', strtotime('-1 hours'));
            $to = date('Y-m-d H:i:s');
        } else {
            $fr = date('Y-m-d H:i:s', strtotime($h . ' hours'));
            $to = date('Y-m-d H:i:s', strtotime(($h + 1) . ' hours'));
        }
        $query = "
            select *
              from " . TABLE_PREFIX . $this->table . "_mon
             where s_no = '$no'
               and reg_date > timestamp('$fr')
               and reg_date < timestamp('$to')
             order by no
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getMonHour($no, $from = null, $to = null)
    {
        if (empty($from)) {
            $from = date('Y-m');
        }
        if (empty($to)) {
            $to = date('Y-m', strtotime('+1 month'));
        }
        $query = "
            select '$from' as ym, count(a.hh) as hs
              from (
                select substr(reg_date, 1, 13) AS hh
                  from " . TABLE_PREFIX . $this->table . "_mon
                 where s_no = '$no'
                   and reg_date > timestamp('$from-01')
                   and reg_date < timestamp('$to-01')
                 group by hh
              ) a
        ";
        $query = $this->db->query($query);
        return $query->row();
    }

    public function getMonHourList($from = null, $to = null)
    {
        if (empty($from)) {
            $from = date('Y-m');
        }
        if (empty($to)) {
            $to = date('Y-m', strtotime('+1 month'));
        }
        $query = "
            select a.s_no as no, count(a.hh) as hs
              from (
                select s_no, substr(reg_date, 1, 13) AS hh
                  from " . TABLE_PREFIX . $this->table . "_mon
                 where s_no > 0
                   and reg_date > timestamp('$from-01')
                   and reg_date < timestamp('$to-01')
                 group by s_no, hh
              ) a
            group by a.s_no
        ";
        $query = $this->db->query($query);
        return $query->result();
    }

    public function insert($data)
    {
        if (empty($data)) {
            return null;
        }
        $this->db->insert(TABLE_PREFIX . $this->table, $data);
        return $this->db->insert_id();
    }

    public function update($no, $data)
    {
        if (empty($data)) {
            return;
        }
        $this->db->set('mod_date', 'NOW()', false);
        $this->db->where('no', $no);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function updateById($id, $data)
    {
        if (empty($data)) {
            return;
        }
        $this->db->set('mod_date', 'NOW()', false);
        $this->db->where('id', $id);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function ping($no, $data)
    {
        $data = array_filter($data, function ($value) {
            return !empty($value);
        });
        $this->db->set('ping_date', 'NOW()', false);
        $this->db->where('no', $no);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function pong($no, $plugYN = null)
    {
        $data = [];
        if (!empty($plugYN)) {
            $data['plugYN'] = $plugYN;
        }
        $this->db->set('pong_date', 'NOW()', false);
        $this->db->where('no', $no);
        $this->db->update(TABLE_PREFIX . $this->table, $data);
    }

    public function delete($no)
    {
        $this->db->where('no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table);
    }

    public function insertMon($data)
    {
        $this->db->insert(TABLE_PREFIX . $this->table . '_mon', $data);
        return $this->db->insert_id();
    }

    public function squeezeMon($no)
    {
        // 2 month 로 해야 전월 사용 금액을 추출 할수 있음
        $query = "
            delete 
              from " . TABLE_PREFIX . $this->table . "_mon
             where s_no = '$no'
               and reg_date < NOW() - interval 2 month
        ";
        $this->db->query($query);
    }

    public function deleteMon($no)
    {
        $this->db->where('s_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table . '_mon');
    }

    public function toggleStar($no, $u_no)
    {
        $data = ['s_no' => $no, 'u_no' => $u_no];
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
        $this->db->where('s_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table . '_star');
    }

    public function deleteStarByUser($no)
    {
        $this->db->where('u_no', $no);
        $this->db->delete(TABLE_PREFIX . $this->table . '_star');
    }

}
