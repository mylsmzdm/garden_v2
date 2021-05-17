<?php

/**
 * redis访问封装，原library下的red类
 * 调用方法： 
 * $redis_comment_cache = $this->load->redis('comment_cache');
 * $redis_comment_cache->get('aaa');
 */
class SmzdmRedis{
    
    private $connect_cfg = [];
    private $ci = null;
    private $master_connected = false;
    private $slave_connected = false;
    private $connect_num = 0;
    private $connect_instance = '';
    private $master = null;
    private $slave = null; #从库实例
    public $redis_instanse_name = 'master';  #确定主库还是从库的实例名 master slave
    public $current_host = [
        'master' => '',
        'slave' => '',
    ];
    public $slave_node_num = 0; #从库数量
    public $locked_instanse = ''; #锁定的读写库 master slave 空
    private $is_cluster = false; 
    public $support_pipeline = false;
    public $prefix = '';
    public $auto_append_prefix = false; #是否redis自动加前缀
    private $is_warm_rename_prefix = false; #是否不下线切前缀
    private $redis_temp = null;
    public $conn = null;
    static public $container = [];
    public $this_query_master = true; #当前查询使用实例
    static public $slave_command = [
        'exists' => 1,
        'keys' => 1,
        'object' => 1,
        'pttl' => 1,
        'sort' => 1,
        'ttl' => 1,
        'type' => 1,
        'scan' => 1,
        'bitcount' => 1,
        'get' => 1,
        'getbit' => 1,
        'getrange' => 1,
        'mget' => 1,
        'strlen' => 1,
        'hexists' => 1,
        'hget' => 1,
        'hgetall' => 1,
        'hkeys' => 1,
        'hlen' => 1,
        'hmget' => 1,
        'hvals' => 1,
        'hscan' => 1,
        'lindex' => 1,
        'llen' => 1,
        'lrange' => 1,
        'scard' => 1,
        'sdiff' => 1,
        'sinter' => 1,
        'sismember' => 1,
        'smembers' => 1,
        'srandmember' => 1,
        'sunion' => 1,
        'sscan' => 1,
        'zcard' => 1,
        'zcount' => 1,
        'zrange' => 1,
        'zrangebyscore' => 1,
        'zrank' => 1,
        'zrevrange' => 1,
        'zrevrangebyscore' => 1,
        'zrevrank' => 1,
        'zscore' => 1,
        'zscan' => 1,
        #pipeline 和 exec  管道操作可以用于纯从库类的查询操作，因此放到slave_command命令组里。如果使用管道必须要锁定主库或者从库
        'pipeline' => 1,
        'exec' => 1,
    ];
    #private $query_heap = []; #自动改前缀过程中 如果有pipeline和watch操作 先将操作暂存此处 待exec后执行

    /**
     * 单例调用入口
     * @staticvar smzdm_redis $container
     * @param type $conn
     * @return \smzdm_redis
     */
    public static function o($conn = 'cache') {
        if (empty(self::$container[$conn]) || !is_object(self::$container[$conn])) {
            self::$container[$conn] = new SmzdmRedis($conn);
            self::$container[$conn]->conn = $conn;
        }
        return self::$container[$conn];
    }

    function __construct($conn = 'cache') {
        $this->ci = & get_instance();
        $this->ci->load->config('cache');
        $this->ci->load->config('service/redis');
        $this->connect_instance = $conn;

        $redis_config = $this->ci->config->item('redis');
        if (!isset($redis_config[$this->connect_instance])) {
            show_error(' redis配置“'.$conn.'”不存在！');
        }
        
        $this->connect_cfg = $redis_config[$this->connect_instance];
        $this->slave_node_num = is_array($this->connect_cfg['hostname']['read']) ? count($this->connect_cfg['hostname']['read']) : 0;
        #$this->init(); 初始化不连接 懒加载
    }
    
    
    /**
     * 锁定后续操作都使用主/从库， 用于读写分离情况下的pipeline 事务等
     * @param string $name 'master'/'slave'
     */
    public function lock_instanse($name = '') {
        if ('master' != $name && 'slave' != $name) {
            $name = '';
        }
        $this->locked_instanse = $name;
    }
    
