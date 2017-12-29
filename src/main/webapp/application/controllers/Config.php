<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!$this->_has_auth('system')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $data['list'] = $this->_get_config_list();

        $this->_view('config/list', $data);
    }

    public function item($key)
    {
        if (!$this->_has_auth('system')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $one = $this->_get_config_one($key);

        if (empty($one)) {
            return;
        }

        // config
        $config = $this->m_config->getOneByKey($this->_ono(), $key);

        if (empty($config)) {
            $config = (object)[
                'no' => 0,
                'key' => $key,
                'val' => '',
                'type' => 'area'
            ];
        }

        $config->type = $one->type;

        if ($config->type == 'multi') {
            $config->vals = preg_split('/[\s,]+/', trim($config->val));
        }

        $data['config'] = $config;

        $this->_view('config/item', $data);
    }

    public function save()
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $key = $this->input->get_post('key', true);
        $val = $this->input->get_post('val', true);

        $key = preg_replace('/\s+/', '_', $key);

        if (empty($key)) {
            $this->_message(false, $icon, 'danger', '저장 실패. [' . $key . ']');
            return;
        }

        $one = $this->_get_config_one($key);

        if (empty($one)) {
            $this->_message(false, $icon, 'danger', '저장 실패. [' . $key . ']');
            return;
        }

        if ($one->type == 'multi') {
            $val = join(' ', $val);
        }

        $data = [
            'val' => $val
        ];

        // config
        $config = $this->m_config->getOneByKey($this->_ono(), $key);

        if (empty($config)) {
            $data['o_no'] = $this->_ono();
            $data['key'] = $key;

            $this->m_config->insert($data);
        } else {
            $this->m_config->update($config->no, $data);
        }

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    public function remove($no)
    {
        $icon = 'fa-trash';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $config = $this->m_config->getOne($this->_ono(), $no);

        if (empty($config)) {
            $this->_message(false, $icon, 'danger', '삭제 실패.');
            return;
        }

        // remove
        $this->m_config->delete($no);

        $this->_message(true, $icon, 'remove', 'config-' . $no);
    }

    // call from toast shell
    public function key($key)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        $item = $this->m_config->getOneByKey($this->_ono(), $key);

        if (empty($item)) {
            return;
        }

        $this->_text($item->val);
    }

}
