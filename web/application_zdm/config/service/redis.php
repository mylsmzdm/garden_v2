<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$config['redis'] = [

    'smzdm_data' => [
        'password' => '',
        'database' => '0',
        'port' => '6379',
        'timeout' => '5',
        'open'    => true,
        'hostname' =>   [
            /*'write' => [
                'redis-server_write01_eth01'
            ],
            'read' => [
                'redis-server_read01_eth01', 'redis-server_read01_eth02',
                'redis-server_read01_eth01',
            ],*/

            'write' => [
                '127.0.0.1'
            ],
            'read' => [
                '127.0.0.1'
            ],
        ]
    ],

];