    /**
     * 清除锁定
     */
    public function unlock_instanse() {
        $this->locked_instanse = '';
    }
    
    public function init($action = '') {
        if ($this->slave_node_num > 0) {
            if (empty($this->locked_instanse)) {
                if (isset(self::$slave_command[$action])) {
                    $this->redis_instanse_name = 'slave';
                } else {
                    $this->redis_instanse_name = 'master';
                }
            } else {
                #如果锁定使用主或者从，则要确保只有读操作可以使用从库，写操作不能使用从库
                if (isset(self::$slave_command[$action])) {
                    $this->redis_instanse_name = $this->locked_instanse;
                }else{
                    $this->redis_instanse_name = 'master';
                }
            }
        }
        #如果已经连接过，则直接返回
        if ($this->{"{$this->redis_instanse_name}_connected"}) {
            return;
        }
        
        
        $timeout = isset($this->connect_cfg['timeout']) ? $this->connect_cfg['timeout'] : 10;
        
        $this->is_warm_rename_prefix = isset($this->connect_cfg['is_warm_rename_prefix']) ? $this->connect_cfg['is_warm_rename_prefix'] : false;
        $this->is_cluster = isset($this->connect_cfg['is_cluster']) ? $this->connect_cfg['is_cluster'] : false;
        $this->support_pipeline = isset($this->connect_cfg['support_pipeline']) ? $this->connect_cfg['support_pipeline'] : false;
        if (!$this->is_cluster) { #传统单点
            if ($this->slave_node_num == 0) { #不支持读写分离
                $this->{$this->redis_instanse_name} = new Redis();
                $this->current_host[$this->redis_instanse_name] = $this->connect_cfg['hostname']['write'][0];
                isset($this->ci->cat) && $this->ci->cat->start('Redis Connect', "{$this->connect_instance} => {$this->connect_cfg['hostname']['write'][0]}:{$this->connect_cfg['port']} timeout=>{$timeout}");
                $this->{$this->redis_instanse_name}->connect($this->connect_cfg['hostname']['write'][0], $this->connect_cfg['port'], $timeout);
                $this->set_auth($this->{$this->redis_instanse_name});
                if ($this->connect_cfg['database'] > 0) {
                    $this->{$this->redis_instanse_name}->select($this->connect_cfg['database']);
                }
            } else { #支持读写分离
                if ($this->redis_instanse_name == 'slave') {
                    #从库
                    $connect_hostname = $this->connect_cfg['hostname']['read'][rand(0, $this->slave_node_num - 1)];
                }else{
                    $connect_hostname = $this->connect_cfg['hostname']['write'][0];
                }
                $this->{$this->redis_instanse_name} = new Redis();
                $this->current_host[$this->redis_instanse_name] = $connect_hostname; 
                isset($this->ci->cat) && $this->ci->cat->start("Redis Connect", "{$this->connect_instance} {$this->redis_instanse_name} => {$connect_hostname}:{$this->connect_cfg['port']} timeout=>{$timeout}");
                $this->{$this->redis_instanse_name}->connect($connect_hostname, $this->connect_cfg['port'], $timeout); 
                $this->set_auth($this->{$this->redis_instanse_name});
            }
        } else { #集群
            $hostname_str = implode(' ', $this->connect_cfg['cluster_node_list']);
            $this->current_host[$this->redis_instanse_name] = $this->connect_cfg['cluster_node_list'][0];
            isset($this->ci->cat) && $this->ci->cat->start('Redis Cluster Connect', "{$this->connect_instance} => {$hostname_str} timeout=>{$timeout}");
            $this->{$this->redis_instanse_name} = new RedisCluster(NULL, $this->connect_cfg['cluster_node_list'], $this->connect_cfg['timeout'], $this->connect_cfg['read_timeout']);
        }
        if (!empty($this->connect_cfg['prefix'])) { 
            $this->prefix = $this->connect_cfg['prefix'];
            #自动加前缀
            if(isset($this->connect_cfg['auto_append_prefix']) && $this->connect_cfg['auto_append_prefix']) {
                $this->auto_append_prefix = true;
                $this->{$this->redis_instanse_name}->setOption(Redis::OPT_PREFIX, $this->connect_cfg['prefix']); 
                
            }
        }
        isset($this->ci->cat) && $this->ci->cat->end();
        $this->{"{$this->redis_instanse_name}_connected"} = true;
        $this->connect_num++;
    }
    
