<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 报警处理
 */
class Call extends MY_Controller {
    function init() {
        $this->check_permission(['bgm-sos.lsjl']);
        $this->load->biz(['call_biz']);
        $this->load->helper(['array']);
    }
    
     /**
     * 获取报警列表
     */
    public function list(){
        $page = $this->input->get_post('page');
        $page_size = $this->input->get_post('page_size');
        if(!$page){
            $page = 1;
        }
        if(!$page_size){
            $page_size= 20;
        }

        $zc_name = $this->input->get_post('zc_name');
        $call_date = $this->input->get_post('call_date');
        $r = $this->call_biz->call_list($page,$page_size,$zc_name,$call_date);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    }
    
    /**
     * 获取报警列表
     */
    public function notice_list(){
        $page = $this->input->get_post('page');
        $page_size = $this->input->get_post('page_size');
        $zc_name = $this->input->get_post('zc_name');
        $call_date = $this->input->get_post('call_date');
        $call_id = $this->input->get_post('call_id');
        if(!$page){
            $page = 1;
        }
        if(!$page_size){
            $page_size= 20;
        }
        
        $r = $this->call_biz->get_notice_list($call_id,$page,$page_size,$zc_name,$call_date);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    }

}
