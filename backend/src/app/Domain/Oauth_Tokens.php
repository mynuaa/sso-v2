<?php

namespace App\Domain;

use App\Model\Oauth_Tokens as MOauth_Tokens;

class Oauth_Tokens {
    public function __construct() {
        $this->moauth_tokens = new MOauth_Tokens();
    }
    public function createOrUpdate($user_id, $appid) {
        return $this->moauth_tokens->createOrUpdate($user_id, $appid);
    }
    public function getAuthorizers($appid) {
        return $this->moauth_tokens->getAuthorizers($appid);
    }
    public function byToken($token) {
        return $this->moauth_tokens->byToken($token);
    }
}
