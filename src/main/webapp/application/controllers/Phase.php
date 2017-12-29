<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Phase extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('phase_model', 'm_phase');
        $this->load->model('fleet_model', 'm_fleet');
    }

    public function index()
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        // phase list
        $data['list'] = $this->_get_phase_list();

        // phase count
        $data['phases'] = $this->m_fleet->getListPhaseCount($this->_ono());

        foreach ($data['list'] as $item) {
            $item->servers = 0;
            foreach ($data['phases'] as $phase) {
                if ($item->key == $phase->phase) {
                    $item->servers = $phase->servers ? intval($phase->servers) : 0;
                    break;
                }
            }
        }

        $this->_view('phase/list', $data);
    }

    public function item($key)
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        if (!empty($key)) {
            $phase = $this->m_phase->getOneByName($this->_ono(), $key);
        }

        if (empty($phase)) {
            $phase = (object)[
                'no' => 0,
                'phase' => $key,
                'profile' => '',
                'hosts' => ''
            ];
        }

        $data['phase'] = $phase;

        $data['fleets'] = $this->m_fleet->getListByPhase($this->_ono(), $key);

        foreach ($data['fleets'] as $item) {
            $item->instance = json_decode($item->instance);
        }

        $this->_view('phase/item', $data);
    }

    public function save()
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $phase = $this->input->get_post('phase', true);
        $profile = $this->input->get_post('profile', true);
        $hosts = $this->input->get_post('hosts', true);

        $phase = strtolower(preg_replace('/\s+/', '_', $phase));

        if (empty($phase)) {
            $this->_message(false, $icon, 'danger', '저장 실패.');
            return;
        }

        $data = [
            'profile' => $profile,
            'hosts' => $hosts
        ];

        $one = $this->m_phase->getOneByName($this->_ono(), $phase);

        if (empty($one)) {
            $data['o_no'] = $this->_ono();
            $data['phase'] = $phase;

            $this->m_phase->insert($data);
        } else {
            $this->m_phase->update($one->no, $data);
        }

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    public function remove($no = null)
    {
        $icon = 'fa-trash';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $phase = $this->m_phase->getOne($this->_ono(), $no);

        if (empty($phase)) {
            $this->_message(false, $icon, 'danger', 'phase 없음.');
            return;
        }

        $fleets = $this->m_fleet->getListByPhase($this->_ono(), $phase->phase);

        if (!empty($fleets)) {
            $this->_message(false, $icon, 'danger', 'fleet 모두 삭제 후 삭제.');
            return;
        }

        $this->m_phase->delete($no);

        $this->_message(false, $icon, 'danger', '삭제 실패.');
    }

    // call from toast shell
    public function conn()
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        $list = $this->m_phase->getList($this->_ono());

        $i = 1;
        foreach ($list as $item) {
            echo str_pad($i, 3, ' ', STR_PAD_LEFT) . ' ' . $item->phase . PHP_EOL;
            $i++;
        }
    }

}
