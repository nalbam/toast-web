<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Github
{

    // GITHUB_TOKEN=GHT
    // GITHUB_STATE=GHS

    // COOKIE_DOMAIN=nalbam.com
    // COOKIE_EXPIRES_TEMP=600
    // COOKIE_EXPIRES_SHORT=3600
    // COOKIE_EXPIRES_LONG=2592000

    private $token;
    private $member;

    function __construct()
    {
        // http
        $this->client = new \GuzzleHttp\Client();

        // get token
        //$this->getToken();
    }

    function _token()
    {
        return $this->token;
    }

    function _member()
    {
        return $this->member;
    }

    function _is_login()
    {
        if (empty($this->member)) {
            return false;
        }
        return true;
    }

    function clean()
    {
        $this->token = null;
        $this->member = null;
        $this->setToken(null);
    }

    function login($github_client)
    {
        // token
        if (defined('GITHUB_TOKEN')) {
            $token_name = GITHUB_TOKEN;
        } else {
            $token_name = 'GHT';
        }

        // token set null
        $this->setCookie($token_name);

        // state
        if (defined('GITHUB_STATE')) {
            $state_name = GITHUB_STATE;
        } else {
            $state_name = 'GHS';
        }

        if (defined('COOKIE_EXPIRES_LONG')) {
            $expire = time() + COOKIE_EXPIRES_TEMP;
        } else {
            $expire = time() + 600; // 10분
        }

        // new state
        $state = hash('sha256', microtime(TRUE) . rand() . $_SERVER['REMOTE_ADDR']);
        $this->setCookie($state_name, $state, $expire);

        $authorizeURL = 'https://github.com/login/oauth/authorize';
        $params = [
            'client_id' => $github_client,
            'scope' => 'user,repo,read:org',
            'state' => $state
        ];

        return $authorizeURL . '?' . http_build_query($params);
    }

    function callback($github_client, $github_secret, $github_user = null, $github_org = null, $state = null, $code = null)
    {
        // state
        if (defined('GITHUB_STATE')) {
            $state_name = GITHUB_STATE;
        } else {
            $state_name = 'GHS';
        }
        $saved = $this->getCookie($state_name, '');

        // verify state
        if (empty($code) || empty($state) || $state != $saved) {
            log_message('debug', 'github state : [' . $state . '] != [' . $saved . ']');
            return null;
        }

        $tokenURL = 'https://github.com/login/oauth/access_token';
        $params = [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'form_params' => [
                'client_id' => $github_client,
                'client_secret' => $github_secret,
                'state' => $state,
                'code' => $code
            ]
        ];

        $res = $this->client->request('POST', $tokenURL, $params);

        log_message('debug', 'github oauth : ' . $res->getBody());

        $token = json_decode($res->getBody());

        if (empty($token) || empty($token->access_token)) {
            return;
        }

        // get token
        $this->getToken($token->access_token);

        // check auth
        $auth = $this->auth($token->access_token, $github_user, $github_org);
        if (!$auth) {
            $this->clean();
        }
    }

    function auth($token = null, $user = null, $org = null)
    {
        if (empty($token) || empty($this->member)) {
            return false;
        }

        if (empty($user) || empty($org)) {
            return true;
        }

        if (!empty($user)) {
            log_message('debug', 'github auth user  : ' . $this->member->login);
            log_message('debug', 'github auth users : ' . $user);

            $users = array_filter(explode(' ', str_replace(',', ' ', $user)));

            foreach ($users as $user) {
                if ($user == $this->member->login) {
                    log_message('debug', 'github auth users : ' . $user . ' == ' . $this->member->login);
                    return true;
                }
            }
        }

        if (!empty($org)) {
            $params = [
                'headers' => [
                    'Authorization' => 'token ' . $token
                ]
            ];

            // github user organization
            $url = 'https://api.github.com/user/orgs';
            $res = $this->client->request('GET', $url, $params);

            log_message('debug', 'github auth org  : ' . $org);
            log_message('debug', 'github auth orgs : ' . $res->getBody());

            $teams = json_decode($res->getBody());

            if (!empty($teams)) {
                foreach ($teams as $team) {
                    if ($team->login == $org) {
                        log_message('debug', 'github auth orgs : ' . $team->login . ' == ' . $org);
                        return true;
                    }
                }
            }
        }

        return false;
    }

    function getToken($token = null)
    {
        if (empty($token)) {
            if (defined('GITHUB_TOKEN')) {
                $token_name = GITHUB_TOKEN;
            } else {
                $token_name = 'GHT';
            }
            $token = $this->getCookie($token_name);
        }
        if (empty($token)) {
            return null;
        }

        $params = [
            'headers' => [
                'Authorization' => 'token ' . $token
            ]
        ];

        // github user
        $url = 'https://api.github.com/user';
        $res = $this->client->request('GET', $url, $params);

        log_message('debug', 'github user : ' . $res->getBody());

        $member = json_decode($res->getBody());

        if (empty($member)) {
            return null;
        }

        // set
        $this->token = $token;
        $this->member = $member;

        // set token
        $this->setToken($token);

        return (object)[
            'no' => $member->id,
            'div' => 'github'
        ];
    }

    function setToken($token = null)
    {
        if (defined('GITHUB_TOKEN')) {
            $token_name = GITHUB_TOKEN;
        } else {
            $token_name = 'GHT';
        }
        if (empty($token)) {
            $expire = -1;
        } else {
            if (defined('COOKIE_EXPIRES_LONG')) {
                $expire = time() + COOKIE_EXPIRES_LONG;
            } else {
                $expire = time() + 2592000; // 30일
            }
        }
        $this->setCookie($token_name, $token, $expire);
    }

    function getCookie($n, $v = '')
    {
        if (isset($_COOKIE[$n]) && !empty($_COOKIE[$n])) {
            $v = $_COOKIE[$n];
        }
        return $v;
    }

    function setCookie($n, $v = null, $p = -1, $d = null)
    {
        if (empty($d)) {
            if (defined('COOKIE_DOMAIN')) {
                $d = COOKIE_DOMAIN;
            } else {
                $d = $_SERVER['SERVER_NAME'];
            }
        }
        if (empty($v)) {
            setcookie($n, null, -1, '/', $d);
        } else {
            setcookie($n, $v, $p, '/', $d);
        }
    }

}
