<?php
/**
 * department_acl_model
 *
 * @author  liuchenlin
 * @time    2017-12-11
 */

class zc_model extends MY_Model
{
    private $table_name = 'sos_zc';
    public function __construct() {
        $this->db = $this->load->mysql("sos");
    }

    /**
     * 获取职场列表
     */
    public function get_zc_list($add_type=0)
    {
        $sql  = ' 
                select
                sos_zc.id,sos_zc.zc_name,sos_zc.zc_detail_address,sos_zc.create_time,sos_zc.device_address,sos_zc.longitude,sos_zc.latitude
                from 
                    sos_zc 
                where 
                    sos_zc.deleted=0 ';
        $where = [];
        if ($add_type) {
            $sql  .= ' and   sos_zc.add_type=? ';
            $where[] = $add_type;
        }

        $sql  .= ' order by create_time desc ';
        $res = $this->db->prepare_query($sql,$where);
        return $res;
    }

    /**
     * 通过职场名称获取职场信息
     */
    public function get_zc_by_name($zc_name)
    {
        $sql  = ' 
                select
                     sos_zc.zc_name,sos_zc.zc_detail_address,sos_zc.create_time,sos_zc.device_address
                from 
                    sos_zc 
                where 
                    sos_zc.deleted=0
                    and zc_name = ?
                 ';
                 $where['zc_name'] = $zc_name;
        $res = $this->db->prepare_query($sql,$where,['get'=>'row']);
        return $res;
    }
    
    /**
     * 通过职场id 获取职场列表
     */
    public function get_zc_by_id($zc_id)
    {
        $sql  = ' 
                select
                     sos_zc.zc_name,sos_zc.zc_detail_address,sos_zc.create_time,sos_zc.device_address,sos_zc.longitude,sos_zc.latitude
                from 
                    sos_zc 
                where 
                    sos_zc.deleted=0
                    and id = ?
                 ';
                 $where['id'] = $zc_id;
        $res = $this->db->prepare_query($sql,$where,['get'=>'row']);
        return $res;
    }


    /**
     * 添加新职场
     */
    public function add_zc($zc_name,$zc_deital_address,$longitude,$latitude,$device_address,$add_type=1){
        if($add_type!=1){
            return true;
        }
        $insert_map= [
            'zc_name'=>$zc_name,
            'zc_detail_address'=>$zc_deital_address,
            'longitude'=>$longitude,
            'latitude'=>$latitude,
            'device_address'=>$device_address,
            'add_type'=>$add_type,
            'create_time'=>time(),
            'update_time'=>time(),
        ];
        $id = $this->db->insert($this->table_name, $insert_map);
        if(FALSE === $id){
            return FALSE;
        }
        return $id;
    }

    /**
     * 更新职场信息
     */
    public function update_zc($id, $zc_name,$zc_deital_address,$longitude,$latitude,$device_address){
        $id = intval($id);
        if (empty($id) || empty($zc_name)) {
            return FALSE;
        }

        $update_map = [
            'zc_name'=>$zc_name,
            'zc_detail_address'=>$zc_deital_address,
            'longitude'=>$longitude,
            'latitude'=>$latitude,
            'device_address'=>$device_address,
            'update_time'=>time(),
        ];
        
        $where_conds = [
            '`id`=' => $id,
        ];
        $result = $this->db->update('`sos_zc`', $update_map, $where_conds);
        if(FALSE === $result){
            return FALSE;
        }
        return $result;
    }

      /**
     * 删除一条职场信息（软删除）
     * @param   int     $id     流水ID
     * @return  int|false
     * @author  Dacheng Chen
     * @time    2017-12-13
     */
    public function delete_zc($id){
        if ($id <= 0) {
            return FALSE;
        }
        $update_map = [
            'deleted'=>1,
            'deleted_time'=>time(),
            'update_time'=>time(),
        ];
        
        $where_conds = [
            '`id`=' => $id,
            '`deleted`=' => 0,
        ];
        $query = $this->db->update('`sos_zc`', $update_map, $where_conds);
        if(FALSE === $query){
            return FALSE;
        }
        return $query;
    }
}