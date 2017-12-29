<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Health extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('app');
    }

    public function index()
    {
        $data = $this->app->_data();

        $this->_json($data);
    }

}
