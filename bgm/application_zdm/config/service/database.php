<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * mysql配置
 */
$config['mysql'] = [
    'enterprise_portal' => [
        // 'username' => 'enterprise_portal',
        'username' => 'root',
        // 'password' => 'xsHvf9QChdS85V-cuIGwZ',
        'password' => 'password',
        'database' => 'enterprisePortalDB',
        'db_debug' => true,
        'port' => 3306,
        'hostname' => [
            'write' => [
                // 'enterprise_portal_mysql_m01'
                '127.0.01'
            ],
            'read' => [
                // 'enterprise_portal_mysql_m01'
                '127.0.01'
            ],
        ],
    ],
];
