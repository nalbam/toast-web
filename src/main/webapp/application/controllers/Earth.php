<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Earth extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $this->_view('earth/index');
    }

}
