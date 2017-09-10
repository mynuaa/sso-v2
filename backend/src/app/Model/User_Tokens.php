<?php

namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class User_Tokens extends NotORM {
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
    public function bySid($sid) {
        return $this->getORM()
            ->select('user.id, user.username, user.stu_num, user.name, user.openid')
            ->where('sid', $sid)
            ->and('expires > ?', time())
            ->fetchOne();
    }
    public function destroySid($sid) {
        return $this->getORM()
            ->where('sid', $sid)
            ->update(['expires' => 1]);
    }
}
