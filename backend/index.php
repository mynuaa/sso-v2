<?php

require_once 'Libraries/Macaw.php';
require_once 'Libraries/Medoo.php';
require_once 'Libraries/Request.php';
require_once 'Libraries/Result.php';

session_start();

// 数据库
global $db;
$config = require 'config.php';
$db = new Medoo($config['db']);

// 接收 Post 数据
Request::initialize();

// 引入控制器
if ($dir = opendir('Controllers')){
    while ($file = readdir($dir)) {
        if (!preg_match('/php$/', $file)) {
            continue;
        }
        require_once "Controllers/{$file}";
    }
    closedir($dir);
}

// 覆盖 nginx 传入的值，防止路由出错
$_SERVER['PHP_SELF'] = '';

// 封装的添加路由的函数
function RouterAdd($method, $route, $controller, $loginRequired = false) {
    $prefix = (require 'config.php')['site']['prefix'];
    $route = $prefix . $route;
    if ($loginRequired && $_SERVER['REQUEST_URI'] === $route && !isset($_SESSION['user'])) {
        Result::error('请先登录');
    }
    switch ($method) {
    case 'get':
        Macaw::get($route, $controller);
        break;
    case 'post':
        Macaw::post($route, $controller);
        break;
    }
}

// 添加路由
require_once 'routers.php';
Macaw::dispatch();
