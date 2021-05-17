<?php
/**
 * department_acl_model
 *
 * @author  liuchenlin
 * @time    2017-12-11
 */

class call_model extends MY_Model
{
    public function __construct() {
        $this->db = $this->load->mysql("sos");
    }

    /**
    * 添加报警记录
     */
    public function add_call_record($zc_id,$zc_floor,$call_user_id,$call_user_name,$call_user_mobile,$call_user_job_number,$call_user_email,$call_user_work_place,$call_user_sex)
    {
        if(!$zc_id || !$call_user_id){
            return false;
        }

        $data['zc_id'] = $zc_id;
        $data['zc_floor'] = $zc_floor;
        $data['call_user_id'] = $call_user_id;
        $data['call_user_name'] = $call_user_name;
        $data['call_user_mobile'] = $call_user_mobile;
        $data['call_user_job_number'] = $call_user_job_number;
        $data['call_user_email'] = $call_user_email;
        $data['call_user_work_place'] = $call_user_work_place;
        $data['call_user_sex'] = $call_user_sex;
        $data['create_time'] = time();
        $data['update_time'] = time();
        $res = $this->db->insert('sos_call_record', $data);
        return $res;
    }  
    
    /**
    * 添加通知记录
     */
    public function add_notice_record($insert_data)
    {
        if(!$insert_data || !is_array($insert_data) || count($insert_data)==0){
            return false;
        }
        $res = $this->db->insert_batch('sos_send_notcie_record', $insert_data);
        return $res;
    }

    /**
     * 获取信息
     */
    public function get_call_list($page=1,$page_size=20,$zc_name='',$call_date_time=''){
        $sql  = ' 
                select
                sos_call_record.id,zc_id,zc_floor,call_user_id,call_user_name,call_user_mobile,call_user_job_number,call_user_email,call_user_work_place,call_user_sex,sos_call_record.create_time,sos_zc.zc_name
                from 
                    sos_call_record 
                left join sos_zc on sos_zc.id = sos_call_record.zc_id
                where 
                    sos_call_record.deleted=0
               ';
        $where = [];
        if ($zc_name) {
            $sql .= ' and sos_zc.zc_name like ?';
            $where[] = '%'.$zc_name.'%';
        }
        if ($call_date_time) {
            $sql .= ' and sos_call_record.create_time>=?';
            $where[] = strtotime($call_date_time);

            $sql .= ' and sos_call_record.create_time<=?';
            $where[] = strtotime($call_date_time . ' 23:59:59');
        }

        $sql.='  order by sos_call_record.create_time desc ';
        if ($page > 0 && $page_size > 0) {
            $sql .= ' limit ?,? ';
            $where[] = $page_size * ($page - 1);
            $where[] = $page_size;
        }

        $res = $this->db->prepare_query($sql, $where);
        return $res;
    } 
    /**
     * 获取个数
     */
    public function get_call_count($zc_name='',$call_date_time=''){
        $sql  = ' 
                select
                    count(*) as count
                from 
                    sos_call_record 
                 left join sos_zc on sos_zc.id = sos_call_record.zc_id
                where 
                    sos_call_record.deleted=0 ';
        $where = [];
        if ($zc_name) {
            $sql .= ' and sos_zc.zc_name like ?';
            $where[] = '%' . $zc_name . '%';
        }
        if ($call_date_time) {
            $sql .= ' and sos_call_record.create_time>=?';
            $where[] = strtotime($call_date_time);

            $sql .= ' and sos_call_record.create_time<=?';
            $where[] = strtotime($call_date_time . ' 23:59:59');
        }
            $res = $this->db->prepare_query($sql,$where,['get'=>'row']);
             return $res;
    }

      /**
     * 获取信息
     */
    public function get_notice_list($call_id,$page=1,$page_size=20,$zc_name='',$call_date_time=''){
        $sql  = ' 
                select
                receive_user_name,
                receive_user_job_number,
                receive_user_work_place,
                receive_user_sex,
                receive_user_mobile
                from 
                    sos_send_notcie_record 
                where 
                    sos_send_notcie_record.deleted=0
                    and sos_send_notcie_record.call_id=? ';
        $where[] = $call_id;
        if ($zc_name) {
            $sql .= ' and receive_user_work_place like ?';
            $where[] = '%'.$zc_name.'%';
        }
        if ($call_date_time) {
            $sql .= ' and sos_send_notcie_record.create_time>=?';
            $where[] = strtotime($call_date_time);

            $sql .= ' and sos_send_notcie_record.create_time<=?';
            $where[] = strtotime($call_date_time . ' 23:59:59');
        }

        $sql .= ' group by
        receive_user_name,
        receive_user_job_number,
        receive_user_work_place,
        receive_user_sex,
        receive_user_mobile,
        create_time
         order by sos_send_notcie_record.create_time desc ';
        if ($page > 0 && $page_size > 0) {
            $sql .= '  limit ?,? ';
            $where[] = $page_size * ($page - 1);
            $where[] = intval($page_size);
        }

        $res = $this->db->prepare_query($sql, $where);
        return $res;
    } 
    /**
     * 获取信息
     */
    public function get_notice_all_zc_list($call_id){
        $sql  = ' 
                select
                    receive_user_work_place
                from 
                    sos_send_notcie_record 
                where 
                    sos_send_notcie_record.deleted=0
                    and sos_send_notcie_record.call_id=? 
                    group by receive_user_work_place
                    ';
        $where[] = $call_id;
        $res = $this->db->prepare_query($sql, $where);
        return $res;
    }  
    
    /**
     * 获取信息
     */
    public function get_call_all_zc_list(){
        $sql  = ' 
                select
                    sos_zc.zc_name 
                from 
                    sos_call_record 
                left join sos_zc on sos_zc.id = sos_call_record.zc_id
                where 
                        sos_call_record.deleted=0
                    group by zc_name
                    ';
        $res = $this->db->prepare_query($sql);
        return $res;
    } 
    
    /**
     * 获取信息
     */
    public function get_notice_count($call_id,$zc_name='',$call_date_time=''){
        $sql  = ' 
                select
                    count(*) as count
                from 
                    sos_send_notcie_record 
                where 
                    sos_send_notcie_record.deleted=0
                    and sos_send_notcie_record.call_id=? ';
        $where[] = $call_id;
        if ($zc_name) {
            $sql .= ' and receive_user_work_place like ?';
            $where[] = '%'.$zc_name.'%';
        }

        if ($call_date_time) {
            $sql .= ' and sos_send_notcie_record.create_time>=?';
            $where[] = strtotime($call_date_time);

            $sql .= ' and sos_send_notcie_record.create_time<=?';
            $where[] = strtotime($call_date_time . ' 23:59:59');
        }

        $res = $this->db->prepare_query($sql, $where, ['get' => 'row']);
        return $res;
    } 
}