    public function set_prefix($prefix = '') {
        $prefix = empty($prefix) ? $this->prefix : $prefix;
        $this->{$this->redis_instanse_name}->setOption(Redis::OPT_PREFIX, $prefix);
    }
    
    public function clean_prefix() {
        $this->{$this->redis_instanse_name}->setOption(Redis::OPT_PREFIX, '');
    }
    
    ################创建另一个redis连接 用于改名操作(主要解决到原连接处理管道和事务的时候 请求积攒的问题) 修改前缀使用，修改后可删掉################
    public function init_temp() {
        if (is_object($this->redis_temp)) {
            return;
        }
        
        $timeout = isset($this->connect_cfg['timeout']) ? $this->connect_cfg['timeout'] : 10;
        
        $this->redis_temp = new Redis();
        isset($this->ci->cat) && $this->ci->cat->start('Redis Temp Connect', "{$this->connect_cfg['hostname']['write'][0]}:{$this->connect_cfg['port']} db=>{$this->connect_cfg['database']} timeout=>{$timeout}");
        $this->redis_temp->connect($this->connect_cfg['hostname']['write'][0], $this->connect_cfg['port'], $timeout);
        $this->set_auth($this->redis_temp);
        if ($this->connect_cfg['database'] > 0) {
            $this->redis_temp->select($this->connect_cfg['database']);
        }
        
        if (!empty($this->connect_cfg['prefix'])) {
            $this->prefix = $this->connect_cfg['prefix'];
            #不是用全局前缀
            #$this->redis_temp->setOption(Redis::OPT_PREFIX, $this->connect_cfg['prefix']);
        }
        isset($this->ci->cat) && $this->ci->cat->end();
        
    }
    
    /**
     * 特殊情况使用 日常禁止使用
     * @return type
     */
    public function orig_redis() {
        return $this->master;
    }
    
