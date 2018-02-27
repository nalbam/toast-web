<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    private $org;
    private $user;

    private $config_cache = [];

    private $config_list;
    private $phase_list;
    private $status_list;
    private $instance_list;
    private $instance_types;

    public function __construct()
    {
        parent::__construct();

        log_message('debug', 'access [' . @$_SERVER['REQUEST_METHOD'] . '] ' . @$_SERVER['REQUEST_URI'] . ' <- ' . @$_SERVER['HTTP_USER_AGENT']);

        $this->_get_org();
        $this->_get_user();
    }

    public function _org()
    {
        return $this->org;
    }

    public function _user()
    {
        return $this->user;
    }

    public function _ono()
    {
        if (!empty($this->org)) {
            return $this->org->no;
        }
        return 0;
    }

    public function _uno()
    {
        if (!empty($this->user)) {
            return $this->user->no;
        }
        return 0;
    }

    public function _auth()
    {
        if (!empty($this->user)) {
            return $this->user->auth;
        }
        return '';
    }

    public function _get_org()
    {
        $org = $this->m_org->getOneById(DEFAULT_ORG);
        if (empty($org)) {
            return;
        }
        $this->org = $org;
    }

    public function _get_user()
    {
        // toast token
        $token = $this->input->get_post('token', true);
        if (empty($token)) {
            $token = $this->input->cookie(TOAST_TOKEN, true);
        }
        if (!empty($token)) {
            $tt = $this->crypto->decryptToken($token);
            if (!empty($tt) && !empty($tt->no) && !empty($tt->div)) {
                $this->_set_user($tt->no, $tt->div);
                //$this->_set_cookie(TOAST_TOKEN, $token);
                return;
            }
        }

        // github token
        $tt = $this->github->getToken();
        if (!empty($tt) && !empty($tt->no) && !empty($tt->div)) {
            $this->_set_user($tt->no, $tt->div);
            $token = $this->crypto->encryptToken($tt);
            $this->_set_cookie(TOAST_TOKEN, $token);
            return;
        }
    }

    public function _set_user($no, $div)
    {
        $user = $this->m_user->getOneByMemberNo($this->_ono(), $no, $div);
        if (!empty($user)) {
            $this->user = $user;
        }
    }

    public function _get_config($key, $val = '')
    {
        if (in_array($key, $this->config_cache)) {
            return $this->config_cache[$key];
        }
        $config = $this->m_config->getOneByKey($this->_ono(), $key);
        if (!empty($config) && !empty($config->val)) {
            $this->config_cache[$key] = trim($config->val);
            return $this->config_cache[$key];
        }
        return $val;
    }

    public function _is_login()
    {
        if (!empty($this->user)) {
            return true;
        }
        header('Location: /home/login?redirect_url=' . THIS_URL);
        return false;
    }

    public function _has_auth($need, $icon = null)
    {
        $r = true;
        if (empty($this->user)) {
            $r = false;
        }
        $auth = $this->_auth();
        if (empty($auth)) {
            $r = false;
        }
        if (strpos($auth, $need) === false) {
            if ($need != 'read') {
                $r = false;
            }
        }
        if (!$r) {
            if (!empty($icon)) {
                $this->_message(false, $icon, 'danger', '권한이 없습니다. [need ' . $need . ']');
            }
        }
        return $r;
    }

    public function _redirect_url($default = '/')
    {
        $url = $this->input->cookie(R_URL, true);
        $this->_set_cookie(R_URL);

        if (empty($url)) {
            header('Location: ' . $default);
            return;
        }

        header('Location: ' . $url);
    }

    public function _set_cookie($name, $value = null, $expire = COOKIE_EXPIRES_LONG, $domain = COOKIE_DOMAIN)
    {
        if (empty($value)) {
            $expire = -1;
        }
        $cookie = [
            'name' => $name,
            'value' => $value,
            'expire' => $expire,
            'domain' => $domain,
            'path' => '/',
            'prefix' => '',
            'secure' => false
        ];
        $this->input->set_cookie($cookie);
    }

    public function _log($sno, $output, $success)
    {
        $data = [
            's_no' => $sno,
            'u_no' => $this->_uno(),
            'data' => $output,
            'success' => $success ? 'Y' : 'N'
        ];
        $this->m_log->insert($data);
    }

    public function _sms($data)
    {
        $url = '/sms/send';

        try {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($curl);
            curl_close($curl);

            log_message('debug', 'sms send : ' . $output);
        } catch (Exception $e) {
            log_message('debug', 'sms send : ' . $e->getMessage());
        }
    }

    public function _slack($text, $attachments, $channel = null)
    {
        $url = $this->_get_config('slack_hook');

        if (empty($url)) {
            return;
        }

        $param = [
            'text' => $text,
            'attachments' => $attachments
        ];

        if (!empty($channel)) {
            $param['channel'] = $channel;
        }

        $data = 'payload=' . json_encode($param);

        try {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($curl);
            curl_close($curl);

            log_message('debug', 'slack webhook : ' . $output);
        } catch (Exception $e) {
            log_message('debug', 'slack webhook : ' . $e->getMessage());
        }
    }

    public function _aws($param)
    {
        $cmd = 'aws ' . $param;

        log_message('debug', 'aws cmd : ' . $cmd);

        $out = shell_exec($cmd);

        log_message('debug', 'aws out : ' . PHP_EOL . $out);

        return $out;
    }

    public function _toast($param, $user = null, $host = null, $port = null)
    {
        $ssh_user = $this->_get_config('ssh_user', DEFAULT_USR);

        if (empty($ssh_user)) {
            $path = '~';
        } else {
            $path = '/home/' . $ssh_user;
        }

        if (empty($user) || empty($host) || empty($port)) {
            $cmd = '. ' . $path . '/.toast; ' . $path . '/toaster/remote.sh ${USER} ${HOST} ${PORT} ' . $param;
        } else {
            $cmd = $path . '/toaster/remote.sh ' . $user . ' ' . $host . ' ' . $port . ' ' . $param;
        }

        log_message('debug', 'cmd : ' . $cmd);

        $out = shell_exec($cmd);

        log_message('debug', 'out : ' . PHP_EOL . $out);

        return $out;
    }

    public function _check_null($value)
    {
        return empty($value) ? null : $value;
    }

    public function _check_numeric($value)
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    public function _check_space($value)
    {
        return strtolower(preg_replace('/\s+/', '_', $value));
    }

    public function _get_config_list()
    {
        if (!empty($this->config_list)) {
            return $this->config_list;
        }
        $this->config_list = [
            (object)['key' => '---', 'desc' => 'Default', 'type' => '---'],
            (object)['key' => 'url', 'desc' => 'This URL (http://' . DEFAULT_ORG . '.toast.sh)', 'type' => 'text'],
            (object)['key' => '---', 'desc' => 'Default', 'type' => '---'],
            (object)['key' => 'email', 'desc' => 'Admin Email (toast@' . DEFAULT_ORG . '.com)', 'type' => 'text'],
            (object)['key' => '---', 'desc' => 'Default', 'type' => '---'],
            (object)['key' => 'hosts', 'desc' => '/etc/hosts : Default Hosts', 'type' => 'area'],
            (object)['key' => 'profile', 'desc' => '.bash_profile : Default Bash Profile', 'type' => 'area'],
            (object)['key' => '---', 'desc' => 'SSH', 'type' => '---'],
            (object)['key' => 'ssh_user', 'desc' => 'User Account (' . DEFAULT_USR . ')', 'type' => 'text'],
            (object)['key' => '---', 'desc' => 'AWS', 'type' => '---'],
            (object)['key' => 'aws_config', 'desc' => '.aws/config : AWS CLI - Config', 'type' => 'area'],
            (object)['key' => 'aws_slave', 'desc' => '.aws/credentials : AWS CLI - S3 Slave', 'type' => 'area'],
            (object)['key' => 'aws_master', 'desc' => '.aws/credentials : AWS CLI - EC2/S3 Master', 'type' => 'area'],
            (object)['key' => 'aws_key_pair', 'desc' => 'AWS EC2 Key Name', 'type' => 'text'],
            (object)['key' => 'aws_key_pem', 'desc' => 'AWS EC2 Key Pem (Only Master)', 'type' => 'area'],
            (object)['key' => '---', 'desc' => 'Token', 'type' => '---'],
            (object)['key' => 'tt_token', 'desc' => 'Toast Token', 'type' => 'text'],
            (object)['key' => '---', 'desc' => 'Github', 'type' => '---'],
            (object)['key' => 'github_client', 'desc' => 'Github Client ID', 'type' => 'text'],
            (object)['key' => 'github_secret', 'desc' => 'Github Client Secret', 'type' => 'text'],
            (object)['key' => 'github_user', 'desc' => 'Github User List', 'type' => 'text'],
            (object)['key' => 'github_org', 'desc' => 'Github Organization (' . DEFAULT_ORG . ')', 'type' => 'text'],
            (object)['key' => '---', 'desc' => 'Repository', 'type' => '---'],
            (object)['key' => 'repo_group', 'desc' => 'Package GroupId (' . DEFAULT_GID . ')', 'type' => 'text'],
            (object)['key' => 'repo_bucket', 'desc' => 'Package Bucket (repo.' . DEFAULT_ORG . '.com)', 'type' => 'text'],
            (object)['key' => '---', 'desc' => 'CI', 'type' => '---'],
            (object)['key' => 'ci_url', 'desc' => 'Jenkins URL (http://ci.' . DEFAULT_ORG . '.com/jenkins)', 'type' => 'text'],
            (object)['key' => 'ci_token', 'desc' => 'Jenkins Token for Remote Build', 'type' => 'text'],
            (object)['key' => '---', 'desc' => 'Notification', 'type' => '---'],
            (object)['key' => 'slack_hook', 'desc' => 'Slack Webhook URL', 'type' => 'text'],
        ];
        return $this->config_list;
    }

    public function _get_config_one($key)
    {
        $one = '';
        $list = $this->_get_config_list();
        foreach ($list as $item) {
            if ($item->key == $key) {
                $one = $item;
                break;
            }
        }
        return $one;
    }

    public function _get_phase_list()
    {
        if (!empty($this->phase_list)) {
            return $this->phase_list;
        }
        $this->phase_list = [
            (object)['key' => 'local', 'desc' => 'Developer'],
            (object)['key' => 'dev', 'desc' => 'Development'],
            (object)['key' => 'qa', 'desc' => 'Development Test'],
            (object)['key' => 'stage', 'desc' => 'Production Test'],
            (object)['key' => 'live', 'desc' => 'Production'],
            (object)['key' => 'lb', 'desc' => 'Load Balancer (nginx)'],
            (object)['key' => 's3', 'desc' => 'S3 Static Web'],
            (object)['key' => 'build', 'desc' => 'Toast / CI'],
        ];
        return $this->phase_list;
    }

    public function _get_phase_one($key)
    {
        $one = '';
        $list = $this->_get_phase_list();
        foreach ($list as $item) {
            if ($item->key == $key) {
                $one = $item;
                break;
            }
        }
        return $one;
    }

    public function _get_status_list()
    {
        if (!empty($this->status_list)) {
            return $this->status_list;
        }
        $status_0 = json_decode('"\u2615"'); // Hot Beverage
        $status_2 = json_decode('"\u26C4"'); // Snowman Without Snow
        $status_5 = json_decode('"\u2697"'); // Alembic
        $status_8 = json_decode('"\u26C8"'); // Thunder Cloud and Rain
        $status_9 = json_decode('"\u26F3"'); // Flag In Hole
        $this->status_list = [
            (object)['code' => '10', 'name' => $status_0 . ' DEV 빌드'],
            (object)['code' => '12', 'name' => $status_2 . ' DEV 배포'],
            (object)['code' => '18', 'name' => $status_8 . ' DEV 반려'],
            (object)['code' => '19', 'name' => $status_9 . ' DEV 완료'],
            (object)['code' => '30', 'name' => $status_0 . ' QA 접수'],
            (object)['code' => '32', 'name' => $status_2 . ' QA 배포'],
            (object)['code' => '35', 'name' => $status_5 . ' QA 테스트'],
            (object)['code' => '38', 'name' => $status_8 . ' QA 반려'],
            (object)['code' => '39', 'name' => $status_9 . ' QA 통과'],
            (object)['code' => '50', 'name' => $status_0 . ' STAGE 대기'],
            (object)['code' => '52', 'name' => $status_2 . ' STAGE 배포'],
            (object)['code' => '55', 'name' => $status_5 . ' STAGE 테스트'],
            (object)['code' => '58', 'name' => $status_8 . ' STAGE 반려'],
            (object)['code' => '59', 'name' => $status_9 . ' STAGE 완료'],
            (object)['code' => '70', 'name' => $status_0 . ' LIVE 대기'],
            (object)['code' => '72', 'name' => $status_2 . ' LIVE 배포'],
            (object)['code' => '75', 'name' => $status_5 . ' LIVE 테스트'],
            (object)['code' => '78', 'name' => $status_8 . ' LIVE 반려'],
            (object)['code' => '79', 'name' => $status_9 . ' LIVE 완료'],
        ];
        return $this->status_list;
    }

    public function _get_status_one($code)
    {
        $one = '';
        $list = $this->_get_status_list();
        foreach ($list as $item) {
            if ($item->code == $code) {
                $one = $item;
                break;
            }
        }
        if (empty($one)) {
            $one = $list[0];
        }
        return $one;
    }

    public function _get_instance_list()
    {
        if (!empty($this->instance_list)) {
            return $this->instance_list;
        }
        $this->instance_list = [
            (object)['value' => 'ami-e21cc38c', 'text' => 'Amazon Linux AMI 2017.03.1', 'user' => 'ec2-user'],
            (object)['value' => 'ami-008a596e', 'text' => 'CentOS 7.3', 'user' => 'centos'],
            (object)['value' => 'ami-034b966d', 'text' => 'CentOS 6.8', 'user' => 'centos'],
            (object)['value' => 'ami-94d20dfa', 'text' => 'Ubuntu Server 16.04 LTS', 'user' => 'ubuntu']
        ];
        return $this->instance_list;
    }

    public function _get_instance_one($value)
    {
        $one = null;
        $list = $this->_get_instance_list();
        foreach ($list as $item) {
            if ($item->value == $value) {
                $one = $item;
                break;
            }
        }
        return $one;
    }

    public function _get_instance_types()
    {
        if (!empty($this->instance_types)) {
            return $this->instance_types;
        }
        $this->instance_types = [
            (object)['type' => 't2.nano', 'cpu' => 1, 'mem' => 0.5, 'price' => 0.008],
            (object)['type' => 't2.micro', 'cpu' => 1, 'mem' => 1, 'price' => 0.016],
            (object)['type' => 't2.small', 'cpu' => 1, 'mem' => 2, 'price' => 0.032],
            (object)['type' => 't2.medium', 'cpu' => 2, 'mem' => 4, 'price' => 0.064],
            (object)['type' => 't2.large', 'cpu' => 2, 'mem' => 8, 'price' => 0.128],
            (object)['type' => 't2.xlarge', 'cpu' => 4, 'mem' => 16, 'price' => 0.256],
            (object)['type' => 't2.2xlarge', 'cpu' => 8, 'mem' => 32, 'price' => 0.512],
            (object)['type' => 'm4.large', 'cpu' => 2, 'mem' => 8, 'price' => 0.123],
            (object)['type' => 'm4.xlarge', 'cpu' => 4, 'mem' => 16, 'price' => 0.246],
            (object)['type' => 'm4.2xlarge', 'cpu' => 8, 'mem' => 32, 'price' => 0.492],
            (object)['type' => 'm4.4xlarge', 'cpu' => 16, 'mem' => 64, 'price' => 0.984],
            (object)['type' => 'm4.10xlarge', 'cpu' => 40, 'mem' => 160, 'price' => 2.46],
            (object)['type' => 'm4.16xlarge', 'cpu' => 64, 'mem' => 256, 'price' => 3.936],
            (object)['type' => 'c4.large', 'cpu' => 2, 'mem' => 3.75, 'price' => 0.114],
            (object)['type' => 'c4.xlarge', 'cpu' => 4, 'mem' => 7.5, 'price' => 0.227],
            (object)['type' => 'c4.2xlarge', 'cpu' => 8, 'mem' => 15, 'price' => 0.454],
            (object)['type' => 'c4.4xlarge', 'cpu' => 16, 'mem' => 30, 'price' => 0.907],
            (object)['type' => 'c4.8xlarge', 'cpu' => 36, 'mem' => 60, 'price' => 1.815],
            (object)['type' => 'r4.large', 'cpu' => 2, 'mem' => 15.25, 'price' => 0.16],
            (object)['type' => 'r4.xlarge', 'cpu' => 4, 'mem' => 30.5, 'price' => 0.32],
            (object)['type' => 'r4.2xlarge', 'cpu' => 8, 'mem' => 61, 'price' => 0.64],
            (object)['type' => 'r4.4xlarge', 'cpu' => 16, 'mem' => 122, 'price' => 1.28],
            (object)['type' => 'r4.8xlarge', 'cpu' => 32, 'mem' => 244, 'price' => 2.56],
            (object)['type' => 'r4.16xlarge', 'cpu' => 64, 'mem' => 488, 'price' => 5.12],
        ];
        return $this->instance_types;
    }

    public function _get_instance_type($type)
    {
        $one = null;
        $list = $this->_get_instance_types();
        foreach ($list as $item) {
            if ($item->type == $type) {
                $one = $item;
                break;
            }
        }
        return $one;
    }

    public function _get_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function _view($view, $data = [], $single = false)
    {
        $data['org'] = $this->_org();
        $data['user'] = $this->_user();
        $data['auth'] = $this->_auth();

        if (!$single) {
            $this->load->view('/include/head', $data);
        }
        $this->load->view($view, $data);
        if (!$single) {
            $this->load->view('/include/footer', $data);
        }
    }

    public function _message($result = false, $icon = 'fa-floppy-o', $action = 'danger', $message = '저장 실패.')
    {
        $data = [
            'result' => $result,
            'icon' => $icon,
            'action' => $action,
            'message' => $message
        ];
        $this->_json($data);
    }

    public function _json($data = [])
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json; charset=UTF-8');

        echo json_encode($data);
    }

    public function _text($data = null)
    {
        echo preg_replace('/\r/', '', $data);
    }

    public function _sleep($sec = 1, $msg = 'sleep')
    {
        for ($i = 0; $i < $sec; $i++) {
            log_message('debug', $msg . ' : ' . $i);
            sleep(1);
        }
    }

}
