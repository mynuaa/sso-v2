<?php

namespace App\Api;

use PhalApi\Api;
use PhalApi\Exception;
use PhalApi\Exception\BadRequestException;

use App\Domain\User as DUser;
use App\Domain\OAuth_Apps as DOauth_Apps;
use App\Domain\OAuth_Tokens as DOauth_Tokens;
use App\Domain\OAuth_Codes as DOauth_Codes;

/**
 * Oauth 类
 * @author Rex Zeng <rex@rexskz.info> 2017-09-11
 */
class Oauth extends Api {
    public function __construct() {
        $this->duser = new DUser();
        $this->doauth_apps = new DOauth_Apps();
        $this->doauth_tokens = new DOauth_Tokens();
        $this->doauth_codes = new DOauth_Codes();
    }
    public function getRules() {
        return [
            'appinfo' => [
                'appid' => [
                    'name' => 'appid',
                    'require' => true,
                ],
            ],
            'authorize' => [
                'appid' => [
                    'name' => 'appid',
                    'require' => true,
                ],
            ],
            'getAccessToken' => [
                'appid' => [
                    'name' => 'appid',
                    'require' => true,
                ],
                'appsecret' => [
                    'name' => 'appsecret',
                    'require' => true,
                ],
                'code' => [
                    'name' => 'code',
                    'require' => true,
                ]
            ],
            'refresh' => [
                'appid' => [
                    'name' => 'appid',
                    'require' => true,
                ],
                'appsecret' => [
                    'name' => 'appsecret',
                    'require' => true,
                ],
                'old_token' => [
                    'name' => 'old_token',
                    'require' => true,
                ]
            ],
            'getUserInfo' => [
                'access_token' => [
                    'name' => 'access_token',
                    'require' => true,
                ],
                'types' => [
                    'name' => 'types',
                    'require' => true,
                ]
            ],
        ];
    }
    /**
     * 获取指定的应用信息
     * @desc 通过应用 ID 获取 app 的详细信息
     * @return string appid 应用
     * @exception 404 应用不存在
     */
    public function appinfo() {
        if ($t = $this->doauth_apps->getInfo($this->appid)) {
            $t['authorizers'] = $this->doauth_tokens->getAuthorizers($this->appid);
            return $t;
        } else {
            throw new BadRequestException(T('app_not_exists'), 4);
        }
    }
    /**
     * 授权应用
     * @desc 已登录的用户授权应用，返回 code
     * @return string code 用于换取访问凭证的标记
     * @exception 401 用户未登录
     * @exception 404 应用不存在
     */
    public function authorize() {
        $user = null;
        if ($sid = DI()->cookie->get('sid')) {
            $user = $this->duser->bySid($sid);
        } else {
            throw new BadRequestException(T('user_not_login'), 1);
        }
        if (!$this->doauth_apps->getInfo($this->appid)) {
            throw new BadRequestException(T('app_not_exists'), 4);
        }
        return $this->doauth_codes->createOrUpdate($user['id'], $this->appid);;
    }
    /**
     * 换取访问凭证
     * @desc 通过 code 换取访问凭证，第三方应用服务器专用
     * @return string access_token 访问凭证
     * @return int expires_in 过期时间
     * @exception 401 APPID/APPSECRET/CODE错误
     */
    public function getAccessToken() {
        // appid, appsecret
        if (!$this->doauth_apps->verify($this->appid, $this->appsecret)) {
            throw new BadRequestException(T('appid_appsecret_error'), 1);
        }
        if (!$t = $this->doauth_codes->byCode($this->code)) {
            throw new BadRequestException(T('code_error'), 1);
        }
        list($access_token, $expires_in) = $this->doauth_tokens->createOrUpdate($t['user_id'], $this->appid);
        return [
            'access_token' => $access_token,
            'expires_in' => $expires_in,
        ];
    }
    /**
     * 刷新访问凭证
     * @desc 访问凭证过期时刷新，第三方应用服务器专用
     * @return string access_token 新的访问凭证
     * @exception 401 APPID/APPSECRET/访问凭证错误
     */
    public function refresh() {
        // appid, appsecret
        if (!$this->doauth_apps->verify($this->appid, $this->appsecret)) {
            throw new BadRequestException(T('appid_appsecret_error'), 1);
        }
        if (!$t = $this->doauth_tokens->byToken($this->old_token)) {
            throw new BadRequestException(T('old_token_error'), 1);
        }
        list($access_token, $expires_in) = $this->doauth_tokens->createOrUpdate($t['user_id'], $this->appid);
        return [
            'access_token' => $access_token,
            'expires_in' => $expires_in,
        ];
    }
    /**
     * 获取用户信息
     * @desc 类型用英文逗号分隔，第三方应用服务器专用
     * @return object user_info 用户信息，取决于传入的参数
     * @exception 401 访问凭证错误/请求的数据超出权限范围
     */
    public function getUserInfo() {
        // 不存在该 token
        if (!$t = $this->doauth_tokens->byToken($this->access_token)) {
            throw new BadRequestException(T('access_token_error'), 1);
        }
        // token 已过期
        $now = intval(strtotime(Date('Y-m-d H:i:s')));
        if ($t['expires'] < $now) {
            throw new BadRequestException(T('access_token_expired'), 1);
        }
        // 权限检测
        $types = \explode(',', $this->types);
        if (!$this->doauth_apps->checkPermission($types, $t['appid'])) {
            throw new BadRequestException(T('data_access_out_of_range'), 1);
        }
        $user_id = $t['user_id'];
        $user = $this->duser->byId($user_id);
        $info = [];
        if (in_array('id', $types)) $info['id'] = $user_id;
        if (in_array('username', $types)) $info['username'] = $user['username'];
        if (in_array('stu_num', $types)) $info['stu_num'] = $user['stu_num'];
        if (in_array('openid', $types)) $info['openid'] = $user['openid'];
        if (in_array('name', $types)) $info['name'] = $user['name'];
        return $info;
    }
}
