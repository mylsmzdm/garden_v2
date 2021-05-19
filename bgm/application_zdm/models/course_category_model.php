<?php
class course_category_model extends MY_Model
{
    protected function getTable()
    {
        return 'epwp_course_category';
    }

    /**
     * 获取分类列表
     */
    public function getCourseCategoryList($parent_id,$page=1,$page_size=20){
        $condition_work = [];
        $where = 'WHERE  epwp_course_category.is_delete =0  ';
        if($parent_id>0){
            $where =  " AND epwp_course_category.parent_category_id =?";
            $condition_work[] = $parent_id;
        }
        $limit = '';
        # 分页
        if (!empty($page) && !empty($page_size))
        {
            $limit = " LIMIT ?,?";
            $condition_work[] = ($page-1)*$page_size;
            $condition_work[] = $page_size;
        }    
        $sql = "SELECT
                        epwp_course_category.category_id, epwp_course_category.category_name, epwp_course_category.create_uid,epwp_course_category.create_user_name,epwp_course_category.sort_order,parent_course_category.category_name as parent_category_name,parent_course_category.category_id as parent_category_id
                     FROM epwp_course_category 
                LEFT JOIN epwp_course_category as parent_course_category  ON parent_course_category.category_id=epwp_course_category.parent_category_id and parent_course_category.is_delete=0
                {$where}
                ORDER BY  epwp_course_category.sort_order DESC,epwp_course_category.created_at DESC
                {$limit}";

        $rows = $this->db->prepare_query($sql,$condition_work);
        if (false === $rows)
        {
            return false;
        }

        # 符合条件的数量
        $sql_count = "SELECT count(*) as total 
                FROM epwp_course_category
                {$where} ";
        $total = $this->db->prepare_query($sql_count,[],['get' => 'row']);
        if (false === $total)
        {
            return false;
        }

        $return_data = [
            'total' => $total['total'],
            'rows'  => $rows
        ];

        return $return_data;
        }
        
    /**
     * 获取分类信息
     */
    public function getCourseCategoryInfo($category_id){
        $where = 'WHERE  epwp_course_category.is_delete =0';
        $where .= ' AND epwp_course_category.category_id=? ';
        $condition_work[] = $category_id;
        $sql = "SELECT
                        epwp_course_category.category_id, epwp_course_category.category_name, epwp_course_category.create_uid,epwp_course_category.create_user_name,epwp_course_category.sort_order,parent_course_category.category_name as parent_category_name,parent_course_category.category_id as parent_category_id
                     FROM epwp_course_category 
                     LEFT JOIN epwp_course_category as parent_course_category  ON parent_course_category.category_id=epwp_course_category.parent_category_id and parent_course_category.is_delete=0
                {$where} ";
        $row = $this->db->prepare_query($sql, $condition_work, ['get' => 'row']);
        return $row;
        }
        
    /**
     * 新增分类
     */
    public function addNewCourseCategory($category_name,$sort_order,$create_uid,$create_username,$parent_id=0){
        $insert_arr['category_name'] = $category_name;
        $insert_arr['create_uid']  = $create_uid;
        $insert_arr['create_user_name']  = $create_username;
        $insert_arr['sort_order']  = $sort_order;
        $insert_arr['updated_at']  = date('Y-m-d H:i:s');
        $insert_arr['update_uid']  =   $create_uid;
        $insert_arr['update_user_name']  =   $create_username;
        $insert_arr['is_delete']  =  0;
        $insert_arr['parent_category_id']  = $parent_id;
        return $this->db->insert($this->getTable(), $insert_arr); 
        }

   /**
     * 编辑分类
     */
    public function modifyCourseCategory($category_id,$category_name,$sort_order,$create_uid,$create_username,$parent_id=0){
        $update_arr['category_name'] = $category_name;
        $update_arr['update_uid']  = $create_uid;
        $update_arr['update_user_name']  = $create_username;
        $update_arr['sort_order']  = $sort_order;
        $update_arr['updated_at']  = date('Y-m-d H:i:s');
        $update_arr['parent_category_id']  = $parent_id;
        return  $this->db->update($this->getTable(), $update_arr, ['`category_id` =' => $category_id]);
    }

   /**
     * 删除分类
     */
    public function deleteCourseCategory($category_id,$create_uid,$create_username){
        $update_arr['update_uid']  = $create_uid;
        $update_arr['update_user_name']  =   $create_username;
        $update_arr['updated_at']  = date('Y-m-d H:i:s');
        $update_arr['is_delete']  =  1;
        $update_arr['deleted_at']  =   date('Y-m-d H:i:s');
        return  $this->db->update($this->getTable(), $update_arr, ['`category_id` =' => $category_id,'is_delete='=>0]);
    }

     /**
     * 通过别名获取信息
     */
    public function getCourseCategoryBySlug($slug){
       return  $this->db->select_row($this->getTable(),['slug=' => $slug],['fields' => 'category_id,category_name']);
      }
       /**
     * 通过名称获取信息
     */
    public function getCourseCategoryByName($category_name){
       return  $this->db->select_row($this->getTable(),['category_name=' => $category_name,'is_delete='=>0],['fields' => 'category_id,category_name']);
      }
    
}