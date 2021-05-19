<?php
/**
 * 对接新权限系统，本文件是复制过来的
 */

/**
 * Created by PhpStorm.
 * User: crazy
 * Date: 2016/10/28
 * Time: 16:16
 */
class User_rbac_api extends CI_Model{
    function __construct() {
        $this->load->library('http');//$user_cookie=urlencode($user_cookie);
    }
    /**
     * User  :zhangsiming · Date: 2016/10/31 11:10
     * Info  :获取用户权限信息
     * Param :string user_cookie
     * Param :string appcode
     * Return:array
     */
    public function get_user_rbac($user_cookie,$appCode){
        $api_url = Config::$url['bgm_api_url'] . "/api/user_rbac/getUserRbac";
        $data=[
            'user_cookie'=>$user_cookie,
            'appCode'=>$appCode,
        ];
        $res = Http::request($api_url,$data,'POST');
        $data=json_decode($res,true);
        return $data;
    }
    /**
     * User  :zhangsiming · Date: 2016/10/24 14:11
     * Info  :根据cookie获取用户信息
     * Param :string user_cookie
     * Return:array
     */
    public function get_user_info($user_cookie){
        $params=['cookie'=>$user_cookie];
        $res = Http::request(Config::$url['sso_api_url'] . '/uas-sso/sso/checkAuthenticationState',$params,'POST');
        $res=json_decode($res,true);
        return $res;
    }
    /**
     * User  :zhangsiming · Date: 2016/10/24 15:59
     * Info  :根据用户userId、appCode获取用户在该模块下的权限
     * Param :string $userId
     * Param :string $appCode
     * Return:array
     */
    public function get_user_power($userId,$appCode,$treelike){
        $this->load->library('http');
        $params=[
            'userId'=>$userId,
            'appCode'=>$appCode,
            'treelike'=>$treelike
        ];
        $data = Http::request(Config::$url['auth_api_url'] . '/uas-api/api/getResByApp',$params,'POST');
        $data=json_decode($data,true);
        return $data;
    }
    /**
     * User  :zhangsiming · Date: 2016/11/18 17:44
     * Info  :根据用户id获取用户信息
     * Param :string $userId用户id
     * Return:array
     */
    public function get_user($user_id){
        $params=['userId'=>$user_id];
        $res = Http::request(Config::$url['auth_api_url'] . '/uas-api/api/getUserInfo',$params,'POST');
        $res=json_decode($res,true);
        return $res;
    }

    /**
     * 获得某用户多个项目的权限
     *      接口文档： http://twiki:8081/twiki/Main/Api_getResByMultiApps
     *
     * @param   array   $args       条件参数
     * @return  array|null
     * @author  Dacheng Chen
     * @time    2016-12-8
     */
    public function get_auth_permission_data($args = []){
        $default_args = [
            'userId' => 0,
            'appCode' => '',
        ];
        $args = array_merge($default_args, $args);
        $result = Http::request(Config::$url['auth_api_url'] . '/uas-api/api/getResByApps/', $args, 'GET');
        $result = json_decode($result, true);
        return $result;
    }

    /**
     * 根据角色查询所有用户集合
     *      接口文档：http://twiki:8081/twiki/Main/Api_getUserByRole
     * 
     * @param  array    $args       参数
     * @return array|null
     * @author  Dacheng Chen
     * @time    2016-12-18
     */
    public function get_user_by_role($args = []){
        $result = Http::request(Config::$url['auth_api_url'] . '/uas-api/api/getUserByRole', $args, 'GET');
        $result = json_decode($result, true);
        return $result;
    }

    /**
     * 根据系统标识批量获取用户信息
     *      zindex，仅返回所属系统角色
     * 
     *      接口文档：http://twiki:8081/twiki/Main/GetUserListByResCode
     * 
     * @param  array    $args       参数
     * @return array|null
     * @author  Dacheng Chen
     * @time    2016-12-18
     */
    public function get_user_list_by_rescode($args = []){
        $result = Http::request(Config::$url['auth_api_url'] . '/uas-api/api/getUserListByResCode', $args, 'GET');
        $result = json_decode($result, true);
        return $result;
    }

    public function batchGetUserInfo($userId){
        $params=['userId'=>$userId];
        $res = Http::request('http://authapi.smzdm.com:8080/uas-api/api/batchGetUserInfo',$params,'POST');
        if(!$res || empty($res)){
            return false;
        }
        $res=json_decode($res,true);
        if(!$res || $res['success']==false){
            return false;
        }
        return $res;
    }
}