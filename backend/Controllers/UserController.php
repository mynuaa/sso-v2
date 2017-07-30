<?php

class UserController {
    private function nuaaLogin($verify, $db, $username, $password) {
        $split = preg_split('/(:|：)/', $username);
        $username = $split[0];
        $order = max([1, count($split) == 2 ? intval($split[1]) : 1]) - 1;
        if ($realname = $verify->verify($username, $password)) {
            $user = $db->select('users', ['uid', 'username', 'stu_num', 'name', 'openid'], ['stu_num' => $username]);
            if (isset($user[$order])) {
                $user = $user[$order];
                $sid = createSid();
                setcookie('sid', $sid, time() + 86400000, '/', '', false, true);
                $db->insert('user_tokens', [
                    'uid' => $user['uid'],
                    'sid' => $sid,
                    'expires' => time() + 86400000
                ]);
                Response::success($user);
            } else if ($order == 0) {
                $user = [
                    'stu_num' => $username,
                    'password' => $password,
                    'name' => $realname
                ];
                session_start();
                $_SESSION['confirm'] = $user;
                $_SESSION['confirm_need'] = 'discuz';
                Response::jump('/complete/discuz');
            } else {
                Response::error('没有该子账号');
            }
        } else {
            Response::error('用户名或密码错误');
        }
    }
    private function discuzLogin($db, $username, $password) {
        require_once __DIR__ . '/../UC_Client/client.php';
        list($uid, $dzUsername) = uc_user_login($username, $password);
        if ($uid <= 0) {
            Response::error('用户名或密码错误');
        }
        $user = $db->get('users', ['uid', 'username', 'stu_num', 'name', 'openid'], ['uid' => $uid]);
        if (!$user) {
            $user = [
                'uid' => $uid,
                'username' => $dzUsername
            ];
            session_start();
            $_SESSION['confirm'] = $user;
            $_SESSION['confirm_need'] = 'nuaa';
            Response::jump('/complete/nuaa');
        }
        Response::success($user);
    }
    public function login(
        DataBase $db,
        NuaaVerify $verify
    ) {
        $type = Request::get('type');
        $username = Request::get('username');
        $password = Request::get('password');
        switch ($type) {
            case 'nuaa':
                self::nuaaLogin($verify, $db, $username, $password);
                break;
            case 'discuz':
                self::discuzLogin($db, $username, $password);
                break;
            case 'wechat':
                break;
            default:
                Response::error('未定义的 type');
                break;
        }
    }
    public function logout(
        CurrentUser $user,
        DataBase $db
    ) {
        $db->update('users', ['sid' => null], ['sid' => $_COOKIE['sid']]);
        setcookie('sid', '', time() - 86400000, '/', '', false, true);
    }
    public function current(CurrentUser $user) {
        Response::success($user);
    }
    public function completeDiscuz(DataBase $db) {
        session_start();
        if ($_SESSION['confirm_need'] != 'discuz') {
            Response::error('用户信息已完整');
        }
        print_r($_SESSION);
        $user = $_SESSION['user'];
        $username = Request::get('username');
        $password = $user['password'];
        $stu_num = $user['stu_num'];
        // 若登录成功，则绑定已有账号
        require_once __DIR__ . '/../UC_Client/client.php';
        list($uid, $username) = uc_user_login($username, $password);
        if ($uid > 0) {
            $ssoUser = $db->get('users', 'stu_num', ['uid' => $uid]);
            if ($ssoUser) {
                Response::error('该账号已绑定学号：' . $ssoUser['stu_num']);
            } else {
                $db->update('users', ['stu_num' => $stu_num], ['uid' =>  $uid]);
                session_destroy();
                Response::jump('/user');
            }
        }
        // 否则，注册新账号
        $email = Request::get('email');
        $uid = uc_user_register($username, $password, $email);
        if ($uid < 0) {
            $msg = [
                -1 => '用户名不符合要求',
                -2 => '用户名有敏感词汇',
                -3 => '该用户名已经存在',
                -4 => 'Email格式有错误',
                -5 => 'Email不允许注册',
                -6 => 'Email已经被注册'
            ];
            Response::error($msg[$uid]);
        } else {
            session_destroy();
            Response::jump('/user');
        }
    }
    public function completeNuaa(
        NuaaVerify $verify,
        DataBase $db
    ) {
        session_start();
        if ($_SESSION['confirm_need'] != 'nuaa') {
            Response::error('学号已认证');
        }
        $user = $_SESSION['user'];
        $stu_num = Request::get('stu_num');
        $password = Request::get('password');
        if ($realname = $verify->verify($stu_num, $password)) {
            $user['stu_num'] = $stu_num;
            $user['name'] = $realname;
            $db->insert('users', [
                'uid' => $user['uid'],
                'username' => $user['username'],
                'stu_num' => $user['stu_num'],
                'name' => $user['name']
            ]);
            session_destroy();
            Response::jump('/user');
        } else {
            Response::success('认证失败');
        }
    }
}
