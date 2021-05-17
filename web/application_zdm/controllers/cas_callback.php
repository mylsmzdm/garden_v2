<?php

/**
 * CAS回调
 *
 * Class Cas_Callback
 * @author liangdong@smzdm.com
 * @date 2018/3/12 下午5:53
 */
class Cas_Callback extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->library('cas');
    }

    // 统一登出回调, 注销CAS本地会话
    public function logout() {
        $this->cas->logoutCallback();
    }
}