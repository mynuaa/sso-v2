<?php

/**
 * 分库分表的自定义数据库路由配置
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author: dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

$mynuaa_config_all = require __DIR__ . '/../src/config.php';

return [
    'servers' => [
        'db_master' => [
            'host'      => $mynuaa_config_all['db_host'],
            'user'      => $mynuaa_config_all['db_user'],
            'password'  => $mynuaa_config_all['db_pass'],
            'port'      => $mynuaa_config_all['db_port'],
            'name'      => $mynuaa_config_all['db_name'],
            'charset'   => $mynuaa_config_all['db_charset'],
        ],
    ],
    'tables' => [
        '__default__' => [
            'prefix' => '',
            'key' => 'id',
            'map' => [
                ['db' => 'db_master'],
            ],
        ],
    ],
];
