<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 附件图片配置
 *      优惠类：优惠精选、发现频道、海淘专区使用同一个图床；
 *      图文类：晒物广场、经验盒子、资讯中心、众测评测使用同一个图床。
 * 
 * 注意：
 *      domain 前面都有 http:// 。
 */
Class PicConfig {

    #优惠类没有密码；图文类有密码
    public static $file_secret = 'a1d33d0dfe';

    #图文类
    public static $article = array(
        'is_add_mark'   => true,
        #有水印
        'mark'    => array(
                            'isFigureBed' => true,
                            'figureBed'   => array(
                                                'domain'  => 'http://am.zdmimg.com',
                                                'space'   => 'zdm-article-mark',
                                                'user'    => 'zdmarticle',
                                                'password'=> 'LshO7RwD'
                                            ),
                            'savePath'=>'/',
                            'allowExt'=>'jpeg,jpg,png,gif',
                        ),
        #无水印
        'normal'    => array(
                            'isFigureBed' => true,
                            'figureBed'   => array(
                                                'domain'  => 'http://a.zdmimg.com',
                                                'space'   => 'zdm-article',
                                                'user'    => 'zdmarticle',
                                                'password'=> 'LshO7RwD',
                            ),
                            'savePath'=>'/',
                            'allowExt'=>'jpeg,jpg,png,gif',
                        )
    );

    #优惠类
    public static $youhui = array(
        #有水印
        'mark'    => array(
                            'isFigureBed' => true,
                            'figureBed'   => array(
                                                'domain'  => 'http://ym.zdmimg.com',
                                                'space'   => 'zdm-youhuis-mark',
                                                'user'    => 'zdmyouhui',
                                                'password'=> 'e640sQQ4',
                                            ),
                            'savePath'=>'/',
                            'allowExt'=>'jpeg,jpg,png,gif',
                        ),
        #无水印
        'normal'    => array(
                            'isFigureBed' => true,
                            'figureBed'   => array(
                                                'domain'  => 'http://y.zdmimg.com',
                                                'space'   => 'zdm-youhuis',
                                                'user'    => 'zdmyouhui',
                                                'password'=> 'e640sQQ4'
                            ),
                            'savePath'=>'/',
                            'allowExt'=>'jpeg,jpg,png,gif',
                        )
    );
    
    #头像
    public static $avatar = [
        'normal' => [
            'isFigureBed' => true,
            'figureBed' => [
                'domain' => 'http://ym.zdmimg.com',
                'space' => 'zdm-users-avatar',
                'user' => 'zdmuseravatar',
                'password' => '7cIeRy90',
            ],
            'savePath'=>'/',
            'allowExt'=>'jpeg,jpg,png,gif',
        ],
    ];

    public static $figureBedChoice = array(
        'youhui' => '' //默认是又拍云(为空) 七牛:qiniu 并且必须要存在 $figureBed.Config 类
    );
}
$config['pic_config_data'] = 1;
class QiniuConfig {
    public static $access_key = 'gx-jLoLxqU65O2IrJAmFMVhM-v7T0rPPiX5s3m93';
    public static $secret_key = 'I1X9z6Pvtw4d6nY6yaSvrDDEl5VDEZQaD6qkobnD';

    public static $mark_set = array(
        'normal' => array(
            'domain' => 'http://qny.zdmimg.com',
            'bucket' => 'zdm-youhuis-7n'
        ),
        'mark' => array(
            'domain' => 'http://qnym.zdmimg.com',
            'bucket' => 'zdm-youhuis-mark-7n'
        )
    );



}





