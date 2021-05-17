<?php
/**
 * 报警处理
 */
class call_biz extends CI_Biz
{
    public function __construct()
    {     
        $this->load->model(['zc_model','call_model','ding_model']);
    }

   /**
    * 处理报警信息
    */
    public function handle_call($zc_id,$zc_floor,$call_user_id,$app_id=1)
    {
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];

        $zc_info  = $this->zc_model->get_zc_by_id($zc_id);
        $zc_name = '';
        if($zc_info){
            $zc_name = $zc_info['zc_name'];
        }
     
        $useridList = [$call_user_id];
        $this->load->biz(['dy_biz']);
        $dy_list = $this->dy_biz->get_dy_list($page=1,$page_size=PHP_INT_MAX,$id = 1, $app_id = 1,$name='',$jobNumber='',$zc_name,$sex=-1);
        if(FALSE === $dy_list){
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }

        $user_list = $dy_list['data']['user_list'];
        $userids = array_column($user_list,'userid');
        $useridList = array_merge($useridList,$userids);

        $this->load->config('ding');
        $ding_config = $this->config->item('ding');
        $call_user_mobile_list = $ding_config['chat']['call_user'];

        $receivecalluserids = [];
        foreach($call_user_mobile_list as $call_user_mobile){
            $receive_call_user_info = $this->ding_model->get_user_info_by_mobile($app_id, $call_user_mobile);
            $receive_call_user_id = 0;
            if (FALSE === $receive_call_user_info) {
                $r['error_code'] = DATABASE_FALSE;
                $r['error_msg'] = '钉钉接口请求报错';
                goto ARCHOR_RESULT;
            }
            $receive_call_user_id = $receive_call_user_info['result']['userid'];
            $receivecalluserids [] = $receive_call_user_id;
        }

