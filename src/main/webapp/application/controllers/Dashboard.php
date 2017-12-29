<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('phase_model', 'm_phase');
        $this->load->model('fleet_model', 'm_fleet');
        $this->load->model('server_model', 'm_server');
        $this->load->model('project_model', 'm_project');
    }

    public function index()
    {
        // phase list
        $data['list'] = $this->_get_phase_list();

        $this->_view('dashboard/index', $data);
    }

    public function servers()
    {
        // server list
        $list = $this->m_server->getList($this->_ono());

        // server count
        $data['count'] = count($list);

        $data['ping_up'] = 0;
        $data['pong_up'] = 0;

        $max = 0;

        // phase count
        $data['phases'] = $this->m_fleet->getListPhaseCount($this->_ono());

        foreach ($data['phases'] as $phase) {
            $phase->servers = $phase->servers ? intval($phase->servers) : 0;
            if ($phase->servers > $max) {
                $max = $phase->servers;
            }
        }

        $max++;

        foreach ($data['phases'] as $phase) {
            $phase->rate = intval($phase->servers * 100 / $max);
        }

        // servers
        $data['servers'] = [];
        $hash = '';
        $down = '';

        foreach ($list as $item) {
            $item->h = _health_map($item->ping_date, $item->pong_date);

            if ($item->h->ping->s < 66) {
                $data['ping_up']++;
            }
            if ($item->h->pong->s < 66) {
                $data['pong_up']++;
            }

            $hash .= '|' . $item->no;
            if ($item->h->ping->s >= 66 || $item->h->pong->s >= 66) {
                $data['servers'][] = $item;
                $down .= '|' . $item->no . '|' . $item->h->ping->s . '|' . $item->h->pong->s;
            }
        }

        $data['hash'] = md5($hash);
        $data['down'] = md5($down);

        $this->_json($data);
    }

    public function projects()
    {
        $data['list'] = $this->m_project->getList($this->_ono());

        $this->_json($data);
    }

}
