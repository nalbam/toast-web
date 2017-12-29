<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Help extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function instance()
    {
        $data['list'] = $this->_get_instance_types();

        $this->_view('help/instance', $data);
    }

    public function lb()
    {
        $this->_view('help/lb');
    }

}
