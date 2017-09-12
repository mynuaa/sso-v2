<?php

namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class OAuth_Apps extends NotORM {
    public function getInfo($appid) {
        return $this->getORM()
            ->select('appid, appname, permissions, offical, created')
            ->where('appid', $appid)
            ->fetchOne();
    }
    public function verify($appid, $appsecret) {
        return $this->getORM()
            ->where('appid = ? AND appsecret = ?', $appid, $appsecret)
            ->count() == 1;
    }
}
