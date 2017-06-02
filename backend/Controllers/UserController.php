<?php

class UserController {
    public function login() {
        global $db;
        if (isset($_SESSION['user'])) {
            Result::error('您已登录');
        }
        Result::error('用户名或密码错误');
    }
    public function logout() {
        session_destroy();
        Result::success();
    }
    public function current() {
        if (isset($_SESSION['user'])) {
            Result::success($_SESSION['user']);
        } else {
            Result::success(['id' => 0]);
        }
    }
}
