<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 队员
 */
class dy extends MY_Controller {
    function init() {
        $this->check_permission(['bgm-sos.jjdy']);
        $this->load->biz(['dy_biz']);
        $this->load->helper(['array']);
    }

     /**
     * 获取队员列表
     */
    public function list(){
        $name = $this->input->get_post('name');
        $job_number = $this->input->get_post('job_number');
        $zc_name = $this->input->get_post('zc_name');
        $sex = $this->input->get_post('sex');
        if($sex==''){
            $sex = -1;
        }
        $page = $this->input->get_post('page');
        $page_size = $this->input->get_post('page_size');
        if(!$page){
            $page = 1;
        }
        if(!$page_size){
            $page_size= 20;
        }

        if($sex===false){
            $sex = -1;
        }

        $sex = intval($sex);
        $id = 1;
         $app_id = 1;
        $r = $this->dy_biz->get_dy_list($page,$page_size,$id, $app_id,$name,$job_number,$zc_name,$sex);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    }
    
    /**
     * 设置机器人webhook
     */
    public function webhook(){
        $webhook = $this->input->get_post('webhook');
        $webhook  = urldecode($webhook);
        $r = $this->dy_biz->update_webhook($webhook);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    }
}
