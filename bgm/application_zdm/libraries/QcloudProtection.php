<?php

require_once __DIR__."/qcloud/RegisterProtection.php";

class QcloudProtection {

    public $register_obj = null;
    
    public function __construct($args = array()) {
        $this->init();
    }

    public function init() {
        $this->register_obj = new RegisterProtection();
        
    }

    /**
     * 检查手机号危险状态
     */
    public function check_account_status($accountType = 4, $uid = '', $postTime = '', $userIp = '',  $args = []) {
        $real_url = $this->register_obj->makeURL($accountType, $uid, $postTime, $userIp, $args);
        $CI = &get_instance();
        $CI->load->library('Http', 'http');

        $encode_result = $CI->http->request($real_url);
        $result = json_decode($encode_result, true);

        $C_I = &get_instance();
        if(!empty($result) && $result['code'] == 0 && isset($C_I->cat)) {
            $C_I->cat->metric_count('风控检测', 1);
        }
        
        return $result;    
    }
    
}

?>