    /**
     * redis操作 所有redis操作应该收入此方法 以便统一控制
     * 
     * @param type $action
     * @param type $arguments
     * @return boolean
     */
    public function __call($action, $arguments = []) {
        $action = strtolower($action);
        $this->init($action);
        
        
        if ($this->{"{$this->redis_instanse_name}_connected"} && method_exists($this->{$this->redis_instanse_name}, $action)) {

            #判断如果是管道命令必须锁定主库或者从库，否则报错
            if (($action == 'pipeline' || $action == 'exec') && empty($this->locked_instanse)) {
                #exit("pipeline and exec must use when locking redis instance!");
                isset($this->ci->cat) && $this->ci->cat->event('Cache.redis.pipelineexec', $action);
            }
            #如果锁定了使用从库，但是使用了写命令，则报错
            if($this->locked_instanse == 'slave' and !isset(self::$slave_command[$action])) {
                #exit("locked_instance to slave ,but use command not in slave_command array!or you should unlock to use command not in slave_command");
                isset($this->ci->cat) && $this->ci->cat->event('Cache.redis.slaveusewrite', $action);
            }

            $key_name = isset($arguments[0]) ? $arguments[0] : '';
            
            ################不下线切前缀(迁入集群后应当删掉此段代码，减少判断################
            if ($this->is_warm_rename_prefix && !empty($this->prefix)) {
                $this->init_temp();
                $key2_name = '';
                #如果暂存处有命令，需要继续暂存命令
                #if (!empty($this->query_heap)) {
                #    $this->query_heap[] = [$action, $arguments];
                #}
                #
                #判断是否是不支持的命令
                if (in_array($action, ['scan'])) {
                    goto ARCHOR_AFTER_RENAME_PREFIX;
                }
                #判断是否有key 没有略过
                if (empty($key_name)) {
                    goto ARCHOR_AFTER_RENAME_PREFIX;
                }
                #特殊命令处理 -- param2也是key
                if (in_array($action, ['sdiff', 'sinter', 'sunion', 'zunionstore', 'zinterstore', 'watch', 'unwatch'])) {
                    $key2_name = isset($arguments[1]) ? $arguments[1] : '';
                }
                #如果key是数组 报警 手动特殊处理
                if (!is_scalar($key_name)) {
                    $alert_key_name = json_encode($key_name);
                    isset($this->ci->cat) && $this->ci->cat->event("Redis Rename Prefix ERROR", "redis->{$action}({$alert_key_name})");
                    goto ARCHOR_AFTER_RENAME_PREFIX;
                }
                /*#判断是否是事务或管道操作
                if (in_array($action, ['pipeline', 'watch', 'multi'])) {
                    $this->query_heap[] = [$action, $arguments];
                }
                #判断是否事务终止，终止时需要去除暂存区命令一次性执行。
                if (in_array($action, ['exec', 'unwatch', 'discard'])) {
                    
                }*/
                
                #key开头 不存在前缀"u_"
                if(stripos($key_name, $this->prefix) !== 0) {
                    if(!$this->redis_temp->exists($this->prefix.$key_name)) {
                        #$redis->rename($key_name, $prefix.$key_name);
                        isset($this->ci->cat) && $this->ci->cat->start("Redis Rename Prefix", "{$key_name} => {$this->prefix}{$key_name}");
                        $this->redis_temp->rename($key_name, $this->prefix . $key_name);
                        isset($this->ci->cat) && $this->ci->cat->end();
                        #全局搜索代码 暂时没有发现存在param2情况， 先注释该代码
                        /*if (!empty($key2_name) && $this->redis_temp->exists($this->prefix.$key2_name)) {
                            isset($this->ci->cat) && $this->ci->cat->start("Redis Rename Prefix Key2", "{$key2_name} => {$this->prefix}{$key2_name}");
                            $this->redis_temp->rename($key2_name, $this->prefix . $key2_name);
                            isset($this->ci->cat) && $this->ci->cat->end();
                        }*/
                    }
                    $key_name = $this->prefix.$key_name;
                    $arguments[0] = $key_name;
                }
                
                #判断池子中是否有不带前缀的key (已经有带前缀的key,说明已作转变, 如果带与不带前缀的key都没有,说明还未设置 这两种情况直接走后面正常请求流程)
                /*if ($this->redis_temp->exists($key_name)) {
                    #如果是未处理前缀的key,直接处理
                    isset($this->ci->cat) && $this->ci->cat->start("Redis Rename Prefix", "{$key_name} => {$this->prefix}{$key_name}");
                    $this->redis_temp->rename($key_name, $this->prefix . $key_name);
                    isset($this->ci->cat) && $this->ci->cat->end();
                    if (!empty($key2_name) && $this->redis_temp->exists($key2_name)) {
                        isset($this->ci->cat) && $this->ci->cat->start("Redis Rename Prefix Key2", "{$key2_name} => {$this->prefix}{$key2_name}");
                        $this->redis_temp->rename($key2_name, $this->prefix . $key2_name);
                        isset($this->ci->cat) && $this->ci->cat->end();
                    }
                    
                }*/
                
                ARCHOR_AFTER_RENAME_PREFIX:
            }
            ################不下线切前缀结束(迁入集群后应当删掉此段代码，减少判断)################
            #redis自动加前缀
            if($this->auto_append_prefix && !empty($key_name)) {
                if(stripos($key_name, $this->prefix) === 0) {
                    $key_name = substr($key_name, strlen($this->prefix));
                }

                $arguments[0] = $key_name;

                foreach ($arguments as $k => $name) {
                    if($k >= 1 && is_string($name)) {
                        if(stripos($name, $this->prefix) === 0) {
                            $arguments[$k] = substr($name, strlen($this->prefix));
                        }
                    }
                }
            }
            
            $abstract_name = $this->abstraction_key($key_name);
            #$params = json_encode($arguments);
            
            if (false  #预留开关，可以禁止对 youhui_data  redis库写操作
                    && in_array($this->connect_instance, ['youhui_data']) 
                    && in_array($action, ['set', 'setex', 'setnx', 'expire', 'expireat', 'del', 'rename', 'append', 'decr', 'decrby', 'getset', 'incr', 'incrby', 'incrbyfloat', 'mset', 'msetnx', 'psetex', 'set', 'setbit', 'setex', 'setnx', 'setrange', 'hdel', 'hincrby', 'hincrbyfloat', 'hmset', 'hset', 'hsetnx', 'blpop', 'brpop', 'brpoplpush', 'linsert', 'lpop', 'lpush', 'lpushx', 'lrem', 'lset', 'ltrim', 'rpop', 'rpoplpush', 'rpush', 'rpushx', 'sadd', 'smove', 'spop', 'srem', 'zadd', 'zincrby', 'zrem', 'zremrangebyrank', 'zremrangebyscore', 'exec'])) {
                $result = false;
            } else {
                #isset($this->ci->cat) && $this->ci->cat->start('Redis', "{$this->connect_instance} => {$action}", $abstract_name);
                isset($this->ci->cat) && $this->ci->cat->start("Cache.redis-{$this->connect_instance}", "{$this->connect_instance} {$this->redis_instanse_name} {$action}", $abstract_name);
                isset($this->ci->cat) && $this->ci->cat->event('Cache.redis.server', $this->current_host[$this->redis_instanse_name]);
                $result_status = 0;
                try {
                    $result = call_user_func_array([$this->{$this->redis_instanse_name}, $action], $arguments);

                    ##### 大key筛查 开始 #####
                    if (!empty($arguments[0])) {
                        $str_result = is_string($result) ? $result : serialize($result);
                        $result_len = strlen($str_result);
                        $keyname = $arguments[0];
                        if ($result_len > 14336) { #>14k
                            $size = "";
                            if ($result_len > 1048576) { #1m
                                $size = "1Mb+";
                            } elseif ($result_len > 512000) { #0.5m
                                $size = "500Kb+";
                            } else if ($result_len > 102400) { #100k
                                $size = "100Kb+";
                            } else if ($result_len > 14336) { #14k
                                $size = intval($result_len / 10240) * 10; #每10K一档
                                $size = "{$size}Kb+";
                            }
                            isset($this->ci->cat) && $this->ci->cat->event("{$this->connect_instance}.BigKey: {$size}", $keyname);
                        }
                    }
                    ##### 大key筛查 结束 #####
                } catch (Exception $e) {
                    $this->ci->cat->exception($e);
                    $result = false;
                    $result_status = $e->getCode();
                }
                
                isset($this->ci->cat) && $this->ci->cat->end(null, $result_status);
            }
            if (is_object($result)) {
                return $this; #链式操作 每个命令强制走__call
            } else {
                return $result; #常规数据返回
            }
            #return $this;
        } else {
            return false;
        }
    }
    
