<?php

/**
 *
 *
 * @file CatClient.php
 * @author Jxt
 * @creation_date 2016-7-7 13:31:42
 *
 * 10.10.105.242 2280
 */
if (!class_exists('CatClient')) {
class CatClient {

    public $is_open = true; #总开关
    public $cat_config_server_list = [
        'cat-itoamms.smzdm.com',
    ];
    private $_cat_server_addr;
    private $_version = 'PT1';
    private $_domain = '';
    private $_hostname = '';
    private $_server_ip = '127.0.0.1';
    private $_client_ip = '127.0.0.1';
    private $_message_id = '';
    private $_parent_message_id = '';
    private $_root_message_id = '';
    private $_spacing = "\t";
    private $_depth = 0;
    private $_trans_container = [];
    private $_head = '';
    private $_body = '';
    private $_data = '';
    private $redis = [];
    public $call_from_method = '';
    public $error_hash = [
        0 => 'NONE',
        1 => 'ERROR',
        2 => 'WARNING',
        4 => 'PARSE',
        8 => 'NOTICE',
        16 => 'CORE_ERROR',
        32 => 'CORE_WARNING',
        64 => 'COMPILE_ERROR',
        128 => 'COMPILE_WARNING',
        256 => 'USER_ERROR',
        512 => 'USER_WARNING',
        1024 => 'USER_NOTICE',
        2048 => 'STRICT',
        4096 => 'RECOVERABLE_ERROR',
        8192 => 'DEPRECATED',
        16384 => 'USER_DEPRECATED',
        32767 => 'ALL',
    ];
    public $start_memory = 0;
    public $uri = '';
    
    private $redis_conf = [
        'hosts' => [
            'default' => ['cat_cache_redis_m01', '6379'],
            'api.smzdm.com' => ['cat_api_cache_redis_m01', '6379'],
            'userapi.smzdm.com' => ['cat_user_cache_redis_m01', '6379'],
            'commentapi.smzdm.com' => ['cat_user2_cache_redis_m01', '6379'],
            'zhiyou.smzdm.com' => ['cat_user2_cache_redis_m01', '6379'],
            'h5.smzdm.com' => ['cat_user2_cache_redis_m01', '6379'],
            'user.bgm.smzdm.com' => ['cat_user2_cache_redis_m01', '6379'],
            'user.job' => ['cat_user2_cache_redis_m01', '6379'],
            'duihuan.smzdm.com' => ['cat_user2_cache_redis_m01', '6379'],
            'duihuanapi.smzdm.com' => ['cat_user2_cache_redis_m01', '6379'],
            '2api.smzdm.com.smzdm.com' => ['cat_user2_cache_redis_m01', '6379'],
            '2.smzdm.com.smzdm.com' => ['cat_user2_cache_redis_m01', '6379'],
            'zhiyou.m.smzdm.com' => ['cat_user2_cache_redis_m01', '6379'],
            'searchapi.smzdm.com	' => ['cat_article_cache_redis_m01', '6379'],
            'youhuiapi.smzdm.com' => ['cat_article_cache_redis_m01', '6379'],
            'youhui.smzdm.com' => ['cat_article_cache_redis_m01', '6379'],
            'postapi.smzdm.com' => ['cat_article_cache_redis_m01', '6379'],
            'post.smzdm.com' => ['cat_article_cache_redis_m01', '6379'],
            'wikiapi.smzdm.com' => ['cat_article_cache_redis_m01', '6379'],
            'mall.service.smzdm.com' => ['cat_public_cache_redis_m01', '6379'],
            'dingyueapi.smzdm.com' => ['cat_public_cache_redis_m01', '6379'],
            'widgetapi.smzdm.com' => ['cat_public_cache_redis_m01', '6379'],
            'api.category.smzdm.com' => ['cat_public_cache_redis_m01', '6379'],
            'api.tag.smzdm.com' => ['cat_public_cache_redis_m01', '6379'],
            'api.brand.smzdm.com' => ['cat_public_cache_redis_m01', '6379'],
        ],
        'timeout' => 0.1,
    ];
    public $final_error_code = 0; #最终请求的错误码

    function __construct($params = []) {
        if ($this->is_open) {
            $this->is_open = isset($params['is_open']) ? $params['is_open'] : $this->is_open;
        }#如果总开关关闭,就是关闭
        if (!$this->is_open) {
            return;
        }
        #$this->start_memory = memory_get_usage();

        $num = rand(1, 100);
        if ($num > 100) {
            $this->is_open = false;
            return;
        }

        $this->_domain = !empty($params['domain']) ? $params['domain'] : 'Unknow Domain';
        $this->_hostname = gethostname();
        #获取上一个请求传入的消息id
        $params['message_id'] = isset($_SERVER['HTTP__CATCHILDMESSAGEID']) ? $_SERVER['HTTP__CATCHILDMESSAGEID'] : '';
        $params['parent_message_id'] = isset($_SERVER['HTTP__CATPARENTMESSAGEID']) ? $_SERVER['HTTP__CATPARENTMESSAGEID'] : '';

        $this->_server_ip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
        $this->_client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        if (empty($params['message_id'])) {
            $this->_message_id = /*$this->_root_message_id =*/
                $this->generate_message_id();
        } else {
            $this->_message_id = $params['message_id'];
        }
        $this->_message_id = empty($params['message_id']) ? $this->generate_message_id() : $params['message_id'];
        #$this->_root_message_id = empty($root_message_id) ? $this->_message_id : $root_message_id; #目前传值会导致CAT后台日志查询不到 等待解决
        $this->_parent_message_id = empty($params['parent_message_id']) ? '' : $params['parent_message_id'];
        $this->_cat_server_addr = $this->get_server_addr();

        set_error_handler([$this, 'smzdm_warning_handler'], E_ALL);
        // set_exception_handler([$this, 'smzdm_exception_handler']);
    }

    /**
     * PHP notice warning 记录cat set_error_handler只能捕获到一些NOTICE WARNING级别的警告
     * @staticvar array $error_hash
     * @param type $error_code
     * @param type $error_msg
     * @param type $error_file
     * @param type $error_line
     * @param type $error_context
     * @return type
     */
    function smzdm_warning_handler($error_code, $error_msg, $error_file, $error_line) {
        if ($error_code == E_STRICT) {
            return;
        }
        if (strpos($error_msg, 'mcrypt_generic_init') === 0) {
            #mcrypt_generic_init(): Key size too large; supplied length: 20, max: 8 in /data/webroot/phpsrc/base_framework_v2/api/zdm_application/libraries/Des.php on line 27
            #放过这种报错
            return;
        }
        $this->final_error_code = "PHP:[{$this->error_hash[$error_code]}]";
        #$get_info = isset($error_context['_GET']) ? $error_context['_GET'] : '';
        #$post_info = isset($error_context['_POST']) ? $error_context['_POST'] : '';
        #$cookie_info = isset($error_context['_COOKIE']) ? $error_context['_COOKIE'] : '';

        #$stack_arr = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $stack_arr = debug_backtrace();
        $stack_info = '';
        foreach ($stack_arr as $n => $row) {
            if (0 == $n) {
                continue;
            }

            $file = isset($row['file']) ? $row['file'] : '';
            $line = isset($row['line']) ? "({$row['line']})" : '';
            if (empty($line)) {
                $line = '[internal function]:';
            }
            $type = isset($row['type']) ? $row['type'] : '';
            $class = isset($row['class']) ? "{$row['class']}{$type}" : '';
            $args = isset($row['args']) ? $row['args'] : [];
            ###debug###
            #if (1 == $n && isset($row['function']) && strpos($row['function'], 'json_encode') === 0) {
            #$stack_info .= '<<' . serialize($stack_arr[$n]['args']) . '>>'."\n";
            #}
            ###debug###
            foreach ($args as $k => $arg) {
                if (is_string($arg)) {
                    if (mb_strlen($arg, 'UTF-8') > 50) {
                        #$arg = substr($arg, 0, 50) . '...';
                        $arg = mb_substr($arg, 0, 50, 'UTF-8') . '...';
                    }
                    $args[$k] = "'{$arg}'";
                } elseif (is_null($arg)) {
                    $arg = NULL;
                } elseif (is_bool($arg)) {
                    $arg = $arg ? 'TRUE' : 'FALSE';
                } elseif (!is_scalar($arg)) {
                    $args[$k] = gettype($arg);
                }
            }
            
            if (isset($row['function'])) {
                if (in_array($row['function'], ['mysqli_real_connect', 'mysqli_connect'])) {
                    #处理mysql报错 过滤掉栈里的密码
                    $args[3] = '[password]';
                }
                $args = implode(', ', $args);
                $function = "{$class}{$row['function']}({$args})";
            } else {
                $function = '';
            }

            $stack_info .= "#{$n} {$file}{$line} {$function} \n";
        }


        #$this->event("E_{$error_hash[$error_code]}", "{$error_msg} in {$error_file} on line {$error_line}", ['GET' => $get_info, 'POST' => $post_info, 'COOKIE' => $cookie_info], $this->final_error_code);
        $this->event("E_{$this->error_hash[$error_code]}", "{$error_msg} in {$error_file} on line {$error_line}", $stack_info, $this->final_error_code);
        $this->event('Error', "E_{$this->error_hash[$error_code]}", $stack_info, "{$error_msg} in {$error_file} on line {$error_line}"); #报错大盘
        if (function_exists('_error_handler')) {
            #新版ci叫"_error_handler"
            _error_handler($error_code, $error_msg, $error_file, $error_line);
        } elseif (function_exists('_exception_handler')) {
            #老版CI叫"_exception_handler"
            _exception_handler($error_code, $error_msg, $error_file, $error_line);
        }
    }

    /**
     * 致命错误捕获, 在register_shutdown_function中被调用
     */
    function smzdm_error_handler() {
        if ($e = error_get_last()) {
            if (strpos($e['message'], 'PHP Startup: It is not safe to rely on the system\'s timezone settings') === 0) {
                return;
            }
            $this->final_error_code = "PHP:[{$this->error_hash[$e['type']]}]";
            $message = "{$this->error_hash[$e['type']]}: {$e['message']} in {$e['file']} on line {$e['line']}";
            $this->event("F_{$this->error_hash[$e['type']]}", $message, null, $this->final_error_code);
            $this->event('Error', "F_{$this->error_hash[$e['type']]}", null, $message); #报错大盘
        }
    }

    /**
     * 未捕获的异常记录cat
     * @param type $e
     */
    function smzdm_exception_handler($e) {
        $exception_name = get_class($e);
        $message = "{$exception_name}: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}";
        $this->event("UNCAUGHT_EXCEPTION", $message, $e->getTraceAsString(), $exception_name);
        $this->event("RuntimeException", $exception_name, $e->getTraceAsString(), $exception_name); #报错大盘
        http_response_code(405);
    }

    /**
     * catch中捕获异常调用此方法记录至cat
     * @param type $e
     */
    function exception($e) {
        if (!is_object($e)) {
            return;
        }
        $exception_name = get_class($e);
        $message = "{$exception_name}: {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}";
        $this->event($exception_name, $message, $e->getTraceAsString(), $exception_name);
        $this->event('RuntimeException', $exception_name, $e->getTraceAsString(), $exception_name); #报错大盘
    }

    public function generate_message_id($url = '') {
        #$hexip = dechex(ip2long($this->_client_ip));
        #$hexip = dechex(ip2long('127.0.0.1'));

        /*if (!empty($url)) {
            $pathinfo = parse_url($url);
            $host = isset($pathinfo['host']) ? $pathinfo['host'] : '';
            $ip = gethostbyname($host);
            $hexip = dechex(ip2long($ip));
        } else {
            $hexip = dechex(ip2long('127.0.0.1'));
        }*/

        $hexip = dechex(ip2long('127.0.0.1'));

        $timestamp = intval(time() / 3600);
        #$uniqid = uniqid();
        #$rand = rand(1, 2100000000);
        $domain_string = '';
        if (empty($url)) {
            $domain_string = $this->_domain;
        } else {
            $u = parse_url($url);
            $domain_string = isset($u['host']) ? $u['host'] : $this->_domain;
        }

        $redis_node = $this->get_redis_node($domain_string);
        $seq = $this->get_seq($redis_node, "{$domain_string}:{$hexip}");
        if (empty($seq)) {
            $this->is_open = false;
        }
        return "{$domain_string}-{$hexip}-{$timestamp}-{$seq}";
    }

    public function get_redis_node($domain = 'default') {
        return isset($this->redis_conf['hosts'][$domain]) ? $domain : 'default';
    }

    public function get_seq($redis_node, $fieldname = '') {
        if (!isset($this->redis[$redis_node])) {
            $result = $this->redis_connect($redis_node);
            if (!$result) {
                return false;
            }
        }
        $h = date('H');
        $result = false;
        try {
            // $result = $this->redis[$redis_node]->hincrby('seq:' . $h, $fieldname, 1);
            // $this->redis_close($redis_node);
        } catch (Exception $e) {

        }
        return $result;
    }

    public function redis_connect($redis_node = 'default') {
        // if (!isset($this->redis[$redis_node])) {
        //     $this->redis[$redis_node] = new Redis();
        //     $config = $this->redis_conf['hosts'][$redis_node];
        //     $result = @$this->redis[$redis_node]->connect($config[0], $config[1], $this->redis_conf['timeout']);
        //     if (!$result) {
        //         $this->redis[$redis_node] = null;
        //         unset($this->redis[$redis_node]);
        //     }
        //     return $result;
        // } else {
            return true;
        // }
    }

    public function redis_close($redis_node = 'default') {
        if ($this->redis[$redis_node] instanceof Redis) {
            try {
                $result = $this->redis[$redis_node]->close();
                if ($result) {
                    $this->redis[$redis_node] = null;
                    unset($this->redis[$redis_node]);
                }
            } catch (Exception $e) {

            }
        }
    }

    public function redis_close_all() {
        if (is_array($this->redis)) {
            foreach ($this->redis as $node => $redis) {
                if ($redis instanceof Redis) {
                    try {
                        $result = $redis->close();
                        if ($result) {
                            $redis = null;
                            $this->redis[$node] = null;
                            unset($this->redis[$node]);
                        }
                    } catch (Exception $e) {

                    }
                }
            }
        }
    }

    /**
     * 防注入
     * @param null $get
     * @param null $post
     * @param null $cookie
     * @param null $refer
     */
    function security_check($get = null, $post = null, $cookie = null, $refer = null, $callback = null) {
        is_null($get) && $get = $_GET;
        is_null($post) && $post = $_POST;
        is_null($cookie) && $cookie = $_COOKIE;
        is_null($refer) && $refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : [];
        list($uri, ) = explode('?', $_SERVER['REQUEST_URI']);
        $this->current_uri = trim($uri);

    }

    function url_trace($uri = '', $params = []) {
        if (!$this->is_open) {
            return;
        }

        if (empty($uri)) {
            $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] . '?' : '?';
            list($uri, $get_str) = explode('?', $url);

            $uri = trim($uri, '/');
            $uris = explode('/', $uri);
            #$end_uri = end($uris);
            foreach ($uris as $k => $v) {
                if (is_numeric($v)) {
                    $uris[$k] = '{num}';
                } elseif (preg_match('/([a-uw-z]{1,2})\d+/i', $v, $x)) {
                    if (isset($x[1])) {
                        $uris[$k] = $x[1] . '{num}';
                    }
                }
            }
            $uri = implode('/', $uris);
            $params['url'] = $url;
            if (!empty($get_str)) {
                $params['get'] = $get_str;
            }
            if (empty($uri)) {
                $uri = '／';
            }
            /*$post_str = file_get_contents('php://input');
            if (!empty($post_str)) {
                $params['post'] = $post_str;
            }*/
        }
        $this->call_from_method = $uri;
        $this->uri = $uri;
        $this->start('URL', $uri, $params);
        ###request_from###
        $request_from = isset($_GET['request_from']) ? $_GET['request_from'] : '';
        if (!empty($request_from)) {
            $this->event('REQUEST_FROM', "{$uri} ({$request_from})");
        }
        ###request_from###
        
        
        $_catCallerDomain = isset($_SERVER['HTTP__CATCALLERDOMAIN']) ? $_SERVER['HTTP__CATCALLERDOMAIN'] : '';
        $_catCallerMethod = isset($_SERVER['HTTP__CATCALLERMETHOD']) ? $_SERVER['HTTP__CATCALLERMETHOD'] : '';
        
        if (!empty($_catCallerDomain) && !empty($_catCallerMethod)) {
            ###call_from_method###
            $call_from_method = isset($_SERVER['HTTP__CATCALLFROMMETHOD']) ? $_SERVER['HTTP__CATCALLFROMMETHOD'] : '';
            if (!empty($call_from_method)) {
                $_catCallerMethod = urldecode($_catCallerMethod);
                $this->event('Request.from', "{$_catCallerMethod} <= {$_catCallerDomain}/{$call_from_method}");
            }
            ###call_from_method###
            ###dependency###
            $this->start('Service', $_catCallerMethod);
            $this->event('Service.client', $this->_client_ip);
            $this->event('Service.app', $_catCallerDomain);
            $this->end();
            header('_catServerDomain: ' . $this->_domain);
            header('_catServer: ' . $this->_server_ip);
            ###dependency###
        }

        register_shutdown_function([$this, 'send'], true);
    }

    function job_trace() {
        if (!$this->is_open) {
            return;
        }
        $path_name = implode(' ', $_SERVER['argv']);
        $this->call_from_method = $path_name;
        $this->uri = $path_name;
        $this->start('JOB', $path_name);
        register_shutdown_function([$this, 'send'], true);
    }

    /**
     * 聚合封装 傻瓜化 CURL请求transaction开始
     * @param type $url CURL请求的URL
     * @return type
     */
    public function curl_start($url = '') {
        if (!$this->is_open) {
            return [];
        }
        $next_message_id = $this->generate_message_id($url);

        $pathinfo = parse_url($url);
        $scheme = isset($pathinfo['scheme']) ? "{$pathinfo['scheme']}://" : '';
        $host = isset($pathinfo['host']) ? $pathinfo['host'] : '';
        $port = isset($pathinfo['port']) ? $pathinfo['port'] : '80';
        $path = isset($pathinfo['path']) ? $pathinfo['path'] : '';


        #$curl_name = preg_match('/^(http:\/\/)*[.-\w]*.smzdm.com:\d{2,5}[\s\S]*$/i', $url) ? 'CURL INNER' : 'CURL OUTER'; #区别内外网请求
        #list($uri, ) = explode('?', $url);
        $curl_name = strpos($host, 'smzdm.com') !== false ? 'CURL INNER' : 'CURL OUTER'; #区别内外网请求
        $uri = "{$scheme}{$host}:{$port}{$path}";
        #$uri = $path;
        #$params = [];
        $uri = trim($uri, '/');
        $uris = explode('/', $uri);
        #$end_uri = end($uris);
        foreach ($uris as $k => $v) {
            if (is_numeric($v)) {
                $uris[$k] = '{num}';
            }
        }
        $uri = implode('/', $uris);
        #if (!empty($get_str)) {
        #    $params['get'] = $get_str;
        #}
        if (empty($uri)) {
            $uri = '／';
        }
        
        if ('CURL INNER' == $curl_name) {
            $this->event('RemoteCall', 'PigeonRequest', $next_message_id);
            $this->start('Call', $uri);
        } else {
            $this->start('Curl.Outter', $uri);
        }

        #$this->event('Call.server', gethostbyname($host));
        $this->event('PigeonCall.app', $host);

        $message_id_group = $this->get_message_id_group();
        $next_parent_message_id = isset($message_id_group['message_id']) ? $message_id_group['message_id'] : '';
        $root_message_id = isset($message_id_group['root_message_id']) ? $message_id_group['root_message_id'] : '';


        $return = [
            '_catChildMessageId: ' . $next_message_id,
            '_catParentMessageId: ' . $next_parent_message_id,
            '_catRootMessageId: ' . $root_message_id,
        ];

        #X-Zhi-Request-Id
        if ('CURL INNER' == $curl_name) {
            isset($_SERVER['HTTP_X_ZHI_REQUEST_ID']) && ($return[] = "X-Zhi-Request-Id: {$_SERVER['HTTP_X_ZHI_REQUEST_ID']}");
            isset($_SERVER['HTTP_X_ZHI_TRACE_STR']) && ($return[] = "X-Zhi-Trace-Str: {$_SERVER['HTTP_X_ZHI_TRACE_STR']}");
            #$uri = urlencode($uri);
            $uri = urlencode($uri);
            $return[] = "_catCallerDomain: {$this->_domain}";
            $return[] = "_catCallerMethod: {$uri}";
            $return[] = "_catCallFromMethod: {$this->call_from_method}"; #PHP自加的 显示来自哪个方法调用
        }

        return $return;
    }

    /**
     *
     * @param type $rh Response Header
     */
    function curl_end($rh = null, $data = null) {
        $http_code = isset($rh['http_code']) ? $rh['http_code'] : 0;
        if (is_null($rh)) {
            goto ARCHOR_RESULT;
        }
        $rh['request_header'] = isset($rh['request_header']) ? $rh['request_header'] : '';
        $header = explode("\n", $rh['request_header']);
        foreach ($header as $n => $h) {
            $a = explode(': ', $h);
            if (count($a) == 2) {
                $header[trim($a[0])] = trim($a[1]);
            }
            unset($header[$n]);
        }
        $server_domain = isset($header['_catServerDomain']) ? $header['_catServerDomain'] : '';
        $server_ip = isset($header['_catServer']) ? $header['_catServer'] : ''; #由server回传 但不准确
        #$server_ip = gethostbyname($server_domain); #阻塞非常大
        $this->event('Call.server', $server_ip);
        #$this->event('PigeonCall.app', $server_domain);#出现unknow问题 暂时在start中加入
        if (200 == $http_code) {
            $http_code = 0;
        } else {
            if (is_scalar($data)) {
                $data = 'request_info:' . json_encode($rh) . $data;
            } elseif (is_array($data)) {
                $data['request_info'] = $rh;
            } else {
                $data = $rh;
            }
        }
        ARCHOR_RESULT:
        $this->end($data, $http_code);
    }

    function get_current_time() {
        $time = date('Y-m-d H:i:s');
        $m_time = microtime();
        $m_time = substr($m_time, 2, 3);
        return "{$time}.{$m_time}";
    }

    function get_micro_time() {
        $t = microtime();
        $t = substr($t, -4) . substr($t, 2, 6);
        return intval($t);
    }

    private function _record($action, $type, $name, $other_params = []) {
        $time = $this->get_current_time();

        $name = str_replace(["\n", "\t"], '', $name);
        $elements = [
            "{$action}{$time}",
            $type,
            $name,
        ];
        isset($other_params['status']) && $elements[] = $other_params['status'];
        isset($other_params['us']) && $elements[] = $other_params['us'];

        if (isset($other_params['data'])) {
            if (!is_scalar($other_params['data'])) {
                $other_params['data'] = @json_encode($other_params['data'], JSON_UNESCAPED_UNICODE);
            }
            $elements[] = $other_params['data'];
        }

        $elements = implode($this->_spacing, $elements);
        $this->_body .= "{$elements}{$this->_spacing}\t\n";
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
        if (!$this->is_open) {
            return;
        }
        if (substr($micro_second, -2) != 'us') {
            $micro_second .= 'us';
        }
        $this->_record('A', $type, $name, ['data' => $data, 'status' => $status, 'us' => $micro_second,]);
    }

    /**
     * Transaction Start 事务开始
     *
     * @param type $type
     * @param type $name
     * @param type $data
     * @return type
     */
    public function start($type, $name, $data = null) {
        if (!$this->is_open) {
            return;
        }
        if ('URL' == $type && isset($_SERVER['HTTP_X_ZHI_REQUEST_ID'])) {
            $data['X-Zhi-Request-Id'] = $_SERVER['HTTP_X_ZHI_REQUEST_ID'];
        }
        $this->_record('t', $type, $name);
        $micro_start = $this->get_micro_time();
        $this->_trans_container[$this->_depth] = [$type, $name, $micro_start, $data];
        $this->_depth++;
    }


    /**
     * Transaction End 事务结束
     *
     * @param type $data
     * @return type
     */
    public function end($data = null, $status = 0) {
        if (!$this->is_open) {
            return;
        }
        $this->_depth--;
        if (!isset($this->_trans_container[$this->_depth])) {
            return;
        }
        list($type, $name, $micro_start, $start_data) = $this->_trans_container[$this->_depth];

        $micro_over = $this->get_micro_time();
        $micro_second = ($micro_over - $micro_start) . 'us';

        $data = is_null($data) ? $start_data : $data;
        $this->_record('T', $type, $name, ['data' => $data, 'status' => $status, 'us' => $micro_second,]);

        $this->_trans_container[$this->_depth] = null;
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
        if (!$this->is_open) {
            return;
        }
        $this->_record('E', $type, $name, ['data' => $data, 'status' => $status]);
    }
    
    public function metric($type, $name, $value) {
        $this->_record('M', '', $name, ['data' => $value, 'status' => $type]);
    }

    #M2016-10-31 17:47:09.270    支付总额  S  100.50
    public function metric_sum($name, $value) {
        $this->metric('S', $name, $value);
    }

    public function metric_count($name, $value) {
        $this->metric('C', $name, $value);
    }

    public function metric_duration($name, $value) {
        $this->metric('D', $name, $value);
    }

    public function heartbeat() {

    }

    public function get_body() {
        return $this->_body;
    }

    public function dump() {
        $this->end();
        $this->_assem_head();
        echo $this->_head . $this->_body;
        exit;
    }

    private function _assem_head() {
        $ip = $this->_server_ip;
        $thread_group_name = 'PHP-GROUP';
        $thread_name = 'PHP';
        $process_id = getmygid();
        $session_token = '';
        $this->_head = "{$this->_version}\t{$this->_domain}\t{$this->_hostname}\t{$ip}\t{$thread_group_name}\t{$process_id}\t{$thread_name}\t{$this->_message_id}\t{$this->_parent_message_id}\t{$this->_root_message_id}\t{$session_token}\n";
    }

    private function _assem_data() {
        $this->_data = $this->_head . $this->_body;
        $len = strlen($this->_data);
        $len_bin = pack('N', $len);
        $data_bin = pack("a{$len}", $this->_data);
        $this->_data = $len_bin . $data_bin;
    }

    public function get_message_id_group() {
        return [
            'message_id' => $this->_message_id,
            'parent_message_id' => $this->_parent_message_id,
            'root_message_id' => $this->_root_message_id,
        ];
    }


    /**
     * 读取服务端IP配置 (配置文件由个项目作业异步生成)
     *
     * @return type
     */
    public function get_server_addr() {
        /*if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $pwd = $_SERVER['DOCUMENT_ROOT'];
        } elseif (!empty($_SERVER['PWD'])) {
            $pwd = $_SERVER['PWD'];
        } else {
            $dir = __DIR__;
            $pwd = preg_replace('/\/index.php*$/', '', $_SERVER['PHP_SELF']);
        }
        $log_path = $pwd . '/' . APPPATH. 'logs/cat_server.php';
         */
        /*$log_path = APPPATH.'logs/cat_server.php';
        @$ip = file_get_contents($log_path);
        return $ip;*/
        @include_once APPPATH . 'logs/cat_server.php';
        global $cat_server_addr;
        return $cat_server_addr;
    }

    /**
     * 最终发送数据
     *
     * @param type $auto_close_trans
     * @return type
     */
    public function send($auto_close_trans = false) {
        $this->redis_close_all();
        if (!$this->is_open) {
            return;
        }

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        date_default_timezone_set('Asia/Shanghai');

        if ($auto_close_trans) {
            #报错警告
            $this->smzdm_error_handler();
            #释放连接
            if (class_exists('SmzdmRedis') && method_exists('SmzdmRedis', 'close_all')) {
                SmzdmRedis::close_all();
            }
            if (class_exists('Mysql') && method_exists('Mysql', 'close_all')) {
                Mysql::close_all();
            }

            $http_code = http_response_code();
            if (!empty($http_code) && $http_code >= 400) {
                $this->final_error_code = "HTTP:[{$http_code}]";
            }

            /*
            $end_memory = memory_get_usage();
            $memory_apply = memory_get_usage(TRUE);
            $used_memory = $end_memory - $this->start_memory;
            
            $type_name = '100Mb+';
            if ($used_memory > 1024 * 1024 * 100) {
                $type_name = '100Mb+';
            } elseif ($used_memory > 1024 * 1024 * 10) {
                $type_name = '10Mb+';
            } elseif ($used_memory > 1024 * 1024) {
                $type_name = '1Mb+';
            } elseif ($used_memory > 1024 * 512) {
                $type_name = '512Kb+';
            } elseif ($used_memory > 1024 * 100) {
                $type_name = '100Kb+';
            } else {
                $type_name = '100Kb-';
            }
            $type_name_apply = '100Mb+';
            if ($memory_apply > 1024 * 1024 * 100) {
                $type_name_apply = '100Mb+';
            } elseif ($memory_apply > 1024 * 1024 * 10) {
                $type_name_apply = '10Mb+';
            } elseif ($memory_apply > 1024 * 1024) {
                $type_name_apply = '1Mb+';
            } elseif ($memory_apply > 1024 * 512) {
                $type_name_apply = '512Kb+';
            } elseif ($memory_apply > 1024 * 100) {
                $type_name_apply = '100Kb+';
            } else {
                $type_name_apply = '100Kb-';
            }
            
            $use_level = intval(log($used_memory, 10)) + 1;
            $use_level_apply = intval(log($memory_apply, 10)) + 1;
            $start_mem_level = intval(log($this->start_memory, 10)) + 1;
            
            $this->event("mem:{$use_level}", "{$this->uri} {$type_name} {$type_name_apply}", ['start_mem' => $this->start_memory, 'apply' => $memory_apply, 'usage' => $used_memory]);
            $this->event("mem_apply:{$use_level_apply}", "{$this->uri} {$type_name} {$type_name_apply}", ['start_mem' => $this->start_memory, 'apply' => $memory_apply, 'usage' => $used_memory]);
            */
            
            #关闭最外层trans
            $this->end(null, $this->final_error_code);
        }

        $this->_assem_head();
        $this->_assem_data();
        #echo $this->_data;#exit;
        /*$CI = &get_instance();
        $CI->load->library('http');
        $CI->http->request_non_blocking('http://logapi.smzdm.com:801/lua', ['section' => 'api', 'level' => 1, 'node' => 'cat', 'content' => urlencode($this->_data)]);*/

        // 建立客户端的socet连接  
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, ['sec' => 0, 'usec' => 10000]);
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 0, 'usec' => 1]);
        if (!empty($this->_cat_server_addr)) {
            @$conn_result = socket_connect($socket, $this->_cat_server_addr, 2280);    #连接服务器端socket  
            #将客户的信息写到通道中，传给服务器端  
            if ($conn_result) {
                socket_write($socket, $this->_data, strlen($this->_data));
            }
            #var_dump($result);
        }


        /*$fp = fsockopen($this->_cat_server_addr, 2280, $errno, $errstr, 3);
        if (!$fp) {
            return false;
        }
        stream_set_blocking($fp,0);
        fwrite($fp, $this->_data);
        fclose($fp);*/
    }


}

}