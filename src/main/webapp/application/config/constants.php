<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE') OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ') OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESCTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS') OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

// server name
define('SERVER_NAME', $_SERVER['SERVER_NAME']);

$arr = explode('.', SERVER_NAME);
if (count($arr) > 0) {
    $org = $arr[0];
} else {
    $org = 'demo';
}

// info
define('SITE_NAME', 'Toast');
define('SITE_DESC', '토스트 / 노르스름하게 굽다 / 축복하여 건배하다');
define('SITE_AUTHOR', 'nalbam');

// toast token
define('TOAST_TOKEN', 'TT');
define('KEY_CODE', 'kFeXuBWCd3xHyZj7256vQEgEM8vq3e2x');
define('IV_CODE', 'nRadLqpN3Ef5fc5Jys5n4tbAyW9qbJuz');

// table
define('TABLE_PREFIX', 'nt_');

// default
define('DEFAULT_USR', 'ec2-user');
define('DEFAULT_ORG', $org);
define('DEFAULT_GID', 'com.' . $org);

// site
define('THIS_HOME', urlencode('http://' . $_SERVER['HTTP_HOST']));
define('THIS_URL', urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));

// github
define('GITHUB_TOKEN', 'GHT');
define('GITHUB_STATE', 'GHS');

// org / auth
define('ORG', 'org');
define('AUTH', 'auth');
define('R_URL', 'redirect_url');

// icons
define('ICON_TOAST', 'fa-anchor');
define('ICON_DEPLOY', 'fa-download');
define('ICON_APACHE', 'fa-pied-piper');
define('ICON_LAUNCH', 'fa-ship');

// cookie
define('COOKIE_DOMAIN', SERVER_NAME);
define('COOKIE_EXPIRES', 86400);            // 1일   (86400)
define('COOKIE_EXPIRES_TEMP', 600);         // 10분
define('COOKIE_EXPIRES_SHORT', 3600);       // 1시간
define('COOKIE_EXPIRES_LONG', 2592000);     // 30일  (86400 * 30)
define('COOKIE_EXPIRES_FOREVER', 31536000); // 365일 (86400 * 365)
define('COOKIE_EXPIRES_MICRO', 5);          // 5초

// upload path
define('UPLOAD_PATH', '/data/site/upload');

// slack
define('SLACK_URL', 'https://slack.com/api/chat.postMessage');
define('SLACK_CHANNEL', 'toast');
define('SLACK_CHANNEL_ALERT', 'toast_alert');
define('SLACK_CHANNEL_BUILD', 'toast_build');
define('SLACK_CHANNEL_DEPLOY', 'toast_deploy');
define('SLACK_CHANNEL_SERVER', 'toast_server');

// version
define('VERSION', '20171205');

define('JQUERY', '2.2.4');
define('FONT_AWESOME', '4.7.0');
define('BOOTSTRAP', '3.3.7');
