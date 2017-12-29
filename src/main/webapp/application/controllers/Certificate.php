<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificate extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('certificate_model', 'm_certificate');
    }

    public function index()
    {
        if (!$this->_has_auth('system')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $data['list'] = $this->m_certificate->getList($this->_ono());

        $this->_view('certificate/list', $data);
    }

    public function item($no = null)
    {
        if (!$this->_has_auth('system')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $data = [];

        if (!empty($no)) {
            $data['certificate'] = $this->m_certificate->getOne($this->_ono(), $no);
        }

        $this->_view('certificate/item', $data);
    }

    public function save()
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $no = $this->input->get_post('no', true);
        $name = $this->input->get_post('name', true);
        $memo = $this->input->get_post('memo', true);
        $certificate = $this->input->get_post('certificate', true);
        $certificate_key = $this->input->get_post('certificate_key', true);
        $client_certificate = $this->input->get_post('client_certificate', true);

        $name = strtolower(preg_replace('/\s+/', '_', $name));

        if (empty($name)) {
            $this->_message(false, $icon, 'danger', '저장 실패. [' . $name . ']');
            return;
        }

        $data = [
            'name' => $name,
            'memo' => $memo,
            'certificate' => $certificate,
            'certificate_key' => $certificate_key,
            'client_certificate' => $client_certificate
        ];

        if (!empty($no)) {
            $one = $this->m_certificate->getOne($this->_ono(), $no);
        }

        if (empty($one)) {
            $data['o_no'] = $this->_ono();

            $no = $this->m_certificate->insert($data);

            $this->_message(true, $icon, 'redirect', '/certificate/item/' . $no);
        } else {
            $this->m_certificate->update($one->no, $data);

            $this->_message(true, $icon, 'info', '저장 성공.');
        }
    }

    public function remove($no)
    {
        $icon = 'fa-trash';

        if (!$this->_has_auth('system', $icon)) {
            return;
        }

        $config = $this->m_certificate->getOne($this->_ono(), $no);

        if (empty($config)) {
            $this->_message(false, $icon, 'danger', '삭제 실패.');
            return;
        }

        // remove
        $this->m_certificate->delete($no);

        $this->_message(true, $icon, 'remove', 'config-' . $no);
    }

    // call from toast shell
    public function name($name)
    {
        if (!$this->_has_auth('system')) {
            return;
        }

        $item = $this->m_certificate->getOneByName($this->_ono(), $name);

        if (empty($item)) {
            return;
        }

        echo "# ssl_certificate.crt" . PHP_EOL;
        $this->_text($item->certificate);
        echo PHP_EOL;

        echo "# ssl_certificate_key.key" . PHP_EOL;
        $this->_text($item->certificate_key);
        echo PHP_EOL;

        echo "# ssl_client_certificate.crt" . PHP_EOL;
        $this->_text($item->client_certificate);
        echo PHP_EOL;
    }

}
