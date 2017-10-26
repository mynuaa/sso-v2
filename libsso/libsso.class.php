<?php

require_once __DIR__ . '/config.php';

class SSO {
    protected static $ssodb = null;
    protected static $ucdb = null;
    protected static $uid = -1;

    protected static function ssoInit($args) {
        self::$ssodb = new mysqli($args['host'], $args['user'], $args['pass'], $args['dbnm'], $args['port']);
        self::$ssodb->set_charset('utf8mb4');
        if (isset($_COOKIE['sid'])) {
            // v2 的登录检测
            $sid = addslashes($_COOKIE['sid']);
            $user = self::$ssodb->query("SELECT `user`.`id` FROM `user`, `user_tokens` WHERE `user_tokens`.`sid` = '{$sid}' AND `expires` > " . time());
            $user = $user->fetch_assoc();
            self::$uid = $user['id'];
        } else if (isset($args['need_v1_compatible']) && $args['need_v1_compatible']) {
            // 加载旧证书
            $prkey = openssl_pkey_get_private(file_get_contents($args['cert'] . '/private_key.pem'));
            // 旧的解密函数
            $ssoDecrypt = function ($str) use ($prkey) {
                $encrypted = base64_decode($str);
                if (!openssl_private_decrypt($encrypted, $str, $prkey)) return false;
                return $str;
            };
            // 旧的解密流程
            if (isset($_COOKIE['myauth_uid'])) {
                if ($uid = $ssoDecrypt($_COOKIE['myauth_uid'])) {
                    $uid = intval(json_decode($uid, true)['uid']);
                    self::$uid = $uid;
                    // 生成新的登录凭据
                    $sid = self::createSid();
                    $expires = time() + 86400000;
                    // 插入到新的数据库中
                    self::$ssodb->query("INSERT INTO `user_tokens` (`user_id`, `sid`, `expires`) VALUES ({$uid}, '{$sid}', {$expires})");
                    setcookie('sid', $sid, time() + 86400000, '/', '', false, true);
                }
            }
        }
    }
    protected static function ucInit($args) {
        self::$ucdb = new mysqli($args['host'], $args['user'], $args['pass'], $args['dbnm'], $args['port']);
        self::$ucdb->set_charset('utf8mb4');
    }
    public function createSid() {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes(16));
        }
        if (function_exists('mcrypt_create_iv')) {
            return bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes(16));
        }
        return 'error';
    }
    public static function init() {
        self::ssoInit($GLOBALS['__CONFIG']['sso']);
        self::ucInit($GLOBALS['__CONFIG']['uc']);
    }
    public static function generateLoginUrl() {
        if (self::$uid !== -1) {
            return "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        }
        $uri = base64_encode($_SERVER['REQUEST_URI']);
        return "http://{$_SERVER['HTTP_HOST']}/sso-v2/login?redirect_uri={$uri}";
    }
    public static function gotoLogin() {
        if (self::$uid !== -1) {
            return;
        }
        header("Location: http://{$_SERVER['HTTP_HOST']}/sso-v2/login?redirect_uri=" . base64_encode($_SERVER['REQUEST_URI']));
        exit();
    }
    public static function getUserInfo($id = null) {
        if ($id === null) $id = self::$uid;
        $ucRow = $ssoRow = null;
        $ucResult = self::$ucdb->query("SELECT `username`, `email` FROM `members` WHERE `uid` = {$id}");
        $ssoResult = self::$ssodb->query("SELECT `stu_num`, `name` FROM `user` WHERE `id` = {$id}");
        $ucRow = $ucResult->fetch_array();
        $ssoRow = $ssoResult->fetch_array();
        if (!$ucRow || !$ssoRow) return null;
        $row = [
            'uid' => $id,
            'username' => $ucRow['username'],
            'email' => $ucRow['email'],
            'stu_num' => $ssoRow['stu_num'],
            'name' => $ssoRow['name'],
        ];
        if (!$ssoRow) return null;
        return $row;
    }
    public static function getUserByOpenid($openid) {
        $info = self::$ssodb->query("SELECT `id`, `stu_num`, `name` FROM `user` WHERE `openid` = '{$openid}' LIMIT 1");
        $info = $info->fetch_array();
        if (!$info) {
            return [
                'uid' => -1,
                'username' => '',
                'email' => '',
                'stu_num' => '',
                'name' => ''
            ];
        }
        $user = self::$ucdb->query("SELECT `username`, `email` FROM `members` WHERE `uid` = {$info['id']}");
        $user = $user->fetch_array();
        return [
            'uid' => $info['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'stu_num' => $info['stu_num'],
            'name' => $info['name']
        ];
    }
    public static function getUserRepeats() {
        $stu_num = self::$ssodb->query('SELECT `stu_num` FROM `user` WHERE `id` = ' . self::$uid);
        $stu_num = $stu_num->fetch_array()['stu_num'];
        if (in_array($stu_num, array('JUST4TEST', 'FRESHMAN', 'MALLUSER'))) {
            return false;
        }
        $result = self::$ssodb->query("SELECT `id` FROM `user` WHERE `stu_num` = '{$stu_num}'");
        $t = [];
        while ($row = $result->fetch_array()) {
            $t []= $row['id'];
        }
        return $t;
    }
    public static function getUserByDed($stu_num){
        if (!$stu_num) return NULL;
        $auth = self::$ssodb->query("SELECT * FROM `user` WHERE `stu_num` = '{$stu_num}'");
        $auth = $auth->fetch_assoc();
        return $auth;
    }
}

SSO::init();
