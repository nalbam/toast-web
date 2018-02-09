<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Server extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('server_model', 'm_server');
        $this->load->model('phase_model', 'm_phase');
        $this->load->model('fleet_model', 'm_fleet');
        $this->load->model('target_model', 'm_target');
        $this->load->model('deploy_model', 'm_deploy');
        $this->load->model('ip_model', 'm_ip');
    }

    public function index()
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $phase = $this->input->get_post('phase', true);

        $data['phase'] = $phase;

        // server list
        if (empty($phase)) {
            $data['list'] = $this->m_server->getList($this->_ono(), $this->_uno());
        } else {
            $data['list'] = $this->m_server->getListByPhase($this->_ono(), $phase, $this->_uno());
        }

        foreach ($data['list'] as $item) {
            $item->instance = json_decode($item->instance);
            $item->h = _health_map($item->ping_date, $item->pong_date);
        }

        // phase list
        $data['phases'] = $this->_get_phase_list();

        $this->_view('server/list', $data);
    }

    public function plain()
    {
        $phase = $this->input->get_post('phase', true);

        // server list
        if (empty($phase)) {
            $data['list'] = $this->m_server->getList($this->_ono(), $this->_uno());
        } else {
            $data['list'] = $this->m_server->getListByPhase($this->_ono(), $phase, $this->_uno());
        }

        foreach ($data['list'] as $item) {
            echo $item->id . ' ' . $item->phase . ' ' . $item->fleet . ' ' . $item->name . ' ' . $item->host . PHP_EOL;
        }
    }

    public function stars()
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        // server list
        $data['list'] = $this->m_server->getListStar($this->_ono(), $this->_uno());

        foreach ($data['list'] as $item) {
            $item->h = _health_map($item->ping_date, $item->pong_date);
        }

        $this->_view('server/list', $data);
    }

    public function price()
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $phase = $this->input->get_post('phase', true);

        $data['phase'] = $phase;

        // server list
        if (empty($phase)) {
            $data['list'] = $this->m_server->getList($this->_ono(), $this->_uno());
        } else {
            $data['list'] = $this->m_server->getListByPhase($this->_ono(), $phase, $this->_uno());
        }

        foreach ($data['list'] as $item) {
            $item->instance = json_decode($item->instance);
        }

        // phase list
        $data['phases'] = $this->_get_phase_list();

        // price key
        $data['price'][] = date('Y-m', strtotime('-1 month'));
        $data['price'][] = date('Y-m');

        $this->_view('server/price', $data);
    }

    public function item($no)
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        // server
        $data['server'] = $this->m_server->getOne($this->_ono(), $no);

        if (empty($data['server'])) {
            return;
        }

        // h
        $data['h'] = $this->input->get_post('h', true);

        // health
        $data['server']->h = _health_map($data['server']->ping_date, $data['server']->pong_date);

        // power
        if ($data['server']->power == 'Y') {
            //  18:15:01 up 193 days,  3:46,  0 users,  load average: 0.00, 0.00, 0.00
            $uptime = explode(',', $data['server']->uptime);
            if (count($uptime) > 2) {
                $data['status'] = substr($uptime[0], 10);
            } else {
                $data['status'] = 'Started';
            }
        } else {
            $data['status'] = 'Stopped';
        }

        $instance = json_decode($data['server']->instance);

        if (!empty($instance->type)) {
            // instance
            $data['instance'] = $this->_get_instance_type($instance->type);

            // hours
            $data['hours'] = [];
            $data['hours'][] = $this->m_server->getMonHour($no, date('Y-m', strtotime('-1 month')), date('Y-m'));
            $data['hours'][] = $this->m_server->getMonHour($no, date('Y-m'), date('Y-m', strtotime('+1 month')));
        }

        $this->_view('server/item', $data);
    }

    public function hours($no)
    {
        if (!$this->_has_auth('read')) {
            $this->_json();
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            $this->_json();
            return;
        }

        $data['no'] = $no;

        $instance = json_decode($server->instance);

        if (!empty($instance->type)) {
            // instance
            $data['instance'] = $this->_get_instance_type($instance->type);

            // hours
            $data['hours'] = [];
            $data['hours'][] = $this->m_server->getMonHour($no, date('Y-m', strtotime('-1 month')), date('Y-m'));
            $data['hours'][] = $this->m_server->getMonHour($no, date('Y-m'), date('Y-m', strtotime('+1 month')));
        }

        $this->_json($data);
    }

    public function one($no)
    {
        if (!$this->_has_auth('read')) {
            $this->_json();
            return;
        }

        $list = [];

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (!empty($server)) {
            $list[] = $server;
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

        // server list
        $list = $this->m_server->getListByFleet($this->_ono(), $f_no);

        $data = $this->_list_data($list);

        $this->_json($data);
    }

    public function mon($no)
    {
        if (!$this->_has_auth('read')) {
            $this->_json();
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            return;
        }

        $h = $this->input->get_post('h', true);

        $health = $this->m_server->getListMon($no, $h);

        if (!empty($health) && count($health) > 0) {
            $labels = [];
            $cpu = [];
            $hdd = [];
            $load1 = [];
            $load5 = [];
            $load15 = [];

            foreach ($health as $item) {
                $labels[] = substr($item->reg_date, 11, 5);
                $cpu[] = $item->cpu;
                $hdd[] = $item->hdd;
                $load1[] = $item->load_1;
                $load5[] = $item->load_5;
                $load15[] = $item->load_15;
            }

            $data = [
                'labels' => $labels,
                'cpu' => $cpu,
                'hdd' => $hdd,
                'load1' => $load1,
                'load5' => $load5,
                'load15' => $load15
            ];
        } else {
            $data = [];
        }

        $this->_json($data);
    }

    public function save($no)
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            $this->_message(false, $icon, 'danger', 'server 없음.');
            return;
        }

        $key = $this->input->get_post('key', true);
        $val = $this->input->get_post('val', true);

        $keys = ['name', 'host', 'port', 'user'];

        if (empty($key) || !in_array($key, $keys)) {
            $this->_message(false, $icon, 'danger', 'params 없음.');
            return;
        }

        if ($key == 'port') {
            $val = $this->_check_numeric($val);
        } else {
            $val = $this->_check_space($val);
        }

        $data[$key] = $val;

        // update
        $this->m_server->update($no, $data);

        // update instance tag
        if ($key == 'name') {
            if (!empty($server->id)) {
                $server->name = $val;

                // fleet
                $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

                $this->_tag($server, $fleet);
            }
        }

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    public function toggle($no)
    {
        $icon = 'fa-toggle-off';

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            $this->_message(false, $icon, 'danger', 'server 없음.');
            return;
        }

        // fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        if (empty($fleet)) {
            $this->_message(false, $icon, 'danger', 'fleet 없음.');
            return;
        }

        if (!$this->_has_auth($fleet->phase, $icon)) {
            return;
        }

        if ($server->plugYN == 'Y') {
            $icon = 'fa-toggle-off danger';
            $data = [
                'plugYN' => 'N'
            ];
        } else {
            $icon = 'fa-toggle-on primary';
            $data = [
                'plugYN' => 'Y'
            ];

            // instance info reload
            if (!empty($server->id)) {
                $instance = $this->_instance($server->id);
                if (!empty($instance)) {
                    $data['instance'] = json_encode($instance);
                }
            }
        }

        // update
        $this->m_server->update($no, $data);

        // lb reset
        if (!empty($fleet->lb_f_no)) {
            $outputs = $this->_toggle_lb($fleet->lb_f_no);

            log_message('debug', 'toggle lb : ' . $outputs);
        }

        if (!empty($outputs)) {
            $this->_message(true, $icon, 'output', $outputs);
        } else {
            $this->_message(true, $icon, 'info', '저장 성공.');
        }
    }

    public function start($no)
    {
        $icon = 'fa-power-off';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            $this->_message(false, $icon, 'danger', 'server 없음.');
            return;
        }
        if (empty($server->id)) {
            $this->_message(false, $icon, 'danger', 'instance-id 없음.');
            return;
        }

        // start
        $status = $this->_start($server->id);

        if (!empty($status) && $status == 'pending') {
            $data = [
                'power' => 'P', // pending
                'plugYN' => ''
            ];

            // update
            $this->m_server->update($no, $data);

            // save log
            $this->_log($no, json_encode($data), true);
        }

        $this->_message(false, $icon, 'warning', 'server is ' . $status . '.');
    }

    public function stop($no)
    {
        $icon = 'fa-power-off';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            $this->_message(false, $icon, 'danger', 'server 없음.');
            return;
        }
        if (empty($server->id)) {
            $this->_message(false, $icon, 'danger', 'instance-id 없음.');
            return;
        }

        // stop
        $status = $this->_stop($server->id);

        $data = [
            'power' => 'N',
            'plugYN' => 'N'
        ];

        // update
        $this->m_server->update($no, $data);

        // save log
        $this->_log($no, json_encode($data), true);

        // fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        // lb reset
        if (!empty($fleet->lb_f_no)) {
            $this->_toggle_lb($fleet->lb_f_no);
        }

        // notification to slack
        $this->_slack_stop($fleet, $server);

        $this->_message(false, $icon, 'danger', 'server is ' . $status . '.');
    }

    public function update($no)
    {
        $icon = ICON_TOAST;

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            $this->_message(false, $icon, 'danger', 'server 없음.');
            return;
        }
        if (empty($server->id)) {
            $this->_message(false, $icon, 'danger', 'instance-id 없음.');
            return;
        }

        $output = $this->_toast('update', $server->user, $server->host, $server->port);

        $this->_message(true, $icon, 'output', $output);
    }

    public function protect($no)
    {
        $icon = 'fa-lock';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            $this->_message(false, $icon, 'danger', 'server 없음.');
            return;
        }

        $protected = false;
        if (!empty($server->id)) {
            // protected
            $protected = $this->_protection($server->id);
        }

        if ($protected) {
            if ($server->locked != 'Y') {
                $this->m_server->update($no, ['locked' => 'Y']);
            }
            $this->_message(false, $icon, 'warning', 'server 보호됨.');
            return;
        }

        if ($server->locked == 'Y') {
            $data = [
                'locked' => 'N'
            ];

            // update
            $this->m_server->update($no, $data);

            // save log
            $this->_log($no, json_encode($data), true);

            $this->_message(true, $icon, 'info', 'server 해제됨.');
        } else {
            // protect
            $this->_protect($server->id);

            $data = [
                'locked' => 'Y'
            ];

            // update
            $this->m_server->update($no, $data);

            // save log
            $this->_log($no, json_encode($data), true);

            $this->_message(true, $icon, 'warning', 'server 보호됨.');
        }
    }

    public function remove($no)
    {
        $icon = 'fa-trash';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            $this->_message(false, $icon, 'danger', 'server 없음.');
            return;
        }

        if ($server->locked == 'Y') {
            $this->_message(false, $icon, 'warning', 'server 보호됨.');
            return;
        }

        if (!empty($server->id)) {
            // protected
            $protected = $this->_protection($server->id);
            if ($protected) {
                if ($server->locked != 'Y') {
                    $this->m_server->update($no, ['locked' => 'Y']);
                }
                $this->_message(false, $icon, 'warning', 'server 보호됨.');
                return;
            }

            // terminate
            $this->_release_ip($server->id, $server->f_no);
            $this->_terminate($server->id);

            // save log
            $this->_log($no, json_encode(['terminate' => $server->id]), true);
        }

        // remove
        $this->m_deploy->deleteServer($no);
        $this->m_server->deleteMon($no);
        $this->m_server->deleteStar($no);
        $this->m_server->delete($no);

        // save log
        $this->_log($no, json_encode(['remove' => $no]), true);

        // fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        // lb reset
        if (!empty($fleet->lb_f_no)) {
            $this->_toggle_lb($fleet->lb_f_no);
        }

        // notification to slack
        $this->_slack_stop($fleet, $server);

        $this->_message(true, $icon, 'script', 'remove_server(' . $no . ');');
    }

    public function star($no)
    {
        if (!$this->_has_auth('read')) {
            return;
        }

        $no = $this->m_server->toggleStar($no, $this->_uno());

        if (empty($no)) {
            $icon = 'fa-star-o star';
        } else {
            $icon = 'fa-star star';
        }

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    // call from toast shell
    public function config()
    {
        if (!$this->_has_auth('system')) {
            $this->_text('Error auth [' . $this->_auth() . ']');
            return;
        }

        $no = $this->input->get_post('no', true);
        $id = $this->input->get_post('id', true);

        $name = $this->input->get_post('name', true);

        $phase = $this->input->get_post('phase', true);
        $fleet = $this->input->get_post('fleet', true);

        $host = $this->_get_ip();
        $port = $this->input->get_post('port', true);
        $user = $this->input->get_post('user', true);

        $phase = strtolower(preg_replace('/\s+/', '_', $phase));
        $fleet = strtolower(preg_replace('/\s+/', '_', $fleet));

        log_message('debug', 'server config : [' . $no . '][' . $id . '][' . $phase . '][' . $fleet . ']');

        // server
        if (!empty($id)) {
            $server = $this->m_server->getOneById($this->_ono(), $id);
        }
        if (empty($server) && !empty($no)) {
            $server = $this->m_server->getOne($this->_ono(), $no);
        }

        if (!empty($server)) {
            log_message('debug', 'server config server : ' . json_encode($server));

            $no = $server->no;

            $item = $this->m_fleet->getOne($this->_ono(), $server->f_no);

            if (!empty($item)) {
                log_message('debug', 'server config fleet : ' . json_encode($item));

                $phase = $item->phase;
                $fleet = $item->fleet;
            }
        }

        if (empty($phase) || empty($fleet)) {
            $this->_text('Error [' . $phase . '][' . $fleet . ']');
            return;
        }

        // phase
        $item = $this->m_phase->getOneByName($this->_ono(), $phase);
        if (empty($item)) {
            $one = $this->_get_phase_one($phase);

            if (empty($one)) {
                $this->_text('Error [' . $phase . ']');
                return;
            }

            $data = [
                'o_no' => $this->_ono(),
                'phase' => $phase
            ];
            $this->m_phase->insert($data);
        }

        // fleet
        $item = $this->m_fleet->getOneByName($this->_ono(), $phase, $fleet);
        if (empty($item)) {
            $data = [
                'o_no' => $this->_ono(),
                'phase' => $phase,
                'fleet' => $fleet
            ];
            $f_no = $this->m_fleet->insert($data);
        } else {
            $f_no = $item->no;
        }

        // server
        $data = [
            'f_no' => $f_no,
            'id' => $id,
            'name' => $name,
            'host' => $host,
            'port' => $port ? $port : '22',
            'user' => $user
        ];

        if (empty($server)) {
            $data['o_no'] = $this->_ono();
            $data['power'] = 'P'; // pending

            $no = $this->m_server->insert($data);
        } else {
            $this->m_server->update($no, $data);
        }

        $this->_text('OK ' . $no . ' ' . $host . ' ' . $phase . ' ' . $fleet . ' ' . $name);
    }

    // call from toast shell
    public function hosts($no = null)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        if (empty($no)) {
            return;
        }

        $text = "";

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            return;
        }

        // fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        if (empty($fleet)) {
            return;
        }

        // phase
        $phase = $this->m_phase->getOneByName($this->_ono(), $fleet->phase);

        if (empty($phase)) {
            return;
        }

        // config
        $hosts = $this->_get_config('hosts');

        $text .= "# toast default hosts" . PHP_EOL;
        $text .= PHP_EOL;
        if (!empty($server->name)) {
            $text .= "127.0.0.1 " . $server->name . PHP_EOL;
        }
        $text .= "127.0.0.1 localhost localhost.localdomain" . PHP_EOL;
        $text .= PHP_EOL;
        $text .= $hosts . PHP_EOL;
        $text .= PHP_EOL;

        $text .= "# toast " . $phase->phase . " hosts" . PHP_EOL;
        $text .= PHP_EOL;
        $text .= $phase->hosts . PHP_EOL;
        $text .= PHP_EOL;

        $text .= "# toast " . $fleet->fleet . " hosts" . PHP_EOL;
        $text .= PHP_EOL;
        $text .= $fleet->hosts . PHP_EOL;
        $text .= PHP_EOL;

        $this->_text($text);
    }

    // call from toast shell
    public function profile($no = null)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        if (empty($no)) {
            return;
        }

        $text = "";

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            return;
        }

        // fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        if (empty($fleet)) {
            return;
        }

        // phase
        $phase = $this->m_phase->getOneByName($this->_ono(), $fleet->phase);

        if (empty($phase)) {
            return;
        }

        // config
        $profile = $this->_get_config('profile');

        $text .= "# toast default profile" . PHP_EOL;
        $text .= PHP_EOL;
        $text .= $profile . PHP_EOL;
        $text .= PHP_EOL;

        $text .= "# toast " . $phase->phase . " profile" . PHP_EOL;
        $text .= PHP_EOL;
        $text .= $phase->profile . PHP_EOL;
        $text .= PHP_EOL;

        $text .= "# toast " . $fleet->fleet . " profile" . PHP_EOL;
        $text .= PHP_EOL;
        $text .= $fleet->profile . PHP_EOL;
        $text .= PHP_EOL;

        $this->_text($text);
    }

    // call from toast shell
    public function apps($no = null)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        if (empty($no)) {
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            return;
        }

        // fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        if (empty($fleet)) {
            return;
        }

        $this->_text($fleet->apps);
    }

    // call from toast shell
    public function script($no = null)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        if (empty($no)) {
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            return;
        }

        // fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        if (empty($fleet)) {
            return;
        }

        $this->_text($fleet->script);
    }

    // call from toast shell
    public function lb($no = null)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        if (empty($no)) {
            return;
        }

        // lb server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            return;
        }

        // lb fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        if (empty($fleet)) {
            return;
        }

        // lb no
        if (!empty($fleet->no)) {
            echo "NO " . $fleet->no . PHP_EOL;
        }

        // lb config
        $lb = json_decode($fleet->lb);

        // ssl
        if (!empty($lb->ssl)) {
            echo "SSL " . $lb->ssl . PHP_EOL;
        }

        // custom
        if (!empty($lb->custom)) {
            echo "CUSTOM " . $lb->custom->type . PHP_EOL;
        }

        // lb fleet -> fleet list
        $fleets = $this->m_fleet->getListByLB($this->_ono(), $fleet->no);

        if (empty($fleets)) {
            return;
        }

        $dupe = [];

        foreach ($fleets as $item) {
            $no = $item->no;

            // server list
            $servers = $this->m_server->getListByFleet($this->_ono(), $no);

            if (empty($servers)) {
                continue;
            }

            // fleet no
            echo "FLEET " . $no . PHP_EOL;

            $hosts = [];
            foreach ($servers as $server) {
                if (!empty($server->host) && $server->plugYN == 'Y' && $server->power == 'Y') {
                    $hosts[] = $server->host;
                }
            }
            if (count($hosts) < 1) {
                continue;
            }
            $hosts = join(' ', $hosts);

            // hosts (ip)
            echo "HOST " . $hosts . PHP_EOL;

            // target list
            $targets = $this->m_target->getListByFleet($no);

            $domains = [];
            foreach ($targets as $target) {
                if (!empty($target->domain)) {
                    if (in_array($target->domain, $dupe)) {
                        continue;
                    }
                    $domains[] = $target->domain;
                    $dupe[] = $target->domain;
                }
            }

            if (count($domains) > 0) {
                $domains = join(' ', $domains);

                // domains
                echo "DOM " . $domains . PHP_EOL;

                // http
                if (!empty($lb->http)) {
                    echo "HTTP " . $lb->http . PHP_EOL;
                }

                // https
                if (!empty($lb->https)) {
                    echo "HTTPS " . $lb->https . PHP_EOL;
                }
            }

            // fleet lb config
            $f_lb = json_decode($item->lb);

            // tcp
            if (!empty($f_lb->tcp)) {
                $tcp = $f_lb->tcp;
            } else if (!empty($lb->tcp)) {
                $tcp = $lb->tcp;
            } else {
                $tcp = null;
            }
            if (!empty($tcp)) {
                $ports = array_filter(explode(' ', str_replace(',', ' ', $tcp)));
                if (count($ports) > 0) {
                    foreach ($ports as $port) {
                        if (in_array($port, $dupe)) {
                            continue;
                        }
                        echo "TCP " . $port . PHP_EOL;
                        $dupe[] = $port;
                    }
                }
            }
        }
    }

    // call from toast shell
    public function vhost($no = null)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        if (empty($no)) {
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            return;
        }

        // target
        $targets = $this->m_target->getListByFleet($server->f_no);

        $text = '';

        foreach ($targets as $item) {
            if (!empty($item->domain)) {
                $text .= $item->domain . ' ' . $item->port . ' ' . $item->le . PHP_EOL;
            }
        }

        $this->_text($text);
    }

    // call from toast shell
    public function deploy($no = null, $t_no = null)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        if (empty($no)) {
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            return;
        }

        // target
        $targets = $this->m_target->getListByFleet($server->f_no, $t_no);

        $text = '';

        foreach ($targets as $target) {
            if ($target->deployYN == 'Y') {
                $data = [
                    $target->no, $target->groupId, $target->artifactId, $target->version, $target->packaging, $target->domain, $target->deploy, $target->port
                ];

                $text .= join(' ', $data) . PHP_EOL;
            }
        }

        $this->_text($text);
    }

    // call from toast shell
    public function conn($phase, $fleet)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        $item = $this->m_fleet->getOneByName($this->_ono(), $phase, $fleet);

        if (empty($item)) {
            return;
        }

        $list = $this->m_server->getListByFleet($this->_ono(), $item->no);

        $len = 5;
        foreach ($list as $item) {
            $v = strlen($item->host);
            if ($v > $len) {
                $len = $v;
            }
        }

        $ssh_user = $this->_get_config('ssh_user', DEFAULT_USR);

        $i = 1;
        foreach ($list as $item) {
            if (empty($item->host)) {
                continue;
            }
            $no = str_pad($i, 3, ' ', STR_PAD_LEFT);
            $host = str_pad($item->host, $len, ' ', STR_PAD_RIGHT);
            $port = $item->port ? $item->port : '22';
            $user = $item->user ? $item->user : $ssh_user;
            $name = $item->name ? $item->name : 'unnamed';
            echo $no . ' ' . $host . ' ' . $port . ' ' . $user . ' ' . $name . PHP_EOL;
            $i++;
        }
    }

    // call from toast shell
    public function health($no = null)
    {
        if (!$this->_has_auth('system')) {
            $this->_text('Error auth [' . $this->_auth() . ']');
            return;
        }

        $id = $this->input->get_post('id', true);

        // server
        if (!empty($id)) {
            $server = $this->m_server->getOneById($this->_ono(), $id);
        }
        if (empty($server) && !empty($no)) {
            $server = $this->m_server->getOne($this->_ono(), $no);
        }

        if (empty($server)) {
            $this->_text('Error not exist server [' . $no . '][' . $id . ']');
            return;
        }

        // fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        if (empty($fleet)) {
            $this->_text('Error not exist server-fleet [' . $no . '][' . $id . ']');
            return;
        }

        $no = $server->no;
        $id = $server->id;
        $name = $server->name;

        $power = null;
        $plugYN = null;
        $ip = null;

        // 첫 ping 에서 power on
        if ($server->power == 'P') {
            $power = 'Y';

            $instance = $this->_instance($server->id);
            if (!empty($instance['pip'])) {
                $ip = $instance['pip'];
            }

            // lb 는 첫 ping 에서 plug in
            if ($fleet->phase == 'lb') {
                $plugYN = 'Y';
            }
        }

        $os = $this->input->get_post('os', true);
        $cpu = $this->input->get_post('cpu', true);
        $hdd = $this->input->get_post('hdd', true);
        $uptime = $this->input->get_post('uptime', true);
        $toast = $this->input->get_post('toast', true);

        $cpu = $cpu ? round($cpu, 2) : 0;
        $hdd = $hdd ? round($hdd, 2) : 0;

        // save ping
        $data = [
            'a_no' => $this->_uno(),
            'host' => $this->_get_ip(),
            'ip' => $ip,
            'plugYN' => $plugYN,
            'power' => $power,
            'os' => $os,
            'uptime' => $uptime,
            'cpu' => $cpu,
            'hdd' => $hdd,
            'toast' => $toast
        ];
        $this->m_server->ping($no, $data);

        // pong
        if (!empty($fleet->health)) {
            // health check
            $this->_health_pong($server, $fleet);
        } else {
            // health check skip
            $this->m_server->pong($server->no);
        }

        // mon
        $this->_health_mon($no, $uptime, $cpu, $hdd);

        // hostname
        if (empty($name)) {
            //$num = str_pad($no, 3, '0', STR_PAD_LEFT);
            $name = $fleet->fleet . '-' . $no;

            $server->name = $name;

            // update name
            $this->m_server->update($no, ['name' => $name]);

            // update instance name
            if (!empty($id)) {
                $this->_tag($server, $fleet);
            }
        }

        if ($hdd > 90 && $hdd != $server->hdd) {
            // notification to slack
            $this->_slack_warn($fleet, $server, $hdd);
        }

        if ($power == 'Y') {
            // notification to slack
            $this->_slack_start($fleet, $server);
        }

        $this->_text('OK ' . $no . ' ' . $name . ' ' . $fleet->fleet . ' ' . $fleet->phase);
    }

    // call from toast shell
    public function info($no = null)
    {
        if (!$this->_has_auth('system')) {
            $this->_text('Error auth [' . $this->_auth() . ']');
            return;
        }

        if (empty($no)) {
            return;
        }

        // server
        $server = $this->m_server->getOne($this->_ono(), $no);

        if (empty($server)) {
            return;
        }

        // fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        $this->_text('OK ' . $server->no . ' ' . $server->name . ' ' . $fleet->fleet . ' ' . $fleet->phase);
    }

    private function _health_mon($no, $uptime, $cpu, $hdd)
    {
        // mon
        $data = [
            's_no' => $no,
            'uptime' => $uptime,
            'cpu' => $cpu,
            'hdd' => $hdd
        ];
        if (!empty($uptime)) {
            $needle = 'load average:';
            $load = substr($uptime, strpos($uptime, $needle) + strlen($needle));
            $loads = explode(',', $load);
            if (count($loads) == 3) {
                $data['load_1'] = $loads[0];
                $data['load_5'] = $loads[1];
                $data['load_15'] = $loads[2];
            }
        }
        $this->m_server->insertMon($data);
        $this->m_server->squeezeMon($no);
    }

    private function _health_pong($server, $fleet)
    {
        if ($fleet->health == '80') {
            $url = 'http://' . $server->host . '/health.html';
        } else {
            $url = 'http://' . $server->host . ':' . $fleet->health . '/health';
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_exec($curl);
        $errno = curl_errno($curl);
        curl_close($curl);

        if ($errno == 0) {
            // 첫 health pong 에서 Y
            if (empty($server->plugYN) && $fleet->phase != 'lb') {
                $plugYN = 'Y';

                // save pong
                $this->m_server->pong($server->no, $plugYN);

                // lb reset
                if (!empty($fleet->lb_f_no)) {
                    $outputs = $this->_toggle_lb($fleet->lb_f_no);

                    log_message('debug', 'health lb : ' . $outputs);
                }
            } else {
                // save pong
                $this->m_server->pong($server->no);
            }
        } else {
            if ($server->plugYN == 'Y') {
                log_message('error', 'health pong : ' . json_encode($server));

                // notification to sms
                $this->_sms_down($fleet, $server);

                // notification to slack
                $this->_slack_alert($fleet, $server);
            }
        }
    }

    private function _toggle_lb($lb_f_no)
    {
        $outputs = '';

        // lb servers
        $lbs = $this->m_server->getListByFleet($this->_ono(), $lb_f_no);

        foreach ($lbs as $lb) {
            // lb up/down
            $output = $this->_toast('vhost lb', $lb->user, $lb->host, $lb->port);

            $outputs .= $output;
        }

        return $outputs;
    }

    private function _data($item)
    {
        $str = $item->id . $item->name . $item->ip . $item->host . $item->port . $item->user;
        $item->h = _health_map($item->ping_date, $item->pong_date);
        if ($item->h->ping->s > 66) {
            $str .= $item->h->ping->s;
        }
        if ($item->h->pong->s > 66) {
            $str .= $item->h->pong->s;
        }
        return $str;
    }

    private function _list_data($list)
    {
        $str = '';
        foreach ($list as $item) {
            $str .= $this->_data($item);
        }
        return [
            'hash' => md5($str),
            'list' => $list
        ];
    }

    private function _tag($server, $fleet)
    {
        // save name to instances
        $tag = 'Key=Name,Value=' . $server->name;
        $tag .= ' Key=ToastPhase,Value=' . $fleet->phase;
        $tag .= ' Key=ToastFleet,Value=' . $fleet->fleet;

        // create tags
        $params = 'ec2 create-tags --resources ' . $server->id . ' --tags ' . $tag;
        $this->_aws($params);
    }

    private function _instance($id)
    {
        // describe instances
        $params = 'ec2 describe-instances --instance-ids ' . $id;
        $output = $this->_aws($params);

        $data = [];

        if (!empty($output)) {
            $json = json_decode($output);

            if (!empty($json) && !empty($json->Reservations)) {
                if (!empty($json->Reservations[0]->Instances)) {
                    if (!empty($json->Reservations[0]->Instances[0]->InstanceType)) {
                        $data['type'] = $json->Reservations[0]->Instances[0]->InstanceType;
                    }
                    if (!empty($json->Reservations[0]->Instances[0]->ImageId)) {
                        $data['image'] = $json->Reservations[0]->Instances[0]->ImageId;
                    }
                    if (!empty($json->Reservations[0]->Instances[0]->VpcId)) {
                        $data['vpc'] = $json->Reservations[0]->Instances[0]->VpcId;
                    }
                    if (!empty($json->Reservations[0]->Instances[0]->SubnetId)) {
                        $data['subnet'] = $json->Reservations[0]->Instances[0]->SubnetId;
                    }
                    if (!empty($json->Reservations[0]->Instances[0]->SecurityGroups)) {
                        $data['security'] = [];
                        foreach ($json->Reservations[0]->Instances[0]->SecurityGroups as $item) {
                            $data['security'][] = $item->GroupId;
                        }
                    }
                    if (!empty($json->Reservations[0]->Instances[0]->PublicIpAddress)) {
                        $data['pip'] = $json->Reservations[0]->Instances[0]->PublicIpAddress;
                    }
                }
            }
        }

        return $data;
    }

    private function _release_ip($id, $f_no)
    {
        // describe address by instance-id
        $params = 'ec2 describe-addresses --filters "Name=instance-id,Values=' . $id . '"';
        $output = $this->_aws($params);

        $json = json_decode($output);

        if (empty($json) || empty($json->Addresses)) {
            log_message('error', 'release address : ' . $output);
            return false;
        }

        foreach ($json->Addresses as $item) {
            if (!empty($item) && !empty($item->AllocationId)) {
                // disassociate address
                $params = 'ec2 disassociate-address --association-id ' . $item->AssociationId;
                $this->_aws($params);

                // save ip pool
                $ip = $this->m_ip->getOneById($item->AllocationId);
                if (empty($ip)) {
                    $ip = [
                        'f_no' => $f_no,
                        's_no' => 0,
                        'id' => $item->AllocationId,
                        'ip' => $item->PublicIp
                    ];
                    $this->m_ip->insert($ip);
                } else {
                    $ip = [
                        'f_no' => $f_no,
                        's_no' => 0
                    ];
                    $this->m_ip->updateById($item->AllocationId, $ip);
                }
            }
        }

        return true;
    }

    private function _terminate($id)
    {
        // terminate instance
        $params = 'ec2 terminate-instances --instance-ids ' . $id;
        $output = $this->_aws($params);

        $json = json_decode($output);

        if (empty($json) || empty($json->TerminatingInstances)) {
            log_message('error', 'terminate instance : ' . $output);
            return false;
        }

        return true;
    }

    private function _start($id)
    {
        // instance stop
        $params = 'ec2 start-instances --instance-ids ' . $id;
        $output = $this->_aws($params);

        $json = json_decode($output);

        if (empty($json) || empty($json->StartingInstances)) {
            log_message('error', 'start instance : ' . $output);
            return false;
        }

        return $json->StartingInstances[0]->CurrentState->Name;
    }

    private function _stop($id)
    {
        // instance stop
        $params = 'ec2 stop-instances --instance-ids ' . $id;
        $output = $this->_aws($params);

        $json = json_decode($output);

        if (empty($json) || empty($json->StoppingInstances)) {
            log_message('error', 'stop instance : ' . $output);
            return false;
        }

        return $json->StoppingInstances[0]->CurrentState->Name;
    }

    private function _protect($id)
    {
        // instance protection
        $params = 'ec2 modify-instance-attribute --instance-id ' . $id . ' --disable-api-termination';
        $this->_aws($params);
    }

    private function _protection($id)
    {
        // instance protection
        $params = 'ec2 describe-instance-attribute --instance-id ' . $id . ' --attribute disableApiTermination';
        $output = $this->_aws($params);

        $json = json_decode($output);

        if (empty($json) || empty($json->DisableApiTermination)) {
            log_message('error', 'protect instance : ' . $output);
            return false;
        }

        return $json->DisableApiTermination->Value;
    }

    private function _slack_start($fleet, $server)
    {
        $url = $this->_get_config('url', 'http://' . SERVER_NAME);

        if (empty($server->name)) {
            $name = 'unnamed';
        } else {
            $name = $server->name;
        }

        $title = '[시작] ' . ($name == 'unnamed' ? $fleet->fleet : $name);
        $text = $fleet->phase
            . ' > <' . $url . '/fleet/item/' . $fleet->no . '|' . $fleet->fleet . '>'
            . ' > <' . $url . '/server/item/' . $server->no . '|' . $name . '>';

        // chat slack
        $attachments[] = [
            'color' => 'good',
            'text' => $text,
            'footer' => date('Y-m-d H:i')
        ];

        if (defined('SLACK_CHANNEL_SERVER')) {
            $channel = SLACK_CHANNEL_SERVER;
        } else {
            $channel = SLACK_CHANNEL;
        }

        $this->_slack($title, $attachments, $channel);
    }

    private function _slack_stop($fleet, $server)
    {
        $url = $this->_get_config('url', 'http://' . SERVER_NAME);

        if (empty($server->name)) {
            $name = 'unnamed';
        } else {
            $name = $server->name;
        }

        $title = '[중지] ' . ($name == 'unnamed' ? $fleet->fleet : $name);
        $text = $fleet->phase
            . ' > <' . $url . '/fleet/item/' . $fleet->no . '|' . $fleet->fleet . '>'
            . ' > ' . $name;

        $user = $this->_user();

        // chat slack
        $attachments[] = [
            'color' => '#F35A00',
            'text' => $text,
            'footer' => date('Y-m-d H:i') . ' - ' . $user->username
        ];

        if (defined('SLACK_CHANNEL_SERVER')) {
            $channel = SLACK_CHANNEL_SERVER;
        } else {
            $channel = SLACK_CHANNEL;
        }

        $this->_slack($title, $attachments, $channel);
    }

    private function _slack_warn($fleet, $server, $hdd)
    {
        if ($fleet->phase == 'dev') {
            return;
        }

        $url = $this->_get_config('url', 'http://' . SERVER_NAME);

        if (empty($server->name)) {
            $name = 'unnamed';
        } else {
            $name = $server->name;
        }

        $title = '[경고] HDD ' . $hdd . ' %';
        $text = $fleet->phase
            . ' > <' . $url . '/fleet/item/' . $fleet->no . '|' . $fleet->fleet . '>'
            . ' > <' . $url . '/server/item/' . $server->no . '|' . $name . '>';

        if ($hdd > 95) {
            $color = 'danger';
        } else {
            $color = 'warning';
        }

        // chat slack
        $attachments[] = [
            'color' => $color,
            'text' => $text,
            'footer' => date('Y-m-d H:i')
        ];

        if (defined('SLACK_CHANNEL_ALERT')) {
            $channel = SLACK_CHANNEL_ALERT;
        } else {
            $channel = SLACK_CHANNEL;
        }

        $this->_slack($title, $attachments, $channel);
    }

    private function _slack_alert($fleet, $server)
    {
        if ($fleet->phase == 'dev') {
            return;
        }

        $url = $this->_get_config('url', 'http://' . SERVER_NAME);

        if (empty($server->name)) {
            $name = 'unnamed';
        } else {
            $name = $server->name;
        }

        $title = '[경고] Server is not responding.';
        $text = $fleet->phase
            . ' > <' . $url . '/fleet/item/' . $fleet->no . '|' . $fleet->fleet . '>'
            . ' > <' . $url . '/server/item/' . $server->no . '|' . $name . '>';

        // chat slack
        $attachments[] = [
            'color' => 'danger',
            'text' => $text,
            'footer' => date('Y-m-d H:i')
        ];

        if (defined('SLACK_CHANNEL_ALERT')) {
            $channel = SLACK_CHANNEL_ALERT;
        } else {
            $channel = SLACK_CHANNEL;
        }

        $this->_slack($title, $attachments, $channel);
    }

    private function _sms_down($fleet, $server)
    {
        if ($fleet->phase == 'dev') {
            return;
        }

        $data = [];

        $list = $this->m_user->getListByFleetStar($fleet->no);
        foreach ($list as $item) {
            if (!empty($item->phoneNum)) {
                $data[] = [
                    'to' => $item->phoneNum,
                    'phase' => $fleet->phase,
                    'fleet' => $fleet->fleet,
                    'server' => $server->name
                ];
            }
        }

        if (empty($data)) {
            return;
        }

        $sms = [
            'uid' => 'server_alert',
            'data' => json_encode($data)
        ];

        $this->_sms($sms);
    }

}
