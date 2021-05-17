<?php

class RegisterProtection {
    public static $API_URL = 'csec.api.qcloud.com/v2/index.php';

    public function makeURL($accountType = 4, $uid = '', $postTime = '', $userIp = '',  $args = []) {
        if($accountType == "4") {
            $uid = "0086-".$uid;
        }
        $default_args = [
            #必选
            'accountType'  => $accountType, #用户账号类型 0：其他账号 1：QQ开放帐号 2：微信开放帐号 4：手机账号 6：手机动态码 7：邮箱账号
            'uid'          => $uid, /* 用户ID，accountType不同对应不同的用户ID。如果是QQ或微信用户则填入对应的openId */
            'postTime' => !empty($postTime) ? $postTime : time(), #操作时间戳，单位秒
            'userIp' => $userIp, #操作来源的外网IP
            
            /* 账号信息 */
            // 'associateAccount'     => '', #accountType是QQ或微信开放账号时，用于标识QQ或微信用户登录后关联业务自身的账号ID
            // 'nickName'             => '', #昵称，utf8编码 =================================
            // 'phoneNumber'          => '', #手机号；国家代码-手机号， 如0086-15912345687. 注意0086前不需要+号
            // 'emailAddress'         => '', #用户邮箱地址（非系统自动生成） =====================
            // 'registerTime'         => '', #注册时间戳，单位秒 ===============================
            // 'registerIp'           => '', #注册来源的外网IP   ==============================
            // 'passwordHash'         => '', #用户密码进行2次hash的值，只要保证相同密码Hash值一致即可

            // /* 行为信息 */
            // 'loginSource'          => '', #登录来源 0：其他 1：PC网页 2：移动页面 3：APP 4：微信公众号  ================================
            // 'loginType'            => '', #登录方式 0：其他 1：手动帐号密码输入 2：动态短信密码登录 3：二维码扫描登录 ===========================
            // 'rootId'               => '', #用户操作的目的ID 比如：点赞，该字段就是被点 赞的消息 id，如果是投票，就是被投号码的 ID
            // 'referer'              => '', #用户Http请求的referer值
            // 'jumpUrl'              => '', #登录成功后跳转页面
            // 'cookieHash'           => '', #用户Http请求中的cookie进行2次hash的值，只要保证相同cookie的Hash值一致即可
            // 'userAgent'            => '', #用户Http请求的userAgent ===========================
            // 'xForwardedFor'        => '', #用户Http请求中的x_forward_for
            // 'mouseClickCount'      => '', #用户操作过程中鼠标点击次数
            // 'keyboardClickCount'   => '', #用户操作过程中键盘点击次数

            /* 设备信息 */
            // 'macAddress'           => '', #mac地址或设备唯一标识
            // 'vendorId'             => '', #手机制造商ID，如果手机注册，请带上此信息
            // 'imei'                 => '', #手机设备号
            // // 'appVersion'           => '', #APP客户端版本 =======================================

            // /* 其他信息 */
            // 'businessId'           => '', #业务ID，网站或应用在多个业务中使用此服务，通过此ID区分统计数据
            

        ];
        $args = array_merge($default_args, $args);
        if(!empty($args['phoneNumber'])) {
            $args['phoneNumber'] = "0086-".$args['phoneNumber'];
        }
        // var_dump($args); exit();
        $method = 'GET';
        $action = 'ActivityAntiRush';

        $region = 'gz';
        $args['Nonce'] = (string)rand(0, 0x7fffffff);
        $args['Action'] = $action;
        $args['Region'] = $region;
        $args['SecretId'] = Config::$qcloud['secret_id'];
        $args['Timestamp'] = (string)time();
        ksort($args);
        $args['Signature'] = base64_encode(hash_hmac('sha1', $method . self::$API_URL . '?' . $this->makeQueryString($args, FALSE), Config::$qcloud['secret_key'], TRUE));
        return 'https://' . self::$API_URL . '?' . $this->makeQueryString($args, TRUE);
    }

    function makeQueryString($args, $isURLEncoded) {
        return implode('&', array_map(function ($key, $value) use (&$isURLEncoded) {
            if (!$isURLEncoded) return "$key=$value"; else
                return $key . '=' . urlencode($value);
        }, array_keys($args), $args));
    }
}
