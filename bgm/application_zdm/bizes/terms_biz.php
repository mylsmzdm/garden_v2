<?php
/**
 * Created by PhpStorm.
 * User: smzdm
 * Date: 2017/12/8
 * Time: 下午2:03
 */

class terms_biz extends CI_Biz
{
    public function __construct()
    {
        $this->load->model(['terms_model','terms_taxonomy_model']);
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
	public function get_terms_list($parent_term_id,$page=1,$page_size=20){
	    $r = [
	        'error_code' => 0,
            'error_msg'  => '',
            'data'       =>[],
        ];
	    $res = $this->terms_model->getTermsList($parent_term_id,$page,$page_size);
	    if (FALSE === $res)
	    {
	        $r['error_code'] = DATABASE_FALSE;
	        $r['msg']        = '网络错误，请稍后再试';
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
	public function get_terms_info($term_id){
	    $r = [
	        'error_code' => 0,
            'error_msg'  => '',
            'data'       =>[],
        ];
	    $res = $this->terms_model->getTermsInfo($term_id);
	    if (FALSE === $res)
	    {
	        $r['error_code'] = DATABASE_FALSE;
	        $r['msg']        = '网络错误，请稍后再试';
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
	public function delete_terms($term_id){
	    $r = [
	        'error_code' => 0,
            'error_msg'  => '',
            'data'       =>[],
        ];

        $this->db->begin();
	    $res = $this->terms_model->deleteTerms($term_id);
	    if (FALSE === $res)
	    {
            $this->db->rollback();
	        $r['error_code'] = DATABASE_FALSE;
	        $r['msg']        = '网络错误，请稍后再试';
	        return $r;
        }

        $taxonomy = 'category';
        $res = $this->terms_taxonomy_model->deleteTermsTaxonomyByTermId($term_id,$taxonomy);
        if (FALSE === $res)
	    {
            $this->db->rollback();
	        $r['error_code'] = DATABASE_FALSE;
	        $r['msg']        = '网络错误，请稍后再试';
	        return $r;
        }

        $this->db->commit();
        return $r;
    }
    
    /**
     * 获取全部分类信息
     *
     * @return array
     * @author liubin
     * @Time:2018/5/7 15:36
     */
	public function get_university_terms_list(){
	    $r = [
	        'error_code' => 0,
            'error_msg'  => '',
            'data'       =>[],
        ];

        $slug = 'zdm_university_course';
        //根据别名查询大学分类id
        $university_term = $this->terms_model->getTermsBySlug($slug);
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
        $res = $this->terms_model->getTermsList($category_id_list);
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
            $category_list = $this->terms_taxonomy_model->getTermIdsByParentIds($term_ids);
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
    public function  edit_terms($term_id,$term_name,$slug='',$sort_order=0,$parent=0){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];

        if(!$slug){
            $slug = $term_name;
        }
    
        $creator_user_id = $this->current_user['user']['userId'];
        // $creator_user_name = $this->current_user['user']['userCode'];#SSO用户名
        $creator_user_name = $this->current_user['user']['realName'];#SSO用户名

        $this->db->begin();
        if(empty($term_id)){
            $terms_info = $this->terms_model->getTermsByName($term_name);
            if(FALSE === $terms_info){
                $this->db->rollback();
                $r['error_code'] = ERROR_PARAMS;
                $r['error_msg'] = '分类目录名称已经存在,请勿重复添加~';
                return $r;
            }
            $term_id  = $this->terms_model->addNewTerms($term_name,$slug,$sort_order,$creator_user_id,$creator_user_name);
            if(FALSE === $term_id){
                $this->db->rollback();
                $r['error_code'] = DATABASE_FALSE;
                $r['error_msg'] = '网络错误';
                return $r;
            }
        }else{
            $res  = $this->terms_model->modifyTerms($term_id,$term_name,$slug,$sort_order,$creator_user_id,$creator_user_name);
            if(FALSE === $res){
                $this->db->rollback();
                $r['error_code'] = DATABASE_FALSE;
                $r['error_msg'] = '网络错误';
                return $r;
            }
        }

        $taxonomy='category';
        $description='';
        $count=0;
        $terms_taxonomy_info = $this->terms_taxonomy_model->getTermsTaxonomyByTermId($term_id,$taxonomy);
        if(!$terms_taxonomy_info){
            $terms_taxonomy_id  = $this->terms_taxonomy_model->addNewTermsTaxonomy($term_id,$taxonomy,$parent,$description,$count);
            if(FALSE === $terms_taxonomy_id){
                $this->db->rollback();
                $r['error_code'] = DATABASE_FALSE;
                $r['error_msg'] = '网络错误';
                return $r;
            }
        }else{
            $terms_taxonomy_id = $terms_taxonomy_info['id'];
        }

        $res  = $this->terms_taxonomy_model->modifyTermsTaxonomyByTermId($terms_taxonomy_id, $term_id, $taxonomy, $parent, $description, $count);
        if (FALSE === $res) {
            $this->db->rollback();
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            return $r;
        }

        $this->db->commit();
        return $r;
    }
}