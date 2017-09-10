<?php

namespace App\Domain;

use App\Model\User as MUser;
use App\Model\User_Tokens as MUser_Tokens;

use App\Domain\Nuaa_Verify as DNuaa_Verify;

class User {
    public function __construct() {
        $this->muser = new MUser();
        $this->muser_tokens = new MUser_Tokens();
        $this->dnuaa_verify = new DNuaa_Verify();
    }
    public function bySid($sid) {
        return $this->muser_tokens->bySid($sid);
    }
    public function byId($id) {
        return $this->muser->byId($id);
    }
    public function nuaaLogin($username, $password) {
        // 分离子账号
        $split = preg_split('/(:|：)/', $username);
        $username = $split[0];
        $index = @intval($split[1]);
        // 子账号编号越界
        if ($index < 0 || $index > 1) {
            return 1;
        }
        // 验证未通过
        if (!($realname = $this->dnuaa_verify->verify($username, $password))) {
            return 2;
        }
        // 获取全部的用户列表
        $users = $this->muser->listByStuNum($username);
        // sso 中没有该子账号（没有绑定 discuz 账号）
        if (!isset($users[$index])) {
            $user = [
                'stu_num' => $username,
                'password' => $password,
                'name' => $realname
            ];
            session_start();
            $_SESSION['confirm'] = $user;
            $_SESSION['confirm_need'] = 'discuz';
            return 3;
        }
        // 更新真实姓名
        $this->muser->setRealname($username, $realname);
        // 正常登录
        $user = $users[$index];
        $user['realname'] = $realname;
        $sid = $this->muser_tokens->createSid();
        setcookie('sid', $sid, time() + 86400000, '/', '', false, true);
        $this->muser_tokens->insert([
            'user_id' => $user['id'],
            'sid' => $sid,
            'expires' => time() + 86400000,
        ]);
        return $user;
    }
    public function discuzLogin($username, $password) {
        // 引入 uc_client
        require_once __DIR__ . '/../Common/UC_Client/client.php';
        // discuz 认证
        list($id, $dzUsername) = uc_user_login($username, $password);
        // 认证未通过
        if ($id <= 0) {
            return 2;
        }
        // 获取用户信息
        $user = $this->muser->byId($id);
        // sso 中不存在此用户（学号未验证）
        if (!$user) {
            $user = [
                'id' => $id,
                'username' => $dzUsername,
            ];
            session_start();
            $_SESSION['confirm'] = $user;
            $_SESSION['confirm_need'] = 'nuaa';
            return 3;
        }
        $sid = $this->muser_tokens->createSid();
        setcookie('sid', $sid, time() + 86400000, '/', '', false, true);
        $this->muser_tokens->insert([
            'user_id' => $id,
            'sid' => $sid,
            'expires' => time() + 86400000,
        ]);
        return $user;
    }
    public function destroySid($sid = '') {
        if ($sid == '') {
            return;
        }
        $this->muser_tokens->destroySid($sid);
    }
    public function completeDiscuz($username, $password, $email) {
        session_start();
        if ($_SESSION['confirm_need'] != 'discuz') {
            return 1;
        }
        $user = $_SESSION['confirm'];
        // 加载 uc_client
        require_once __DIR__ . '/../Common/UC_Client/client.php';
        if ($password != '') {
            // discuz 登录，绑定已有账号
            list($id, $username) = uc_user_login($username, $password);
            // 登录失败
            if ($id <= 0) {
                return 2;
            }
            // 登录成功，获取现有用户信息
            $ssoUser = $this->muser->byId($id);
            if ($ssoUser) {
                // 用户已存在
                if ($ssoUser['stu_num'] != '') {
                    // 已绑定学号
                    return 3;
                }
                // 更新现有用户学号信息
                $this->muser->setStuNum($id, $user['stu_num']);
            } else {
                // 插入新用户
                $this->muser->addUser([
                    'id' => $id,
                    'username' => $username,
                    'stu_num' => $user['stu_num'],
                    'openid' => '',
                    'name' => $user['name'],
                ]);
            }
            $user['id'] = $id;
            $user['username'] = $username;
            $user['openid'] = $ssoUser['openid'] ? $ssoUser['openid'] : '';
            unset($user['password']);
            session_destroy();
            return $user;
        } else if ($email != '') {
            // 注册新账号
            $id = uc_user_register($username, $user['password'], $email);
            // 注册失败
            if ($id < 0) {
                return $id;
            }
            // 注册成功
            $this->muser->addUser([
                'id' => $id,
                'username' => $username,
                'stu_num' => $user['stu_num'],
                'openid' => '',
                'name' => $user['name'],
            ]);
            $user['id'] = $id;
            $user['username'] = $username;
            $user['openid'] = '';
            unset($user['password']);
            session_destroy();
            return $user;
        }
    }
    public function completeNuaa($stu_num, $password) {
        session_start();
        if ($_SESSION['confirm_need'] != 'nuaa') {
            return 1;
        }
        $user = $_SESSION['confirm'];
        // 认证未通过
        if (!($realname = $this->dnuaa_verify->verify($stu_num, $password))) {
            return 2;
        }
        // 绑定了至少两个账号了
        if ($this->muser->bindUserCount($stu_num) >= 2) {
            return 3;
        }
        // 绑定账号
        $user['stu_num'] = $stu_num;
        $user['name'] = $realname;
        $this->muser->addUser([
            'id' => $user['id'],
            'username' => $user['username'],
            'stu_num' => $user['stu_num'],
            'name' => $user['name']
        ]);
        session_destroy();
        return $user;
    }
}
