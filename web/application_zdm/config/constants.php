<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// SSO权限错误
define("ERROR_PERM", 20000);
// 服务端异常
define("ERROR_EXCEPTION", 20001);
// 参数错误
define("ERROR_PARAMS", 20002);
// 第三方接口服务端异常
define("ERROR_THIRD", 20003);
// 来源错误不是来源于smzdm.com
define("ERROR_REFFER", 20004);
// 部门或工作地权限错误
define("ERROR_DEPARTMENT_WORKPLACE_PERM", 20005);

//数据库FALSE
define("DATABASE_FALSE", 30001);
//SQL执行影响零行
define("DATABASE_ZERO", 30002);

// 配置
class Config {
    static $url = [
        'root' => 'smzdm.com',
        #bgm权限
        'sso_bgm_url'   =>  'http://sso1-bgm.smzdm.com',
        'bgm_api_url'   =>  'http://bgm.smzdm.com:809',
        'sso_api_url'   =>  'http://sso-api.smzdm.com:8080',
        'auth_api_url'   =>  'http://authapi.smzdm.com:8080',
        'commonservice_api_url' => 'http://commonservice.smzdm.com:809',
        'sgardenos'           =>  'https://garden.zdm.net/',
    ];

    #sos同步bgm操作类型map
    static $sso_sos_operation_map = [
        1 => 'add',
        2 => 'update',
        3 => 'delete'
    ];

    #garden-sso配置
    static $garden_sso_api = [
        'is_open' => 1, //0关闭 1打开
        'number_attempts' => 3, //尝试重写次数，达到此次数后报警
        'number_fail' => 5, //失败数量界限，达到此界限后报警
        'interval_time' => 10, //间隔时间(分钟)
    ];

    // CAS单点登录配置
    static $cas = [
        'open' => true, // 是否开启CAS登录，否则默认为BGM-SSO登录。true开启；false关闭。
    ];

    static $constant = [
        'bgm_app_code' => 'zindex',
    ];
}




