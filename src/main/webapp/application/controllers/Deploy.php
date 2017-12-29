<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deploy extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('fleet_model', 'm_fleet');
        $this->load->model('server_model', 'm_server');
        $this->load->model('target_model', 'm_target');
        $this->load->model('version_model', 'm_version');
        $this->load->model('user_model', 'm_user');
        $this->load->model('log_model', 'm_log');
    }

    public function fleet($f_no, $t_no = null)
    {
        $icon = ICON_DEPLOY;

        // fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $f_no);

        if (empty($fleet)) {
            $this->_message(false, $icon, 'danger', 'fleet 없음.');
            return;
        }

        // auth
        if (!$this->_has_auth($fleet->phase, $icon)) {
            return;
        }

        // check
        if (!$this->_check($fleet, $t_no)) {
            return;
        }

        if ($fleet->phase == 's3') {
            // deploy s3
            $servers = [
                (object)['no' => 0, 'plugYN' => 'Y']
            ];
        } else {
            // servers
            $servers = $this->m_server->getListByFleet($this->_ono(), $f_no);
        }

        if (empty($servers)) {
            $this->_message(false, $icon, 'danger', 'server 없음.');
            return;
        }

        $failure = 0;
        $outputs = '';

        foreach ($servers as $server) {
            if ($server->plugYN == 'Y') {
                // deploy
                $data = $this->_deploy($fleet, $server, $t_no);

                if (!$data->result) {
                    $failure++;
                }

                $outputs .= $data->output;
            }
        }

        $this->_message($failure == 0 ? true : false, $icon, 'output', $outputs);
    }

    public function server($s_no, $t_no = null)
    {
        $icon = ICON_DEPLOY;

        // server
        $server = $this->m_server->getOne($this->_ono(), $s_no);

        if (empty($server)) {
            $this->_message(false, $icon, 'danger', 'server 없음.');
            return;
        }

        if ($server->plugYN == 'Y') {
            $this->_message(false, $icon, 'danger', '먼저 서버를 연결 해제해 주세요.');
            return;
        }

        // fleet
        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        if (empty($fleet)) {
            $this->_message(false, $icon, 'danger', 'fleet 없음.');
            return;
        }

        // auth
        if (!$this->_has_auth($fleet->phase, $icon)) {
            return;
        }

        // check
        if (!$this->_check($fleet, $t_no)) {
            return;
        }

        // deploy
        $data = $this->_deploy($fleet, $server, $t_no);

        $this->_message($data->result, $icon, 'output', $data->output);
    }

    public function vhost($s_no)
    {
        $icon = ICON_APACHE;

        // server
        $server = $this->m_server->getOne($this->_ono(), $s_no);

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

        // auth
        if (!$this->_has_auth($fleet->phase, $icon)) {
            return;
        }

        // slack 알람
        $this->_slack_deploy($fleet, $server);

        // remote call
        $output = $this->_toast('vhost', $server->user, $server->host, $server->port);

        $result = strpos($output, 'done.') ? true : false;

        // save log
        $this->_log($server->no, $output, $result);

        $this->_message($result, $icon, 'output', $output);
    }

    public function lb($s_no)
    {
        $icon = ICON_APACHE;

        // server
        $server = $this->m_server->getOne($this->_ono(), $s_no);

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

        // auth
        if (!$this->_has_auth($fleet->phase, $icon)) {
            return;
        }

        // slack 알람
        $this->_slack_deploy($fleet, $server);

        // remote call
        $output = $this->_toast('vhost lb', $server->user, $server->host, $server->port);

        $result = strpos($output, 'done.') ? true : false;

        // save log
        $this->_log($server->no, $output, $result);

        $this->_message($result, $icon, 'output', $output);
    }

    private function _check($fleet, $t_no = null)
    {
        if ($fleet->phase == 'live' || $fleet->phase == 'stage') {
            $targets = $this->m_target->getListByFleet($fleet->no, $t_no);

            foreach ($targets as $item) {
                if ($item->deployYN == 'Y') {
                    // TODO 0.0.0 을 live,stage 에 배포 금지
//                    if ($item->version == '0.0.0') {
//                        $this->_message(false, ICON_DEPLOY, 'danger', '배포 실패. [' . $fleet->phase . '][0.0.0]');
//                        return false;
//                    }
                    // TODO QA 통과되지 않은 버전을 live,stage 에 배포 금지
//                    if ($item->status < 39) {
//                        $one = $this->_get_status_one($item->status);
//                        $this->_message(false, ICON_DEPLOY, 'danger', '배포 실패. [' . $fleet->phase . '][' . $one->name . ']');
//                        return false;
//                    }
                }
            }
        }
        return true;
    }

    private function _deploy($fleet, $server, $t_no = null)
    {
        // slack 알람
        $this->_slack_deploy($fleet, $server, $t_no);

        if (empty($server) || empty($server->no)) {
            $param = 'bucket ' . $t_no;

            // toast local call
            $output = $this->_toast($param);
        } else {
            if (empty($t_no)) {
                $param = 'deploy fleet';
            } else {
                $param = 'deploy target ' . $t_no;
            }

            // toast remote call
            $output = $this->_toast($param, $server->user, $server->host, $server->port);
        }

        $result = strpos($output, 'done.') ? true : false;

        // save log
        $this->_log($server->no, $output, $result);

        return (object)[
            'result' => $result,
            'output' => $output
        ];
    }

    private function _slack_deploy($fleet, $server, $t_no = null)
    {
        $url = $this->_get_config('url', 'http://' . SERVER_NAME);

        $title = '[배포] ' . $fleet->fleet;
        $text = $fleet->phase
            . ' > <' . $url . '/fleet/item/' . $fleet->no . '|' . $fleet->fleet . '>';

        if (!empty($server) && !empty($server->no)) {
            $title = '[배포] ' . $server->name;
            $text .= ' > <' . $url . '/server/item/' . $server->no . '|' . $server->name . '>';
        }

        $fields[] = [
            'title' => 'Server',
            'value' => $text,
            'short' => true
        ];

        if (!empty($t_no)) {
            $target = $this->m_target->getOneForDeploy($t_no);

            if (!empty($target)) {
                $version = $this->m_version->getOneByVersion($target->p_no, $target->version);

                if (!empty($target->domain)) {
                    $title .= ' :: ' . '<http://' . $target->domain . '|' . $target->domain . '>';
                } else {
                    $title .= ' :: ' . $target->artifactId;
                }

                $text = '<' . $url . '/project/item/' . $target->p_no . '|' . $target->artifactId . '>'
                    . ' - <' . $url . '/version/item/' . $version->no . '|' . $target->version . '>';

                $fields[] = [
                    'title' => 'Target',
                    'value' => $text,
                    'short' => true
                ];
            }
        }

        $user = $this->_user();

        // chat slack
        $attachments[] = [
            'color' => 'warning',
            'fields' => $fields,
            'footer' => date('Y-m-d H:i') . ' - ' . $user->username
        ];

        if (defined('SLACK_CHANNEL_DEPLOY')) {
            $channel = SLACK_CHANNEL_DEPLOY;
        } else {
            $channel = SLACK_CHANNEL;
        }

        $this->_slack($title, $attachments, $channel);
    }

}
