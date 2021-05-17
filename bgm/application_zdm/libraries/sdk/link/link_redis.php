<?php

/**
 * redis类简单封装 命令参考:http://redisdoc.com/
 */
class LinkRedis extends Redis
{

    private $conn = 'dr';
    private $connected;
    private $redis_config;

    function __construct($conn = 'dr')
    {
        require("link_config.php");
        $this->redis_config = SmzdmLinkCpsConfig::$redis;
        $connect = array();
        if ('dr' == $conn) {//数据读
            $rand_number = rand(0, count($this->redis_config['data']['read']) - 1);
            $connect = $this->redis_config['data']['read'][$rand_number];
        } elseif ('dw' == $conn) {//数据写
            $connect = $this->redis_config['data']['write'];
        }elseif ('cr' == $conn) {//缓存读
            $rand_number = rand(0, count($this->redis_config['cache']['read']) - 1);
            $connect = $this->redis_config['cache']['read'][$rand_number];
        }elseif ('cw' == $conn) {//缓存写
            $connect = $this->redis_config['cache']['write'];
        }
        $this->conn = $conn;

        parent::__construct();
        $timeout = isset($connect['timeout']) ? $connect['timeout'] : 10;
        try {
            $this->connected = $this->connect($connect['hostname'], $connect['port'], $timeout) ? true : false;
        } catch (Exception $ex) {
            //redis异常
            $this->connected = false;
            log_message('error', serialize($ex));
        }
    }

    /**
     * 切换redis库连接
     * @global type $config
     * @param array $custom_config
     */
    function change_connect($conn = 'dr')
    {
        if ($conn == $this->conn) {
            return $this;
        }

        $connect = array();
        if ('dr' == $conn) {//数据读
            $rand_number = rand(0, count($this->redis_config['data']['read']) - 1);
            $connect = $this->redis_config['data']['read'][$rand_number];
        } elseif ('dw' == $conn) {//数据写
            $connect = $this->redis_config['data']['write'];
        }elseif ('cr' == $conn) {//缓存读
            $rand_number = rand(0, count($this->redis_config['cache']['read']) - 1);
            $connect = $this->redis_config['cache']['read'][$rand_number];
        }elseif ('cw' == $conn) {//缓存写
            $connect = $this->redis_config['cache']['write'];
        }
        $this->conn = $conn;
        parent::__construct();
        $timeout = isset($connect['timeout']) ? $connect['timeout'] : 10;

        try {
            $this->connected = $this->connect($connect['hostname'], $connect['port'], $timeout) ? true : false;
        } catch (RedisException $ex) {
            $this->connected = false;
            //redis异常
            log_message('error', serialize($ex));
        }
        return $this;
    }


    /**
     * Save cache
     *
     * @param    string    Cache key identifier
     * @param    mixed    Data to save
     * @param    int    Time to live
     * @return    bool
     */
    public function save($key, $value, $ttl = NULL)
    {
        $this->change_connect("dw");

        if ($this->connected == false) {
            return false;
        }
        return ($ttl) ? $this->setex($key, $ttl, $value) : $this->set( $key, $value);
    }

    /**
     * 设置hash
     * @param $key
     * @param $value
     * @param int $ttl
     * @return bool
     */
    public function hMsave($key, $value, $ttl = 0)
    {
        $this->red->change_connect("dw");
        if ($this->connected == false) {
            return false;
        }
        $ret = $this->red->hMset($key, $value);
        if ($ttl != 0) {
            $this->expire($key, $ttl);
        }
        return $ret;
    }

}

