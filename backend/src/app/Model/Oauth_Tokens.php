<?php

namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

use App\Model\User_Tokens as MUser_Tokens;

class OAuth_Tokens extends NotORM {
    public function __construct() {
        $this->muser_tokens = new MUser_Tokens();
    }
    public function createOrUpdate($user_id, $appid) {
        $content = $this->muser_tokens->createSid();
        $expires_in = 7200;
        $expires = intval(strtotime(Date('Y-m-d H:i:s'))) + $expires_in;
        $this->getORM()
            ->insert_update([], [
                'content' => $content,
                'appid' => $appid,
                'user_id' => $user_id,
                'expires' => $expires,
            ], [
                'content' => $content,
                'expires' => $expires,
            ]);
        return [$content, $expires_in];
    }
    public function getAuthorizers($appid) {
        return $this->getORM()
            ->where('appid', $appid)
            ->sum('user_id');
    }
    public function byToken($content) {
        return $this->getORM()
            ->where('content', $content)
            ->fetchOne();
    }
}
