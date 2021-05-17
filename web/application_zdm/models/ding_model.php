<?php
/**
 * department_acl_model
 *
 * @author  liuchenlin
 * @time    2017-12-11
 */

class Ding_model extends MY_Model
{
    public function __construct() {
        $this->db = $this->load->mysql("sos");
    }

    /**
     * 获取信息
     */
    public function get_app_info($app_id=1){
        $sql  = ' 
        select
            agent_id,appkey,appsecret,access_token,access_token_expire_time,jsapi_ticket,jsapi_ticket_expire_time,corpid
        from 
            sos_app_info
        where 
            deleted=0
            and id =?
         ';
        $res = $this->db->prepare_query($sql,['id'=>$app_id],['get'=>'row']);
        return $res;
      }
    
    /**
     * 获取access_token
     */
    public function get_access_token($app_id=1){
            $info  = $this->get_app_info($app_id);
            if($info['access_token'] && $info['access_token_expire_time']>time() ){
                return $info['access_token'];
            }

            $appKey = $info['appkey'];
            $appSecret = $info['appsecret'];
            $this->load->model('ding_api');
            $access_token_info = $this->ding_api->get_access_token($appKey,$appSecret);
            if($access_token_info && $access_token_info['errcode']==0){
                $access_token = $access_token_info['access_token'];
                $access_token_expire_time= $access_token_info['expires_in']+time();
                $data['access_token'] = $access_token;
                $data['access_token_expire_time'] = $access_token_expire_time;
                $this->update_app_info($app_id,$data);
                return $access_token;
            }

            return false;
    }  
    
    /**
     * 获取js_ticket
     */
    public function get_jsapi_ticket($app_id=1){
            $info  = $this->get_app_info($app_id);
            if($info['jsapi_ticket'] && $info['jsapi_ticket_expire_time']>time() ){
                return $info['jsapi_ticket'];
            }

            $access_token = $this->get_access_token($app_id);
            if(!$access_token){
                return false;
            }
            $this->load->model('ding_api');
            $jsapi_ticket_info = $this->ding_api->get_jsapi_ticket($access_token);
            if($jsapi_ticket_info && $jsapi_ticket_info['errcode']==0){
                $ticket = $jsapi_ticket_info['ticket'];
                $ticket_expire_time= $jsapi_ticket_info['expires_in']+time();
                $data['jsapi_ticket'] = $ticket;
                $data['jsapi_ticket_expire_time'] = $ticket_expire_time;
                $this->update_app_info($app_id,$data);
                return $ticket;
            }

            return false;
    }

    /**
     * 更新钉钉信息
     */
    public function update_app_info($app_id,$data){
        if (!is_array($data) || count($data) == 0 || intval($app_id)==0) {
            return false;
        }

        $data['update_time'] = time();
        $where_conds = [
            '`id`=' => $app_id,
        ];
        $result = $this->db->update('`sos_app_info`', $data, $where_conds);
        if(FALSE === $result){
            return FALSE;
        }
        return $result;
    }
    
    /**
     * 更新钉钉用户信息
     */
    public function update_app_user_info($id,$data){
        if (!is_array($data) || count($data) == 0|| intval($id)==0) {
            return false;
        }

        $data['update_time'] = time();
        $where_conds = [
            '`id`=' => $id,
        ];
        $result = $this->db->update('`sos_app_user`', $data, $where_conds);
        if(FALSE === $result){
            return FALSE;
        }
        return $result;
    }
     /**
     * 获取信息
     */
    public function get_app_user_info($userid,$app_id=1){
        $sql  = ' 
        select
            userid,name,job_number
        from 
            sos_user
        where 
            deleted=0
            and id =?
         ';
        $res = $this->db->prepare_query($sql,['`app_id` =' => $app_id,'userid'=>$userid],['get'=>'row']);
        return $res;
    }

    /**
     * 插入操作
     */
    public function add_app_user($data)
    {
        if(!is_array($data) || count($data)==0){
            return false;
        }

        $data['create_time'] = time();
        $data['update_time'] = time();
        $res = $this->db->insert('sos_user', $data);
        return $res;
    }

