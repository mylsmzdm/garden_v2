<?php

/**
 * Class Cas
 *
 * 值得买Cas单点登录
 *
 * @author liangdong@smzdm.com
 * @date 2018/3/9 上午10:23
 */
class Cas
{
    // CAS SERVER配置
    private $cas_host =  'sso1-bgm.smzdm.com';
    const CAS_CONTEXT = '/cas';
    const CAS_PORT = 443;
    const CAS_SERVER_CA_CERT_PATH = '';

    // SESSION REDIS
    const SESSION_SAVE_PATH = 'tcp://cas_data_redis_m01:6379?timeout=2.5&prefix=ZDM_CAS_SESS:'; // TODO: redis域名向运维申请
    const SESSION_COOKIE_NAME = "ZDM_CAS_SESS_ID";
    const SESSION_LIFETIME = 15 * 86400; // 15天会话过期

    // CAS客户端
    private $casInit = false;

    // 初始化SESSION
    private function initSession() {
        // 设置COOKIE名称
        session_name(self::SESSION_COOKIE_NAME);

        // 设置COOKIE生命期
        session_set_cookie_params(self::SESSION_LIFETIME, '/', '.zdm.net', FALSE, TRUE);

        // 使用REDIS SESSION
        ini_set('session.save_handler','Redis');
        ini_set('session.save_path',self::SESSION_SAVE_PATH);

        // 设置SESSION生命期
        ini_set("session.gc_maxlifetime", self::SESSION_LIFETIME);

        // 启动SESSION
        session_start();
    }
    /**
     * 统一增加配置中心获取数据
     *
     * @return void
     * @author:zhangyong <zhangyong01@smzdm.com>
     */
    private function initCasHost()
    {
        if(class_exists('Dogx')) {
            $dogx_result = Dogx::get_by_keys(["zdm-public.bgmsso_hostname"]);
            if(!empty($dogx_result['zdm-public.bgmsso_hostname'])) {
                $this->cas_host = $dogx_result['zdm-public.bgmsso_hostname'];
            }
        }
    }

    // 因为CAS CLIENT涉及session初始化, 所以延迟到使用时创建
    private function initCasClientOnce() {
        if (empty($this->casInit)) {
            // 初始化SESSION配置
            $this->initSession();
            $this->initCasHost();
            // 创建CAS客户端
            phpCAS::client(CAS_VERSION_3_0, $this->cas_host, self::CAS_PORT, self::CAS_CONTEXT);
            phpCAS::setNoCasServerValidation();
            // For production use set the CA certificate that is the issuer of the cert
            // on the CAS server and uncomment the line below
            // phpCAS::setCasServerCACert($cas_server_ca_cert_path);

            // 如果调试, 打开这两行, 日志输出在/tmp/cas.log文件
            // phpCAS::setDebug("/tmp/cas.log");
            // phpCAS::setVerbose(true);

            $this->casInit = true;
        }
    }
    
    // 验证登录, 返回用户信息
    //
    // 可重复调用
    public function getUserInfo($force = true) {
        // 只初始化一次CAS CLIENT
        $this->initCasClientOnce();

        // 强制登录, 会跳转CAS SERVER
        if (!phpCAS::isAuthenticated()) {
            if ($force) {
                phpCAS::forceAuthentication();
            } else {
                return false;
            }
        }

        // CAS用户名
        $username = phpCAS::getUser();
        // CAS附加属性（主要取里面的BGMSSO UID）
        $attributes = phpCAS::getAttributes();

        return [
            'username' => $username,
            'attributes' => $attributes,
        ];
    }

    // 退出登录, 默认只退出PHP会话, 传casLogout则会跳转到CAS SERVER进行注销
    public function logout($casLogout = false) {
        // 只初始化一次CAS CLIENT
        $this->initCasClientOnce();

        if ($casLogout) {
            phpCAS::logout();
        } else {
            session_unset();
            session_destroy();
        }
    }

    // 注销通知, 实现统一登出
    public function logoutCallback() {
        // 只初始化一次CAS CLIENT
        $this->initCasClientOnce();

        // 暂时不校验登出
        phpCAS::handleLogoutRequests(false);
    }
}