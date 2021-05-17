<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 职场设备
 */
class zc extends MY_Controller {
    function init() {
        $this->check_permission(['bgm-sos.zcsb']);
        $this->load->biz(['zc_biz']);
        $this->load->helper(['array']);
    }

    /**
     * 职场列表
     */
    public function list(){
        $r = $this->zc_biz->get_zc_list();
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    }  
    
    /**
     * 添加职场
     */
    public function add(){
        $zc_name = $this->input->get_post('zc_name');
        $zc_deital_address = $this->input->get_post('zc_detail_address');
        $longitude = $this->input->get_post('longitude');
        $latitude = $this->input->get_post('latitude');
        $device_address = $this->input->get_post('device_address');
        if(!$zc_name){
            $this->response(ERROR_PARAMS,'职场名称未填写',[]);
            return;
        }
        if(!$zc_deital_address){
            $this->response(ERROR_PARAMS,'职场详细地址未填写',[]);
            return;
        }  
        if(!$longitude){
            $this->response(ERROR_PARAMS,'经度未填写',[]);
            return;
        }
         if(!$longitude){
            $this->response(ERROR_PARAMS,'纬度未填写',[]);
            return;
        }
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];

        $r = $this->zc_biz->add_or_update_zc_info(0,$zc_name,$zc_deital_address,$longitude,$latitude,$device_address);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    }  
    
    /**
     * 更新职场
     */
    public function update(){
        $id = $this->input->get_post('id');
        if(!$id){
            $this->response(ERROR_PARAMS,'职场ID未填写',[]);
        }
        
        $zc_name = $this->input->get_post('zc_name');
        $zc_deital_address = $this->input->get_post('zc_detail_address');
        $longitude = $this->input->get_post('longitude');
        $latitude = $this->input->get_post('latitude');
        $device_address = $this->input->get_post('device_address');
        if(!$id){
            $this->response(ERROR_PARAMS,'职场ID未填写',[]);
            return;
        }
          if(!$zc_name){
            $this->response(ERROR_PARAMS,'职场名称未填写',[]);
            return;
        }
        if(!$zc_deital_address){
            $this->response(ERROR_PARAMS,'职场详细地址未填写',[]);
            return;
        }  
        if(!$longitude){
            $this->response(ERROR_PARAMS,'经度未填写',[]);
            return;
        }
         if(!$longitude){
            $this->response(ERROR_PARAMS,'纬度未填写',[]);
            return;
        }
        $r = $this->zc_biz->add_or_update_zc_info($id,$zc_name,$zc_deital_address,$longitude,$latitude,$device_address);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    }   
    
    /**
     * 删除职场
     */
    public function delete(){
        $id = $this->input->get_post('id');
        if(!$id){
            $this->response(ERROR_PARAMS,'职场ID未填写',[]);
            return ;
        }

        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $r = $this->zc_biz->delete_zc($id);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    } 

    /**
     * 获取职场详情
     */
    public function info(){
        $id = $this->input->get_post('id');
        if(!$id){
            $this->response(ERROR_PARAMS,'职场ID未填写',[]);
            return ;
        }
        
        $r = $this->zc_biz->get_zc_by_id($id);
        $this->response($r['error_code'],$r['error_msg'],$r['data']);
        return;
    } 
}
