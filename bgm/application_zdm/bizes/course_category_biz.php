<?php
/**
 * Created by PhpStorm.
 * User: smzdm
 * Date: 2017/12/8
 * Time: 下午2:03
 */

class course_category_biz extends CI_Biz
{
    public function __construct()
    {
        $this->load->model(['course_category_model']);
		$this->load->helper(['array']);
        $this->db = $this->load->mysql("garden");
    }
    /**
     * 获取全部分类信息
     *
     * @return array
     * @author liubin
     * @Time:2018/5/7 15:36
     */
	public function get_course_category_list($parent_id,$page=1,$page_size=20){
	    $r = [
	        'error_code' => 0,
            'error_msg'  => '',
            'data'   =>[],
        ];
	    $res = $this->course_category_model->getCourseCategoryList($parent_id,$page,$page_size);

	    if (FALSE === $res)
	    {
	        $r['error_code'] = DATABASE_FALSE;
	        $r['error_msg']  = '网络错误，请稍后再试';
	        return $r;
        }
        $r['data']['rows'] =  $res;
        return $r;
    }
    
    /**
     * 获取全部分类信息
     * @return array
     * @author liubin
     * @Time:2018/5/7 15:36
     */
	public function get_course_category_info($term_id){
	    $r = [
	        'error_code' => 0,
            'error_msg'  => '',
            'data'       =>[],
        ];
	    $res = $this->course_category_model->getCourseCategoryInfo($term_id);
	    if (FALSE === $res)
	    {
	        $r['error_code'] = DATABASE_FALSE;
	        $r['error_msg']        = '网络错误，请稍后再试';
	        return $r;
        }
        $r['data']['row'] =  $res;
        return $r;
    }   
    
    /**
     * 删除课程分类
     * @return array
     * @author liubin
     * @Time:2018/5/7 15:36
     */
	public function delete_course_category($term_id){
	    $r = [
	        'error_code' => 0,
            'error_msg'  => '',
            'data'       =>[],
        ];

        $creator_user_id = $this->current_user['user']['userId'];
        // $creator_user_name = $this->current_user['user']['userCode'];#SSO用户名
        $creator_user_name = $this->current_user['user']['realName'];#SSO用户名
	    $res = $this->course_category_model->deleteCourseCategory($term_id,$creator_user_id,$creator_user_name);
	    if (FALSE === $res)
	    {
	        $r['error_code'] = DATABASE_FALSE;
	        $r['msg']        = '网络错误，请稍后再试';
	        return $r;
        }

        return $r;
    }
    
    /**
     * 获取全部分类信息
     *
     * @return array
     * @author liubin
     * @Time:2018/5/7 15:36
     */
	public function get_university_course_category_list(){
	    $r = [
	        'error_code' => 0,
            'error_msg'  => '',
            'data'       =>[],
        ];

        $slug = 'zdm_university_course';
        //根据别名查询大学分类id
        $university_term = $this->course_category_model->getcourse_categoryBySlug($slug);
        if($university_term==false){
            $r['error_code'] = DATABASE_FALSE;
	        $r['error_msg']   = '还没添加值得买大学分类目录';
	        return $r;
        }

        $university_term_id = $university_term['term_id'];
        //获取大学下的所有课程分类
        $category_list =  $this->get_category_id_list([$university_term_id]);
        if (FALSE === $category_list)
	    {
	        $r['error_code'] = DATABASE_FALSE;
	        $r['error_msg']      = '网络错误，请稍后再试';
	        return $r;
        }

        //树状结构
        // $res = $this->recursion($ret,$university_term_id);
        // echo json_encode($res);
        // exit;

        $category_id_list = array_column($category_list,'term_id');
        $res = $this->course_category_model->getcourse_categoryList($category_id_list);
	    if (FALSE === $res)
	    {
	        $r['error_code'] = DATABASE_FALSE;
	        $r['error_msg']        = '网络错误，请稍后再试';
	        return $r;
        }

        $r['data']['rows'] =  $res;
        return $r;
    }
    /**
     * 获取分类列表
     */
    private function get_category_id_list($term_ids){
        $list = [];
        while(1){
            $category_list = $this->course_category_taxonomy_model->getTermIdsByParentIds($term_ids);
            if (FALSE === $category_list)
            {
                return false;
            }

            if(count($category_list)==0){
                    return $list;
            }

            $term_ids = array_column($category_list,'term_id');
            $list = array_merge($list,$category_list );
        }
        return [];
    }

/**
 * 根据父级id查找子级数据
 * @param $data     要查询的数据
 * @param int $pid 父级id
 */
public function recursion($data, $pid = 0)
{
    $child = [];   // 定义存储子级数据数组
    foreach ($data as $key => $value) {
        if ($value['parent'] == $pid) {
            unset($data[$key]);  // 使用过后可以销毁
            $value['child'] = $this->recursion($data, $value['term_id']);   // 递归调用，查找当前数据的子级
            $child[] = $value;   // 把子级数据添加进数组
        }
    }
    return $child;
}

    /**
     * 添加/更新职场
     */
    public function  edit_course_category($course_category_id,$course_category_name,$sort_order=0,$parent_category_id=0){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];

        $creator_user_id = $this->current_user['user']['userId'];
        // $creator_user_name = $this->current_user['user']['userCode'];#SSO用户名
        $creator_user_name = $this->current_user['user']['realName'];#SSO用户名
        $course_category_info = $this->course_category_model->getCourseCategoryByName($course_category_name);
        if(FALSE === $course_category_info){
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            return $r;
        }

        if(empty($course_category_id)){
            if($course_category_info){
                $r['error_code'] = ERROR_PARAMS;
                $r['error_msg'] = '课程分类已经存在,请勿重复添加~';
                return $r;
            }

            $course_category_id  = $this->course_category_model->addNewcourseCategory($course_category_name,$sort_order,$creator_user_id,$creator_user_name,$parent_category_id);
            if(FALSE === $course_category_id){
                $r['error_code'] = DATABASE_FALSE;
                $r['error_msg'] = '网络错误';
                return $r;
            }
        }else{
            if($course_category_info && $course_category_info['category_id']!=$course_category_id){
                $r['error_code'] = ERROR_PARAMS;
                $r['error_msg'] = '课程分类已经存在,请修改分类目录名称~';
                return $r;
            }
            
            $res  = $this->course_category_model->modifyCourseCategory($course_category_id,$course_category_name,$sort_order,$creator_user_id,$creator_user_name,$parent_category_id);
            if(FALSE === $res){
                $r['error_code'] = DATABASE_FALSE;
                $r['error_msg'] = '网络错误';
                return $r;
            }
        }

        return $r;
    }
}