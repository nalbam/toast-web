<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $url = $this->input->get_post(R_URL, true);
        if (!empty($url)) {
            $this->_set_cookie(R_URL, $url);
        }
    }

    public function index()
    {
        $this->_view('home/index');
    }

    public function auth()
    {
        $this->_view('home/auth');
    }

    public function login()
    {
        header('Location: /home/github');
    }

    public function logout()
    {
        foreach ($_COOKIE as $key => $val) {
            $this->_set_cookie($key);
        }

        header('Location: /');
    }

    public function github()
    {
        $state = $this->input->get_post('state', true);
        $code = $this->input->get_post('code', true);

        $github_client = $this->_get_config('github_client');

        if (empty($state) || empty($code)) {
            // github login url
            $url = $this->github->login($github_client);

            log_message('debug', 'github login : ' . $url);

            header('Location: ' . $url);
            return;
        }

        $github_secret = $this->_get_config('github_secret');
        $github_user = $this->_get_config('github_user');
        $github_org = $this->_get_config('github_org');

        // github callback
        $this->github->callback($github_client, $github_secret, $github_user, $github_org, $state, $code);

        // github member
        $token = $this->github->_token();
        $member = $this->github->_member();

        if (empty($token) || empty($member)) {
            header('Location: /home/logout');
            return;
        }

        // user
        $user = $this->m_user->getOneByMemberNo($this->_ono(), $member->id, 'github');

        if (empty($user)) {
            $data = [
                'o_no' => $this->_ono(),
                'provider' => 'github',
                'memberNo' => $member->id,
                'username' => $member->login,
                'nickname' => $member->name,
                'email' => $member->email,
                'picture' => $member->avatar_url,
                'token' => $token
            ];
            $this->m_user->insert($data);
        } else {
            $data = [
                'username' => $member->login,
                'nickname' => $member->name,
                'email' => $member->email,
                'picture' => $member->avatar_url,
                'token' => $token
            ];
            $this->m_user->update($user->no, $data);
        }

        $this->_redirect_url();
    }

    public function ldap()
    {
        $username = $this->input->get_post('username', true);
        $password = $this->input->get_post('password', true);

        if (empty($username) || empty($password)) {
            echo '<form action="?" method="post">' . PHP_EOL;
            echo '<label for="username">Username: </label><input type="text" id="username" name="username" />' . PHP_EOL;
            echo '<label for="password">Password: </label><input type="password" id="password" name="password" />' . PHP_EOL;
            echo '<input type="submit" name="submit" value="submit" />' . PHP_EOL;
            echo '</form>' . PHP_EOL;
        } else {
            //$org = $this->_org();

            $org = (object)[
                'name' => 'nalbam'
            ];

            $ldap = ldap_connect('ldap://ldap.' . $org->name . '.com');

            $ldaprdn = 'uid=' . $username . ',ou=dev,dc=' . $org->name . ',dc=com';

            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

            $bind = @ldap_bind($ldap, $ldaprdn, $password);

            if ($bind) {
                $result = ldap_search($ldap, 'dc=' . $org->name . ',dc=com', '(sAMAccountName=' . $username . ')');

                var_dump($result);

                ldap_sort($ldap, $result, 'sn');

                $info = ldap_get_entries($ldap, $result);

                var_dump($info);

                @ldap_close($ldap);
            } else {
                $msg = 'Invalid email address / password';
                echo $msg;
            }
        }
    }

}
