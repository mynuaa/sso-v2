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
            return $this->duser->bySid($sid);
        } else {
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
