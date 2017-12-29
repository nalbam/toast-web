<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App
{

    // WEB_ACCESS_KEY=a44f22x4-45ae-4867-9xe1-2e5129a7187b

    // COOKIE_DOMAIN=nalbam.com

    private $uuid;
    private $access_key;
    private $device;
    private $groupId;
    private $artifactId;
    private $version;

    protected $domain = 'nalbam.com';

    function __construct()
    {
    }

    function _uuid()
    {
        if (empty($this->uuid)) {
            $this->getUUID();
        }
        return $this->uuid;
    }

    function _access_key()
    {
        if (empty($this->access_key)) {
            $this->getAccessKey();
        }
        return $this->access_key;
    }

    function _device()
    {
        if (empty($this->device)) {
            $this->getDevice();
        }
        return $this->device;
    }

    function _groupId()
    {
        if (empty($this->groupId)) {
            $this->getVersion();
        }
        return $this->groupId;
    }

    function _artifactId()
    {
        if (empty($this->artifactId)) {
            $this->getVersion();
        }
        return $this->artifactId;
    }

    function _version()
    {
        if (empty($this->version)) {
            $this->getVersion();
        }
        return $this->version;
    }

    function _data()
    {
        $data = (object)[
            'result' => 'SUCCESS',
            'uuid' => $this->_uuid(),
            'device' => $this->_device(),
            'groupId' => $this->_groupId(),
            'artifactId' => $this->_artifactId(),
            'version' => $this->_version(),
            'ip' => @$_SERVER['SERVER_ADDR']
        ];
        return $data;
    }

    function _headers()
    {
        $headers = [
            'uuid' => $this->_uuid(),
            'accessKey' => $this->_access_key(),
            'version' => $this->_version(),
            'time' => date('Y-m-d H:i:s'),
            'User-Agent' => @$_SERVER['SERVER_NAME']
        ];
        return $headers;
    }

    function getDevice()
    {
        $device = $this->getRequest('device');
        if (empty($device)) {
            $device = $this->getCookie('device');
        } else {
            if (defined('DEVICE_LIST')) {
                $list = DEVICE_LIST;
            } else {
                $list = 'pc,m';
            }
            $arr = explode(',', $list);
            if (in_array($device, $arr)) {
                $this->setDevice($device);
            } else {
                $device = null;
            }
        }
        if (empty($device)) {
            $browser = '/(iPod|iPad|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS)/';
            $agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            if (preg_match($browser, $agent)) {
                $device = 'm';
            } else {
                $device = 'pc';
            }
            $this->setDevice($device);
        }
        $this->device = $device;
        return $device;
    }

    function setDevice($device = null)
    {
        if (empty($device)) {
            $expire = -1;
        } else {
            $expire = time() + 2592000;
        }
        $this->setCookie('device', $device, $expire);
    }

    function getVersion()
    {
        try {
            $dir = FCPATH . 'META-INF/maven/com.nalbam';
            if (is_dir($dir)) {
                $files = scandir($dir);
                if (count($files) > 2) {
                    $dir = $dir . '/' . $files[2];
                    if (is_dir($dir)) {
                        $files = scandir($dir);
                        if (count($files) > 1) {
                            $file = $dir . '/pom.properties';
                            if (is_file($file)) {
                                $handle = fopen($file, 'r');
                                if ($handle) {
                                    while (($line = fgets($handle)) !== false) {
                                        $pos = strpos($line, 'version');
                                        if ($pos !== false) {
                                            $this->version = trim(substr($line, strlen('version') + 1));
                                            continue;
                                        }
                                        $pos = strpos($line, 'groupId');
                                        if ($pos !== false) {
                                            $this->groupId = trim(substr($line, strlen('groupId') + 1));
                                            continue;
                                        }
                                        $pos = strpos($line, 'artifactId');
                                        if ($pos !== false) {
                                            $this->artifactId = trim(substr($line, strlen('artifactId') + 1));
                                            continue;
                                        }
                                    }
                                    fclose($handle);
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
        }
    }

    function getUUID()
    {
        $uuid = $this->getCookie('UUID');
        if (empty($uuid)) {
            $uuid = $this->uuid_v4();
            if (!empty($uuid)) {
                $expire = time() + 2592000;
                $this->setCookie('UUID', $uuid, $expire);
            }
        }
        $this->uuid = $uuid;
        return $uuid;
    }

    function getAccessKey()
    {
        if (defined('WEB_ACCESS_KEY')) {
            $access_key = WEB_ACCESS_KEY;
        } else {
            $access_key = $this->_uuid();
        }
        $this->access_key = $access_key;
        return $access_key;
    }

    function getRequest($n, $v = '')
    {
        if (isset($_REQUEST[$n]) && !empty($_REQUEST[$n])) {
            $v = trim($_REQUEST[$n]);
        }
        return $v;
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
                $d = $this->domain;
            }
        }
        if (empty($v)) {
            setcookie($n, null, -1, '/', $d);
        } else {
            setcookie($n, $v, $p, '/', $d);
        }
    }

    static function uuid_v4()
    {
        // a44f2284-45ae-4867-9de1-2e5129a7187b
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

}
