<?php
/**
 * 职场service
 */
class zc_biz extends CI_Biz
{
    static $department_info = [];#存储部门数据，在循环处理部门数据时，减少请求数据库次数
    public function __construct()
    {
        $this->load->model(['zc_model']);
    }

   /**
    * 获取职场列表
    */
    public function get_zc_list()
    {
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $result = $this->zc_model->get_zc_list();
        if(FALSE === $result){
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }
        if(empty($result)){
            goto ARCHOR_RESULT;
        }
        foreach($result as $key=>$item){
            $item['device_list'] = [];
            if($item['device_address']){
                $device_list = json_decode($item['device_address'],true);
                if(is_array($device_list) && count($device_list)>0){
                    $item['device_list'] = $device_list;
                }
            }
            unset($item['device_address']);
            $result[$key] = $item;
        }

        $r['data'] = $result;
        ARCHOR_RESULT:
        return $r;
    }


    /**
     * 添加/更新职场
     */
    public function  add_or_update_zc_info($id,$zc_name,$zc_deital_address,$longitude,$latitude,$device_address=''){
        $device_list = [];
        if($device_address){
            $device_list = explode(',',$device_address);
            $device_list = array_unique(array_filter($device_list));
        }

        $device_address = '';
        if(is_array($device_list) && count($device_list)>0){
            $device_address = json_encode($device_list);
        }
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];

        if(empty($id)){
            $zc_info = $this->zc_model->get_zc_by_name($zc_name);
            if($zc_info){
                $r['error_code'] = ERROR_PARAMS;
                $r['error_msg'] = '职场已经存在,请勿重复添加~';
                goto ARCHOR_RESULT;
            }
            $res  = $this->zc_model->add_zc($zc_name,$zc_deital_address,$longitude,$latitude,$device_address);
        }else{
            $res  = $this->zc_model->update_zc($id, $zc_name,$zc_deital_address,$longitude,$latitude,$device_address);
        }
        if(FALSE === $res){
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }
        if(empty($result)){
            goto ARCHOR_RESULT;
        }
        
        $r['data'] = $res;
        ARCHOR_RESULT:
        return $r;
    }

     /**
     * 删除职场
     */
    public function  delete_zc($id){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $res  = $this->zc_model->delete_zc($id);
        if(FALSE === $res){
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }
        if(empty($res)){
            goto ARCHOR_RESULT;
        }
        
        $r['data'] = $res;
        ARCHOR_RESULT:
        return $r;
    }

    /**
     * 通过主键id获取职场信息
     */
    public function get_zc_by_id($zc_id){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $res  = $this->zc_model->get_zc_by_id($zc_id);
        if(FALSE === $res){
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }
        if(empty($res)){
            goto ARCHOR_RESULT;
        }
        
        $res['device_list'] = [];
        if($res['device_address']){
            $device_list = json_decode($res['device_address'],true);
            if(is_array($device_list) && count($device_list)>0){
                $res['device_list'] = $device_list;
            }
        }
        unset($res['device_address']);
        $r['data'] = $res;
        ARCHOR_RESULT:
        return $r;
    }
}