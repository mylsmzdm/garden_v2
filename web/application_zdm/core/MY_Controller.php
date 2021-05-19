<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * @author liangdong@smzdm.com
 * Class MY_Controller
 */
abstract class MY_Controller extends CI_Controller {
    public $current_user = [];
    public abstract function init();

    /**
     * MY_Controller constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->load->library('cat');

        if ($this->input->is_cli_request()) {
            #job
            $this->cat->init('garden.job');
            $this->cat->job_trace();
        } else {
            #web
            $this->cat->init('garden.zdm.net');
            $this->cat->url_trace();
        }

         #放开接口直接json解析可视化界面，但是会影响前端hashids
        #header("Content-type:application/json; charset=utf-8");

        // 配合前端联调 允许跨域访问 这几行不要合并 不要上线!!!

        // $allow_orgin = [
        //     'https://garden.zdm.net'
        // ];
        // header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        // header("Access-Control-Allow-Credentials: true");
        // if(isset($_SERVER['HTTP_ORIGIN'])){
        //     $http_origin = $_SERVER['HTTP_ORIGIN'];
        // //     if(in_array($http_origin,$allow_orgin)){
        //         header("Access-Control-Allow-Origin: $http_origin");
        // // }
        //     } else {
        //     header("Access-Control-Allow-Origin: *");
        // }

        $this->init();

    }

    /**
     * 检查用户权限
     *
     * @param array $permission     所需权限一维数组
     * @return bool
     */
    protected function check_permission_bgmsso(array $permission = []){
        $result = ['error_code'=>0, 'error_msg'=>'', 'data'=>[]];
        $userinfo = $this->get_bgm_user_info();
        if(is_null($userinfo) || !isset($userinfo['error_code'])){
            $result['error_code'] = ERROR_EXCEPTION;
            $result['error_msg'] = '网络错误';
            goto ARCHOR_RESULT;
        }

        if(0 !== $userinfo['error_code']){
            $result['error_code'] = ERROR_PERM;
            $result['error_msg'] = $userinfo['error_msg'];
            goto ARCHOR_RESULT;
        }

        if(empty($permission)){ #仅判断基础权限
            if(empty($userinfo['data']['project'])){
                $result['error_code'] = ERROR_PERM;
                $result['error_msg'] = '无法获取用户权限';
                goto ARCHOR_RESULT;
            }
        }

        $userPower = isset($userinfo['data']['permissions'])?$userinfo['data']['permissions']:[];
        // zhangsiming · 2016/10/26 11:04· 判断权限\
        if(!empty($permission) && !array_intersect($permission, $userPower)){
            $result['error_code'] = ERROR_PERM;
            $result['error_msg'] = '没有权限';
            goto ARCHOR_RESULT;
        }

        $result['data'] = $this->current_user = $this->check_current_user['data'];
        return $result;

        ARCHOR_RESULT:
        echo json_encode($result);
        die;
    }

