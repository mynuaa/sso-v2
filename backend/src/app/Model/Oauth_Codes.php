<?php

namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

use App\Model\User_Tokens as MUser_Tokens;

class OAuth_Codes extends NotORM {
    public function __construct() {
        $this->muser_tokens = new MUser_Tokens();
    }
    public function createOrUpdate($user_id, $appid) {
        $code = $this->muser_tokens->createSid();
        $this->getORM()
            ->insert_update([], [
                'appid' => $appid,
                'user_id' => $user_id,
                'code' => $code,
            ], [
                'code' => $code,
            ]);
        return $code;
    }
    public function byCode($code) {
        return $this->getORM()
            ->where('code', $code)
            ->fetchOne();
    }
}
