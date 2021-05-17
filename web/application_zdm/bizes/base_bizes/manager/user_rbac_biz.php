<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * User: quguangke 拷贝 来自 图片服务项目
 *
 * @property user_rbac_api user_rbac_api
 * Date: 2016/12/28
 * Time: 16:10
 */
class User_rbac_biz extends CI_Biz{
    function __construct() {
        parent::__construct();
        $this->load->model('manager/user_rbac_api');
        $this->load->library('cas');
    }

    /**
     * 基于CAS的账号登录
     * getUserRbacWithId
     * @author liangdong@smzdm.com
     * @param $appCode
     * @param $treelike
     * @param $userId
     */
    public function getUserRbacWithId($appCode, $treelike, $userId) {
        $result=['error_code'=>0,'error_msg'=>'','data'=>''];

        if(empty($appCode)){
            $result['error_code']='2';
            $result['error_msg']='项目ID不得为空';
            goto RESULT;
        }

        // 获取用户信息
        $userinfo = $this->user_rbac_api->get_user($userId);
        if(empty($userinfo)){
            $result['error_code']='3';
            $result['error_msg']='获取用户信息失败';
            goto RESULT;
        }

        // 获取用户权限
        $userPower=$this->user_rbac_api->get_user_power($userId, $appCode,$treelike);
        if(!$userPower){
            $result['error_code']=4;
            $result['error_mas']='获取用户权限错误';
            goto RESULT;
        }

        $result['data'] = $userPower['result'];
        $result['data']['user'] = $userinfo['result'];

        RESULT:
        return $result;
    }

    /**
     * User  :zhangsiming · Date: 2016/11/17 11:47
     * Info  :获取用户信息与权限
     * Param :string $appCode项目的唯一标识
     * Param :string $treelike默认为true返回格式化的数据，false返回未格式化的数据
     * Return:array
     */
    function getUserRbac($appCode,$treelike){
        // CAS打开，走新的获取逻辑
        if (Config::$cas['open']) {
            // liangdong@smzdm.com 2018-04-23
            $casData = $this->cas->getUserInfo(false);
            if(empty($casData)) {
                $result['error_code'] = '1';
                $result['error_msg'] = '无法获取COOKIE';
                goto RESULT;
            }
            $userId = $casData['attributes']['uid'];
            return $this->getUserRbacWithId($appCode, $treelike, $userId);
        }
        // 保持老代码兼容
        $result=['error_code'=>0,'error_msg'=>'','data'=>''];
        // zhangsiming · 2016/11/1 11:34 ·  获取java生成的cookie
        $user_cookie=isset($_COOKIE['SSO_SMZDM'])?$_COOKIE["SSO_SMZDM"]:'';
        if(empty($user_cookie)){
            $result['error_code']='1';
            $result['error_msg']='无法获取COOKIE';
            goto RESULT;
        }
        if(empty($appCode)){
            $result['error_code']='2';
            $result['error_msg']='项目ID不得为空';
            goto RESULT;
        }
        // zhangsiming · 2016/11/17 11:50· 获取用户信息
        $userinfo=$this->user_rbac_api->get_user_info($user_cookie);
        if(!$userinfo){
            $result['error_code']='3';
            $result['error_msg']='获取用户信息失败';
            goto RESULT;
        }
        $userId=$userinfo['result']['user']['userId'];
        //根据用户信息获取用户权限
        $userPower=$this->user_rbac_api->get_user_power($userId,$appCode,$treelike);
        if(!$userPower){
            $result['error_code']=4;
            $result['error_mas']='获取用户权限错误';
            goto RESULT;
        }
        $result['data']=$userPower['result'];
        $result['data']['user']=$userinfo['result']['user'];
        RESULT:
        return $result;
    }


    /**
     * User  :zhangsiming · Date: 2016/11/18 17:49
     * Info  :通过用户id获取用户信息
     * Param :string $userId用户id
     * Return:array
     */
    public function getUser($userId){
        $result=['error_code'=>0,'error_msg'=>'','data'=>''];
        if(empty($userId)){
            $result['error_code']=1;
            $result['error_msg']='userId不得为空';
            goto RELULT;
        }
        $user=$this->user_rbac_api->get_user($userId);
        if(!$user || $user['success']==false){
            $result['error_code']=2;
            $result['error_msg']='获取用户信息失败';
            goto RELULT;
        }
        $result['data']=$user['result'];
        RELULT:
        return $result;
    }