    /**
     * 获取钉钉jsapi_ticket
     */
    public function get_ding_ticket($url,$app_id=1){
        $app_info = $this->get_app_info($app_id);
        if(!$app_info){
            return false;
        }

        $this->load->model('ding_api');
        $nonceStr = $this->ding_api->get_random_str() ;
        $timeStamp = time();
        $ticket = $this->get_jsapi_ticket($app_id);
        if(!$ticket){
            return false;
        }

        $signature = $this->ding_api->sign($ticket, $nonceStr, $timeStamp, $url);
        $arr = array();
        $arr['ticket'] = $ticket;
        $arr['nonceStr'] = $nonceStr;
        $arr['timeStamp'] = $timeStamp;
        $arr['url'] = $url;
        $arr['signature'] = $signature;

        $config = array(
            'url' => $url,
            'nonceStr' => $nonceStr,
            'agentId' => $app_info['agent_id'],
            'timeStamp' => $timeStamp,
            'corpId' => $app_info['corpid'],
            'signature' => $signature);
            return $config;
        return json_encode($config, JSON_UNESCAPED_SLASHES);
    }

//     {
//         "errcode":0,
//         "access_token":"58d2f51329ad33f19f29cf45311072f1",
//         "errmsg":"ok",
//         "expires_in":7200
//     }
//     SECef857f5c7a786d691b588e467850eea655dc6f5b3e8269d629dd2a1cfd6b7d08

//     https://oapi.dingtalk.com/robot/send?access_token=4b996abbb282476bcd3879892d24b26223e7cb6e7ad1e7c3f6ad7eb7f37d73d1

//     {
//         "errcode":0,
//         "result":{
//             "userid":""
//         },025704215233424993
//         "errmsg":"ok",
//         "request_id":"14iz91jsai2ih"
//     }

// {
// 	"errcode":0,
// 	"chatid":"chat1fbd9a9cbb631ee01da7cc5f69fec29f",
// 	"conversationTag":2,
// 	"errmsg":"ok",
// 	"openConversationId":"cid4LHCxcCJ7ACkVnLAVBspMA=="
// }


// {
// 	"errcode":0,
// 	"errmsg":"ok",
// 	"chat_info":{
// 		"owner":"025704215233424993",
// 		"showHistoryType":0,
// 		"chatid":"chat1fbd9a9cbb631ee01da7cc5f69fec29f",
// 		"validationType":0,
// 		"useridlist":[
// 			"",
// 			""025704215233424993
// 		],132125415827750500
// 		"icon":"@lADPDf0ixtgJ3BzNAljNAlg",
// 		"searchable":0,
// 		"chatBannedType":0,
// 		"managementType":0,
// 		"mentionAllAuthority":0,
// 		"conversationTag":2,
// 		"name":"钉钉SOS急救",
// 		"status":1
// 	}
// }

    /**
     * 获取用户id
     */
    public function get_user_info_by_code($code,$app_id=1){
        $this->load->model('ding_api');
        $access_token = $this->get_access_token($app_id);
        if(!$access_token){
            return false;
        }
        $userInfo = $this->ding_api->get_user_info_by_code($access_token,$code);
        // var_dump($userInfo);
        if (!$userInfo || !is_array($userInfo)||  $userInfo['errcode'] != 0) {
            return false;
        }

        return $userInfo;
        // $result = $userInfo['result'];
        // $userid = $result['userid'];
        // $data['device_id'] = $result['device_id'];
        // $data['sys'] = $result['sys'];
        // $data['sys_level'] = $result['sys_level'];
        // $data['associated_unionid'] = $result['associated_unionid'];
        // $data['unionid'] = $result['unionid'];
        // $data['name'] = $result['name'];
        
        // return $this->get_user_info_by_user_id($userid,$data,$app_id);
    }


