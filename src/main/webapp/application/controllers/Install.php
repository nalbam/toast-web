<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $url = 'http://toast.sh/install';

        $client = new \GuzzleHttp\Client();

        $res = $client->request('GET', $url);

        $this->_text($res->getBody());
    }

}
