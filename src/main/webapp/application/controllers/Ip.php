<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ip extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('ip_model', 'm_ip');
        $this->load->model('server_model', 'm_server');
        $this->load->model('fleet_model', 'm_fleet');
    }

    public function index()
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $phase = $this->input->get_post('phase', true);

        $data['phase'] = $phase;

        // ip list
        $data['list'] = $this->m_ip->getList($phase);

        // phase list
        $data['phases'] = $this->_get_phase_list();

        $this->_view('ip/list', $data);
    }

    public function create($f_no)
    {
        $icon = 'fa-plus';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $fleet = $this->m_fleet->getOne($this->_ono(), $f_no);

        if (empty($fleet)) {
            $this->_message(false, $icon, 'danger', 'fleet 없음.');
            return;
        }

        $aid = '';
        $eip = '';

        // describe address
        $params = 'ec2 describe-addresses';
        $output = $this->_aws($params);

        if (!empty($output)) {
            $json = json_decode($output);

            if (!empty($json) && !empty($json->Addresses)) {
                foreach ($json->Addresses as $item) {
                    if (!empty($item->AllocationId) && empty($item->InstanceId)) {
                        // 사용 가능
                        $aid = $item->AllocationId;
                        $eip = $item->PublicIp;
                        break;
                    }
                }
            }
        }

        if (empty($aid)) {
            // allocate address
            $params = 'ec2 allocate-address';
            $output = $this->_aws($params);

            if (!empty($output)) {
                $json = json_decode($output);

                if (!empty($json) && !empty($json->AllocationId)) {
                    $aid = $json->AllocationId;
                    $eip = $json->PublicIp;
                }
            }
        }

        if (!empty($aid)) {
            $ip = [
                'f_no' => $f_no,
                's_no' => 0,
                'id' => $aid,
                'ip' => $eip
            ];
            $this->m_ip->insert($ip);
        }

        $this->_message(true, $icon, 'info', $eip . ' 를 생성 했습니다.');
    }

    public function remove($no)
    {
        $icon = 'fa-trash';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $ip = $this->m_ip->getOne($no);

        if (empty($ip)) {
            $this->_message(false, $icon, 'danger', 'ip 없음.');
            return;
        }
        if (!empty($ip->s_no) && $ip->s_no > 0) {
            $this->_message(false, $icon, 'danger', '할당된 ip 는 삭제 할수 없음.');
            return;
        }

        // release address
        //$params = 'ec2 release-address --allocation-id ' . $ip->id;
        //$this->_aws($params);

        // remove
        $this->m_ip->delete($no);

        $this->_message(true, $icon, 'script', 'remove_ip(' . $no . ')');
    }

    public function fleet($f_no)
    {
        if (!$this->_has_auth('read')) {
            $this->_json();
            return;
        }

        // ip list
        $list = $this->m_ip->getListByFleet($f_no);

        $data = $this->_list_data($list);

        $this->_json($data);
    }

    public function migration()
    {
        if (!$this->_has_auth('system')) {
            $this->_json();
            return;
        }

        $result = [];

        // describe address
        $params = 'ec2 describe-addresses';
        $output = $this->_aws($params);

        if (!empty($output)) {
            $json = json_decode($output);

            if (!empty($json) && !empty($json->Addresses)) {
                foreach ($json->Addresses as $item) {
                    if (!empty($item->AllocationId) && !empty($item->InstanceId)) {
                        $server = $this->m_server->getOneById($this->_ono(), $item->InstanceId);
                        $ip = $this->m_ip->getOneById($item->AllocationId);

                        if (!empty($server)) {
                            if (!empty($ip)) {
                                if (!empty($ip->s_no) && $ip->s_no != $server->no) {
                                    $ip = [
                                        'f_no' => $server->f_no,
                                        's_no' => $server->no
                                    ];
                                    $this->m_ip->updateById($item->AllocationId, $ip);
                                }
                            } else {
                                $ip = [
                                    'f_no' => $server->f_no,
                                    's_no' => $server->no,
                                    'id' => $item->AllocationId,
                                    'ip' => $item->PublicIp
                                ];
                                $this->m_ip->insert($ip);
                                $result[] = $ip;
                            }
                        } else {
                            if (!empty($ip)) {
                                if (!empty($ip->s_no) && $ip->s_no > 0) {
                                    $ip = [
                                        's_no' => 0
                                    ];
                                    $this->m_ip->updateById($item->AllocationId, $ip);
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->_json($result);
    }

    private function _list_data($list)
    {
        $str = '';

        foreach ($list as $item) {
            $str .= $item->id . $item->ip . $item->s_no;
        }

        return [
            'hash' => md5($str),
            'list' => $list
        ];
    }

}
