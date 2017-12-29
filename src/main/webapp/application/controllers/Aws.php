<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aws extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('fleet_model', 'm_fleet');
        $this->load->model('server_model', 'm_server');
        $this->load->model('ip_model', 'm_ip');
    }

    public function image()
    {
        $list = $this->_get_instance_list();

        // describe images
        $params = 'ec2 describe-images --owners self';
        $output = $this->_aws($params);

        if (!empty($output)) {
            $json = json_decode($output);

            // toast=true
            if (!empty($json) && !empty($json->Images)) {
                foreach ($json->Images as $item) {
                    $toast = false;
                    if (!empty($item->Tags)) {
                        foreach ($item->Tags as $tag) {
                            if ($tag->Key == 'toast') {
                                if ($tag->Value == 'true') {
                                    $toast = true;
                                }
                                break;
                            }
                        }
                    }
                    if (!$toast) {
                        continue;
                    }
                    $list[] = (object)[
                        'value' => $item->ImageId,
                        'text' => $item->Name,
                        'user' => ''
                    ];
                }
            }
        }

        usort($list, function ($a, $b) {
            return strcmp($a->text, $b->text);
        });

        $this->_json($list);
    }

    public function vpc()
    {
        $list = [];

        // describe vpcs
        $params = 'ec2 describe-vpcs';
        $output = $this->_aws($params);

        if (!empty($output)) {
            $json = json_decode($output);

            if (!empty($json) && !empty($json->Vpcs)) {
                foreach ($json->Vpcs as $item) {
                    $desc = $item->CidrBlock;
                    if (!empty($item->Tags)) {
                        foreach ($item->Tags as $tag) {
                            if ($tag->Key == 'Name') {
                                $desc = $tag->Value;
                                break;
                            }
                        }
                    }
                    $list[] = [
                        'value' => $item->VpcId,
                        'text' => $desc
                    ];
                }
            }
        }

        usort($list, function ($a, $b) {
            return strcmp($a['text'], $b['text']);
        });

        $this->_json($list);
    }

    public function subnet()
    {
        $vpc = $this->input->get_post('vpc', true);

        $list = [];

        // describe subnets
        $params = 'ec2 describe-subnets';
        $output = $this->_aws($params);

        if (!empty($output)) {
            $json = json_decode($output);

            if (!empty($json) && !empty($json->Subnets)) {
                foreach ($json->Subnets as $item) {
                    if (!empty($vpc) && $vpc != $item->VpcId) {
                        continue;
                    }
                    $desc = $item->SubnetId;
                    if (!empty($item->Tags)) {
                        foreach ($item->Tags as $tag) {
                            if ($tag->Key == 'Name') {
                                $desc = $tag->Value;
                                break;
                            }
                        }
                    }
                    $list[] = [
                        'value' => $item->SubnetId,
                        'text' => $desc
                    ];
                }
            }
        }

        usort($list, function ($a, $b) {
            return strcmp($a['text'], $b['text']);
        });

        $this->_json($list);
    }

    public function security()
    {
        $vpc = $this->input->get_post('vpc', true);

        $list = [];

        // describe security groups
        $params = 'ec2 describe-security-groups';
        $output = $this->_aws($params);

        if (!empty($output)) {
            $json = json_decode($output);

            if (!empty($json) && !empty($json->SecurityGroups)) {
                foreach ($json->SecurityGroups as $item) {
                    if (!empty($vpc) && $vpc != $item->VpcId) {
                        continue;
                    }
                    $desc = $item->GroupName;
                    if (!empty($item->Tags)) {
                        foreach ($item->Tags as $tag) {
                            if ($tag->Key == 'Name' && !empty($tag->Value)) {
                                $desc = $tag->Value;
                                break;
                            }
                        }
                    }
                    $list[] = [
                        'value' => $item->GroupId,
                        'text' => $desc
                    ];
                }
            }
        }

        usort($list, function ($a, $b) {
            return strcmp($a['text'], $b['text']);
        });

        $this->_json($list);
    }

    public function launch($f_no)
    {
        $icon = ICON_LAUNCH;

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $fleet = $this->m_fleet->getOne($this->_ono(), $f_no);

        if (empty($fleet)) {
            $this->_message(false, $icon, 'danger', 'fleet 없음.');
            return;
        }

        $instance = json_decode($fleet->instance);

        if (empty($instance) || empty($instance->image) || empty($instance->type) || empty($instance->security)) {
            $this->_message(false, $icon, 'danger', '인스턴스 생성 조건을 설정해 주세요.');
            return;
        }

        $aws_key_pair = $this->_get_config('aws_key_pair');
        $token = $this->_get_config('ywt_token');
        $token = $this->_get_config('tt_token', $token);

        if (empty($aws_key_pair)) {
            $this->_message(false, $icon, 'danger', '인스턴스 생성을 위해 aws_key 를 설정해 주세요.');
            return;
        }
        if (empty($token)) {
            $this->_message(false, $icon, 'danger', '인스턴스 생성을 위해 token 을 설정해 주세요.');
            return;
        }

        $org = $this->_org();

        $url = $this->_get_config('url', 'http://' . $org->name . '.toast.sh');

        // aws instance
        $one = $this->_get_instance_one($instance->image);
        if (!empty($one) && !empty($one->user)) {
            $ssh_user = $one->user;
        } else {
            $ssh_user = $this->_get_config('ssh_user', DEFAULT_USR);
        }

        // toast.sh
        $user_data = "#!/bin/bash" . PHP_EOL;
        $user_data .= "runuser -l " . $ssh_user . " -c 'curl -s toast.sh/install | bash'" . PHP_EOL;
        $user_data .= "runuser -l " . $ssh_user . " -c 'echo \"TOAST_URL=" . $url . "\" >> ~/.toast'" . PHP_EOL;
        $user_data .= "runuser -l " . $ssh_user . " -c 'echo \"ORG=" . $org->name . "\" >> ~/.toast'" . PHP_EOL;
        $user_data .= "runuser -l " . $ssh_user . " -c 'echo \"TOKEN=" . $token . "\" >> ~/.toast'" . PHP_EOL;
        $user_data .= "runuser -l " . $ssh_user . " -c '~/toaster/toast.sh auto'" . PHP_EOL;

        $user_data = base64_encode($user_data);

        // aws ec2 run-instances --image-id $val --instance-type $val --security-groups $val --key-name $val --user-data $val

        if (empty($instance->security)) {
            $groups = null;
        } else {
            if (is_array($instance->security)) {
                $groups = join(' ', $instance->security);
            } else {
                $groups = $instance->security;
            }
        }

        if (empty($instance->subnet)) {
            $subnet = null;
        } else {
            if (is_array($instance->subnet)) {
                if (count($instance->subnet) > 1) {
                    $subnet = $instance->subnet[rand(0, count($instance->subnet) - 1)];
                } else {
                    $subnet = $instance->subnet[0];
                }
            } else {
                $subnet = $instance->subnet;
            }
        }

        // aws param
        $params = 'ec2 run-instances';
        $params .= ' --image-id ' . $instance->image;
        $params .= ' --instance-type ' . $instance->type;
        if (!empty($groups)) {
            $params .= ' --security-group-ids ' . $groups;
        }
        if (!empty($subnet)) {
            $params .= ' --subnet-id ' . $subnet;
        }
        if (!empty($instance->ip)) {
            $params .= ' --associate-public-ip-address';
        } else {
            //$params .= ' --no-associate-public-ip-address';
        }
        if (!empty($instance->storage)) {
            //$params .= ' --block-device-mapping \'[{"DeviceName":"/dev/xvda","Ebs":{"VolumeSize":' . $instance->storage . '}}]\'';
        }
        $params .= ' --key-name ' . $aws_key_pair;
        $params .= ' --user-data ' . $user_data;

        $ids = [];

        // launch
        $output = $this->_aws($params);

        $json = json_decode($output);

        if (empty($json) || empty($json->Instances)) {
            log_message('error', 'launch Instances : ' . $output);
            $this->_message(false, $icon, 'danger', '서버 생성중 에러가 발생 했습니다.');
            return;
        }

        log_message('info', 'launch Instances : ' . count($json->Instances));

        foreach ($json->Instances as $item) {
            if (!empty($item->InstanceId)) {
                log_message('info', 'launch InstanceId : ' . $item->InstanceId);

                $ids[] = $item->InstanceId;

                $host = '';
                if (!empty($item->PrivateIpAddress)) {
                    $host = $item->PrivateIpAddress;
                }

                $data = [
                    'o_no' => $this->_ono(),
                    'u_no' => $this->_uno(),
                    'f_no' => $f_no,
                    'id' => $item->InstanceId,
                    'host' => $host,
                    'user' => $ssh_user,
                    'power' => 'P', // pending
                    'instance' => json_encode($instance)
                ];

                // save server
                $no = $this->m_server->insert($data);

                // save log
                $this->_log($no, json_encode($data), true);

                // save log
                $this->_log($no, json_encode($item), true);
            }
        }

        $count = count($ids);

        $this->_message(true, $icon, 'info', $count . '개의 서버를 생성중 입니다.');

        if ($count > 0) {
            // ip
            if (!empty($instance->ip) && $instance->ip == 'elastic') {
                $this->_sleep(10, 'elastic ip');

                foreach ($ids as $id) {
                    $host = null;

                    for ($i = 1; $i < 6; $i++) {
                        $this->_sleep(5, 'elastic ip [' . $id . '] ' . $i);

                        $host = $this->_instance_ip($id, $f_no);

                        log_message('info', 'launch Instances elastic ip : ' . $host);

                        if (!empty($host)) {
                            break;
                        }
                    }
                }
            } else {
                $this->_sleep(10, 'public ip');

                foreach ($ids as $id) {
                    $host = $this->_instance_pip($id);

                    log_message('info', 'launch Instances public ip : ' . $host);
                }
            }

            // save tag to instances
            $tag = 'Key=ToastPhase,Value=' . $fleet->phase . ' Key=ToastFleet,Value=' . $fleet->fleet;

            foreach ($ids as $id) {
                $this->_instance_tag($id, $tag);
            }

            // notification to slack
            $this->_slack_launch($fleet, $count);
        }
    }

    // set elastic ip
    private function _instance_ip($id, $f_no)
    {
        $result = $this->_instance_eip($id);

        if (!empty($result)) {
            return $result;
        }

        $aid = '';
        $eip = '';

        $server = $this->m_server->getOneById($this->_ono(), $id);

        // fleet ip pool
        $ip = $this->m_ip->getOneFree($f_no);
        if (!empty($ip)) {
            // describe address by allocation-id
            $params = 'ec2 describe-addresses --filters "Name=allocation-id,Values=' . $ip->id . '"';
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
                // ip 가 aws 에서 지워짐
                $this->m_ip->delete($ip->no);
            }
        }

        if (empty($aid)) {
            // describe address
            $params = 'ec2 describe-addresses';
            $output = $this->_aws($params);

            if (!empty($output)) {
                $json = json_decode($output);

                if (!empty($json) && !empty($json->Addresses)) {
                    foreach ($json->Addresses as $item) {
                        if (!empty($item->AllocationId) && empty($item->InstanceId)) {
                            $ip = $this->m_ip->getOneById($item->AllocationId);
                            if (!empty($ip)) {
                                // 다른 fleet 에 할당 됨
                                continue;
                            }
                            $aid = $item->AllocationId;
                            $eip = $item->PublicIp;
                            break;
                        }
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
            // associate address
            $params = 'ec2 associate-address --allocation-id ' . $aid . ' --instance-id ' . $id;
            $output = $this->_aws($params);

            if (!empty($output)) {
                $json = json_decode($output);

                if (!empty($json) && !empty($json->AssociationId)) {
                    $result = $eip;

                    // save ip pool
                    $ip = $this->m_ip->getOneById($aid);
                    if (empty($ip)) {
                        $ip = [
                            'f_no' => $f_no,
                            's_no' => $server->no,
                            'id' => $aid,
                            'ip' => $eip
                        ];
                        $this->m_ip->insert($ip);
                    } else {
                        $ip = [
                            'f_no' => $f_no,
                            's_no' => $server->no
                        ];
                        $this->m_ip->updateById($aid, $ip);
                    }
                }
            }
        }

        if (!empty($result)) {
            // save server
            $this->m_server->updateById($id, ['ip' => $result]);
        }

        return $result;
    }

    // get public ip
    private function _instance_pip($id)
    {
        $ip = '';

        // describe instances
        $params = 'ec2 describe-instances --instance-ids ' . $id;
        $output = $this->_aws($params);

        if (!empty($output)) {
            $json = json_decode($output);

            if (!empty($json) && !empty($json->Reservations)) {
                if (!empty($json->Reservations[0]->Instances)) {
                    if (!empty($json->Reservations[0]->Instances[0]->PublicIpAddress)) {
                        $ip = $json->Reservations[0]->Instances[0]->PublicIpAddress;
                    }
                }
            }
        }

        if (!empty($ip)) {
            // save server
            $this->m_server->updateById($id, ['ip' => $ip]);
        }

        return $ip;
    }

    // get elastic ip
    private function _instance_eip($id)
    {
        $ip = '';

        // describe address by instance-id
        $params = 'ec2 describe-addresses --filters "Name=instance-id,Values=' . $id . '"';
        $output = $this->_aws($params);

        if (!empty($output)) {
            $json = json_decode($output);

            if (!empty($json) && !empty($json->Addresses)) {
                foreach ($json->Addresses as $item) {
                    if (!empty($item) && !empty($item->PublicIp)) {
                        $ip = $item->PublicIp;
                        break;
                    }
                }
            }
        }

        if (!empty($ip)) {
            // save server
            $this->m_server->updateById($id, ['ip' => $ip]);
        }

        return $ip;
    }

    private function _instance_tag($id, $tag)
    {
        // create tags
        $params = 'ec2 create-tags --resources ' . $id . ' --tags ' . $tag;
        $this->_aws($params);
    }

    private function _slack_launch($fleet, $count)
    {
        $url = $this->_get_config('url', 'http://' . SERVER_NAME);

        $title = '[생성] ' . $fleet->fleet;
        $text = $fleet->phase
            . ' > <' . $url . '/fleet/item/' . $fleet->no . '|' . $fleet->fleet . '>'
            . ' > unnamed';

        if ($count > 1) {
            $text .= ' * ' . $count;
        }

        $user = $this->_user();

        // chat slack
        $attachments[] = [
            'color' => 'warning',
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

}
