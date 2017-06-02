<?php

return [
    'db' => [
        'database_type' => 'mysql',
        'database_name' => 'myauth',
        'server' => '127.0.0.1',
        'username' => 'rexskz',
        'password' => '2147483647',
        'charset' => 'utf8',
        'port' => 3306,
        'prefix' => '',
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ]
    ],
    'site' => [
        'prefix' => '/sso-v2'
    ]
];
