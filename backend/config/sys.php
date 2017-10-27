<?php

$mynuaa_config_all = require __DIR__ . '/../src/config.php';

return [
    'debug' => $mynuaa_config_all['is_debug'],
    'mc' => [
        'host' => $mynuaa_config_all['mc_host'],
        'port' => $mynuaa_config_all['mc_port'],
    ],
    'crypt' => [
        'mcrypt_iv' => '',
    ],
    'need_v1_compatible' => $mynuaa_config_all['need_v1_compatible'],
];
