<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 职场设备
 */
class Api extends MY_Controller {
    function init() {
        $this->load->biz(['zc_biz','call_biz']);
        $this->load->helper(['array']);
    }

    /**
     * 职场列表
     */
    public function zc_list(){
        $r = $this->zc_biz->get_zc_list();
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    } 

    /**
     * 获取api鉴权
     */
    public function get_jsapi_ticket(){
        $url = $this->input->get_post('url');
        if($url){
            $url  = urldecode($url);
        }
        $r = $this->call_biz->get_ding_ticket($url);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    } 
  
    /**
     * 获取用户信息
     */
    public function get_user_info(){
        $code = $this->input->get_post('code');
        $r = $this->call_biz->get_user_info($code);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    } 

    
    /**
     * 报警
     */
    public function call(){
        $zc_id = $this->input->get_post('zc_id');
        $zc_floor = $this->input->get_post('zc_floor');
        $call_user_id = $this->input->get_post('user_id');
        if(!$zc_id || !$call_user_id){
            $this->response(ERROR_PARAMS,'参数错误',[]);
            return ;
        }
        $r = $this->call_biz->handle_call($zc_id,$zc_floor,$call_user_id);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    } 
    
    /**
     * 添加通知记录
     */
    public function notice(){
        $call_id = $this->input->get_post('call_id');
        $notice_user_id = $this->input->get_post('notice_user_id');
        $notice_content = $this->input->get_post('notice_content');
        $notice_type = $this->input->get_post('notice_type');
        $notice_result = $this->input->get_post('notice_result');
        $notice_fail_reason = $this->input->get_post('notice_fail_reason');
        if(!$call_id || !$notice_user_id){
            $this->response(ERROR_PARAMS,'参数错误',[]);
            return ;
        }
        if($notice_content){
            $notice_content = urldecode($notice_content);
        }
        
        if($notice_content){
            $notice_fail_reason = urldecode($notice_fail_reason);
        }
  
        $r = $this->call_biz->add_notice($call_id,$notice_user_id,$notice_content,$notice_type,$notice_result,$notice_fail_reason);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    }
}