        $useridList = array_merge($useridList,$receivecalluserids);
        $useridList = array_unique($useridList);
        $ding_user_list_info = $this->ding_model->get_user_list_by_userids(implode(',',$useridList),implode(',',array_keys(dy_biz::DING_MAPPING)));
        if (FALSE === $ding_user_list_info) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '钉钉接口请求报错';
            goto ARCHOR_RESULT;
        }

        $call_user_name = '';
        $call_user_mobile = '';
        $call_user_job_number ='';
        $call_user_email = '';
        $call_user_zc_name = '';
        $call_user_sex_type= -1;
        $call_user_list = [];
        $ding_user_list = [];
        $user_list_result = $ding_user_list_info['result'];
        $notice_data = [];
        $call_data_list = [];
        foreach ($user_list_result as $ding_user_info) {
            $field_list = $ding_user_info['field_list'];
            $item = [
                'name' => '',
                'email' => '',
                'mobile' => '',
                'job_number' => '',
                'zc_name' => '',
                'sex_type' => -1,
                'userid' => $ding_user_info['userid'],
            ];
            foreach ($field_list as $filed_info) {
                $filed_code = $filed_info['field_code'];
                $key = dy_biz::DING_MAPPING[$filed_code];
                if(array_key_exists('value',$filed_info)){
                    $item[$key] = $filed_info['value'];
                }
            }
            $userList[] = $item;

            if (in_array($item['userid'], $receivecalluserids) && $item['userid'] != $call_user_id) {
                $call_user_list[] = $item;
                $call_data = [];
                $call_data['receive_user_id'] = $item['userid'];
                $call_data['receive_user_name'] = $item['name'];
                $call_data['receive_user_mobile'] = $item['mobile'];
                $call_data['receive_user_job_number'] = $item['job_number'];
                $call_data['receive_user_email'] = $item['email'];
                $call_data['receive_user_work_place'] = $item['zc_name'];
                $call_data['receive_user_sex'] = $item['sex_type'];
                $call_data_list[] = $call_data;
            }
            
            if(in_array( $item['userid'],$userids) || in_array($item['userid'], $receivecalluserids)){
                $ding_user_list[] = $item;
                $data = [];
                $data['receive_user_id'] = $item['userid'];
                $data['receive_user_name'] = $item['name'];
                $data['receive_user_mobile'] = $item['mobile'];
                $data['receive_user_job_number'] = $item['job_number'];
                $data['receive_user_email'] = $item['email'];
                $data['receive_user_work_place'] = $item['zc_name'];
                $data['receive_user_sex'] = $item['sex_type'];
                $notice_data[] = $data;
            }
           
            //添加职场信息
            if ($item['zc_name']) {
                $this->load->model('zc_model');
                $zc_info = $this->zc_model->get_zc_by_name($item['zc_name']);
                if (!$zc_info) {
                    $this->zc_model->add_zc($item['zc_name'], '', '', '', '', 2);
                }
            }

            if( $item['userid']==$call_user_id){
                $call_user_name = $item['name'];
                $call_user_mobile = $item['mobile'];
                $call_user_job_number = $item['job_number'];
                $call_user_email = $item['email'];
                $call_user_zc_name = $item['zc_name'];
                $call_user_sex_type= $item['sex_type'];
            }
        }
      
        $call_id = $this->call_model->add_call_record($zc_id,$zc_floor,$call_user_id,$call_user_name,$call_user_mobile,$call_user_job_number ,$call_user_email ,$call_user_zc_name ,$call_user_sex_type);
        if(FALSE === $call_id){
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '保存失败';
            goto ARCHOR_RESULT;
        }
        $content= $call_user_name;
        if($call_user_mobile){
            $content .= '('.$call_user_mobile.')';
        }
        $content .= '发现'.$zc_name.$zc_floor.'层有人倒地，请快速前往施救，如能赶到回复“1”。';
         $webhook = $dy_list['data']['chat_info']['webhook'];
        if ($webhook) {
            //机器人发送文案
            $this->load->model('ding_api');
            $isAtAll=false;
            $atMobiles=array_column($notice_data,'receive_user_mobile');
            $result = $this->ding_api->robot_send_text($webhook,$content,$atMobiles,$isAtAll);

            //机器人通知
            $errmsg = '';
            $result = 1;
            if(!$result ){
                $result = 2;
                $errmsg = '请求钉钉出错';
            }else if($result['errcode']!=0){
                $result = 2;
                $errmsg = $result['errmsg'];
            }

            $arr = [
                'call_id'=>$call_id,
                'notice_type' => 1,
                'notice_content'=> $content,
                'notice_result'=> $result,
                'notice_fail_reason'=> $errmsg,
                'create_time' => time(),
                'update_time' => time(),
            ];
            array_walk($notice_data, function (&$value, $key, $arr) {
                $value = array_merge($value, $arr);
                },$arr);
              $this->call_model->add_notice_record($notice_data);
        }

        //通知拨打电话的人员
        $arr2 = [
                'call_id'=>$call_id,
                'notice_type' => 2,
                'notice_content'=> $content,
                'notice_result'=> 1,
                'notice_fail_reason'=> '',
                'create_time' => time(),
                'update_time' => time(),
            ];
            array_walk($call_data_list, function (&$value, $key, $arr2) {
                $value = array_merge($value, $arr2);
                },$arr2);
              $this->call_model->add_notice_record($call_data_list);
        
        // $r['data']['robot_user_list'] = $user_list;
        $r['data'][  'corpid']= $dy_list['data']['corpid'];
        $r['data'][  'call_user_list']= array_column($call_user_list,'userid') ;
        $r['data'][  'ding_text']= $content;
        $r['data'][  'ding_user_list']= array_column($ding_user_list,'userid') ;
        $r['data'][  'ding_alert_date']= date('Y-m-d H:i:s');
        $r['data'][  'call_id']= $call_id;
        ARCHOR_RESULT:
        return $r;
    }

    public function get_ding_ticket($url,$app_id=1){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];

        if (!$url) {
            $r['error_code'] = ERROR_PARAMS;
            $r['error_msg'] = '参数错误';
            goto ARCHOR_RESULT;
        }
    
        $result = $this->ding_model->get_ding_ticket($url,$app_id);
        if(FALSE === $result){
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '保存失败';
            goto ARCHOR_RESULT;
        }

        $r['data'] = $result;
        ARCHOR_RESULT:
        return $r;  
    }
    
    public function get_user_info($code,$app_id=1){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];

        if (!$code) {
            $r['error_code'] = ERROR_PARAMS;
            $r['error_msg'] = '参数错误';
            goto ARCHOR_RESULT;
        }
    
        $result = $this->ding_model->get_user_info_by_code($code,$app_id);
        if(FALSE === $result){
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '钉钉接口请求报错';
            goto ARCHOR_RESULT;
        }

        $userid = $result['result']['userid'];
        $useridList = [$userid];

        $this->load->biz(['dy_biz']);
        $ding_user_list_info = $this->ding_model->get_user_list_by_userids(implode(',',$useridList),implode(',',array_keys(Dy_biz::DING_MAPPING)));
        if (FALSE === $ding_user_list_info) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '钉钉接口请求报错';
            goto ARCHOR_RESULT;
        }

        $userList = [];
        $use_list_result = $ding_user_list_info['result'];
        foreach ($use_list_result as $ding_user_info) {
            $field_list = $ding_user_info['field_list'];
            $item = [
                'name'=>'',
                'email'=>'',
                'mobile'=>'',
                'job_number'=>'',
                'zc_name'=>'',
                'sex_type'=>-1,
                'userid'=>$ding_user_info['userid'],
            ];
            foreach($field_list as $filed_info){
                $filed_code = $filed_info['field_code'];
                $key = Dy_biz::DING_MAPPING[$filed_code];
                if(array_key_exists('value',$filed_info)){
                    $item[$key] = $filed_info['value'];
                }
            }
       
            $userList[] = $item;

            //添加职场信息
            $this->load->model('zc_model');
            $zc_info = $this->zc_model->get_zc_by_name($item['zc_name']);
            if (!$zc_info) {
                $this->zc_model->add_zc($item['zc_name'], '', '', '', '', 2);
            }
      }

        $r['data'] = $userList[0];
        ARCHOR_RESULT:
        return $r;  
    }

    /**
     * 添加通知信息
     */
    public function add_notice($call_id,$notice_user_id,$notice_content,$notice_type,$notice_result=1,$notice_fail_reason=''){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];

        $useridList = [$notice_user_id];
        $this->load->biz(['dy_biz']);
        $ding_user_list_info = $this->ding_model->get_user_list_by_userids(implode(',',$useridList),implode(',',array_keys(dy_biz::DING_MAPPING)));
        if (FALSE === $ding_user_list_info) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '钉钉接口请求报错';
            goto ARCHOR_RESULT;
        }
    
        $user_list_result = $ding_user_list_info['result'];
        $notice_data = [];
        foreach ($user_list_result as $ding_user_info) {
            $field_list = $ding_user_info['field_list'];
            $item = [
                'name'=>'',
                'email'=>'',
                'mobile'=>'',
                'job_number'=>'',
                'zc_name'=>'',
                'sex_type'=>-1,
                'userid'=>$ding_user_info['userid'],
            ];
            foreach($field_list as $filed_info){
                $filed_code = $filed_info['field_code'];
                $key = dy_biz::DING_MAPPING[$filed_code];
                if(array_key_exists('value',$filed_info)){
                    $item[$key] = $filed_info['value'];
                }
            }
            $userList[] = $item;
            //添加职场信息
            $this->load->model('zc_model');
            $zc_info = $this->zc_model->get_zc_by_name($item['zc_name']);
            if (!$zc_info) {
                $this->zc_model->add_zc($item['zc_name'], '', '', '', '', 2);
            }
            $data = [];
            $data['receive_user_id'] = $item['userid'];
            $data['receive_user_name'] = $item['name'];
            $data['receive_user_mobile'] = $item['mobile'];
            $data['receive_user_job_number'] = $item['job_number'];
            $data['receive_user_email'] = $item['email'];
            $data['receive_user_work_place'] = $item['zc_name'];
            $data['receive_user_sex'] = $item['sex_type'];
            $notice_data[] = $data;
      }
        $arr = [
            'call_id' => $call_id,
            'notice_type' => $notice_type,
            'notice_content' => $notice_content,
            'notice_result' => $notice_result,
            'notice_fail_reason' => $notice_fail_reason,
            'create_time' => time(),
            'update_time' => time(),
        ];

        array_walk($notice_data, function (&$value, $key, $arr) {
            $value = array_merge($value, $arr);
        }, $arr);
        $add = $this->call_model->add_notice_record($notice_data);
        if (FALSE === $add) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '钉钉接口请求报错';
            goto ARCHOR_RESULT;
        }
        ARCHOR_RESULT:
        return $r;  
    }

    /**
     * 报警记录
     */
    public function call_list($page=1,$page_size=20,$zc_name='',$call_date_time=''){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $list = $this->call_model->get_call_list($page,$page_size,$zc_name,$call_date_time);
        if (FALSE === $list) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        } 
        
        $count_info = $this->call_model->get_call_count($zc_name,$call_date_time);
        if (FALSE === $count_info) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }
        foreach($list as $key=>$item){
            $list[$key]['create_time'] =  date('Y-m-d H:i:s',$item['create_time']);
        }

        $zc_list = $this->call_model->get_call_all_zc_list();
        if (FALSE === $zc_list) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }

        $count = $count_info['count'];
        $r['data'] ['list'] = $list;
        $r['data'] ['count'] = $count;
        $r['data'] ['zc_list'] = array_column( $zc_list,'zc_name');

        if($page_size<=0){
            $page_size = $count;
        }

        $total_pages = 1;
        if($page_size>0){
            $total_pages = ceil($count/$page_size);
        }
        $r['data'] ['total_pages'] = $total_pages;
        ARCHOR_RESULT:
        return $r;  
    }
    
    /**
     * 通知记录
     */
    public function get_notice_list($call_id,$page=1,$page_size=20,$zc_name='',$call_date_time=''){
      
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];

        $list = $this->call_model->get_notice_list($call_id,$page,$page_size,$zc_name,$call_date_time);
        if (FALSE === $list) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        } 

        $this->load->config('ding');
        $ding_config = $this->config->item('ding');
        $call_user_mobile_list = $ding_config['chat']['call_user'];
        foreach($list as $key=>$item){
            $receive_user_mobile = $item['receive_user_mobile'];
            $real_mobile  = substr( $receive_user_mobile,-11);
            if(in_array($real_mobile,$call_user_mobile_list)){
                $item['receive_user_name'] .= '-健康师';
            }
            $list[$key] = $item;
        }

        $zc_list = $this->call_model->get_notice_all_zc_list($call_id);
        if (FALSE === $zc_list) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }


        $count_info = $this->call_model->get_notice_count($call_id,$zc_name,$call_date_time);
        if (FALSE === $count_info) {
            $r['error_code'] = DATABASE_FALSE;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }

        $count = $count_info['count'];
        $r['data'] ['list'] = $list;
        $r['data'] ['count'] = $count;
        $r['data'] ['zc_list'] = array_column( $zc_list,'receive_user_work_place');

        if($page_size<=0){
            $page_size = $count;
        }

        $total_pages = 1;
        if($page_size>0){
            $total_pages = ceil($count/$page_size);
        }
        $r['data'] ['total_pages'] = $total_pages;
        ARCHOR_RESULT:
        return $r;  
    }
}