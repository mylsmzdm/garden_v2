<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * redis类简单封装 命令参考:http://redis.readthedocs.org/en/latest/
 */
class Http {
    private static $is_multi = false; // 是否处于并发准备阶段
    private static $callstack = []; // 并发调用的准备栈
    private static $raw_params = []; // 并发调用的原始请求


    /**
     * remove_duplicate_headers 删除重复的key-value
     * @author liangdong@smzdm.com
     * @param $headers
     * @return array
     */
    private static function remove_duplicate_headers($headers) {
        $header_map = [];
        foreach ($headers as $header) {
            list($key, $value) = explode(':', $header);
            $header_map[$key] = $value;
        }
        $headers = [];
        foreach ($header_map as $key => $value) {
            $headers[] = "{$key}: {$value}";
        }
        return $headers;
    }

    /**
     * multi_prepare 准备并发,后续request调用会被暂时缓存
     * @author liangdong@smzdm.com
     */
    public static function multi_prepare() {
        self::multi_cancel(); // 取消未发出的任务
        self::$is_multi = true;
    }

    /**
     * multi_cancel 取消并发,清理未发出的request
     * @author liangdong@smzdm.com
     */
    public static function multi_cancel() {
        if (!self::$is_multi) {
            return;
        }
        self::$raw_params = [];
        self::$is_multi = false;
    }

    /**
     * multi_perform_once
     * @author liangdong@smzdm.com
     * @param $params_indexes
     */
    private static function multi_perform_once($params_indexes) {
        // 准备并发调用栈
        foreach ($params_indexes as $index) {
            $params = self::$raw_params[$index];
            self::do_request($params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
        }

        // 并发句柄
        $multi_handle = curl_multi_init();

        $C_I = &get_instance();

        // 单个句柄添加到并发句柄
        foreach (self::$callstack as $item) {
            // 启动CAT
            $cat_headers = isset($C_I->cat) ? $C_I->cat->curl_start($item[0]) : [];
            // 生成最终的header
            $headers = array_merge($cat_headers, $item[1]);
            $headers = self::remove_duplicate_headers($headers);
            if (!empty($headers)) {
                curl_setopt($item[2], CURLOPT_HTTPHEADER, $headers);
            }
            // 添加句柄
            curl_multi_add_handle($multi_handle, $item[2]);
        }

        // 并发调用
        $active = null;
        do {
            $mrc = curl_multi_exec($multi_handle, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            while (curl_multi_exec($multi_handle, $active) === CURLM_CALL_MULTI_PERFORM);
            if (curl_multi_select($multi_handle) != -1) {
                do {
                    $mrc = curl_multi_exec($multi_handle, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        // 整理所有结果
        $ret = [];
        while (!empty(self::$callstack)) {
            $item = array_pop(self::$callstack);
            $handle = $item[2];

            $errno = curl_errno($handle); // 错误码
            $error = curl_error($handle); // 错误描述
            $content = $errno ? false : curl_multi_getcontent($handle); // 应答
            $response_header = $errno ? ['http_code' => $errno] : curl_getinfo($handle);

            $ret[] = [
                'errno' => $errno,
                'error' => $error,
                'content' => $content,
                'response_header' => $response_header,
            ];

            curl_multi_remove_handle($multi_handle, $handle);
            curl_close($handle);

            if (isset($C_I->cat)) {
                $C_I->cat->curl_end($response_header);
            }
        }
        curl_multi_close($multi_handle);
        return array_reverse($ret);
    }

    /**
     * multi_perform 并发调用
     * @author liangdong@smzdm.com
     * @param bool $inc_detail 是否返回应答的错误码等详细信息（默认只返回body）
     * @return array
     */
    public static function multi_perform($inc_detail = false, $retry_times = 0) {
        if (!self::$is_multi || empty(self::$raw_params)) {
            self::$is_multi = false;
            return [];
        }

        $cur_times = 0;
        $result_arr = array_fill(0, count(self::$raw_params), false);
        do {
            // 收集本轮需要发起的请求下标
            $params_indexes = [];
            foreach ($result_arr as $i => $v) {
                if (empty($v) || $v['error'] != 0 || $v['response_header']['http_code'] != 200) { // 还没有请求过,或者上次请求有错
                    $params_indexes[] = $i;
                }
            }
            // 如果没有需要发起的请求，那么退出
            if (empty($params_indexes)) {
                break;
            }
            // 发起这一批请求
            $result_once = self::multi_perform_once($params_indexes);
            // 将结果填回result_arr中
            foreach ($result_once as $i => $result_item) {
                $result_arr[$params_indexes[$i]] = $result_item;
            }
        } while ($cur_times++ < $retry_times);

        // 执行到这里, 至少每个请求都发起过一次curl调用, 只需要整理一次结果即可
        if (!$inc_detail) {
            foreach ($result_arr as $i => $result_item) {
                $result_arr[$i] = $result_item['errno'] ? false : $result_item['content'];
            }
        }
        self::multi_cancel(); // 结束并发
        return $result_arr;
    }

    /**
     * do_request 发起CURL操作
     * @author liangdong@smzdm.com
     * @param $url
     * @param array $params
     * @param string $method
     * @param bool $multi
     * @param array $extheaders
     * @param array $args
     * @return bool|mixed
     */
    private static function do_request($url, $params = array(), $method = 'GET', $multi = false, $extheaders = array(), $args=array()) {
        $timeout = isset($args['timeout']) ? (int)$args['timeout'] : 3;
        $method = strtoupper($method);
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_USERAGENT, 'SMZDM PHP CURL 1.0');
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ci, CURLOPT_HEADER, false);

        $C_I = &get_instance();
        $extheaders = (array) $extheaders;

        // 默认header
        $default_headers = [];

        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($params)) {
                    if ($multi) {
                        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
                            foreach ($multi as $key => $file) {
                                $params[$key] =   new CURLFile($file);
                            }
                        } else {
                            foreach ($multi as $key => $file) {
                                $params[$key] = '@' . $file;
                            }
                        }
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                        $default_headers[] = 'Expect: ';
                    } else {
                        if (is_array($params)) {    // 默认x-www-form-data
                            $params = http_build_query($params);
                        } else {
                            // 支持raw data POST
                            $default_headers[] = "Content-Type: application/octet-stream";
                        }
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                    }
                }
                break;
            case 'DELETE':
            case 'GET':
                $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($params)) {
                    $url = $url . (strpos($url, '?') ? '&' : '?')
                        . (is_array($params) ? http_build_query($params) : $params);
                }
                break;
        }

        if (self::$is_multi) { // 并发调用，CAT延迟到最终发起时刻开始记录
            $cat_headers = [];
        } else {
            $cat_headers = isset($C_I->cat) ? $C_I->cat->curl_start($url) : [];
        }

        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ci, CURLOPT_URL, $url);

