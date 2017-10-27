<?php

namespace App\Api;

use PhalApi\Api;
use PhalApi\Exception;
use PhalApi\Exception\BadRequestException;

use App\Domain\User as DUser;

/**
 * 用户接口类
 * @author Rex Zeng <rex@rexskz.info> 2017-09-10
 */
class User extends Api {
    public function __construct() {
        $this->duser = new DUser();
    }
    public function getRules() {
        return [
            'login' => [
                'type' => [
                    'name' => 'type',
                    'type' => 'enum',
                    'range' => ['nuaa', 'discuz', 'wechat'],
                    'require' => true,
                ],
                'username' => [
                    'name' => 'username',
                    'require' => true,
                ],
                'password' => [
                    'name' => 'password',
                    'require' => true,
                ],
            ],
            'complete' => [
                'type' => [
                    'name' => 'type',
                    'type' => 'enum',
                    'range' => ['nuaa', 'discuz'],
                    'require' => true,
                ],
                'username' => [
                    'name' => 'username',
                    'require' => true,
                ],
                'password' => [
                    'name' => 'password',
                ],
                'email' => [
                    'name' => 'email',
                ],
            ],
        ];
    }
    /**
     * 获取当前用户信息
     * @desc 获取当前登录用户信息，若未登录则抛出异常
     * @return int id 用户标记
     * @return string username 用户名
     * @return string stu_num 学号/工号
     * @return string name 真实姓名
     * @return string openid 微信标记
     * @exception 401 用户未登录
     */
    public function current() {
        if ($sid = DI()->cookie->get('sid')) {
            // 通过 sid 寻找用户
            return $this->duser->bySid($sid);
        } else if ($uid = DI()->cookie->get('myauth_uid')) {
            // 需要兼容 v1 的 cookie
            if (DI()->config->get('sys.need_v1_compatible')) {
                // 加载旧证书
                $prkey = openssl_pkey_get_private(file_get_contents($_ENV['MYNUAA_ROOT_PATH'] . '/sso/cert/private_key.pem'));
                // 旧的解密函数
                $ssoDecrypt = function ($str) use ($prkey) {
                    $encrypted = base64_decode($str);
                    if (!openssl_private_decrypt($encrypted, $str, $prkey)) return false;
                    return $str;
                };
                // 旧的解密流程
                if (isset($_COOKIE['myauth_uid'])) {
                    if ($uid = $ssoDecrypt($_COOKIE['myauth_uid'])) {
                        $uid = intval(json_decode($uid, true)['uid']);
                        $sid = $this->duser->v1Login($uid);
                        return $this->duser->bySid($sid);
                    }
                }
            }
        } else {
            // 判定用户未登录
            throw new BadRequestException(T('user_not_login'), 1);
        }
    }
    /**
     * 用户登录
     * @desc 使用教务处/论坛账号/微信登录，并返回用户信息
     * @return int id 用户标记，-1 为需要补全身份信息
     * @return string username 用户名
     * @return string stu_num 学号/工号
     * @return string name 真实姓名
     * @return string openid 微信标记
     * @exception 401 用户名或密码错误
     */
    public function login() {
        switch ($this->type) {
            case 'nuaa':
                switch ($t = $this->duser->nuaaLogin($this->username, $this->password)) {
                    case 1: throw new BadRequestException(T('subaccount_index_overflow'), 1);
                    case 2: throw new BadRequestException(T('wrong_username_password'), 1);
                    case 3: return ['id' => -1];
                }
                return $t;
            case 'discuz':
                switch ($t = $this->duser->discuzLogin($this->username, $this->password)) {
                    case 1: throw new BadRequestException(T('subaccount_index_overflow'), 1);
                    case 2: throw new BadRequestException(T('wrong_username_password'), 1);
                    case 3: return ['id' => -1];
                }
                return $t;
            case 'wechat':
                throw new Exception(T('not_implemented'), 1);
        }
    }
    /**
     * 用户注销
     * @desc 销毁 cookie 中的 sid，并让数据库中的凭据过期
     */
    public function logout() {
        // 需要同时注销 v1，否则会出现只要 v1 登录则 v2 无法注销的情况
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
        setcookie('myauth_uid', '', time() - 86400000, '/', '', false, true);
        // v2 的注销
        $this->duser->destroySid(DI()->cookie->get('sid'));
        setcookie('sid', '', time() - 86400000, '/', '', false, true);
        return null;
    }
    /**
     * 完善个人信息
     * @desc 完善 nuaa/discuz 的信息
     */
    public function complete() {
        switch ($this->type) {
            case 'nuaa':
                switch ($t = $this->duser->completeNuaa($this->username, $this->password)) {
                    case 1: throw new BadRequestException(T('no_need_complete'), 1);
                    case 2: throw new BadRequestException(T('wrong_username_password'), 1);
                    case 3: throw new BadRequestException(T('too_much_bind_account'), 1);
                }
                return $t;
            case 'discuz':
                $re = "/^(\\w)+(\\.\\w+)*@(\\w)+((\\.\\w+)+)$/i";
                if ($this->email && !preg_match($re, $this->email)) {
                    throw new BadRequestException(T('email_format_error'), 0);
                }
                switch ($t = $this->duser->completeDiscuz($this->username, $this->password, $this->email)) {
                    case -1: case -2: case -3: case -4: case -5: case -6:
                        $msg = [
                            -1 => '用户名不符合要求',
                            -2 => '用户名有敏感词汇',
                            -3 => '该用户名已经存在',
                            -4 => 'Email格式有错误',
                            -5 => 'Email不允许注册',
                            -6 => 'Email已经被注册'
                        ];
                        throw new BadRequestException($msg[$t], 3);
                    case 1: throw new BadRequestException(T('no_need_complete'), 1);
                    case 2: throw new BadRequestException(T('wrong_username_password'), 1);
                    case 3: throw new BadRequestException(T('account_exists'), 1);
                }
                return $t;
        }
    }
}
