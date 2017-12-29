<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fleet extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('phase_model', 'm_phase');
        $this->load->model('fleet_model', 'm_fleet');
        $this->load->model('server_model', 'm_server');
        $this->load->model('target_model', 'm_target');
        $this->load->model('certificate_model', 'm_certificate');
        $this->load->model('ip_model', 'm_ip');
    }

    public function json($phase = null)
    {
        if (empty($phase)) {
            $data['list'] = $this->m_fleet->getListSimple($this->_ono());
        } else {
            $data['list'] = $this->m_fleet->getListByPhaseSimple($this->_ono(), $phase);
        }

        $this->_json($data);
    }

    public function index()
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $phase = $this->input->get_post('phase', true);

        $data['phase'] = $phase;

        // fleet list
        if (empty($phase)) {
            $data['list'] = $this->m_fleet->getList($this->_ono(), $this->_uno());
        } else {
            $data['list'] = $this->m_fleet->getListByPhase($this->_ono(), $phase, $this->_uno());
        }

        foreach ($data['list'] as $item) {
            $item->instance = json_decode($item->instance);
        }

        // phase list
        $data['phases'] = $this->_get_phase_list();

        $this->_view('fleet/list', $data);
    }

    public function stars()
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $data['list'] = $this->m_fleet->getListStar($this->_ono(), $this->_uno());

        $this->_view('fleet/list', $data);
    }

    public function item($no = null)
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $data = [];

        if (!empty($no)) {
            // fleet
            $data['fleet'] = $this->m_fleet->getOne($this->_ono(), $no);

            if (empty($data['fleet'])) {
                return;
            }
        } else {
            $phase = $this->input->get_post('phase', true);

            // fleet
            $data['fleet'] = (object)[
                'phase' => $phase
            ];
        }

        $this->_view('fleet/item', $data);
    }

    public function form($no = null)
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $data = [];

        if (!empty($no)) {
            // fleet
            $data['fleet'] = $this->m_fleet->getOne($this->_ono(), $no);

            if (empty($data['fleet'])) {
                return;
            }

            // ec2 instance
            $data['instance'] = json_decode($data['fleet']->instance);

            // subnet to array
            if (!empty($data['instance']->subnet) && !is_array($data['instance']->subnet)) {
                $data['instance']->subnet = [$data['instance']->subnet];
            }

            // lb
            $data['lb'] = json_decode($data['fleet']->lb);

            // lb lit
            $data['lbs'] = $this->m_fleet->getListByPhase($this->_ono(), 'lb');
        } else {
            $phase = $this->input->get_post('phase', true);

            // fleet
            $data['fleet'] = (object)[
                'phase' => $phase
            ];

            // phase list
            $data['phases'] = $this->_get_phase_list();
        }

        // certificate list
        $data['certificate'] = $this->m_certificate->getList($this->_ono());

        // aws instance type list
        $data['types'] = $this->_get_instance_types();

        $this->_view('fleet/form', $data);
    }

    public function save($no = null)
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $phase = $this->input->get_post('phase', true);
        $fleet = $this->input->get_post('fleet', true);

        $profile = $this->input->get_post('profile', true);
        $hosts = $this->input->get_post('hosts', true);

        $apps = $this->input->get_post('apps', true);
        $health = $this->input->get_post('health', true);

        $lb_f_no = $this->input->get_post('lb_f_no', true);
        $lb_http = $this->input->get_post('lb_http', true);
        $lb_https = $this->input->get_post('lb_https', true);
        $lb_port = $this->input->get_post('lb_port', true);
        $lb_ssl = $this->input->get_post('lb_ssl', true);
        $lb_tcp = $this->input->get_post('lb_tcp', true);

        $lb_custom = $this->input->get_post('lb_custom', true);
        $lb_custom_http = $this->input->get_post('lb_custom_http', true);
        $lb_custom_https = $this->input->get_post('lb_custom_https', true);
        $lb_custom_config = $this->input->get_post('lb_custom_config', true);

        $type = $this->input->get_post('type', true);
        $image = $this->input->get_post('image', true);
        $vpc = $this->input->get_post('vpc', true);
        $subnet = $this->input->get_post('subnet', true);
        $security = $this->input->get_post('security', true);
        $storage = $this->input->get_post('storage', true);
        $ip = $this->input->get_post('ip', true);

        $instance = [
            'type' => $type,
            'image' => $image,
            'vpc' => $vpc,
            'subnet' => $subnet,
            'security' => $security,
            'storage' => $storage,
            'ip' => $ip
        ];

        $lb = [
            'http' => $lb_http,
            'https' => $lb_https,
            'port' => $lb_port,
            'ssl' => $lb_ssl,
            'tcp' => $lb_tcp,
            'custom' => []
        ];

        if (!empty($lb_custom)) {
            $lb['custom'] = [
                'type' => $lb_custom,
                'http' => $lb_custom_http,
                'https' => $lb_custom_https,
                'config' => $lb_custom_config
            ];
        }

        $data = [
            'profile' => $profile,
            'hosts' => $hosts,
            'health' => $health,
            'lb_f_no' => $lb_f_no,
            'instance' => json_encode($instance),
            'lb' => json_encode($lb)
        ];

        if (!empty($apps)) {
            $data['apps'] = join(' ', $apps);
        } else {
            if ($phase == 'lb') {
                $data['apps'] = 'nginx';
            } else {
                $data['apps'] = '-';
            }
        }

        if (!empty($no)) {
            $item = $this->m_fleet->getOne($this->_ono(), $no);
        }

        if (empty($item)) {
            if (empty($phase) || empty($fleet)) {
                $this->_message(false, $icon, 'danger', '저장 실패.');
                return;
            }

            $phase = $this->_check_space($phase);
            $fleet = $this->_check_space($fleet);

            $length = strlen($phase);
            if (substr($fleet, 0, $length) !== $phase) {
                $fleet = $phase . '-' . $fleet;
            }

            $data['o_no'] = $this->_ono();
            $data['phase'] = $phase;
            $data['fleet'] = $fleet;

            $no = $this->m_fleet->insert($data);

            $this->_message(true, $icon, 'redirect', '/fleet/item/' . $no);
        } else {
            $this->m_fleet->update($no, $data);

            if ($item->lb_f_no != $lb_f_no) {
                $action = 'reload';
            } else {
                $action = 'info';
            }

            $this->_message(true, $icon, $action, '저장 성공.');
        }
    }

    public function security($no)
    {
        $icon = 'fa-shield';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $fleet = $this->m_fleet->getOne($this->_ono(), $no);

        if (empty($fleet)) {
            $this->_message(false, $icon, 'danger', 'fleet 없음.');
            return;
        }

        $instance = json_decode($fleet->instance);

        if (empty($instance) || empty($instance->security)) {
            $this->_message(false, $icon, 'danger', '인스턴스 생성 조건을 설정해 주세요.');
            return;
        }

        if (is_array($instance->security)) {
            $groups = join(' ', $instance->security);
        } else {
            $groups = $instance->security;
        }

        // server list
        $list = $this->m_server->getListByFleet($this->_ono(), $no);

        foreach ($list as $item) {
            if (empty($item->id)) {
                continue;
            }

            // modify security groups
            $params = 'ec2 modify-instance-attribute --instance-id ' . $item->id . ' --groups ' . $groups;
            $this->_aws($params);
        }

        $this->_message(true, $icon, 'info', count($list) . '개의 서버에 적용 했습니다.');
    }

    public function remove($no)
    {
        $icon = 'fa-trash';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $fleet = $this->m_fleet->getOne($this->_ono(), $no);

        if (empty($fleet)) {
            $this->_message(false, $icon, 'danger', 'fleet 없음.');
            return;
        }

        $servers = $this->m_server->getListByFleet($this->_ono(), $no);

        if (!empty($servers)) {
            $this->_message(false, $icon, 'danger', 'server 모두 삭제 후 삭제 하세요.');
            return;
        }

        // remove
        $this->m_ip->deleteFleet($no);
        $this->m_target->deleteFleet($no);
        $this->m_fleet->deleteStar($no);
        $this->m_fleet->delete($no);

        $this->_message(true, $icon, 'remove', 'fleet-' . $no);
    }

    public function star($no)
    {
        if (!$this->_has_auth('read')) {
            return;
        }

        $no = $this->m_fleet->toggleStar($no, $this->_uno());

        if (empty($no)) {
            $icon = 'fa-star-o star';
        } else {
            $icon = 'fa-star star';
        }

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    // call from toast shell
    public function conn($phase = null)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        if (empty($phase)) {
            return;
        }

        $list = $this->m_fleet->getListByPhase($this->_ono(), $phase);

        $i = 1;
        foreach ($list as $item) {
            echo str_pad($i, 3, ' ', STR_PAD_LEFT) . ' ' . $item->phase . ' ' . $item->fleet . PHP_EOL;
            $i++;
        }
    }

    // call from toast shell
    public function custom($no = null, $name = null)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        if (empty($no)) {
            return;
        }

        // lb fleet
        $item = $this->m_fleet->getOne($this->_ono(), $no);

        if (empty($item)) {
            return;
        }

        // lb
        $lb = json_decode($item->lb);

        if (!empty($lb->custom)) {
            if ($name == 'http') {
                echo @$lb->custom->http ? @$lb->custom->http : @$lb->custom->config;
            } else if ($name == 'https') {
                echo @$lb->custom->https ? @$lb->custom->https : @$lb->custom->config;
            } else {
                echo @$lb->custom->config;
            }
        }
    }

}
