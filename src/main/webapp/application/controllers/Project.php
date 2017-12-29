<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('project_model', 'm_project');
    }

    public function index()
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $data['list'] = $this->m_project->getList($this->_ono(), $this->_uno());

        $this->_view('project/list', $data);
    }

    public function stars()
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $data['list'] = $this->m_project->getListStar($this->_ono(), $this->_uno());

        $this->_view('project/list', $data);
    }

    public function item($no = null)
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        $data = [];

        if (!empty($no)) {
            $data['project'] = $this->m_project->getOne($this->_ono(), $no);

            if (empty($data['project'])) {
                return;
            }

            $data['project']->git_url = $this->_git_url($data['project']->git_url);

            $this->load->model('version_model', 'm_version');
            $this->load->model('phase_model', 'm_phase');
            $this->load->model('fleet_model', 'm_fleet');
            $this->load->model('target_model', 'm_target');

            $data['versions'] = $this->m_version->getList($no);
            $last = '';
            foreach ($data['versions'] as $item) {
                $item->reg_days = _days_count($item->reg_date);
                $item->mod_days = _days_count($item->mod_date);
                if (empty($last)) {
                    $last = $item->mod_date;
                }
            }
            $data['last'] = $last;

            $data['fleets'] = $this->m_fleet->getList($this->_ono(), $this->_ono());

            $data['targets'] = $this->m_target->getListByProject($no);

            $data['statuses'] = $this->_get_status_list();
        } else {
            $data['project'] = (object)[
                'no' => 0,
                'name' => '',
                'groupId' => DEFAULT_GID,
                'artifactId' => '',
                'major' => 1,
                'minor' => 0,
                'build' => 0,
                'packaging' => 'war'
            ];
        }

        $this->_view('project/item', $data);
    }

    public function save($no = null)
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('dev', $icon)) {
            return;
        }

        $name = $this->input->get_post('name', true);
        $groupId = $this->input->get_post('groupId', true);
        $artifactId = $this->input->get_post('artifactId', true);
        $packaging = $this->input->get_post('packaging', true);
        $major = $this->input->get_post('major', true);
        $minor = $this->input->get_post('minor', true);
        $build = $this->input->get_post('build', true);

        if (empty($name) || empty($groupId) || empty($artifactId) || empty($packaging)) {
            $this->_message(false, $icon, 'danger', '저장 실패.');
            return;
        }

        $data = [
            'name' => $name,
            'groupId' => $groupId,
            'artifactId' => $artifactId,
            'packaging' => $packaging
        ];

        if (!empty($no)) {
            $project = $this->m_project->getOne($this->_ono(), $no);
        }

        if (empty($project)) {
            $data['o_no'] = $this->_ono();

            $this->m_project->insert($data);

            $action = 'reload';
        } else {
            $this->m_project->update($no, $data);

            if (!empty($major) || !empty($minor) || !empty($build)) {
                $data = [
                    'major' => $major,
                    'minor' => $minor,
                    'build' => $build
                ];
                $this->m_project->updateVersion($no, $data);
            }

            if ($project->packaging != $packaging) {
                $action = 'reload';
            } else {
                $action = 'info';
            }
        }

        $this->_message(true, $icon, $action, '저장 성공.');
    }

    public function remove($no)
    {
        $icon = 'fa-trash';

        if (!$this->_has_auth('dev', $icon)) {
            return;
        }

        $project = $this->m_project->getOne($this->_ono(), $no);

        if (empty($project)) {
            $this->_message(false, $icon, 'danger', 'project 없음.');
            return;
        }

        $this->load->model('version_model', 'm_version');
        $this->load->model('target_model', 'm_target');

        // remove
        $this->m_target->deleteProject($no);
        $this->m_version->deleteProject($no);
        $this->m_project->deleteStar($no);
        $this->m_project->delete($no);

        // remove package
        $this->_remove($project);

        // slack 알람
        $this->_slack_remove($project);

        $this->_message(true, $icon, 'remove', 'project-' . $no);
    }

    public function star($no)
    {
        if (!$this->_has_auth('read')) {
            return;
        }

        $no = $this->m_project->toggleStar($no, $this->_uno());

        if (empty($no)) {
            $icon = 'fa-star-o star';
        } else {
            $icon = 'fa-star star';
        }

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    private function _git_url($url)
    {
        if (empty($url)) {
            return null;
        }
        return 'https://' . substr(preg_replace('/\:/', '/', $url), 4, strlen($url) - 8);
    }

    private function _remove($project)
    {
        $repo = $this->_get_config('repo_path');

        if (!empty($repo)) {
            $group_path = str_replace('.', '/', $project->groupId);

            $path = $repo . '/maven2/' . $group_path . '/' . $project->artifactId;

            // s3 rm
            $params = 's3 rm ' . $path . ' --recursive';
            $this->_aws($params);
        }
    }

    private function _slack_remove($project)
    {
        $url = $this->_get_config('url', 'http://' . SERVER_NAME);

        $title = '[삭제] ' . $project->artifactId;
        $text = '<' . $url . '/project/item/' . $project->no . '|' . $project->artifactId . '>';

        $user = $this->_user();

        // chat slack
        $attachments[] = [
            'color' => 'danger',
            'text' => $text,
            'footer' => date('Y-m-d H:i') . ' - ' . $user->username
        ];

        if (defined('SLACK_CHANNEL_BUILD')) {
            $channel = SLACK_CHANNEL_BUILD;
        } else {
            $channel = SLACK_CHANNEL;
        }

        $this->_slack($title, $attachments, $channel);
    }

}
