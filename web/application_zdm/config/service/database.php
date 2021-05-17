<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * mysql配置
 */
$config['mysql'] = [
    'sos' => [
        // 'username' => 'ding_sos_user',
        'username' => 'root',
        // 'password' => 'A0gqlejlUDqlOmRd',
        'password' => 'password',
        // 'database' => 'dingSosDB',
        'database' => 'dingSosDB',
        'db_debug' => true,
        'port' => 3306,
        'hostname' => [
            'write' => [
                // 'ding_sos_mysql_m01'
                '127.0.01'
            ],
            'read' => [
                // 'ding_sos_mysql_m01'
                '127.0.01'
            ],
        ],
    ],
];
