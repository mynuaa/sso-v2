<?php

namespace App\Domain;

use App\Model\User as MUser;

/**
 * 教务处认证相关代码
 * @author Rex
 */

class Nuaa_Verify {
    public function __construct() {
        $this->hosts = (require __DIR__ . '/../../config.php')['hosts'];
        $this->muser = new MUser();
    }
    private function dedverify($stuid, $password) {
        $cookie = @tempnam('/tmp', 'MYAUTH_');
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_NOBODY => true,
            CURLOPT_URL => 'http://' . $this->hosts['ded'] . '/NetEAn/User/login.asp',
            CURLOPT_COOKIEJAR => $cookie,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_exec($curl);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_URL => 'http://' . $this->hosts['ded'] . '/NetEAn/User/check.asp',
            CURLOPT_POSTFIELDS => 'user=' . $stuid . '&pwd=' . $password,
            CURLOPT_REFERER => 'http://ded.nuaa.edu.cn/netean/user/login.asp',
            CURLOPT_HTTPHEADER => [
                'Origin: http://ded.nuaa.edu.cn',
                'Content-type: application/x-www-form-urlencoded'
            ],
            CURLOPT_COOKIEFILE => $cookie
        ]);
        $response = curl_exec($curl);
        $success = strstr($response, 'switch (0){') != false;
        $realname = false;
        if ($success) {
            if (!$realname = $this->muser->byStuNum($stuid)['name']) {
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'http://' . $this->hosts['ded'] . '/netean/newpage/xsyh/title.asp',
                    CURLOPT_COOKIEFILE => $cookie,
                    CURLOPT_RETURNTRANSFER => true,
                ]);
                $result = iconv('GB2312', 'UTF-8', curl_exec($curl));
                preg_match('/^.+\.(.+?)\).+$/s', $result, $arr);
                $realname = $arr[1];
            }
        }
        curl_close($curl);
        unlink($cookie);
        return $realname;
    }
    private function gsmverify($gsmid, $password) {
        $gsmid = $gsmid;
        $password = $password;
        $prepare_curl = curl_init();
        curl_setopt_array($prepare_curl, [
            CURLOPT_URL => 'http://' . $this->hosts['gsm'] . '/pyxx/login.aspx',
            CURLOPT_RETURNTRANSFER => true,
        ]);
        preg_match('/name="__VIEWSTATE" value=".+?"/', curl_exec($prepare_curl), $viewstate);
        $viewstate = substr($viewstate[0], 26);
        $viewstate = preg_replace('/"/', '', $viewstate);
        $viewstate = urlencode($viewstate);
        curl_close($prepare_curl);
        $x = intval(rand(0, 60));
        $y = intval(rand(0, 60));
        $post = "__VIEWSTATE={$viewstate}&_ctl0%3Atxtusername={$gsmid}&_ctl0%3AImageButton1.x={$x}&_ctl0%3AImageButton1.y={$y}&_ctl0%3Atxtpassword={$password}";
        $url = "http://gsmis.nuaa.edu.cn/pyxx/login.aspx";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER, array(
                "Content-type: application/x-www-form-urlencoded",
                "Origin: http://gsmis.nuaa.edu.cn"
            ),
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_RETURNTRANSFER => 1,
        ]);
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $http_code == 302 || preg_match('/您已超过学习期限/', $response);
    }
    private function hrverify($tid, $password) {
        $url = 'http://' . $this->hosts['gsm'] . '/api/verifyUser.do?token=dd64533c961eb9d527a608f9cd13fb06&username={$tid}&password={$password}';
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        return $response['status'] == 0;
    }
    private function cceverify($username, $password) {
        // TODO: cce.nuaa.edu.cn verify
    }
    public function verify($username, $password) {
        $username = urlencode($username);
        $password = urlencode($password);
        if (preg_match("/(^7020|^LZ)/i", $username)) return $this->hrverify($username, $password);
        if (preg_match("/(^SX|^SY|^SZ|^BX)/i", $username)) return $this->gsmverify($username, $password);
        if (preg_match("/(^CZ)/i", $username)) return $this->cceverify($username, $password);
        return $this->dedverify($username, $password);
    }
}
