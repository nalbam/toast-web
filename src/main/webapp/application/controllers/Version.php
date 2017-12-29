<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Version extends MY_Controller
{

    private $project;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('version_model', 'm_version');
        $this->load->model('project_model', 'm_project');
        $this->load->model('server_model', 'm_server');
        $this->load->model('target_model', 'm_target');
        $this->load->model('deploy_model', 'm_deploy');
    }

    public function item($no)
    {
        if (!$this->_has_auth('read')) {
            header('Location: /home/auth?msg=NEED_AUTH');
            return;
        }

        // version
        $version = $this->m_version->getOne($no);

        if (empty($version)) {
            return;
        }

        // project
        $project = $this->m_project->getOne($this->_ono(), $version->p_no);

        // status
        $statuses = $this->_get_status_list();

        $data = [
            'version' => $version,
            'project' => $project,
            'statuses' => $statuses
        ];

        $this->_view('version/item', $data);
    }

    public function check($no)
    {
        if (!$this->_has_auth('read')) {
            return;
        }

        $version = $this->m_version->getLastVersion($no);

        $this->_json($version);
    }

    public function status($no)
    {
        $icon = 'fa-floppy-o';

        // version
        $version = $this->m_version->getOne($no);

        if (empty($version)) {
            $this->_message(false, $icon, 'danger', 'version 없음.');
            return;
        }

        // project
        $project = $this->m_project->getOne($this->_ono(), $version->p_no);

        if (empty($project)) {
            $this->_message(false, $icon, 'danger', 'project 없음.');
            return;
        }

        $status = $this->input->get_post('status', true);
        if (empty($status)) {
            $status = 0;
        }

        // check auth
        $need = $this->_status_auth($status);
        if (!empty($need)) {
            $this->_message(false, $icon, 'danger', '권한이 없습니다. [need ' . $need . ']');
            return;
        }

        $data = [
            'status' => $status
        ];

        // update
        $this->m_version->update($no, $data);

        // notification to slack
        $this->_slack_update($project, $version, $status);

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    public function save($no)
    {
        $icon = 'fa-floppy-o';

        if (!$this->_has_auth('dev', $icon)) {
            return;
        }

        // version
        $version = $this->m_version->getOne($no);

        if (empty($version)) {
            $this->_message(false, $icon, 'danger', 'version 없음.');
            return;
        }

        // project
        $project = $this->m_project->getOne($this->_ono(), $version->p_no);

        if (empty($project)) {
            $this->_message(false, $icon, 'danger', 'project 없음.');
            return;
        }

        $note = $this->input->get_post('note', true);

        $data = [
            'note' => $note
        ];

        // update
        $this->m_version->update($no, $data);

        // notification to slack
        $this->_slack_update($project, $version, 0, $note);

        $this->_message(true, $icon, 'info', '저장 성공.');
    }

    public function remove($no)
    {
        $icon = 'fa-trash';

        if (!$this->_has_auth('dev', $icon)) {
            return;
        }

        $version = $this->m_version->getOne($no);

        if (empty($version)) {
            $this->_message(false, $icon, 'danger', 'version 없음.');
            return;
        }

        // remove
        $this->m_version->delete($no);

        // remove package
        $this->_remove($version->p_no, $version->version);

        $this->_message(true, $icon, 'remove', 'version-' . $no);
    }

    // call from toast shell
    public function latest($artifactId)
    {
        if (!$this->_has_auth('dev')) {
            return;
        }

        $branch = $this->input->get_post('branch', true);
        $git_id = '';

        $project = $this->m_project->getOneByName($this->_ono(), $artifactId);

        if (empty($project)) {
            $version = '0.0.0';
        } else {
            $version = $project->major . '.' . $project->minor . '.' . $project->build;

            if (!empty($branch)) {
                $last = $this->m_version->getLastVersion($project->no, $branch);

                if (!empty($last)) {
                    $git_id = $last->git_id;
                }
            }
        }

        $this->_text('OK ' . $version . ' ' . $git_id);
    }

    // call from toast shell
    public function build($artifactId, $ver = '0.0.0')
    {
        if (!$this->_has_auth('dev')) {
            return;
        }

        $groupId = $this->input->get_post('groupId', true);
        $packaging = $this->input->get_post('packaging', true);
        $branch = $this->input->get_post('branch', true);
        $note = $this->input->get_post('note', true);
        $git = $this->input->get_post('git', true);
        $url = $this->input->get_post('url', true);

        if (empty($groupId)) {
            $groupId = DEFAULT_GID;
        }

        // project
        $p_no = $this->_project($groupId, $artifactId, $packaging, $ver, $url);

        // version
        $v_no = $this->_version($p_no, $ver, 10, $branch, $note, $git);

        // notification to slack
        $this->_slack_build($p_no, $v_no, $artifactId, $ver, $note);

        // squeeze version
        $this->_squeeze($p_no);

        $this->_text('OK ' . $ver);
    }

    // call from toast shell
    public function deploy($artifactId, $ver = '0.0.0')
    {
        if (!$this->_has_auth('dev')) {
            return;
        }

        $groupId = $this->input->get_post('groupId', true);
        $packaging = $this->input->get_post('packaging', true);

        if (empty($groupId)) {
            $groupId = DEFAULT_GID;
        }

        $s_no = $this->input->get_post('no', true);
        $t_no = $this->input->get_post('t_no', true);

        // project
        $p_no = $this->_project($groupId, $artifactId, $packaging);

        // target
        if (empty($t_no)) {
            $t_no = $this->_target($s_no, $p_no);
        }

        // server
        $server = $this->_server($s_no);

        // status
        $status = $this->_status($server->phase);

        // save version
        $v_no = $this->_version($p_no, $ver, $status);

        // save deploy version
        $this->_deployed($s_no, $t_no, $ver);

        // notification to slack
        $this->_slack_deploy($server, $p_no, $v_no, $artifactId, $ver);

        $this->_text('OK ' . $ver);
    }

    private function _status_auth($status)
    {
        if ($status > 29) {
            if ($this->_has_auth('qa')) {
                return null;
            }
            return 'qa';
        } else if ($status > 9) {
            if ($this->_has_auth('dev')) {
                return null;
            }
        }
        return 'dev';
    }

    private function _status($phase)
    {
        if ($phase == 'dev' || $phase == 'build') {
            $status = 12;
        } else if ($phase == 'qa') {
            $status = 32;
        } else if ($phase == 'stage') {
            $status = 52;
        } else if ($phase == 'live') {
            $status = 72;
        } else {
            $status = 1;
        }
        return $status;
    }

    private function _server($s_no)
    {
        if (!empty($s_no)) {
            $server = $this->m_server->getOne($this->_ono(), $s_no);
        }

        if (empty($server)) {
            $phase = $this->input->get_post('phase', true);
            $fleet = $this->input->get_post('fleet', true);
            $name = $this->input->get_post('name', true);

            $server = (object)[
                'no' => 0,
                'phase' => $phase,
                'fleet' => $fleet,
                'name' => $name
            ];
        }

        return $server;
    }

    private function _squeeze($p_no)
    {
        $list = $this->m_version->getOldList($p_no);

        foreach ($list as $item) {
            // remove package
            $this->_remove($p_no, $item->version);

            // remove
            $this->m_version->delete($item->no);
        }
    }

    private function _target($s_no, $p_no)
    {
        if (empty($s_no) || empty($p_no)) {
            return 0;
        }

        $target = $this->m_target->getOneByServer($s_no, $p_no);

        if (empty($target)) {
            $t_no = 0;
        } else {
            $t_no = $target->no;
        }

        return $t_no;
    }

    private function _deployed($s_no, $t_no, $ver)
    {
        if (empty($s_no) || empty($t_no)) {
            return;
        }

        $deploy = $this->m_deploy->getOne($s_no, $t_no);

        if (empty($deploy)) {
            $data = [
                's_no' => $s_no,
                't_no' => $t_no,
                'deployed' => $ver
            ];

            $this->m_deploy->insert($data);
        } else {
            $data = [
                'deployed' => $ver
            ];

            $this->m_deploy->update($deploy->no, $data);
        }
    }

    private function _version($p_no, $ver, $status, $branch = null, $note = null, $git_id = null)
    {
        $version = $this->m_version->getOneByVersion($p_no, $ver);

        if (empty($version)) {
            $data = [
                'p_no' => $p_no,
                'version' => $ver,
                'status' => $status,
                'branch' => $branch,
                'git_id' => $git_id,
                'note' => $note
            ];

            $v_no = $this->m_version->insert($data);
        } else {
            $v_no = $version->no;

            if ($status == 10) {
                $data = [
                    'status' => $status,
                    'branch' => $branch,
                    'git_id' => $git_id,
                    'note' => $note
                ];

                $this->m_version->update($v_no, $data);
            } else {
                $this->m_version->updateStatus($v_no, $status);
            }
        }

        return $v_no;
    }

    private function _project($groupId, $artifactId, $packaging = null, $ver = null, $url = null)
    {
        if (empty($this->project)) {
            $this->project = $this->m_project->getOneByName($this->_ono(), $artifactId);
        }

        $project = $this->project;

        if ($project == null) {
            $name = str_replace('.', '-', $artifactId);

            $data = [
                'o_no' => $this->_ono(),
                'name' => $name,
                'groupId' => $groupId,
                'artifactId' => $artifactId,
                'packaging' => $packaging,
                'git_url' => $url
            ];

            $p_no = $this->m_project->insert($data);
        } else {
            $p_no = $project->no;

            $data = [
                'groupId' => $groupId,
                'artifactId' => $artifactId,
                'packaging' => $packaging,
                'git_url' => $url
            ];

            if (!empty($ver)) {
                list($major, $minor, $build) = explode('.', $ver);

                // increase
                if ($project->major == $major && $project->minor == $minor && $project->build == $build) {
                    $build = $build + 1;

                    $data['build'] = $build;
                }
            }

            if (!empty($data)) {
                $this->m_project->update($p_no, $data);
            }
        }

        return $p_no;
    }

    private function _remove($p_no, $ver)
    {
        if (empty($this->project)) {
            $this->project = $this->m_project->getOne($this->_ono(), $p_no);
        }

        $project = $this->project;

        if (!empty($project)) {
            $repo = $this->_get_config('repo_path');

            if (!empty($repo)) {
                $group_path = str_replace('.', '/', $project->groupId);

                $path = $repo . '/maven2/' . $group_path . '/' . $project->artifactId . '/' . $ver;

                // s3 rm
                $params = 's3 rm ' . $path . ' --recursive';
                $this->_aws($params);
            }
        }
    }

    private function _slack_build($p_no, $v_no, $artifactId, $version, $note = null)
    {
        $url = $this->_get_config('url', 'http://' . SERVER_NAME);

        $title = '[빌드] ' . $artifactId . ' - ' . $version;
        $text = '<' . $url . '/project/item/' . $p_no . '|' . $artifactId . '>' .
            ' - <' . $url . '/version/item/' . $v_no . '|' . $version . '>';

        if (!empty($note)) {
            $text .= PHP_EOL . $note;
        }

        // chat slack
        $attachments[] = [
            'color' => 'good',
            'text' => $text,
            'footer' => date('Y-m-d H:i')
        ];

        if (defined('SLACK_CHANNEL_BUILD')) {
            $channel = SLACK_CHANNEL_BUILD;
        } else {
            $channel = SLACK_CHANNEL;
        }

        $this->_slack($title, $attachments, $channel);
    }

    private function _slack_update($project, $version, $status, $note = null)
    {
        $url = $this->_get_config('url', 'http://' . SERVER_NAME);

        $user = $this->_user();

        $title = '[변경] ' . $project->artifactId . ' - ' . $version->version;
        $text = '<' . $url . '/project/item/' . $project->no . '|' . $project->artifactId . '>'
            . ' - <' . $url . '/version/item/' . $version->no . '|' . $version->version . '>';

        if (defined('SLACK_CHANNEL_BUILD')) {
            $channel = SLACK_CHANNEL_BUILD;
        } else {
            $channel = SLACK_CHANNEL;
        }

        if (!empty($note)) {
            // chat slack
            $attachments[] = [
                'color' => '#7CD197',
                'text' => $text . PHP_EOL . $note,
                'footer' => date('Y-m-d H:i') . ' - ' . $user->username
            ];

            $this->_slack($title, $attachments, $channel);
        }

        $attachments = [];

        if ($status > 1) {
            $one = $this->_get_status_one($status);

            $title .= ' :: ' . $one->name;

            $fields[] = [
                'title' => 'Project',
                'value' => $text,
                'short' => true
            ];

            $fields[] = [
                'title' => 'Status',
                'value' => $one->name,
                'short' => true
            ];

            // chat slack
            $attachments[] = [
                'color' => '#764FA5',
                'fields' => $fields,
                'footer' => date('Y-m-d H:i') . ' - ' . $user->username
            ];

            $this->_slack($title, $attachments, $channel);
        }
    }

    private function _slack_deploy($server, $p_no, $v_no, $artifactId, $version)
    {
        $this->load->model('fleet_model', 'm_fleet');

        $fleet = $this->m_fleet->getOne($this->_ono(), $server->f_no);

        if (empty($fleet)) {
            return;
        }

        $url = $this->_get_config('url', 'http://' . SERVER_NAME);

        $title = '[배포] ' . $fleet->fleet;
        $text = $fleet->phase
            . ' > <' . $url . '/fleet/item/' . $fleet->no . '|' . $fleet->fleet . '>';

        if (empty($server->name)) {
            $text .= ' > unnamed';
        } else {
            $title = '[배포] ' . $server->name;
            $text .= ' > <' . $url . '/server/item/' . $server->no . '|' . $server->name . '>';
        }

        $fields[] = [
            'title' => 'Server',
            'value' => $text,
            'short' => true
        ];

        $title .= ' :: ' . $artifactId . ' - ' . $version;
        $text = '<' . $url . '/project/item/' . $p_no . '|' . $artifactId . '>'
            . ' - <' . $url . '/version/item/' . $v_no . '|' . $version . '>';

        $fields[] = [
            'title' => 'Target',
            'value' => $text,
            'short' => true
        ];

        // chat slack
        $attachments[] = [
            'color' => 'good',
            'fields' => $fields,
            'footer' => date('Y-m-d H:i')
        ];

        if (defined('SLACK_CHANNEL_DEPLOY')) {
            $channel = SLACK_CHANNEL_DEPLOY;
        } else {
            $channel = SLACK_CHANNEL;
        }

        $this->_slack($title, $attachments, $channel);
    }

}
