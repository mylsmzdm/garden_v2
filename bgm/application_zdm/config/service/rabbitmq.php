<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$config['rabbitmq'] = [
    #对接上海asso系统
    'asso' => [
        #'host' => '10.9.96.149', #158
        'host' => 'smzdm_cas_rabbitmq_queue_01', #230（IP是10.9.173.35）
        'port' => '5672', //default 5672
        'vhost' => 'sso_user_ldap',
        'login' => 'ssouser',
        'password' => 'N3Itor664V3lr',
        'read_timeout' => 5,
        'write_timeout' => 5,
        'connect_timeout' => 5,
        'prefetch_count' => 1, //公平策略
    ],

];