<?php

//课程分类
defined('BASEPATH') OR exit('No direct script access allowed');
class course_category extends MY_Controller
{
    function init() {
        $this->check_permission(['zindex.study.category']);
        $this->load->biz(['course_category_biz']);
    }

    public function edit_course_category()
    {
        $this->check_permission(['zindex.study.category.edit']);
        $course_category_id = intval($this->input->get_post('category_id', 0));
        $course_category_name= $this->input->get_post('category_name', '');
        $parent= intval($this->input->get_post('parent', 0));
        $sort_order= intval($this->input->get_post('sort_order', 0));
        $res = $this->course_category_biz->edit_course_category($course_category_id,$course_category_name,$sort_order,$parent);
        return $this->response($res['error_code'], $res['error_msg'], $res['data']);       
    }
    
    public function course_category_list()
    {
        $this->check_permission(["zindex.study.category.list"]);
        $page = $this->input->get_post('page', 1);
        $page_size = intval($this->input->get_post('page_size', 10));
        $parent_id = intval($this->input->get_post('parent_id', 0));
        if(empty($page_size)){
            $page_size = 10;
        }
        if(empty($page)){
            $page = 1;
        }
        $res = $this->course_category_biz->get_course_category_list($parent_id,$page,$page_size);
        return $this->response($res['error_code'], $res['error_msg'], $res['data']);
    }    
    
    public function course_category_info()
    {
        $this->check_permission(["zindex.study.category.info"]);
        $course_category_id = $this->input->get_post('category_id', 0);
        if(!$course_category_id){
            $this->response(ERROR_PARAMS,'category_id不能为空',[]);
            return;
        }
        $res = $this->course_category_biz->get_course_category_info($course_category_id);
        return $this->response($res['error_code'], $res['error_msg'], $res['data']);
    }
    public function delete_course_category()
    {
        $this->check_permission(["zindex.study.category.info"]);
        $course_category_id = $this->input->get_post('category_id', 0);
        if(!$course_category_id){
            $this->response(ERROR_PARAMS,'category_id不能为空',[]);
            return;
        }
        
        $res = $this->course_category_biz->delete_course_category($course_category_id);
        return $this->response($res['error_code'], $res['error_msg'], $res['data']);
    }
}