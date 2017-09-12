<?php

namespace App\Domain;

use App\Model\Oauth_Codes as MOauth_Codes;

class Oauth_Codes {
    public function __construct() {
        $this->moauth_codes = new MOauth_Codes();
    }
    public function createOrUpdate($user_id, $appid) {
        return $this->moauth_codes->createOrUpdate($user_id, $appid);
    }
    public function byCode($code) {
        return $this->moauth_codes->byCode($code);
    }
}