    // CAS权限校验
    function check_permission(array $permission = [], $appCode = 'zindex', $treelike = true, $force = false, array $args = []) {
        return $result = [
            'error_code' => 0,
            'error_msg' => '',
            'data' => [],
        ];
        $default_args = [
            'return_type' => 0, #0返回array结果；1跳转走；
        ];
        $args = array_merge($default_args, $args);

        // CAS开关
        if (!Config::$cas['open']) {
            $res_bgmsso = $this->check_permission_bgmsso($permission);
            $result = array_merge($result, $res_bgmsso);
            goto RESULT;
        }

        $this->load->library('cas');
        $this->load->helper(array('url'));
        $this->load->biz('base_bizes/manager/user_rbac_biz');

        // 强制CAS登录
        $casUserInfo = $this->cas->getUserInfo($force);
        // $casUserInfo = json_decode('{"username":"miaoyulu","attributes":{"isFromNewLogin":"true","mail":"miaoyulu@smzdm.com","authenticationDate":"2021-04-20T10:54:17.645+08:00[Asia\/Shanghai]","entryDate":"2021-02-02","displayName":"\u82d7\u96e8\u9732","sex":"1","successfulAuthenticationHandlers":"ZdmMaster","mobile":"iuJZOgOTcna4HNfmYtx\/AQ==","description":"warn","dept":"\u5317\u4eac\u503c\u5f97\u4e70\u79d1\u6280\u80a1\u4efd\u6709\u9650\u516c\u53f8-\u503c\u5f97\u4e70\u96c6\u56e2-CTO\u4f53\u7cfb-\u96c6\u56e2\u7814\u53d1\u90e8-test22","cn":"miaoyulu","employeeNumber":"601683","credentialType":"UsernamePasswordCredential","samlAuthenticationStatementAuthMethod":"urn:oasis:names:tc:SAML:1.0:am:password","uid":"12170","employeeType":"normal","authenticationMethod":"ZdmMaster","longTermAuthenticationRequestTokenUsed":"false"}}',true);

        if ($casUserInfo === false) {
            $result['error_code']=ERROR_PERM;
            $result['error_msg']='没有登陆';
            goto RESULT;
        }

        // LDAP账号未绑定到BGMSSO ID
        if (empty($casUserInfo['attributes']['uid'])) {
            // 若LDAP登录成功，但是没绑定BGMSSO，则采用旧SSO账号的登录流程
            $res_bgmsso = $this->check_permission_bgmsso($permission, $appCode, $treelike);
            $result = array_merge($result, $res_bgmsso);
            goto RESULT;
        }

        // BGMSSO的用户ID
        $bgmUid = $casUserInfo['attributes']['uid'];
        // 获取BGM里的信息
        $userinfo = $this->user_rbac_biz->getUserRbacWithId($appCode, $treelike, $bgmUid);
        // 校验权限
        $userPower = isset($userinfo['data']['permissions']) ? $userinfo['data']['permissions'] : [];
        // 公共资源跳过验证
        if ($permission == ['zindex.department-manage.bumen']){
            goto ARCHOR_PERMISSION;
        }
        if(!empty($permission) && !array_intersect($permission,$userPower)){
            $result['error_code']=ERROR_PERM;
            $result['error_msg']='没有权限';
            goto RESULT;
        }
        ARCHOR_PERMISSION:
        $this->menu['project'] = isset($userinfo['data']['project']) ? $userinfo['data']['project'] : [];
        if (empty($this->menu['project']))
        {
            $result['error_code']=ERROR_PERM;
            $result['error_msg']='没有权限';
            goto RESULT;
        }

        $this->menu['left_menu'] = isset($userinfo['data']['left_menu']) ? $userinfo['data']['left_menu'] : [];
        $this->menu['userinfo'] = $this->userinfo = isset($userinfo['data']['user']) ? $userinfo['data']['user'] : [];
        $this->current_user['user'] = $userinfo['data']['user'];

//        $result['error_code'] = 0;
//        $result['error_msg'] = '';
        $result['data']['project'] = $this->menu['project'];
        $result['data']['permissions'] = $userPower;
        $result['data']['left_menu'] = $this->menu['left_menu'];
        $result['data']['user'] = $this->userinfo;
        return $result;

        RESULT:
        $result['data']['login_url'] = 'https://sso1-bgm.smzdm.com/cas/login?service='.urldecode('https://garden.zdm.net:443/check_cas/login');
        //非法访问跳转到bgm后台
        if (!empty($args['return_type']))
        {
            redirect($result['data']['login_url'], 'location', 302);
            return ;
        }
        else
        {
            echo json_encode($result);
            die;
        }
    }