    /**
     * @param $userId
     * @return array
     */
    public function batchGetUserInfo($userId) {
        $result=['error_code'=>0,'error_msg'=>'','data'=>''];
        if(empty($userId)){
            $result['error_code']=1;
            $result['error_msg']='userId不得为空';
            goto RELULT;
        }
        $user=$this->user_rbac_api->batchGetUserInfo($userId);
        if(!$user || $user['success']==false){
            $result['error_code']=2;
            $result['error_msg']='获取用户信息失败';
            goto RELULT;
        }
        $result['data']=$user['result'];
        RELULT:
        return $result;
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
            'appCode' => [],
        ];
        $args = array_merge($default_args, $args);
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];

        $args['userId'] = intval($args['userId']);
        if($args['userId'] <= 0){
            $r['error_code'] = 1;
            $r['error_msg'] = '必要参数获取失败';
            goto ARCHOR_RESULT;
        }

        $result = $this->user_rbac_api->get_auth_permission_data($args);
        if(is_null($result)){
            $r['error_code'] = -1;
            $r['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }
        if(!isset($result['success']) || empty($result['success'])){
            $r['error_code'] = 1;
            $r['error_msg'] = '获取数据失败';
            goto ARCHOR_RESULT;
        }
        if(empty($result['result'])){
            goto ARCHOR_RESULT;
        }
        $r['data'] = $result['result'];

        ARCHOR_RESULT:
        return $r;
    }

    /**
     * 根据cookie获取用户信息
     *
     * @return array
     * @author Dongdong Wang
     * @date 2016-12-08
     */
    public function get_userinfo_by_cookie() {
        $result=['error_code'=>0,'error_msg'=>'','data'=>[]];
        // zhangsiming · 2016/11/1 11:34 ·  获取java生成的cookie
        $user_cookie = !empty($_COOKIE['SSO_SMZDM'])?$_COOKIE["SSO_SMZDM"]:'';
        if(empty($user_cookie)){
            $result['error_code']=1;
            $result['error_msg']='无法获取COOKIE';
            goto RESULT;
        }
        // zhangsiming · 2016/11/17 11:50· 获取用户信息
        $userinfo=$this->user_rbac_api->get_user_info($user_cookie);
        if(!$userinfo || empty($userinfo['result']['user'])){
            $result['error_code']=-1;
            $result['error_msg']='网络错误';
            goto RESULT;
        }

        $result['data'] = $userinfo['result']['user'];
        RESULT:
        return $result;
    }

    /**
     * 根据角色查询所有用户集合
     *
     * @param  string   $role_code  SSO系统用户角色编码
     * @return array|null
     * @author  Dacheng Chen
     * @time    2016-12-18
     */
    public function get_user_by_role($role_code){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $role_code = trim($role_code);
        if(empty($role_code)){
            $result['error_code'] = ERROR_PARAMS;
            $result['error_msg'] = '角色code不能为空';
            goto ARCHOR_RESULT;
        }

        $res = $this->user_rbac_api->get_user_by_role(['roleCode' => $role_code]);
        if(empty($res)){ #含null
            $result['error_code'] = ERROR_THIRD;
            $result['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }
        if(empty($res['success'])){
            $r['error_code'] = 1;
            $r['error_msg'] = !empty($res['error']) ? trim($res['error']) : '根据角色code获得用户信息失败';
            goto ARCHOR_RESULT;
        }
        if(empty($res['result']) || empty($res['total'])){
            goto ARCHOR_RESULT;
        }

        $r['data']['total'] = $res['total'];
        foreach($res['result'] as $val){
            $_temp = [
                'sso_userid' => $val['userId'], #sso系统用户ID
                'sso_username' => $val['userCode'], #sso系统用户账号
                'sso_nickname' => $val['realName'], #sso系统用户真实姓名
            ];
            $r['data']['rows'][] = $_temp;
        }

        ARCHOR_RESULT:
        return $r;
    }

    /**
     * 根据系统标识批量获取用户信息
     *      bgm-sos，仅返回所属系统角色
     *
     * @param  array    $args       参数
     * @return array
     * @author  Dacheng Chen
     * @time    2016-12-18
     */
    public function get_user_list_by_rescode($args = []){
        $r = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $default_args = [
            'user_ids' => '',
            'app_code' => 'bgm-sos', #系统标识，约定会写死使用'bgm-sos'
        ];
        $args = array_merge($default_args, $args);

        #筛选数据
        $params = [
            'userIds' => $args['user_ids'],
            'appCode' => $args['app_code'],
        ];
        $res = $this->user_rbac_api->get_user_list_by_rescode($params);
        if(empty($res)){ #含null
            $result['error_code'] = ERROR_THIRD;
            $result['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }
        if(empty($res['success'])){
            $r['error_code'] = 1;
            $r['error_msg'] = !empty($res['error']) ? trim($res['error']) : '根据系统标识批量获取用户信息失败';
            goto ARCHOR_RESULT;
        }
        if(empty($res['result']) || empty($res['total'])){
            goto ARCHOR_RESULT;
        }

        $r['data']['total'] = $res['total'];
        foreach($res['result'] as $val){
            $_temp = [
                'sso_userid' => $val['userId'], #sso系统用户ID
                'sso_username' => $val['userCode'], #sso系统用户账号
                'sso_nickname' => $val['realName'], #sso系统用户真实姓名
                'role_names' => !empty($val['roleNames']) ? $val['roleNames'] : '', #SSO用户角色名称，英文逗号分隔
            ];
            $r['data']['rows'][] = $_temp;
        }

        ARCHOR_RESULT:
        return $r;
    }
}