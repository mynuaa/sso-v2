<?php
/**
 * 统一初始化
 */

// 定义项目路径
defined('API_ROOT') || define('API_ROOT', dirname(__FILE__) . '/..');

// 引入composer
require_once API_ROOT . '/vendor/autoload.php';

// 时区设置
date_default_timezone_set('Asia/Shanghai');

// 引入DI服务
include API_ROOT . '/config/di.php';

// 调试模式
if (\PhalApi\DI()->debug) {
    // 启动追踪器
    \PhalApi\DI()->tracer->mark();
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

// 翻译语言包设定
\PhalApi\SL('zh_cn');

// Cookie 设置
\PhalApi\DI()->cookie = new PhalApi\Cookie(['httponly' => true]);

// 使用 JSON 格式
require_once dirname(__FILE__) . '/../src/classes/PhalApi_JSON_Request.php';
\PhalApi\DI()->request = new PhalApi\JSON_Request();
