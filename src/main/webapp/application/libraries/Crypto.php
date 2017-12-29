<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Crypto
{

    // KEY_CODE
    // IV_CODE

    // encrypt
    public function encrypt($text)
    {
        $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_CBC, '');
        if (mcrypt_generic_init($cipher, KEY_CODE, IV_CODE) != -1) {
            $encrypted = mcrypt_generic($cipher, $text);
            mcrypt_generic_deinit($cipher);
            return bin2hex($encrypted);
        }
        return false;
    }

    // decrypt
    public function decrypt($text)
    {
        $text = hex2bin($text);
        $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', MCRYPT_MODE_CBC, '');
        if (mcrypt_generic_init($cipher, KEY_CODE, IV_CODE) != -1) {
            $decrypted = mdecrypt_generic($cipher, $text);
            mcrypt_generic_deinit($cipher);
            return str_replace("\0", "", $decrypted);
        }
        return false;
    }

    // web token encrypt
    public function encryptToken($data)
    {
        $data = $this->base64url_encode(json_encode($data));
        $hash = hash_hmac('sha256', $data, KEY_CODE);
        return $data . '.' . $hash;
    }

    // web token decrypt
    public function decryptToken($data)
    {
        $data = explode('.', $data);
        if (count($data) != 2) {
            return false;
        }
        $hash = hash_hmac('sha256', $data[0], KEY_CODE);
        if ($hash != $data[1]) {
            return false;
        }
        $data = json_decode($this->base64url_decode($data[0]));
        return $data;
    }

    // base64 encode
    public function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // base64 decode
    public function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

}