    public function abstraction_key($key) {
        if (!is_scalar($key)) {
            $key = json_encode($key);
        }
        $abstract_key = preg_replace('/:\d+$/', ':{num}', $key);
        if (!empty($abstract_key)) {
            $abstract_key = $this->prefix . $abstract_key;
        }
        return $abstract_key;
    }

    /**
     * 设置一组缓存
     * @param type $array
     * @param type $prefix
     * @param type $expiration
     */
    public function set_array($array, $prefix = '', $expiration = 0) {
        if ($this->support_pipeline) {
            $this->lock_instanse('master');
            $this->pipeline();
        }
        $prefix = empty($prefix) ? '' : $prefix . ':';
        isset($this->ci->cat) && $this->ci->cat->start('Redis', 'set_array', "{$prefix}:{xxx}");
        foreach ($array as $key => $value) {
            $this->setex_auto_serl($prefix . $key, $expiration, $value);
        }
        isset($this->ci->cat) && $this->ci->cat->end();
        if ($this->support_pipeline) {
            $this->exec();
            $this->unlock_instanse();
        }
    }

    /**
     * 获取一组缓存, 返回缓存中存在与不存在的数据
     * @param type $keys
     * @param type $prefix
     * @return int
     */
    public function get_array($keys, $prefix = '') {
        $return = array('have' => array(), 'nohave' => array());
        if ($this->support_pipeline) {
            $this->lock_instanse('slave');
            $this->pipeline();
        }
        
        $prefix = empty($prefix) ? '' : $prefix . ':';
        #isset($this->ci->cat) && $this->ci->cat->start("Redis get_array", "{$prefix}:{xxx}", json_encode($keys));
        $result = [];
        foreach ($keys as $key) {
            if ($this->support_pipeline) {
                $this->get($prefix . $key);
            } else {
                $result[] = $this->get($prefix . $key);
            }
        }
        if ($this->support_pipeline) {
            $result = $this->exec();
            $this->unlock_instanse();
        }

        $n = 0;
        foreach ($keys as $key) {
            if (isset($result[$n]) && false !== $result[$n]) {
                if ($this->is_json($result[$n])) {
                    $result[$n] = json_decode($result[$n], true);
                }
                $return['have'][$key] = $result[$n];
            } else {
                $return['nohave'][$key] = 1;
            }
            $n++;
        }
        #isset($this->ci->cat) && $this->ci->cat->end();
        return $return;
    }

