<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * url 配置
 */
Class LinkConfig {

    #source 所有来源
    static $source = array(
        "shoposphere"=>"bc",
        "ticwear"=>"ba",
        "maxthon"=>"az",
        "weixin"=>"ay",
        "people_daily"=>"ax",
        "wps"=>"aw",            
        "bing"=>"av",           //API get coupon and haitao for brand
        "wordpress"=>"au",      //主站
        "netease"=>"at",        //API 网易新闻客户端 xml 2014-12-23
        "sina"=>"as",           //新浪合作
        "meizuyuedu"=>"ar",     //API 魅族阅读器
        "xiaomibrowser"=>"aq",  //API 小米浏览器
        "wangyishuma"=>"ap",    //API 网易数码
        "wangyi"=>"ao",     //API 网易
        "jingdong"=>"an",   //京东（母婴）
        "meizu"=>"am",      //魅族推送
        "firefox"=>"al",    //firefox推送
        "gtalk"=>"ak",  //Gtalk推送
        "hao123"=>"aj", //hao123合作
        "chrome"=>"ai", //chrome推送
        "smzdm"=>"aa"   //主站          
    );

    #source 所有频道
    static $channel = array(
        "haowu"=>"hw",          //好物
        "weixin"=>"wx",         # 微信
        "wiki"=>"wk",           //商品百科         
        "yuanchuang"=>"yc",     #原创
        "zhuanti"=>"zt",        #专题
        "mall"=>"ml",           #商城
        "haiwaigou"=>"hg",      #中亚海外购
        "haiwaizhicai"=>"hz",   #中亚海外直采
        "zmiaosha"=>"ms",   //Z秒杀
        "other"=>"ot",      //其他
        "qingdan"=>"qd",    //清单
        "pingce"=>"pc",     //评测
        "test"=>"zc",       //众测
        "news"=>"zx",       //资讯
        "haitao"=>"ht",     //海淘
        "jingyan"=>"jy",    //经验
        "shaiwu"=>"sd",     //晒单
        "faxian"=>"fx",   //发现
        "youhui"=>"yh",  //优惠
        "qianggou"=>"qg", //精选秒杀
        'pinglun' => 'pl', #评论
    );

    #平台
    static $platform = array(
        "web"=>"ca",
        "wap"=>"cb",
        "iphone"=>"cc",
        "ipad"=>"cd",
        "android"=>"ce",
        "androidpad"=>"cf",
        "wp"=>"cg",
        "win"=>"ch"
    );
}
$config['link_config_data'] = 1;
