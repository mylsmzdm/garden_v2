<?php
class terms_taxonomy_model extends MY_Model
{
    protected function getTable()
    {
        return 'epwp_term_taxonomy';
    }
    
   /**
     * 新增分类系统
     */
    public function addNewTermsTaxonomy($term_id,$taxonomy,$parent,$description,$count=0){
        $insert_arr['term_id'] = $term_id;
        $insert_arr['taxonomy '] = $taxonomy;
        $insert_arr['parent']  = $parent;
        $insert_arr['description']  = $description;
        $insert_arr['count']  = $count;
        return $this->db->insert($this->getTable(), $insert_arr); 
    }

   /**
     * 编辑分类系统
     */
    public function modifyTermsTaxonomyByTermId($term_taxonomy_id,$term_id,$taxonomy,$parent,$description,$count=0){
        $update_arr['term_id'] = $term_id;
        $update_arr['taxonomy '] = $taxonomy;
        $update_arr['parent']  = $parent;
        $update_arr['description']  = $description;
        $update_arr['count']  = $count;
        return  $this->db->update($this->getTable(), $update_arr, ['`term_taxonomy_id` =' => $term_taxonomy_id]);
    }

   /**
     * 删除分类系统
     */
    public function deleteTermsTaxonomyByTermId($term_id,$taxonomy='category'){
        return  $this->db->delete($this->getTable(), ['`term_id` =' => $term_id,'taxonomy'=>$taxonomy]);
    }

      /**
     * 通过父类获取子类信息
     */
    public function getTermIdsByParentId($parent_id,$taxonomy='category'){
        return  $this->db->select($this->getTable(),['parent=' => $parent_id,'taxonomy'=>$taxonomy],['fields' => 'term_taxonomy_id','term_id','name']);
    }
      
     /**
     * 通过terms_id获取信息
     */
    public function getTermsTaxonomyByTermId($term_id,$taxonomy='category'){
        return  $this->db->select_row($this->getTable(),['term_id=' => $term_id,'taxonomy'=>$taxonomy],['fields' => 'term_taxonomy_id','term_id,name']);
    }

    /**
     * 通过多个父类获取子类信息
     */
    public function getTermIdsByParentIds($parent_ids){
        $where_arr[] = '`parent`in (?)';
        $bind_conds[] = implode(',',$parent_ids);
        $where_sql = !empty($where_arr) ? ' WHERE '.implode(' AND ', $where_arr) : '';
        $sql = 'SELECT term_taxonomy_id, epwp_term_taxonomy.term_id, epwp_terms.name,epwp_term_taxonomy.parent
                     FROM `'.$this->getTable().'` 
                LEFT JOIN `epwp_terms` ON epwp_terms.term_id=epwp_term_taxonomy.term_id'.
                $where_sql;
        $rows = $this->db->prepare_query($sql, $bind_conds);
        if(false === $rows){
            return false;
        }
        return $rows;
      } 


}