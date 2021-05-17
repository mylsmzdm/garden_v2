<?php
/**
 * 钉钉API接口
 */
class Ding_api extends CI_Model{
    function __construct() {
        $this->load->library('http');
        $this->load->library('cat');
    }
    
    /**
     * 获取access_token
     */
    public function get_access_token($appKey,$appSecret){
        $api_url = Config::$url['ding_api'] . "/gettoken";
        $data=[
            'appkey'=>$appKey,
            'appsecret'=>$appSecret,
        ];
        $method  = 'GET';
        $res = Http::request($api_url,$data,$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }
        return $data;
    }
     
    /**
     * 通过免登陆码获取用户信息
     */
    public function get_user_info_by_code($access_token,$code){
        $api_url = Config::$url['ding_api'] . "/topapi/v2/user/getuserinfo?access_token=".$access_token;
        $data=[
            'code'=>$code,
        ];
        $method  = 'POST';
        $res = Http::request($api_url,$data,$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }
        return $data;
    }
    /**
     * 获取jsapi_ticket
     */
    public function get_jsapi_ticket($access_token){
        $api_url = Config::$url['ding_api'] . "/get_jsapi_ticket";
        $data=[
            'access_token'=>$access_token,
        ];
        $method  = 'GET';
        $res = Http::request($api_url,$data,$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }
        return $data;
    }

    /**
     * 通过userId获取用户详细信息
     */
    public function get_user_info_by_user_id($access_token,$user_id,$language='zh_CN'){
        $api_url = Config::$url['ding_api'] . "/topapi/v2/user/get?access_token=".$access_token;
        $data=[
            'userid'=>$user_id,
            'language'=>$language,
        ];
        $method  = 'POST';
        $res = Http::request($api_url,$data,$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }
        return $data;
    }

    /**
     * 通过mobile获取用户详细信息
     */
    public function get_user_info_by_mobile($access_token,$mobile){
        $api_url = Config::$url['ding_api'] . "/topapi/v2/user/getbymobile?access_token=".$access_token;
        $data=[
            'mobile'=>$mobile,
        ];
        $method  = 'POST';
        $res = Http::request($api_url,$data,$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }
        return $data;
    }
    
    /**
     * 创建群会话
     */
    public function create_chat_group($access_token,$name,$owner,$useridlist,$showHistoryType=1,$searchable=1,$validationType=0,$mentionAllAuthority=0,$managementType=0,$chatBannedType=0){
        $api_url = Config::$url['ding_api'] . "/chat/create?access_token=".$access_token;
        $data=[
            'name'=>$name,
            'owner'=>$owner,
            'useridlist'=>json_encode($useridlist),
            'showHistoryType'=>$showHistoryType,
            'searchable'=>$searchable,
            'validationType'=>$validationType,
            'mentionAllAuthority'=>$mentionAllAuthority,
            'managementType'=>$managementType,
            'chatBannedType'=>$chatBannedType,
        ];
        $method  = 'POST';
        $res = Http::request($api_url,json_encode($data),$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }

        return $data;
    } 
    
    /**
     * 更新群会话
     */
    public function update_chat_group($access_token,$chatid,$name,$owner,$ownerType,$add_useridlist,$del_useridlist,$add_extidlist,$del_extidlist,$icon,$showHistoryType=1,$searchable=1,$validationType=0,$mentionAllAuthority=0,$managementType=0,$chatBannedType=0){
        $api_url = Config::$url['ding_api'] . "/chat/update?access_token=".$access_token;
        $data=[
            'chatid'=>$chatid,
            'name'=>$name,
            'owner'=>$owner,
            'ownerType'=>$ownerType,
            'add_useridlist'=>$add_useridlist,
            'del_useridlist'=>$del_useridlist,
            'add_extidlist'=>$add_extidlist,
            'del_extidlist'=>$del_extidlist,
            'icon'=>$icon,
            'showHistoryType'=>$showHistoryType,
            'searchable'=>$searchable,
            'validationType'=>$validationType,
            'mentionAllAuthority'=>$mentionAllAuthority,
            'managementType'=>$managementType,
            'chatBannedType'=>$chatBannedType,
        ];
        $method  = 'POST';
        $res = Http::request($api_url,$data,$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }

        return $data;
    }
    
