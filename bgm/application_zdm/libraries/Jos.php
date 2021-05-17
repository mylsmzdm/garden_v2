<?php

/**
 * 京东宙斯SDK
 */
require_once __DIR__ . '/jos/JosClient.php';

class Jos {

    public $callback_url = 'https://h5.smzdm.com/jd/access/callback/'; #回调地址 此地址判断是web还是wap
    public $exchange_client_id = '14'; #京东分配的client_id
    public $exchange_key = '648e24b40eb64d0f8b92cb410d319be1'; #兑换接口密钥 非jos密钥

    public function __construct() {
        #$this->pub_key = __DIR__.'/jos/jos_pub_key.php';
        #$this->pri_key = __DIR__.'/jos/rsa_private_key.php';
        $this->init();
    }

    public function init() {
        $this->jos_client = new JosClient();
    }
    
    /**
     * 返回绑定京东账号的url 用于前端跳转
     * 
     * @author jxt
     */
    public function get_user_request_url($param = [], $is_wap = false) {
        $client_id = urlencode($this->jos_client->appKey);
        $callback_url = urlencode($this->callback_url);
        $param['view'] = $is_wap ? 'wap' : 'web';
        $state = urlencode(json_encode($param));
        $wap_view = $is_wap ? 'view=wap' : '';
        
        $url = "https://oauth.jd.com/oauth/authorize?response_type=code&client_id={$client_id}&redirect_uri={$callback_url}&state={$state}&{$wap_view}";
        return $url;
    }
    
    /**
     * 返回获取access_token的url 用于服务端请求
     * 
     * @author jxt
     */
    public function get_access_request_url($code, $param = []) {
        $redirect_uri = 'https://h5.smzdm.com/jd/access/callback/';
        $callback_url = urlencode($this->callback_url);
        $state = urlencode(json_encode($param));
        $url = "https://oauth.jd.com/oauth/token?grant_type=authorization_code&client_id={$this->jos_client->appKey}&client_secret={$this->jos_client->appSecret}&scope=read&redirect_uri={$callback_url}&code={$code}&state={$state}";
        return $url;
    }
    
    
    
    public function get_access_token($code, $param = []) {
        $access_request_url = $this->get_access_request_url($code, $param);#echo $access_request_url;exit;
        $result = $this->jos_client->curl($access_request_url);
        if (false !== $result) {
            $result = iconv('gbk', 'utf-8', $result);
        }
        return $result;
    }
    
    
    /**
     * 兑换京豆 http://jos.jd.com/api/detail.htm?id=567
     *  
     * 
          0000	request success	请求成功
          0001	client no authorize	客户端未授权
          0002	error signature	签名验证失败
          0003	customer balance insufficient	用户余额不足
          0004	repeat transaction exception	重复交易
          0005	customer account not exist	用户账户不存在
          0006	parameters illegal	参数非法
          0007	interface has not authorized to client	接口未授权给客户端，客户端访问接口前必须在ERP后台选择该接口。
          0010	EXCEED_POOL_BANLANCE	超出京豆池数量
          1000	server unknown exception	接口异常
          JD10000	SUCCESS	成功
          JD00404	NOT_EXISTS	不存在的实体
          JD00409	CONFLICT	实体已存在
          JD00500	ERROR	服务错误（超时或字段名写错）
          JD00002	KEY_ERROR	数字签名错误
          JD00003	DISABLE	服务不可用
          JD00006	REPEAT_SUBMIT	重复交易
          JD00004	ARGUMENT_ERROR	参数不合法
          JD10001	ACTIVITY_STATUS_ERROR	活动状态错误
     * 
     * @param $access_token 用户京东授权token
     * @param $gold 金币(扣减为负数)
     * @param $jpea 京豆(正数) 京豆与金币必须10:1
     * @param $business_id 外部唯一id 相当于订单id
     * 
     * @author jxt
     */
    public function sns_exchange_jpeas($access_token, $gold = 0, $jpea = 0, $business_id = '') {
        #测试
        /*return [
            'code' => 0, #jos层报错
            'exchange_result' => [
                'code' => 'JD100001',
                'data' => true,
                'msg' => 'success',
            ],
        ];*/
        if (empty($this->jos_client)) {
            $this->init();
        }
        if (empty($this->jos_client)) {
            return false;
        } else {
            require_once __DIR__ . '/jos/request/SnsExchangejpeasRequest.php';
            $req = new SnsExchangejpeasRequest();
            $req->setClientId($this->exchange_client_id); #客户号  即创建应用时的Appkey（从JOS控制台->管理应用中获取）
            $req->setBusinessId($business_id); #业务号  交易流水号，每笔唯一，小于18位
            $req->setKey($this->exchange_key); #业务key 
            #$req->setSignature('8a8ac1fc61c94ea1950ad9134f5a8dbb'); #签名
            $req->setSignature(strtolower(md5("{$this->exchange_client_id}#{$business_id}_{$this->exchange_key}"))); #签名
            $req->setIntegral(-$gold); #金币
            $req->setJpeas($jpea); #京豆
            $req->setRemark('什么值得买金币兑换京豆'); #备注
            $req->setStatus(1); #状态
            $req->setOriginType(1); #京豆=1  钢镚=4
            $resp = $this->jos_client->execute($req, $access_token); 
            if (empty($resp)) {
                return false;
            }
            $resp = (array) $resp;
            return $resp;
        }
    }

    public function sns_exchange_status_get($access_token, $business_id = '') {
        #测试
        /*return false;
        return [
            'code' => 0,
            'queryexchangestatus_result' => [
                'code' => 'JD100001',
                'data' => true,
                'msg' => 'success',
            ],
        ];*/
        
        if (empty($this->jos_client)) {
            $this->init();
        }
        if (empty($this->jos_client)) {
            return false;
        } else {
            require_once __DIR__ . '/jos/request/SnsExchangestatusGetRequest.php';
            $req = new SnsExchangestatusGetRequest();
            $req->setClientId($this->exchange_client_id); #客户号
            $req->setBusinessId($business_id); #业务号
            $req->setKey($this->exchange_key); #业务key
            $req->setSignature(strtolower(md5("{$this->exchange_client_id}#{$business_id}_{$this->exchange_key}"))); #签名
            $req->setOriginType(1); #京豆=1  钢镚=4
            $resp = $this->jos_client->execute($req, $access_token); 
            if (empty($resp)) {
                return false;
            }
            $resp = (array) $resp;
            return $resp;
        }
    }

}
