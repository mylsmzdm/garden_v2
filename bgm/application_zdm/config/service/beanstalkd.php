<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$config['beanstalkd'] = [
	/**
    'comment_0' => [
        'host' => 'comment_beanstalkd_m01', 
        'port' => '11301',
        'timeout' => '5',
        'pconnect' => false,
        'logger' => null,
    ],
    'comment_1' => [
        'host' => 'comment_beanstalkd_m02', 
        'port' => '11302',
        'timeout' => '5',
        'pconnect' => false,
        'logger' => null,
    ],*/
	
	
];

//var_dump($config);
/* End of file beanstalkd.php */
/* Location: ./application/config/beanstalkd.php */