    /**
     * 获取群会话
     */
    public function get_chat_group($access_token,$chat_id){
        $api_url = Config::$url['ding_api'] . "/chat/get?access_token=".$access_token.'&chatid='.$chat_id;
        $method  = 'GET';
        $res = Http::request($api_url,[],$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }

        return $data;
    }
    
    /**
     * 设置群管理员
     */
    public function set_chat_admin($access_token,$chat_id,$userids,$role){
        $api_url = Config::$url['ding_api'] . "/topapi/chat/subadmin/update?access_token=".$access_token;
        $data=[
            'chat_id'=>$chat_id,
            'userids'=>$userids,
            'role'=>$role,
        ];
        $method  = 'POST';
        $res = Http::request($api_url,$data,$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }

        return $data;
    }
    
    /**
     * 创建群会话
     */
    public function set_chat_friendswitch($access_token,$chat_id,$userids,$is_prohibit){
        $api_url = Config::$url['ding_api'] . "/topapi/chat/member/friendswitch/update?access_token=".$access_token;
        $data=[
            'chat_id'=>$chat_id,
            'userids'=>$userids,
            'is_prohibit'=>$is_prohibit,
        ];
        $method  = 'POST';
        $res = Http::request($api_url,$data,$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }

        return $data;
    }

    /**
     * 获取入群链接
     */
    public function get_chat_qrcode($access_token,$chat_id,$userid){
        $api_url = Config::$url['ding_api'] . "/topapi/chat/qrcode/get?access_token=".$access_token;
        $data=[
            'chat_id'=>$chat_id,
            'userid'=>$userid,
        ];
        $method  = 'POST';
        $res = Http::request($api_url,$data,$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }

        return $data;
    }
    /**
     *  设置群成员昵称
     */
    public function set_chat_user_nickename($access_token,$chat_id,$userid,$group_nick){
        $api_url = Config::$url['ding_api'] . "/topapi/chat/updategroupnick?access_token=".$access_token;
        $data=[
            'chat_id'=>$chat_id,
            'userid'=>$userid,
            'group_nick'=>$group_nick,
        ];
        $method  = 'POST';
        $res = Http::request($api_url,$data,$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }

        return $data;
    }

    public function sign($ticket, $nonceStr, $timeStamp, $url)
    {
        $plain = 'jsapi_ticket=' . $ticket .
            '&noncestr=' . $nonceStr .
            '&timestamp=' . $timeStamp .
            '&url=' . $url;
        return sha1($plain);
    }

    public function get_random_str($count=10) {
        $rand_str = '';
        $base = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < $count; $i++) {
            $number = rand(0,strlen($base)-1);
            $rand_str .= substr($base,$number,1);
        }
        return $rand_str;
    }

    /**
     * 批量获取用户信息
     */
    public function get_user_list_by_userids($access_token,$user_ids,$field_filter_list,$agentid){
        $api_url = Config::$url['ding_api'] . "/topapi/smartwork/hrm/employee/list?access_token=".$access_token;
        $data=[
            'field_filter_list'=>$field_filter_list,
            'userid_list'=>$user_ids,
            'agentid'=>$agentid,
        ];
        $method  = 'POST';
        $res = Http::request($api_url,$data,$method);
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }
        return $data;
    }
    
    /**
     * 发送机器人消息
     */
    public function robot_send_text($api_url,$content,$atMobiles,$isAtAll=false){
        $data=[
            'msgtype'=>'text',
            'text'=>['content'=>$content],
            'at'=>[
                'atMobiles'=>$atMobiles,
                'isAtAll'=> $isAtAll,
                ]
        ];
        $method  = 'POST';
        $res = $this->request_by_curl($api_url, json_encode($data));
        $data=json_decode($res,true);
        if(!$data || !is_array($data) || $data['errcode']!=0){
            $this->cat->event('log:Ding_api:request_log',date("Y-m-d H:i:s"),'url:'.$api_url.'_data:'.json_encode($data).'_method:'.$method.'_result:'.print_r($res,true));
        }
        return $data;
    }

    function request_by_curl($remote_server, $post_string) {  
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $remote_server);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); 
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);                
        return $data;  
    }  
}