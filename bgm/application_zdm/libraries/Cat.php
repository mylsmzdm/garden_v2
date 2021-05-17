<?php
/**
 * cat入口
 * 
 * @twiki http://twiki:8081/twiki/Main/Php_cat
 * @author jxt
 */

require_once __DIR__ . '/sdk/cat/CatClient.php';

class Cat {
    
    public $is_open = true;
    public $client_obj = null;
    
    function __construct($params = []) {
        if (!empty($params['domain'])) {
            $params['is_open'] = $this->is_open;
            $this->client_obj = new CatClient($params);
        }
    }
    
    function init($domain = 'Unknow Domain') {
        $params['is_open'] = $this->is_open;
        $params['domain'] = $domain;
        
        $this->client_obj = new CatClient($params);
    }

    /**
     * CURL追踪transaction傻瓜化开始
     */
    function curl_start($url = '') {
        return $this->client_obj->curl_start($url);
    }

    /**
     * Atom Transaction 原子性事务记录
     * 
     * @param type $type
     * @param type $name
     * @param type $status
     * @param type $micro_second
     * @param type $data
     * @return type
     */
    public function atom($type, $name, $micro_second, $status = '0', $data = null) {
        $this->client_obj->atom($type, $name, $micro_second, $status, $data);
    }
    
    /**
     * event 打点
     * 
     * @param type $type
     * @param type $name
     * @param type $data
     * @param type $status
     * @return type
     */
    public function event($type, $name, $data = '', $status = 0) {
        $this->client_obj->event($type, $name, $data, $status);
    }
    
    public function get_config_server_addr_list() {
        return $this->client_obj->cat_config_server_list;
    }
    
    public function generate_message_id() {
        return $this->client_obj->generate_message_id();
    }
    
    public function __call($name, $arguments) {
        if (method_exists($this->client_obj, $name)) {
            return call_user_func_array([$this->client_obj, $name], $arguments);
        } else {
            return false;
        }
    }
}

