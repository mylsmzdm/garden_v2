<?php
/**
 * 极验SDK
 */
require_once __DIR__.'/geetest/GeetestLib.php';

/**
 * 极验行为式验证安全平台，php 网站主后台包含的库文件
 * 跟原sdk相比， 有部分修改
 *@author Tanxu
 */
class Geetest  {
    private $geetest_sdk, $CI;
    public function __construct() {
        if(Config::$site == "beiwo.com") {
            $captcha_id = Config::$geetest['h5_captcha_id'];
            $private_key = Config::$geetest['h5_private_key']; 
        } else {
            if(Config::$from == "web") {
                $captcha_id = Config::$geetest['captcha_id'];
                $private_key = Config::$geetest['private_key'];
            } else if(Config::$from == "wap") {
                $captcha_id = Config::$geetest['wap_captcha_id'];
                $private_key = Config::$geetest['wap_private_key']; 
            } else {
                $captcha_id = Config::$geetest['h5_captcha_id'];
                $private_key = Config::$geetest['h5_private_key']; 
            }
            
        }
        $this->geetest_sdk = new GeetestLib($captcha_id, $private_key);
        $this->CI = &get_instance();
        
        // $geetest_challenge = $this->CI->input->zget_post('geetest_challenge', '123');
        
    }

    /**
     * 校验
     * @return [type] [description]
     */
    public function sucess_validate($geetest_challenge = '', $geetest_validate = '', $geetest_seccode = '') {
        $geetest_challenge = !empty($geetest_challenge) ? $geetest_challenge : (isset($_POST['geetest_challenge']) ? $_POST['geetest_challenge'] : "") ;
        $geetest_validate = !empty($geetest_validate) ? $geetest_validate : (isset($_POST['geetest_validate']) ? $_POST['geetest_validate'] : "");
        $geetest_seccode = !empty($geetest_seccode) ?  $geetest_seccode : (isset($_POST['geetest_seccode']) ? $_POST['geetest_seccode'] : "");
        
        if(empty($geetest_challenge) || empty($geetest_validate) || empty($geetest_seccode)) {
            return false;
        }

        $user_cache = $this->CI->load->redis('user_cache');
        $geetest_redis_key = "u_geetest:".date('Y-m-d:H');
        $user_cache->incr($geetest_redis_key);
        $user_cache->expireat($geetest_redis_key, strtotime(date('Y-m-d H:59:59')));
        
        return $this->geetest_sdk->sucess_validate($geetest_challenge, $geetest_validate, $geetest_seccode);
    }

    /**
     * 预先处理检查极验状态
     * @return [type] [description]
     */
    public function pre_process() {
        return $this->geetest_sdk->pre_process();
    }

    /**
     * 获取前台所需验证码
     * @return [type] [description]
     */
    public function get_response_str() {
        return $this->geetest_sdk->get_response_str();
    }

}
?>
