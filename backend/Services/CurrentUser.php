<?php

class CurrentUser {
    public $uid = null;
    public $username = null;
    public $stu_num = null;
    public $openid = null;
    public $name = null;
    public $isLogin = false;
    public function __construct() {
        if (!isset($_COOKIE['sid'])) {
            Response::error('请先登录');
            return;
        }
        $user = (new DataBase())->get('user_tokens', [
            '[>]users' => 'uid'
        ], [
            'users.uid', 'users.username', 'users.stu_num', 'users.name', 'users.openid'
        ], [
            'user_tokens.sid' => $_COOKIE['sid'],
            'user_tokens.expires[>]' => time()
        ]);
        if (!$user) {
            Response::error('错误的 sid');
            return;
        }
        $this->uid = $user['uid'];
        $this->username = $user['username'];
        $this->stu_num = $user['stu_num'];
        $this->openid = $user['openid'];
        $this->name = $user['name'];
        $this->isLogin = true;
    }
}
