<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('user_model', 'm_user');
        $this->load->model('phase_model', 'm_phase');
    }

    public function index()
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $data['list'] = $this->m_user->getList($this->_ono());

        $this->_view('user/list', $data);
    }

    public function item($no = null)
    {
        if (!$this->_has_auth('admin')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $data['item'] = $this->m_user->getOne($this->_ono(), $no);

        if (empty($data['item'])) {
            return;
        }

        // phase list
        $data['phases'] = $this->_get_phase_list();

        $this->_view('user/item', $data);
    }

    public function save($no)
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('admin', $icon)) {
            return;
        }

        $user = $this->m_user->getOne($this->_ono(), $no);

        if (empty($user)) {
            $this->_message(false, $icon, 'danger', 'user 없음.');
            return;
        }

        $auth = $this->input->get_post('auth', true);

        $data = [
            'auth' => join(' ', $auth)
        ];

        $this->m_user->update($no, $data);

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    public function phone()
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $user = $this->_user();

        $phoneNum = $this->input->get_post('phone', true);

        $data = [
            'phoneNum' => $phoneNum
        ];
        $this->m_user->update($user->no, $data);

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    public function remove($no)
    {
        $icon = 'fa-trash';

        if (!$this->_has_auth('admin', $icon)) {
            return;
        }

        $user = $this->m_user->getOne($this->_ono(), $no);

        if (empty($user)) {
            $this->_message(false, $icon, 'danger', 'user 없음.');
            return;
        }

        $this->load->model('fleet_model', 'm_fleet');
        $this->load->model('server_model', 'm_server');
        $this->load->model('project_model', 'm_project');

        $this->m_fleet->deleteStarByUser($no);
        $this->m_server->deleteStarByUser($no);
        $this->m_project->deleteStarByUser($no);

        // remove
        $this->m_user->delete($no);

        $this->_message(true, $icon, 'remove', 'user-' . $no);
    }

}
