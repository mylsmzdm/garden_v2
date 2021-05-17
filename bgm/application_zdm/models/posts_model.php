<?php
class posts_model extends MY_Model
{
    protected function getTable()
    {
        return 'epwp_posts';
    }

    /**
     * 获取分类列表
     */
    public function getTermsList($term_ids,$page=1,$page_size=20){
        $condition_work = [];
        # 分页
        if (!empty($page) && !empty($page_size))
        {
            $limit = " LIMIT ?,?";
            $condition_work[] = ($page-1)*$page_size;
            $condition_work[] = $page_size;
        }
        $where_term_ids  =  trim(implode(",", $term_ids));
        $where =  " WHERE epwp_terms.term_id in  ({$where_term_ids})";
        
        $sql = "SELECT
                        epwp_terms.term_id, epwp_terms.name, epwp_terms.slug,epwp_terms.term_group,epwp_terms.create_uid,epwp_terms.create_user_name,epwp_terms.sort_order,parent_terms.name as parent_name,parent_terms.term_id as parent_term_id
                     FROM epwp_terms 
                LEFT JOIN epwp_term_taxonomy  ON epwp_term_taxonomy.term_id=epwp_terms.term_id
                LEFT JOIN epwp_terms as parent_terms  ON parent_terms.term_id=epwp_term_taxonomy.parent
                {$where}
                ORDER BY  epwp_terms.sort_order DESC,epwp_terms.created_at DESC
                {$limit}";
                
        $rows = $this->db->prepare_query($sql,$condition_work);
        if (false === $rows)
        {
            return false;
        }

        # 符合条件的数量
        $sql_count = "SELECT count(*) as total 
                FROM epwp_terms
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
    public function getTermsInfo($term_id){
        $where = ' epwp_terms.term_id=?';
        $condition_work[] = $term_id;
        $sql = "SELECT
                        epwp_terms.term_id, epwp_terms.name, epwp_terms.slug,epwp_terms.term_group,epwp_terms.create_uid,epwp_terms.create_user_name,epwp_terms.sort_order,parent_terms.name as parent_name,parent_terms.term_id as parent_term_id
                     FROM epwp_terms 
                LEFT JOIN epwp_term_taxonomy  ON epwp_term_taxonomy.term_id=epwp_terms.term_id
                LEFT JOIN epwp_terms as parent_terms  ON parent_terms.term_id=epwp_term_taxonomy.parent
                {$where} ";

        $row = $this->db->prepare_query($sql, $condition_work, ['get' => 'row']);
        return $row;
        }
        
        
    /**
     * 新增分类
     */
    public function addNewTerms($name,$slug,$sort_order,$create_uid,$create_username){
        $insert_arr['name'] = $name;
        $insert_arr['slug '] = $slug;
        $insert_arr['create_uid']  = $create_uid;
        $insert_arr['create_user_name']  = $create_username;
        $insert_arr['sort_order']  = $sort_order;
        $insert_arr['updated_at']  = date('Y-m-d H:i:s');
        $insert_arr['created_at']  = $this->updated_at;
        return $this->db->insert($this->getTable(), $insert_arr); 
        }

   /**
     * 编辑分类
     */
    public function modifyTerms($term_id,$name,$slug,$sort_order,$create_uid,$create_username){
        $update_arr['name'] = $name;
        $update_arr['slug '] = $slug;
        // $update_arr['create_uid']  = $create_uid;
        // $update_arr['create_user_name']  = $create_username;
        $update_arr['sort_order']  = $sort_order;
        $update_arr['updated_at']  = date('Y-m-d H:i:s');
        return  $this->db->update($this->getTable(), $update_arr, ['`term_id` =' => $term_id]);
    }

   /**
     * 删除分类
     */
    public function deleteTerms($id){
        return $this->db->where('term_id', $id)->delete($this->getTable());
    }

     /**
     * 通过别名获取信息
     */
    public function getTermsBySlug($slug){
       return  $this->db->select_row($this->getTable(),['slug=' => $slug],['fields' => 'term_id,name']);
      }
       /**
     * 通过名称获取信息
     */
    public function getTermsByName($name){
       return  $this->db->select_row($this->getTable(),['name=' => $name],['fields' => 'term_id,name']);
      }
    
}