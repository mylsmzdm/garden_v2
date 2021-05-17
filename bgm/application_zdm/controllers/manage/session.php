<?php

if (!defined('BASEPATH')) 
    exit('No direct script access allowed');

// 会话管理
class session extends MY_Controller
{
    public function init() {

    }

    // 校验SSO登录, 返回左侧菜单
    public function check_login() {
        $perm = $this->check_permission();
        echo json_encode($perm);
    }
}