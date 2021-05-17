<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Terms extends MY_Controller
{
    function init() {
        $this->check_permission(['bgm-zhidemai.terms']);
        $this->load->biz(['terms_biz']);
    }

    public function edit_terms()
    {
        $this->check_permission(['bgm-zhidemai.terms.edit']);
        $terms_id = $this->input->get_post('terms_id', 0);
        if(!$terms_id){
            $this->response(ERROR_PARAMS,'terms_id不能为空',[]);
            return;
        }

        $terms_name= $this->input->get_post('terms_name', '');
        $terms_slug= $this->input->get_post('terms_slug', '');
        $parent= $this->input->get_post('parent', 0);
        $sort_order= $this->input->get_post('sort_order', 0);
        $res = $this->terms_biz->edit_terms($terms_id,$terms_name,$terms_slug,$sort_order,$parent);
        return $this->response($res['error_code'], $res['error_msg'], $res['data']);       
    }
    
    public function terms_list()
    {
        $this->check_permission(["bgm-zhidemai.terms.list"]);
        $page = $this->input->get_post('page', 1);
        $page_size = $this->input->get_post('page_size', 10);
        $terms_id = $this->input->get_post('terms_id', 0);
        $res = $this->terms_biz->get_terms_list($terms_id,$page,$page_size);
        return $this->response($res['error_code'], $res['error_msg'], $res['data']);
    }    
    
    public function terms_info()
    {
        $this->check_permission(["bgm-zhidemai.terms.info"]);
        $terms_id = $this->input->get_post('terms_id', 0);
        if(!$terms_id){
            $this->response(ERROR_PARAMS,'terms_id不能为空',[]);
            return;
        }
        $res = $this->terms_biz->get_terms_info($terms_id);
        return $this->response($res['error_code'], $res['error_msg'], $res['data']);
    }
    public function delete_terms()
    {
        $this->check_permission(["bgm-zhidemai.terms.info"]);
        $terms_id = $this->input->get_post('terms_id', 0);
        if(!$terms_id){
            $this->response(ERROR_PARAMS,'terms_id不能为空',[]);
            return;
        }
        
        $res = $this->terms_biz->delete_terms($terms_id);
        return $this->response($res['error_code'], $res['error_msg'], $res['data']);
    }


    public function university_course_category_terms_list()
    {
        $this->check_permission(["bgm-zhidemai.terms.list"]);
        $page = $this->input->get_post('page', 1);
        $page_size = $this->input->get_post('page_size', 10);
        $res = $this->terms_biz->get_university_terms_list($page, $page_size);
        return $this->response($res['error_code'], $res['error_msg'], $res['data']);
    }
}