    /**
     * 获取用户id
     */
    public function get_user_info_by_user_id($userid,$data=[],$app_id=1){
        $access_token = $this->get_access_token($app_id);
        if(!$access_token){
            return false;
        }

        $app_user_detail_info = $this->ding_api->get_user_info_by_user_id($access_token, $userid);
        if (!$app_user_detail_info || !is_array($app_user_detail_info) ||  $app_user_detail_info['errcode'] != 0) {
            return false;
        }

        $app_user_detail_info_result = $app_user_detail_info['result'];
        $data['userid'] = $app_user_detail_info_result['userid'];
        $data['unionid']  = $app_user_detail_info_result['unionid'];
        $data['name']  = $app_user_detail_info_result['name'];
        $data['avatar']  = $app_user_detail_info_result['avatar'];
        $data['state_code']  = $app_user_detail_info_result['state_code'];
        $data['mobile']  = $app_user_detail_info_result['mobile'];
        $data['hide_mobile']  = $app_user_detail_info_result['hide_mobile'];
        $data['telephone']  = $app_user_detail_info_result['telephone'];
        $data['job_number']   = $app_user_detail_info_result['job_number'];
        $data['title']   = $app_user_detail_info_result['title'];
        $data['email']   = $app_user_detail_info_result['email'];
        $data['work_place']   = $app_user_detail_info_result['work_place'];
        $data['remark']   = $app_user_detail_info_result['remark'];
        $data['dept_id_list']   = $app_user_detail_info_result['dept_id_list'];
        $data['dept_order_list']   = $app_user_detail_info_result['dept_order_list'];
        $data['extension']   = $app_user_detail_info_result['extension'];
        $data['hired_date']   = $app_user_detail_info_result['hired_date'];
        $data['active']   = $app_user_detail_info_result['active'];
        $data['real_authed']   = $app_user_detail_info_result['real_authed'];
        $data['senior']   = $app_user_detail_info_result['senior'];
        $data['admin']   = $app_user_detail_info_result['admin'];
        $data['boss']  = $app_user_detail_info_result['boss'];
        $data['leader_in_dept']   = $app_user_detail_info_result['leader_in_dept'];
        $data['union_emp_ext']   = $app_user_detail_info_result['union_emp_ext'];
        $data['app_id']   = $app_id;

        $app_user = $this->get_app_user_info($userid, $app_id);
        if (!$app_user) {
            $this->add_app_user($data);
        } else {
            $this->update_app_user_info($app_user['id'], $data);
        }

        //添加职场信息
        $this->load->model('zc_model');
        $zc_info = $this->zc_model->get_zc_by_name($data['work_place']);
        if(!$zc_info){
            $this->zc_model->add_zc($data['work_place'],'','','','',2);
        }

        return $this->get_app_user_info($userid,$app_id);
    }

    /**
     * 创建群组
     */
    public function create_chat_group($app_id,$name,$owner,$useridlist,$showHistoryType=1,$searchable=1,$validationType=0,$mentionAllAuthority=0,$managementType=0,$chatBannedType=0)
    {
        $this->load->model('ding_api');
        $access_token = $this->get_access_token($app_id);
        if(!$access_token){
            return false;
        }
        $chat_info = $this->ding_api->create_chat_group($access_token,$name,$owner,$useridlist,$showHistoryType,$searchable,$validationType,$mentionAllAuthority,$managementType,$chatBannedType);
        // var_dump($chat_info);
        if (!$chat_info || !is_array($chat_info)||  $chat_info['errcode'] != 0) {
            return false;
        }

        return  $chat_info;
    } 
    
    /**
     * 创建群组
     */
    public function get_chat_group($chat_id,$app_id=1)
    {
        $this->load->model('ding_api');
        $access_token = $this->get_access_token($app_id);
        if(!$access_token){
            return false;
        }
        $chat_info = $this->ding_api->get_chat_group($access_token,$chat_id);
        if (!$chat_info || !is_array($chat_info)||  $chat_info['errcode'] != 0) {
            return false;
        }

        return  $chat_info;
    }


    /**
     * 通过手机号获取用户信息
     */
    public function get_user_info_by_mobile($app_id,$mobile)
    {
        $this->load->model('ding_api');
        $access_token = $this->get_access_token($app_id);
        if(!$access_token){
            return false;
        }
        
        $user_info = $this->ding_api->get_user_info_by_mobile($access_token,$mobile);
        if (!$user_info || !is_array($user_info)||  $user_info['errcode'] != 0) {
            return false;
        }

        return  $user_info;
    } 
    
    /**
     * 通过userids批量获取用户信息
     */
    public function get_user_list_by_userids($user_ids,$field_filter_list='sys00-name,sys00-email,sys00-mobile,sys00-jobNumber,sys00-workPlace,sys02-sexType',$app_id=1)
    {
        $app_info = $this->get_app_info($app_id);
        if(!$app_info){
            return false;
        }

        $this->load->model('ding_api');
        $access_token = $this->get_access_token($app_id);
        if(!$access_token){
            return false;
        }

        $agentid = $app_info['agent_id'];
        $user_info = $this->ding_api->get_user_list_by_userids($access_token,$user_ids,$field_filter_list,$agentid);
        if (!$user_info || !is_array($user_info)||  $user_info['errcode'] != 0) {
            return false;
        }

        return  $user_info;
    }
}