    /**
     * setex自动序列化
     * @param type $key
     * @param type $expire
     * @param type $value
     * @return type
     */
    public function setex_auto_serl($key, $expire, $value) {
        if (empty($value)) {
            $value = '';
        } elseif (!is_scalar($value)) {
            $value = json_encode($value);
            $value = (string) $value;
        }
        #$abstract_key = $this->abstraction_key($key);
        #isset($this->ci->cat) && $this->ci->cat->start("Redis setex", $abstract_key);
        $result = $this->setex($key, $expire, $value);
        #isset($this->ci->cat) && $this->ci->cat->end();
        
        return $result;
    }

    /**
     * get自动解序列化
     * @param type $key
     * @return type
     */
    public function get_auto_serl($key) {
        $abstract_key = $this->abstraction_key($key);
        #isset($this->ci->cat) && $this->ci->cat->start("Redis get", $abstract_key);
        $result = $this->get($key);
        if ($this->is_json($result)) {
            $result = json_decode($result, true);
        }
        #isset($this->ci->cat) && $this->ci->cat->end();
        return $result;
    }

    function is_json($string) {
        if (empty($string)) {
            return false;
        }
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 获取缓存数据通用入口, 缓存时间与空值缓存时间通过配置文件CacheConfig::$ttl控制, 空值缓存的前缀以_null结尾
     * 
     * @param string 缓存前缀. 如果以"_list"结尾, 启用缓存组; 否则为单个值缓存, 当单个值缓存时, 以此参数为缓存的key名.(即如果缓存的内容需要根据key来区分，前缀结尾需增加"_list",否则不需要)
     * @param mixed 调用参数 实例化写法: array($实例化变量, '方法名'); 静态调用写法:(string) '类名::静态方法名' 或 array('类名','静态方法名'); 单例模式写法: array(类名::单例方法(), '方法名')
     * @param array 调用方法的参数, 生成的key以prefix+(第一个参数)命名
     * 
     * @return mixed
     */
    public function get_data($prefix, $method, $func_params = array()) {
        $method_txt = '';
        if (!is_scalar($method)) {
            $method_txt = isset($method[1]) ? $method[1] : '';
        }
        isset($this->ci->cat) && $this->ci->cat->start('Redis', 'get_data', "prefix=>{$prefix} method=>{$method_txt}");
        $ttl = array(0, 0);
        if (array_key_exists($prefix, CacheConfig::$ttl)) {
            $ttl[0] = CacheConfig::$ttl[$prefix];
            if (array_key_exists($prefix . '_null', CacheConfig::$ttl)) {
                $ttl[1] = CacheConfig::$ttl[$prefix . '_null'];
            } else {
                $ttl[1] = CacheConfig::$ttl['null'];
            }
        }

        if (substr($prefix, -5) === '_list') {
            //keys 缓存组
            $final_data = $this->get_rows_data($prefix, $method, $ttl, $func_params);
        } else {
            //key 单个变量缓存
            $final_data = $this->get_row_data($prefix, $method, $ttl, $func_params);
        }
        isset($this->ci->cat) && $this->ci->cat->end();
        return $final_data;
    }

    /**
     * 判断方法是否存在
     * 
     * @param mixed $method
     * @return boolean
     */
    public function is_user_func_exists($method) {
        $is_exists = false; 
        if (is_string($method)) {//普通调用
            $method = explode('::', $method);
            if (count($method) == 2 && method_exists($method[0], $method[1])) {
                $is_exists = true;
            }
        } else if (is_array($method) && count($method) == 2) {//静态方法调用
            if (method_exists($method[0], $method[1])) {
                $is_exists = true;
            }
        }
        return $is_exists;
    }

    public function get_row_data($key, $method, $ttl, $func_params) {
        $cache_data = array();
        if ($ttl[0] > 0) {//用缓存
            $cache_data = $this->get_auto_serl($key);
        }

        if (empty($cache_data) && $cache_data !== '') {
            if ($this->is_user_func_exists($method)) {
                $cache_data = call_user_func_array($method, $func_params);
            } else {
                return false;
            }

            if ($ttl[0] > 0) {//用缓存
                if (empty($cache_data)) {
                    $cache_data = null;
                    if ($ttl[1] > 0) {//空值缓存
                        $this->setex_auto_serl($key, $ttl[1], null);
                    }
                } else {
                    $this->setex_auto_serl($key, $ttl[0], $cache_data);
                }
            }
        }
        return $cache_data;
    }

    public function get_rows_data($prefix, $method, $ttl, $func_params) {
        $cache_data = array('have' => array(), 'nohave' => array());

        $keys = array_shift($func_params);
        if (is_scalar($keys)) {
            array_unshift($func_params, $keys);
            $keys = $prefix;
            foreach ($func_params as $param) {
                if (is_scalar($param)) {
                    $keys .= ':' . $param;
                }
            }
            return $this->get_row_data($keys, $method, $ttl, $func_params);
        }

        if ($ttl[0] > 0) {#用缓存
            $cache_data = $this->get_array($keys, $prefix);
        } else {
            foreach ($keys as $key) {
                $cache_data['nohave'][$key] = 1;
            }
        }
        $rows = array();
        if (!empty($cache_data['nohave']) || empty($keys)) {
            if ($this->is_user_func_exists($method)) {
                array_unshift($func_params, array_keys($cache_data['nohave']));
                $rows = call_user_func_array($method, $func_params);
            } else {
                return false;
            }
            if ($ttl[0] > 0 && false !== $rows && !empty($cache_data['nohave'])) {#用缓存
                $prefix = empty($prefix) ? '' : $prefix . ':';
                if ($this->support_pipeline) {
                    $this->lock_instanse('master');
                    $this->pipeline();
                }
                foreach ($cache_data['nohave'] as $key => $v) {
                    if (array_key_exists($key, $rows)) {
                        $this->setex_auto_serl($prefix . $key, $ttl[0], $rows[$key]);
                    } else {
                        if ($ttl[1] > 0) {#空值缓存
                            $this->setex_auto_serl($prefix . $key, $ttl[1], null);
                        }
                        $rows[$key] = null;
                    }
                }
                if ($this->support_pipeline) {
                    $this->exec();
                    $this->unlock_instanse();
                }
            }
        }
        if (!is_array($rows)) {
            $rows = [];
        }

        $final_data = $cache_data['have'] + $rows;

        return $final_data;
    }

    /**
     * Save cache
     *
     * @param   string  Cache key identifier
     * @param   mixed   Data to save
     * @param   int Time to live
     * @return  bool
     */
    public function save($key, $value, $ttl = NULL) {
        $abstract_key = $this->abstraction_key($key);
        #isset($this->ci->cat) && $this->ci->cat->start("Redis save", "{$abstract_key} ttl=>{$ttl}");
        $result = ($ttl) ? $this->setex($key, $ttl, $value) : $this->set($key, $value);
        #isset($this->ci->cat) && $this->ci->cat->end();
        return $result;
    }
    
    public function smadd($key, Array $arr, $ttl = 0) {
        $result = $this->sadd_array($key, $arr, $ttl);
        return $result;
    }

    /**
     * 设置集合
     * @param type $key
     * @param array $arr
     * @param type $ttl
     */
    public function sadd_array($key, Array $arr, $ttl = 0) {
        $abstract_key = $this->abstraction_key($key);
        #isset($this->ci->cat) && $this->ci->cat->start("Redis smadd", "{$abstract_key} ttl=>{$ttl}");
        array_unshift($arr, $key);
        $result = call_user_func_array(array($this, 'sadd'), $arr);
        if ($ttl != 0) {
            $this->expire($key, $ttl);
        }
        #isset($this->ci->cat) && $this->ci->cat->end();
        return $result;
    }

    public function lmpush($key, Array $arr) {
        $abstract_key = $this->abstraction_key($key);
        #isset($this->ci->cat) && $this->ci->cat->start("Redis lmpush", $abstract_key);
        array_unshift($arr, $key);
        $result = call_user_func_array(array($this, 'lpush'), $arr);
        #isset($this->ci->cat) && $this->ci->cat->end();
        return $result;
    }
    
    public function rmpush($key, Array $arr) {
        $abstract_key = $this->abstraction_key($key);
        #isset($this->ci->cat) && $this->ci->cat->start("Redis rmpush", $abstract_key);
        array_unshift($arr, $key);
        $result = call_user_func_array(array($this, 'rpush'), $arr);
        #isset($this->ci->cat) && $this->ci->cat->end();
        return $result;
    }

    /**
     * 设置单点redis密码， 密码从配置中心获取
     */
    public function set_auth($redis_instance) {
        if(class_exists('Dogx') && $redis_instance instanceof Redis) {
            $pass_result = Dogx::get_by_keys(["zdm-public.redis.pass"]);
            if(!empty($pass_result['zdm-public.redis.pass'])) {
                $result = $redis_instance->auth($pass_result['zdm-public.redis.pass']);
                //isset($this->ci->cat) && $this->ci->cat->event("Redis Set Auth Password ", '');
                return $result;
            }
        }
        return false;
    }

    /**
     * 用户CI最后关闭所有redis连接
     */
    static function close_all() {
        foreach (self::$container as $redis_inst => $obj) {
            $obj->close();
            /*if ($obj->{"{$obj->redis_instanse_name}_connected"} && ($obj->master instanceof Redis || $obj->master instanceof RedisCluster)) {
                $cluster = $obj->is_cluster ? 'Cluster' : '';
                isset($obj->ci->cat) && $obj->ci->cat->start("Redis{$cluster} Closed By CI", $obj->connect_instance);
                try {
                    $result = $obj->master->close();
                } catch (Exception $e) {
                    $result = false;
                }
                isset($obj->ci->cat) && $obj->ci->cat->end();
                if ($result) {
                    $obj->{"{$obj->redis_instanse_name}_connected"} = false;
                    $obj->connect_num = 0;
                    self::$container[$redis_inst] = null;
                    unset(self::$container[$redis_inst]);
                }
            }*/
        }
    }
    
    /**
     * 关闭redis连接
     */
    function close() {
        $result = null;
        if ($this->master_connected && ($this->master instanceof Redis || $this->master instanceof RedisCluster)) {
            $cluster = $this->is_cluster ? 'Cluster' : '';
            isset($this->ci->cat) && $this->ci->cat->start("Redis{$cluster} Close", $this->connect_instance);
            try {
                $result = $this->master->close();
            } catch (Exception $e) {
                $this->ci->cat->exception($e);
                $result = false;
            }
            isset($this->ci->cat) && $this->ci->cat->end();
            if ($result) {
                $this->master_connected = false;
                $this->connect_num = 0;
                self::$container[$this->conn] = null;
                unset(self::$container[$this->conn]);
            }
        }
        if ($this->slave_node_num > 0) {
            if ($this->slave_connected && ($this->slave instanceof Redis || $this->slave instanceof RedisCluster)) {
                isset($this->ci->cat) && $this->ci->cat->start("Redis Slave Close", $this->connect_instance);
                try {
                    $result = $this->slave->close();
                } catch (Exception $e) {
                    $result = false;
                }
                isset($this->ci->cat) && $this->ci->cat->end();
                if ($result) {
                    $this->slave_connected = false;
                    $this->connect_num = 0;
                }
            }
        }
        return $result;
    }
    
    function __destruct() {
        $this->close();
    }
}
