<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jenkins extends MY_Controller
{

    private $client;
    private $base_url;

    public function __construct()
    {
        parent::__construct();

        $this->client = new \GuzzleHttp\Client();
        $this->base_url = $this->_get_config('ci_url');
    }

    public function job($name, $branch = null)
    {
        if (!$this->_has_auth('read')) {
            return;
        }

        if (empty($branch)) {
            $url = $this->base_url . '/job/' . $name . '/api/json';
        } else {
            $url = $this->base_url . '/job/' . $name . '/job/' . $branch . '/api/json';
        }

        log_message('debug', 'jenkins job ' . $url);

        $res = $this->client->request('GET', $url);

        $body = json_decode($res->getBody());

        //log_message('debug', 'jenkins job ' . json_encode($body));

        $this->_json($body);
    }

    public function create($name)
    {
        if (!$this->_has_auth('dev')) {
            return;
        }

        // create jenkins job
        // curl -s -X POST 'http://developer:developer@ci.nalbam.com:8080/jenkins/createItem?name=PROJECT_NAME'
        // --data-binary @config.xml -H "Content-Type:text/xml"

        // config.xml

        $url = $this->base_url . '/createItem?name=' . $name;

        $file = fopen(UPLOAD_PATH . '/config-' . $name . '.xml', 'r');

        $param = [
            'body' => $file
        ];

        log_message('debug', 'jenkins create ' . $url);

        $res = $this->client->request('POST', $url, $param);

        $body = json_decode($res->getBody());

        log_message('debug', 'jenkins create ' . json_encode($body));

        $this->_json($body);
    }

    public function build($name, $branch = null)
    {
        if (!$this->_has_auth('dev')) {
            return;
        }

        if (empty($branch)) {
            $url = $this->base_url . '/job/' . $name . '/build';
        } else {
            $url = $this->base_url . '/job/' . $name . '/job/' . $branch . '/build';
        }

        $param = [
            'form_params' => [
                'delay' => '0sec',
                'token' => $this->_get_config('ci_token')
            ]
        ];

        log_message('debug', 'jenkins build ' . $url);

        $res = $this->client->request('POST', $url, $param);

        if ($res->getStatusCode() == 201) {
            $body = [
                'result' => true
            ];
        } else {
            $body = json_decode($res->getBody());
        }

        //log_message('debug', 'jenkins build ' . json_encode($body));

        $this->_json($body);
    }

}
