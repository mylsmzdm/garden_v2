<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 前端资源库 hashid
 *
 * @author Dacheng Chen
 * @date 2018-05-18
 */
class Resources_hashid extends MY_Controller {

    function init() {

    }


    /**
     * 前端所需资源index.html
     *      访问地址：
     *
     * @author Dacheng Chen
     * @date 2018-05-18
     */
    public function index()
    {
//        $this->load->helper(['url']);
//        if(!is_reffer_from_smzdm()) {
//            echo json_encode(['error_code' => 1, 'error_msg' => '数据错误']);
//            return;
//        }

        $this->check_permission([], 'bgm-sos', TRUE, FALSE, ['return_type' => 1]);

        $this->load->config('hashids');
        $view_data = [
            'resources_hashid_map' => HashidsConfig::$resources_hashid_map,
        ];
        $this->load->view('fore_resources/index', $view_data);
    }

}
