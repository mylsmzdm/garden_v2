<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$config['email_config_data'] = 1;

if (!class_exists('EmailConfig')) {
/**
 * 邮箱配置
 */
Class EmailConfig {

    public static $default = array(
        'protocol' => 'smtp',
        'smtp_host' => 'zmail.smzdm.com',
        'smtp_port' => '25',
        'smtp_user' => 'reply@zmail.smzdm.com',
        'smtp_pass' => 'feMO0qvVbN_lZ',
        'mailtype' => 'html',
        'charset' => 'utf-8',
            #'_smtp_auth' => true,
    );
    public static $send_cloud = array(
        'is_open' => true,
        // 'username' => 'postmaster@pro.sendcloud.org',
        'username' => 'service_smzdm', #新的 
        'password' => 'ck3L8EkRNt5agxc8', #api-key 可能会定期强制更新
    );
    
    #注册发送激活信 (type=1)
    public static $register_activate = [
        'title' => "邮件激活",
        'message' => '',
    ];
    
    #激活成功信 (type=2)
    public static $register_success = [
        'title' => "账号激活通知",
        'message' => '', #普通注册格式
        'message_third' => '', #联合登录注册格式
    ];
    
    #找回密码验证信 (type=3)
    public static $retrive_password = [
        'title' => '',
        'message' => '',
    ];
    
    #找回密码修改成功通知信 (type=4)
    public static $retrive_password_success = [
        'title' => '',
        'message' => '',
    ];
    
    #修改安全密码后发email通知 (type=5)
    public static $change_safepass = [
        'title' => '安全密码修改通知',
        'message' => '',
    ];
    
    # 修改收货地址后发email通知
    public static $change_address =[
        'title'     => '收货地址修改通知',
    ];

    #邮箱绑定修改通知
    public static $first_email_message = [
        'title' => '绑定邮箱修改通知',
        'message' => '',
    ];

    #手机换绑修改通知
    public static $bind_mobile_message = [
        'title' => '绑定手机号修改通知',
        'message' => '',
    ];

    #手机解绑通知
    public static $unbind_mobile_message = [
        'title' => '绑定手机号解绑通知',
        'message' => '',
    ];

    #修改邮箱，验证现有邮箱
    public static $check_curr_email = [
        'title' => '账号邮箱验证',
        'message' => '',
    ];
    #修改邮箱，验证新邮箱
    public static $check_new_email = [
        'title' => '账号邮箱验证',
        'message' => '',
    ];
    #修改邮箱成功发通知邮件（注意：给原有邮箱发）
    public static $check_email = [
        'title' => '绑定邮箱修改通知',
        'message' => '', 
    ];
    #异常登录 发送邮件
    public static $abnormal_login = [
        'title' => '账号安全提醒',
        'message' => '',
    ];
    #忘记密码 发送找回密码邮件
    public static $lost_password_email = [
        'title' => '密码重设',
        'message' => '',
    ];
    #兑换产品后发提醒邮件
    public static $duihuan_email = [
        'title' => '兑换提醒',
        'message' => '',
    ];


}



# 修改安全密码后发email通知
EmailConfig::$change_safepass['message'] = '<html>
    <head>
        <meta charset="utf-8">
        <title>%s</title>
    </head>
    <body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="'.Config::$url['web'].'" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,SimSun;font-weight:bold; padding:40px 0 25px;">亲爱的用户：%s您好！</div>
        <div style="font-size:14px; padding-bottom:20px;">您的什么值得买账号在'.date("m月d日H时i分", time()).'修改了安全密码，如有疑问请尽快更改密码，或撰写申诉邮件发送至<a href=mailto:service@smzdm.com>service@smzdm.com</a></div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,SimSun; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br/>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
    </body>
</html>';

# 修改收货地址后发email通知
EmailConfig::$change_address['message'] = '<html>
    <head>
        <meta charset="utf-8">
        <title>%s</title>
    </head>
    <body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="'.Config::$url['web'].'" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,SimSun;font-weight:bold; padding:40px 0 25px;">亲爱的用户：%s您好！</div>
        <div style="font-size:14px; padding-bottom:20px;">您的什么值得买账号在'.date("m月d日H时i分", time()).'修改了收货地址，如有疑问请尽快更改密码，或撰写申诉邮件发送至<a href=mailto:service@smzdm.com>service@smzdm.com</a></div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,SimSun; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br/>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
    </body>
</html>';


#邮箱绑定验证通知
EmailConfig::$first_email_message['message'] = '<html>
    <head>
        <meta charset="utf-8">
        <title>%s</title>
    </head>
    <body>
        <div style="width:700px; margin:0 auto; font-size:12px;">
            <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
                <a href="'.Config::$url['web'].'" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
            </div>
            <div style="font-size:14px; font-family:arial,SimSun;font-weight:bold; padding:40px 0 25px;">亲爱的用户：%s您好！</div>
            <div style="font-size:14px; font-family:arial,SimSun; color:#555; padding-bottom:30px;line-height:1.6em;">
                   你的什么值得买账号正在申请邮箱绑定%s，如果是你本人申请，<a href="%s" style="color:#bb0200; text-decoration:underline; font-weight:bold;">请点击这里立即完成邮箱验证</a>。如果不是本人申请，请忽略本邮件。
            </div>
            <div style="font-size:12px; color:#555;font-family:arial,SimSun; padding-bottom:70px;">如果上述文字点击无效，请将以下网址复制到浏览器地址栏打开（该链接使用一次或24小时后失效）：<br/><pre>%s</pre></div>
            <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
                <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
                <div style="color:#999;font-family:arial,\'SimSun\'; line-height:1.6em;">
                高性价比产品网购推荐，值得您经常来看看<br>
                Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
                </div>
            </div>
        </div>
    </body>
</html>'; #1昵称 2操作名称(中文) 3.auth_param(flag/base64(email)/random_string) 4操作名称(中文) 5auth_param

#手机换绑修改通知
EmailConfig::$bind_mobile_message['message'] = '<html>
    <head>
        <meta charset="utf-8">
        <title>%s</title>
    </head>
    <body>
        <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="'.Config::$url['web'].'" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,SimSun;font-weight:bold; padding:40px 0 25px;">亲爱的用户：%s您好！</div>
        <div style="font-size:14px; padding-bottom:20px;">您的什么值得买账号在'.date("m月d日H时i分", time()).'修改绑定手机号为%s，如有疑问请尽快更改密码，或撰写申诉邮件发送至<a href=mailto:service@smzdm.com>service@smzdm.com</a></div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,SimSun; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br/>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
    </body>
</html>';

# 手机解绑通知
EmailConfig::$unbind_mobile_message['message'] = '<html>
    <head>
        <meta charset="utf-8">
        <title>%s</title>
    </head>
    <body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="'. Config::$url['login'] . '" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,SimSun;font-weight:bold; padding:40px 0 25px;">亲爱的用户：%s您好！</div>
        <div style="font-size:14px; padding-bottom:20px;">您的什么值得买账号在'.date("m月d日H时i分", time()).'解除了手机号为%s的绑定，如有疑问请尽快更改密码，或撰写申诉邮件发送至<a href=mailto:service@smzdm.com>service@smzdm.com</a></div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,SimSun; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br/>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
    </body>
</html>';
#修改邮箱，验证现有邮箱
EmailConfig::$check_curr_email['message'] = '<html>
    <head>
        <meta charset="utf-8">
        <title>%s</title>
    </head>
    <body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="'. Config::$url['login'] . '" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,SimSun;font-weight:bold; padding:40px 0 25px;">亲爱的用户：%s您好！</div>
        <div style="font-size:14px; color:#555; padding-bottom:20px;">有用户申请修改你在什么值得买的邮箱，如果是您自己申请的，<a href="%s" style="color:#bb0200; text-decoration:underline; font-weight:bold;">请点击这里立即完成邮箱验证</a>。如果不是可以忽略。</div>
        <div style="font-size:12px; color:#555;font-family:arial,SimSun; padding-bottom:70px;">如果上述文字点击无效，请将以下网址复制到浏览器地址栏打开：<br/><pre>%s</pre></div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,SimSun; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br/>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
    </body>
</html>';

#修改邮箱，验证新邮箱
EmailConfig::$check_new_email['message'] = '<html>
    <head>
        <meta charset="utf-8">
        <title>%s</title>
    </head>
    <body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="'. Config::$url['login'] . '" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,SimSun;font-weight:bold; padding:40px 0 25px;">亲爱的用户：%s您好！</div>
        <div style="font-size:14px; color:#bb0200; padding-bottom:20px;"><a href="%s" style="color:#bb0200; text-decoration:underline; font-weight:bold;">请点击这里立即完成邮箱验证</a></div>
        <div style="font-size:12px; color:#555;font-family:arial,SimSun; padding-bottom:70px;">如果上述文字点击无效，请将以下网址复制到浏览器地址栏打开：<br/><pre>%s</pre></div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,SimSun; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br/>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
    </body>
</html>';

#修改邮箱成功发通知邮件（注意：给原有邮箱发）
EmailConfig::$check_email['message'] = '<html>
    <head>
        <meta charset="utf-8">
        <title>%s</title>
    </head>
    <body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="'. Config::$url['login'] . '" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,SimSun;font-weight:bold; padding:40px 0 25px;">亲爱的用户：%s您好！</div>
        <div style="font-size:14px; padding-bottom:20px;">您的什么值得买账号在'.date("m月d日H时i分", time()).'修改绑定邮箱为 %s ，如有疑问请尽快更改密码，或撰写申诉邮件发送至<a href=mailto:service@smzdm.com>service@smzdm.com</a></div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,SimSun; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br/>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
    </body>
</html>';

EmailConfig::$register_activate['message'] = '<html>
<head>
    <meta charset="utf-8">
    <title>%s</title>
</head>
<body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="' . Config::$url['login'] . '" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none; vertical-align:inherit;" /></a>
            <span style="width:100px; text-align:right; padding-top:33px; display:inline-block;"><a href="https://zhiyou.smzdm.com/user/login" style="color:#5183c0; text-decoration:none;">登录</a></span>
        </div>
        <div style="font-size:14px; font-family:arial,SimSun;font-weight:bold; padding:40px 0 25px;">亲爱的 %s 您好！</div>
        <div style="padding-bottom:20px;"><a href="'. Config::$url['login'] . '/user/register/email_activation/%s" style="font-size:14px;color:#fff;background-color:#f04848;width:150px;height:30px;line-height:30px;text-align:center;display:block;text-decoration:none;border-radius:2px;-moz-border-radius:2px;-webkit-border-radius:2px;-ms-border-radius:2px;-o-border-radius:2px;">激活账户</a></div>
        <div style="font-size:12px; color:#555;font-family:arial,SimSun; padding-bottom:70px;">如果上述文字点击无效，请将以下网址复制到浏览器地址栏打开（该链接使用一次或24小时后失效）：<br/>'. Config::$url['login'] . '/user/register/email_activation/%s</div>
        <div style="font-size:16px; font-family:arial,SimSun;font-weight:bold; padding-bottom:15px;">激活成为会员，你可以享受更多的特权：</div>
        <table width="100%%" style="padding-bottom:70px;font-size:14px;">
            <tr>
                <td>
                    <a href="http://faxian.smzdm.com/" style="text-align:center;display:block;width:80px;color:#666;text-decoration:none;">
                        <img src="http://res.smzdm.com/email/haojia.png" alt=""><br /><br />
                        爆料好价
                    </a>
                </td>
                <td>
                    <a href="http://wiki.smzdm.com/" style="text-align:center;display:block;width:80px;color:#666;text-decoration:none;">
                        <img src="http://res.smzdm.com/email/haowu.jpg" alt=""><br /><br />
                        推荐好物
                    </a>
                </td>
                <td>
                    <a href="http://post.smzdm.com/" style="text-align:center;display:block;width:80px;color:#666;text-decoration:none;">
                        <img src="http://res.smzdm.com/email/haowen.png" alt=""><br /><br />
                        分享好文
                    </a>
                </td>
                <td>
                    <a href="http://duihuan.smzdm.com/quan/" style="text-align:center;display:block;width:80px;color:#666;text-decoration:none;">
                        <img src="http://res.smzdm.com/email/youhuiquan.jpg" alt=""><br /><br />
                        领优惠券
                    </a>
                </td>
            </tr>
        </table>
        <div style="font-size:16px; font-family:arial,SimSun;font-weight:bold; padding-bottom:15px;">了解更多资讯，请关注：</div>
        <table width="100%%" style="padding-bottom:70px;font-size:14px;">
            <tr>
                <td>
                    <a href="http://www.smzdm.com/push/" style="text-align:center;display:block;width:130px;text-decoration:none;color:#666;">
                        <img src="http://res.smzdm.com/email/App.png" alt=""><br /><br />
                        App下载
                    </a>
                </td>
                <td>
                    <a href="http://weibo.com/smzdm" style="text-align:center;display:block;width:130px;text-decoration:none;color:#666;">
                        <img src="http://res.smzdm.com/email/weibo.png" alt=""><br /><br />
                        关注微博
                    </a>
                </td>
                <td>
                    <span style="text-align:center;display:block;width:130px;text-decoration:none;color:#666;">
                        <img src="http://res.smzdm.com/email/weixin.png" alt=""><br /><br />
                        扫描关注微信
                    </span>
                </td>
            </tr>
        </table>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,SimSun; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br/>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
</body>
</html>';
// EmailConfig::$register_activate['message'] = '<html>
// <head>
//     <meta charset="utf-8">
//     <title>%s</title>
// </head>
// <body>
//     <div style="width:700px; margin:0 auto; font-size:12px;">
//         <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
//             <a href="'. Config::$url['login'] . '" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
//         </div>
//         <div style="font-size:14px; font-family:arial,SimSun;font-weight:bold; padding:40px 0 25px;">亲爱的 %s 您好！</div>
//         <div style="font-size:14px; color:#bb0200; padding-bottom:20px;"><a href="'. Config::$url['login'] . '/user/register/email_activation/%s" style="color:#bb0200; text-decoration:underline; font-weight:bold;">请点击这里立即完成激活</a></div>
//         <div style="font-size:12px; color:#555;font-family:arial,SimSun; padding-bottom:70px;">如果上述文字点击无效，请将以下网址复制到浏览器地址栏打开（该链接使用一次或24小时后失效）：<br/>'. Config::$url['login'] . '/user/register/email_activation/%s</div>
//         <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
//             <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
//             <div style="color:#999;font-family:arial,SimSun; line-height:1.6em;">
//             高性价比产品网购推荐，值得您经常来看看<br/>
//             Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
//             </div>
//         </div>
//     </div>
// </body>
// </html>';# 1:用户昵称 2:smzdm_id/sign 3:smzdm_id/sign

EmailConfig::$register_success['message'] = '<html>
<head>
    <meta charset="utf-8">
</head>

<body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="' . Config::$url['login'] . '" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,\'SimSun\';font-weight:bold; padding:40px 0 25px;">亲爱的 %s 您好！</div>
        <div style="font-size:14px; font-family:arial,\'SimSun\'; color:#555; padding-bottom:30px;line-height:1.6em;">
            您的什么值得买账号已经激活，现在就可以使用本邮箱和您的密码登录什么值得买网站。
        </div>
        <p style="font-size:14px; font-family:arial,\'SimSun\'; color:#555; padding-bottom:10px;line-height:1.6em;">四幅漫画教你如何省钱、省心，快速玩转什么值得买！</p>
        <div style="margin:0 auto;text-align: center;">
            <div style="margin-bottom:15px;"><a href="http://www.smzdm.com/youhui/"><img src="http://res.smzdm.com/email/reg_1.jpg" style="border:none;" /></a></div>
            <div style="margin-bottom:15px;"><a href="http://post.smzdm.com/"><img src="http://res.smzdm.com/email/reg_2.jpg" style="border:none;"/></a></div>
            <div style="margin-bottom:15px;"><a href="http://duihuan.smzdm.com/"><img src="http://res.smzdm.com/email/reg_3.jpg" style="border:none;"/></a></div>
            <div style="margin-bottom:15px;"><a href="http://zhiyou.smzdm.com/user/tequan"><img src="http://res.smzdm.com/email/reg_4.jpg" style="border:none;"/></a></div>
            <div style="margin-bottom:15px;"><a href="http://www.smzdm.com/push"><img src="http://res.smzdm.com/email/reg_5.jpg" style="border:none;"/></a></div>
        </div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,\'SimSun\'; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
</body>
</html>';#1昵称

EmailConfig::$register_success['message_third'] = '<html>
<head>
    <meta charset="utf-8">
</head>

<body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="' . Config::$url['login'] . '" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,\'SimSun\';font-weight:bold; padding:40px 0 25px;">亲爱的 %s 您好！</div>
        <div style="font-size:14px; font-family:arial,\'SimSun\'; color:#555; padding-bottom:30px;line-height:1.6em;">
            您的什么值得买账号已经激活，现在就可以使用本邮箱和您的密码登录什么值得买网站，或者使用%s账号进行联合登录。
        </div>

        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,\'SimSun\'; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
</body>
</html>'; #1邮箱 2平台名称

EmailConfig::$retrive_password['message'] = '<html>
<head>
    <meta charset="utf-8">
</head>

<body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="' . Config::$url['login'] . '" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,\'SimSun\';font-weight:bold; padding:40px 0 25px;">亲爱的 %s 您好！</div>
        <div style="font-size:14px; font-family:arial,\'SimSun\'; color:#555; padding-bottom:30px;line-height:1.6em;">
                有用户对您的账号使用了找回密码服务<br>
                如不是您本人操作，请忽略此邮件。
        </div>
        <div style="font-size:14px; color:#bb0200; padding-bottom:20px;"><a href="'. Config::$url['login'] . '/user/pass/retrieve/%s" style="color:#bb0200; text-decoration:underline; font-weight:bold;">请点击这里完成密码找回</a></div>
        <div style="font-size:12px; color:#555;font-family:arial,\'SimSun\'; padding-bottom:70px;">如果上述文字点击无效，请将以下网址复制到浏览器地址栏打开（该链接使用一次或24小时后失效）：<br>'. Config::$url['login'] . '/user/pass/retrieve/%s</div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,\'SimSun\'; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
</body>
</html>';#1.用户昵称 2:smzdm_id/sign 3:smzdm_id/sign

EmailConfig::$retrive_password_success['message'] = '您的什么值得买账号在%s进行了找回登录密码操作，如有疑问请尽快通过'.Config::$url['zhiyou'].'/user/find_password找回密码，或撰写申诉邮件发送至<a href=mailto:service@smzdm.com>service@smzdm.com</a>。'; #1.X月X日X时X分

EmailConfig::$abnormal_login['message'] = '<html>
<head>
    <meta charset="utf-8">
    <title>%s</title>
</head>
<body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="'.Config::$url['web'].'" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,SimSun;font-weight:bold; padding:40px 0 25px;">亲爱的什么值得买用户：</div>
        <div style="font-size:14px; color:#555; padding-bottom:20px;">您好！</div>
        <div style="font-size:14px; color:#555; padding-bottom:20px;">您的帐号 <font color="#e12d2d">%s</font> 近期发生了一次异常登录，请核实以下详情。</div>
        <div style="font-size:14px; color:#555; padding-bottom:20px;">
            <table border="1px" cellspacing="0px">
                <tr><td width="150">登录地点</td><td width="150">登录IP</td><td width="150">登录时间</td></tr>
                <tr><td>%s </td><td>%s </td><td>%s </td></tr>
            </table>
        </div>
        <div style="font-size:12px; color:#555;font-family:arial,SimSun; padding-bottom:70px;">
            如非本人操作，则可能您的帐号存在安全风险，请点击如下链接修改密码，以保障您的帐号安全。<br><br>
            <a href="'.Config::$url['zhiyou'].'/user/loginpass/" target="_blank">'.Config::$url['zhiyou'].'/user/loginpass/</a><br><br>
            (如果您无法点击此链接，请将它复制到浏览器地址栏后访问)<br><br>
            如确认为本人操作，请忽略此邮件，由此给您带来的不便请谅解！
        </div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,SimSun; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br/>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
</body>
</html>'; #1邮箱 2平台名称

EmailConfig::$lost_password_email['message'] = '<html>
<head>
    <meta charset="utf-8">
</head>

<body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="'.Config::$url['web'].'" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,\'SimSun\';font-weight:bold; padding:40px 0 25px;">亲爱的 %s 您好！</div>
        <div style="font-size:14px; font-family:arial,\'SimSun\'; color:#555; padding-bottom:30px;line-height:1.6em;">
            有用户对您的账号使用了找回密码服务<br>
            如不是您本人操作，请忽略此邮件。
        </div>
        <div style="font-size:14px; color:#bb0200; padding-bottom:20px;"><a href="http://www.smzdm.com/user/pass/retrieve/%s" style="color:#bb0200; text-decoration:underline; font-weight:bold;">请点击这里完成密码找回</a></div>
        <div style="font-size:12px; color:#555;font-family:arial,\'SimSun\'; padding-bottom:70px;">如果上述文字点击无效，请将以下网址复制到浏览器地址栏打开（该链接使用一次或24小时后失效）：<br>http://www.smzdm.com/user/pass/retrieve/%s</div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,\'SimSun\'; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
</body>
</html>'; #1.用户昵称 2:user_smzdm_id/sign 3:user_smzdm_id/sign

#兑换产品后发提醒邮件
EmailConfig::$duihuan_email['message'] = '<html>
<head>
    <meta charset="utf-8">
    <title>%s</title>
</head>

<body>
    <div style="width:700px; margin:0 auto; font-size:12px;">
        <div style="padding:0 10px; border-bottom:1px solid #888888; height:55px;">
            <a href="'.Config::$url['web'].'" style="width:570px; display:inline-block;"><img src="http://res.smzdm.com/email/logo.png" alt="" style="border:none;" /></a>
        </div>
        <div style="font-size:14px; font-family:arial,\'SimSun\';font-weight:bold; padding:40px 0 25px;">亲爱的用户 %s 您好，</div>
        <div style="font-size:14px; font-family:arial,\'SimSun\'; color:#555; padding-bottom:30px;line-height:1.6em;">
            您于%s在“什么值得买”网站申请兑换“<a href="%s" target="_blank">%s</a>”。<br>
            如非本人操作，可能存在盗号风险，<a style="color:#bb0200; text-decoration:underline; font-weight:bold;" target="_blank" href="'.Config::$url['zhiyou'].'/user/safepass">请点击这里立即修改您的账号密码。</a>
        </div>
        <div style="width:300px; border-top:1px dotted #ccc;height:100px; padding-top:10px;">
            <div style="color:#999; padding-bottom:20px;">此为系统邮件，请勿回复</div>
            <div style="color:#999;font-family:arial,\'SimSun\'; line-height:1.6em;">
            高性价比产品网购推荐，值得您经常来看看<br>
            Copyright 什么值得买 2010-'.date("Y").' All Right Reserved.
            </div>
        </div>
    </div>
</body>
</html>';

}