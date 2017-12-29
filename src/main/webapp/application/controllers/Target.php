<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Target extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('target_model', 'm_target');
        $this->load->model('fleet_model', 'm_fleet');
        $this->load->model('deploy_model', 'm_deploy');
        $this->load->model('version_model', 'm_version');
    }

    public function one($no)
    {
        if (!$this->_has_auth('read')) {
            $this->_json();
            return;
        }

        $list = [];

        // target one
        $target = $this->m_target->getOneForDeploy($no);

        if (!empty($target)) {
            $list[] = $target;
        }

        $data = $this->_list_data($list);

        $this->_json($data);
    }

    public function fleet($f_no)
    {
        if (!$this->_has_auth('read')) {
            $this->_json();
            return;
        }

        // target list
        $list = $this->m_target->getListByFleet($f_no);

        $data = $this->_list_data($list);

        $this->_json($data);
    }

    public function server($s_no)
    {
        if (!$this->_has_auth('read')) {
            $this->_json();
            return;
        }

        // target list
        $list = $this->m_target->getListByServer($s_no);

        $data = $this->_list_data($list);

        $this->_json($data);
    }

    public function save($no = null)
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('dev', $icon)) {
            return;
        }

        $p_no = $this->input->get_post('p_no', true);
        $fleet = $this->input->get_post('fleet', true);
        $version = $this->input->get_post('version', true);
        $domain = $this->input->get_post('domain', true);
        $port = $this->input->get_post('port', true);
        $deploy = $this->input->get_post('deploy', true);
        $le = $this->input->get_post('le', true);

        $domain = $this->_check_space($domain);
        $port = $this->_check_numeric($port);

        if (empty($domain)) {
            $this->_message(true, $icon, 'danger', 'domain 없음.');
            return;
        }

        $data = [
            'version' => $version,
            'domain' => $domain,
            'port' => $port ? $port : '80',
            'deploy' => $deploy,
            'le' => $le == 'Y' ? $le : 'N'
        ];

        if (!empty($p_no)) {
            $data['p_no'] = $p_no;
        }
        if (!empty($fleet)) {
            $data['f_no'] = $fleet;
        }

        if (!empty($no)) {
            $target = $this->m_target->getOne($no);
        }

        if (empty($target)) {
            $this->m_target->insert($data);
            $action = 'reload';
        } else {
            $this->m_target->update($no, $data);
            $action = 'info';
        }

        $this->_message(true, $icon, $action, '저장 성공.');
    }

    public function version($no)
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('dev', $icon)) {
            return;
        }

        $target = $this->m_target->getOne($no);

        if (empty($target)) {
            $this->_message(false, $icon, 'danger', 'target 없음.');
            return;
        }

        $version = $this->input->get_post('version', true);

        $data = [
            'version' => $version
        ];

        $this->m_target->update($no, $data);

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    public function le($no)
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('dev', $icon)) {
            return;
        }

        $target = $this->m_target->getOne($no);

        if (empty($target)) {
            $this->_message(false, $icon, 'danger', 'target 없음.');
            return;
        }

        $le = $this->input->get_post('le', true);

        $data = [
            'le' => $le == 'Y' ? $le : 'N'
        ];

        $this->m_target->update($no, $data);

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    public function toggle($no)
    {
        $icon = 'fa-toggle-off';

        if (!$this->_has_auth('dev', $icon)) {
            return;
        }

        $target = $this->m_target->getOne($no);

        if (empty($target)) {
            $this->_message(false, $icon, 'danger', 'target 없음.');
            return;
        }

        if ($target->deployYN == 'Y') {
            $icon = 'fa-toggle-off danger';
            $data = [
                'deployYN' => 'N'
            ];
        } else {
            $icon = 'fa-toggle-on primary';
            $data = [
                'deployYN' => 'Y'
            ];
        }

        $this->m_target->update($no, $data);

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    public function remove($no)
    {
        $icon = 'fa-trash';

        if (!$this->_has_auth('dev', $icon)) {
            return;
        }

        $target = $this->m_target->getOne($no);

        if (empty($target)) {
            $this->_message(false, $icon, 'danger', 'target 없음.');
            return;
        }

        // remove
        $this->m_deploy->deleteTarget($no);
        $this->m_target->delete($no);

        $this->_message(true, $icon, 'script', 'remove_target(' . $no . ')');
    }

    // call from toast shell
    public function deploy($no = null)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        if (empty($no)) {
            return;
        }

        // target
        $target = $this->m_target->getOneForDeploy($no);

        if (empty($target)) {
            return;
        }
        if ($target->phase == 's3') {
            if (empty($target->domain)) {
                return;
            }
            $target->deploy = 's3';
        }

        $data = [
            $target->no, $target->groupId, $target->artifactId, $target->version, $target->packaging, $target->domain, $target->deploy, $target->port
        ];

        $text = join(' ', $data) . PHP_EOL;

        $this->_text($text);
    }

    private function _list_data($list)
    {
        $str = '';

        foreach ($list as $item) {
            $str .= $item->artifactId . $item->version . $item->packaging . $item->domain . (isset($item->deployed) ? $item->deployed : '');

            if (empty($item->deploy)) {
                $item->deploy = $item->packaging;
            }

            $item->versions = $this->m_version->getList($item->p_no);

            if (count($item->versions) > 0) {
                foreach ($item->versions as $version) {
                    $version->status = $this->_get_status_one($version->status);
                }
            }
        }

        return [
            'hash' => md5($str),
            'list' => $list
        ];
    }

}
