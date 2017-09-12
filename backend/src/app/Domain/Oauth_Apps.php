<?php

namespace App\Domain;

use App\Model\Oauth_Apps as MOauth_Apps;

class Oauth_Apps {
    public function __construct() {
        $this->moauth_apps = new MOauth_Apps();
    }
    public function getInfo($appid) {
        return $this->moauth_apps->getInfo($appid);
    }
    public function verify($appid, $appsecret) {
        return $this->moauth_apps->verify($appid, $appsecret);
    }
    public function checkPermission($types, $appid) {
        $app = $this->moauth_apps->getInfo($appid);
        $permissions = [
            'id',
            'username',
            '-',
            '-',
            '-',
            '-',
            '-',
            '-',
            '-',
            '-',
            'stu_num',
            'email',
            'openid'
        ];
        $allow = intval($app['permissions']);
        for ($i = 0; $i < count($permissions); $i++) {
            if (in_array($permissions[$i], $types) && ($allow & (1 << $i)) === 0) {
                return false;
            }
        }
        return true;
    }
}