    /*
     *  老的BGM SSO权限校验
     */
    /*function check_permission_bgmsso($permission,$appCode,$treelike=true){
        //return;
        $this->load->helper(array('url'));
        $result=['error_code'=>0,'error_msg'=>'','data'=>''];
        // zhangsiming · 2016/10/24 17:06· 获取用户权限
        $this->load->biz('base_bizes/manager/user_rbac_biz');
        $userinfo=$this->user_rbac_biz->getUserRbac($appCode,$treelike);
        if(!isset($userinfo['error_code'])||$userinfo['error_code']>0){
            $result['error_code']=$userinfo['error_code'];
            $result['error_msg']=$userinfo['error_msg'];
            goto RESULT;
        }
        $userPower=isset($userinfo['data']['permissions'])?$userinfo['data']['permissions']:[];
        // zhangsiming · 2016/10/26 11:04· 判断权限
        if($permission!= '' && !in_array($permission,$userPower)){
            $result['error_code']='5';
            $result['error_msg']='没有权限';
            goto RESULT;
        }
        // zhangsiming · 2016/10/28 11:41· 权限验证完成，返回菜单信息
        $this->menu['left_menu']=isset($userinfo['data']['left_menu'])?$userinfo['data']['left_menu']:[];
        $this->menu['project']=isset($userinfo['data']['project'])?$userinfo['data']['project']:[];
        $this->menu['userinfo'] = $this->userinfo = isset($userinfo['data']['user']) ? $userinfo['data']['user'] : [];
        return true;
        RESULT:
        //非法访问跳转到bgm后台
        $error_msg=isset($result['error_msg'])?$result['error_msg']:'无法获取用户权限';
        redirect('http://sso-bgm.smzdm.com/uas-sso/root/error.jsp?error='.$error_msg,'location',302);
    }*/
    /**
     * 从bgm新后台获取用户信息
     *
     * @param  $treelike
     * @return mixed
     * @author Dongdong Wang
     * @date 2016-11-25
     */
    private function get_bgm_user_info($treelike = '') {
        if (empty($this->check_current_user)) {
            $this->load->biz('base_bizes/manager/user_rbac_biz');
            $appCode = Config::$constant['bgm_app_code'];
            $this->check_current_user = $this->user_rbac_biz->getUserRbac($appCode, $treelike);
        }
        return $this->check_current_user;
    }

    /**
     * 值得买协议返回值
     * response
     * @author liangdong@smzdm.com
     * @param int    $error_code
     * @param string $error_msg
     * @param array  $data
     */
    protected function response($error_code = 0, $error_msg = '', $data = []) {
        echo json_encode([
            'error_code' => $error_code,
            'error_msg' => $error_msg,
            'data' => $data,
        ]);
    }

    /**
     * cron_manager 防止脚本并发
     * @author liangdong@smzdm.com
     */
    public function cron_manager() {
        $file_name = ($_SERVER['PHP_SELF']);

        if (empty($file_name)) {
            $file_name == __FILE__;
        }
        #var_dump($file_name);
        //check the program is running
        $log_exec_rs = array();
        $log_exec_str = $_SERVER['PHP_SELF'];
        #var_dump($log_exec_str);
        $log_exec_count = 0;
        exec('ps aux|grep ' . $log_exec_str, $log_exec_rs);
        $params = $this->input->server('argv');
        if (!empty($params)) {
            $log_exec_str = implode(' ', $params);
        }

        foreach ($log_exec_rs as $v) {
            if (strpos($v, $log_exec_str . ' ') > 0 && strpos($v, 'grep') === false && strpos($v, '.sh') === false && strpos($v, 'vim') === false && strpos($v, '/bin/sh') === false) { // 匹配job.php a b > xx.log
                $log_exec_count++;
            } else if (strpos($v, $log_exec_str) > 0 && substr($v, strrpos($v, $log_exec_str)) === $log_exec_str && strpos($v, 'grep') === false && strpos($v, '.sh') === false && strpos($v, 'vim') === false && strpos($v, '/bin/sh') === false) { // 匹配job.php a b
                $log_exec_count++;
            }
        }
        //   	$insert_id = ProductInterBaiduDB::insertData($file_name);
        if ($log_exec_count > 1) {
            echo date('Y-m-d H:i:s') . ' U-F-P' . "\n";
            //Log::er()->append(date('Y-m-d H:i:s') . "，" . $_SERVER['PHP_SELF'] . "上次执行还没有结束" . "\n");
            exit;
        }
    }

    /**
     * 2020开始施行新考核制度半年一评
     * 2020Q2 => 2020H1
     * 2020Q4 => 2020H2
     *
     * @param sting $assessment_stage
     * @return void
     */
    function change_stage($assessment_stage)
    {
        if (!empty($assessment_stage))
        {
            $_t_year = (int)substr($assessment_stage, 0, 4);
            $_t_quarter = substr($assessment_stage, 4);
            if ($_t_year >= 2020)
            {
                $_s_quarter = $_t_quarter;
                if ($_t_quarter=='Q2')
                {
                    $_s_quarter = 'H1';
                }
                else if ($_t_quarter=='Q4')
                {
                    $_s_quarter = 'H2';
                }
                $assessment_stage = $_t_year . $_s_quarter;
            }
        }
        return $assessment_stage;
    }

}