        // 优先级cat header < default header < ext header
        $headers = array_merge($cat_headers, $default_headers, $extheaders);
        // headers需要按key去重，参数传入的优先级大于默认的
        $headers = self::remove_duplicate_headers($headers);
        if ($headers && !self::$is_multi) {
            curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        }

        // 如果是并发请求，那么先缓存起来，等待一起发出
        if (self::$is_multi) {
            self::$callstack[] = [$url, $headers, $ci];
            return true; // 并发请求的调用者不应该立即检查返回值
        }

        // 串行调用，那么立即发起调用获得结果
        $response = curl_exec($ci);
        $curl_errno = curl_errno($ci);
        if ($curl_errno !== 0) {
            $response_header['http_code'] = $curl_errno;
        } else {
            $response_header = curl_getinfo($ci);
        }
        curl_close($ci);
        if (isset($C_I->cat)) {
            $C_I->cat->curl_end($response_header);
        }
        return $response;
    }

    /**
     * 发起一个HTTP/HTTPS的请求
     * @param string $url 接口的URL
     * @param $params 接口参数   array('content'=>'test', 'format'=>'json');
     * @param $method 请求类型    GET|POST
     * @param $multi 图片信息
     * @param $extheaders 扩展的包头信息
     * @param $args 配置信息：timeout请求超时时间
     * @return string
     *
     * 2017-1-6 liangdong@smzdm.com 支持POST raw data，支持并发request
     */
    public static function request($url, $params = array(), $method = 'GET', $multi = false, $extheaders = array(), $args=array()) {
        if (!function_exists('curl_init'))
            exit('Need to open the curl extension');

        // 并发调用，拦截原始参数
        if (self::$is_multi) {
            self::$raw_params[] = [
                $url, $params, $method, $multi, $extheaders, $args
            ];
            return true;
        }

        // 非并发调用，直接发起
        return self::do_request($url, $params, $method, $multi, $extheaders, $args);
    }
    
    /**
     * 非阻塞请求(当不需要返回值与返回状态时，可以使用)
     * 
     * @author jxt
     */
    function request_non_blocking($url, $params = [], $method = 'GET') {
        $query_str = http_build_query($params);
        $info = parse_url($url);
        $fp = fsockopen($info['host'], $info['port'], $errno, $errstr, 3);
        if (!$fp) {
            return false;
        }
        stream_set_blocking($fp,0);
        if ($method == 'GET') {
            $http = "GET {$info['path']}?{$query_str} HTTP/1.0\r\n";
            $http .= "Host: {$info['host']}\r\n";
            $http .= "Connection: Close\r\n\r\n";
        } else {
            $http = "POST {$info['path']} HTTP/1.0\r\n";
            $http .= "Host: {$info['host']}\r\n";
            $http .= "Referer: http://{$info['host']}{$info['path']}\r\n";
            $http .= "Content-type: application/x-www-form-urlencoded\r\n";
            $http .= "Content-Length: ".strlen($query_str)."\r\n";
            $http .= $query_str;
            $http .= "Connection: Close\r\n\r\n";

        }
        fwrite($fp, $http);
        fclose($fp);
    }

}
