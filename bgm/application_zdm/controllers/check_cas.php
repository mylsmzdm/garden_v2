<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// 会话管理
class Check_cas extends MY_Controller
{
    public function init() {

    }

    // 校验SSO登录, 返回左侧菜单
    public function login() {
        $perm = $this->check_permission([], 'zindex', true,  true);
        if ($perm['error_code'] == 0) {
            header('Location: https://garden.zdm.net');
        }
    }
}
