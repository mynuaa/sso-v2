<?php

/**
 * 所有 API 的主入口
 * Author: Rex
 */

// 覆盖 nginx 传入的值，防止路由出错
$_SERVER['PHP_SELF'] = '';

function createSid() {
    if (function_exists('random_bytes')) {
        return bin2hex(random_bytes(16));
    }
    if (function_exists('mcrypt_create_iv')) {
        return bin2hex(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));
    }
    if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }
}

foreach (['Libraries', 'Services', 'Controllers'] as $dir) {
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        $filePath = $dir . '/' . $file;
        if (is_file($filePath)) {
            require_once $filePath;
        }
    }
    closedir($dh);
}

require_once 'routers.php';
