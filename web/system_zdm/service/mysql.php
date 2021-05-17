<?php

class Mysql {

    public $_link = [];
    private $_config = null;
    public $_now = null;
    private $_set_db = array('write' => null, 'read' => null);
    private $_op = null;
    private $_debug = 0;
    private $_stmt = null;
    public $error_code = 0;
    public $error_msg = '';
    public $port = 3306;
    public $sql_list = array();
    public $option = [
        'pconnect' => false,
        'charset' => 'utf8',
    ];
    private $ci = null;
    static public $container = [];
    public $conn = null;
    private $autocommit = true; // 是否处于事务中

    public static function o($conn = '', $option = []) {
        if (empty(self::$container[$conn]) || !is_object(self::$container[$conn])) {
            self::$container[$conn] = new Mysql($conn, $option);
            self::$container[$conn]->conn = $conn;
        }
        return self::$container[$conn];
    }

    public function __get($key) {
        $CI = &get_instance();
        return $CI->$key;
    }

    public function __construct($db_key = null, $option = []) {
        if (null == $db_key) {
            exit('-1');
        }

        $this->ci = &get_instance();
        $this->ci->load->config('service/database');
        $db_config = $this->ci->config->item('mysql');
        $db_config = $db_config[$db_key];
        if (isset($db_config['hostname']['write']) && isset($db_config['hostname']['read'])) {
            $_write_len = count($db_config['hostname']['write']);
            $this->_set_db['write'] = $_write_len == 1 ? '0' : rand(0, $_write_len - 1);

            $_read_len = count($db_config['hostname']['read']);
            $this->_set_db['read'] = $_read_len == 1 ? '0' : rand(0, $_read_len - 1);
        } else {
            $this->_set_db['write'] = '0';
            $this->_set_db['read'] = $this->_set_db['write'];
        }
        $this->_config = $db_config;
        $this->option = array_merge($this->option, $option);
    }

    private function _connect() {
        $db_config = $this->_config;

        if (is_null($db_config)) {
            return false;
        } else {
            if ($this->option['pconnect']) {
                $pconnect = 'p:';
            } else {
                $pconnect = '';
            }

            $port = $this->port = isset($db_config['port']) && $db_config['port'] > 0 ? $db_config['port'] : 3306;
            
            isset($this->ci->cat) && $this->ci->cat->start('MySQL Connect', $this->conn . ' => ' . $pconnect . $db_config['hostname'][$this->sign][$this->_set_db[$this->sign]] . ' => ' . $db_config['database']);
            $this->_link[$this->sign][$this->_set_db[$this->sign]] = mysqli_connect($pconnect . $db_config['hostname'][$this->sign][$this->_set_db[$this->sign]], $db_config['username'], $db_config['password'], $db_config['database'], $port);
            
            
            if ($this->_link[$this->sign][$this->_set_db[$this->sign]]) {
                mysqli_set_charset($this->_link[$this->sign][$this->_set_db[$this->sign]], $this->option['charset']);
            } else {
                return false;
            }
            isset($this->ci->cat) && $this->ci->cat->end();
        }
        return true;
    }

    private function _is_write($sql, $force_read_master = false) {
        $sql = trim($sql);
        $this->_op = strtolower(substr($sql, 0, 6));
        if (true === $force_read_master) {
            $this->sign = 'write';
        } else {
            $this->sign = in_array($this->_op, array('select')) ? 'read' : 'write';
        }

        return $this->sign;
    }

    private function _select_link() {
        $result = true;
        if (!isset($this->_link[$this->sign][$this->_set_db[$this->sign]])) {
            $result = $this->_connect();
        }
        $this->_now = $this->_link[$this->sign][$this->_set_db[$this->sign]];
        if (empty($this->_now)) {
            $result = false;
        }
        return $result;
    }

    public function mysql() {
        return $this->_now;
    }

    public function set_db($db_num) {
        list($tmp_write, $tmp_read) = explode(',', $db_num);
        if (isset($this->_config['write']) && isset($this->_config['read'])) {
            if (!is_null($tmp_write)) {
                $this->_set_db['write'] = $tmp_write;
            }
            if (!is_null($tmp_read)) {
                $this->_set_db['read'] = $tmp_read;
            }
        } else {
            $this->_set_db['write'] = $tmp_write;
            $this->_set_db['read'] = &$this->_set_db['write'];
        }
    }

    
    public function ping() {
        if (method_exists($this->_now, 'ping')) {
            return $this->_now->ping();
        } else {
            return false;
        }
    }

    /**
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 一次执行多个写操作
     * @deprecated
     * @param type $multi_sql
     * @param array $option
     * @return 成功:true 失败:false (有一条失败,全失败)
     */
    public function multi_query($multi_sql = [], array $option = []) {
        $default_option = [
            'sign'            => 'write', #选择操作主库还是从库
            'on_error_return' => false, #查询报错后,自定义本函数返回的值
        ];
        $option = array_merge($default_option, $option);

        $this->sign = $option['sign'];

        foreach ($multi_sql as $k => $v) {
            $multi_sql[$k] = trim($v);
            $multi_sql[$k] = trim($v, ';');
        }
        $sql = implode(';', $multi_sql);

        if (false === $this->_select_link()) {
            return $option['on_error_return'];
        }

        $return = $option['on_error_return'];

        if (mysqli_connect_errno()) {
            return $option['on_error_return'];
        }
        $result = $this->_now->multi_query($sql);
        $this->error_code = $this->_now->errno;
        $this->error_msg = $this->_now->error;

        if ($result) {
            $n = 0;
            do {
                #echo $multi_sql[$n];
                $rows = [];
                if ($result = $this->_now->store_result()) {#var_dump($result);
                    $result->free();
                }
                $n++;
            } while ($this->_now->more_results() && $this->_now->next_result());

            $return = true;
        } else {
            $return = $option['on_error_return'];
        }
        if ($this->error_code) {
            $return = $option['on_error_return'];
        }

        return $return;
    }

    /**
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 
     * @author jxt
     */
    public function query($sql, array $option = []) {
        $default_option = [
            'force_read_master' => false, #从主库读取(可用于对实时性要求高的后台和作业)
            'on_error_return' => false, #查询报错后,自定义本函数返回的值
        ];
        $option = array_merge($default_option, $option);

        $this->_is_write($sql, $option['force_read_master']);
        if (false === $this->_select_link()) {
            return $option['on_error_return'];
        }
        
        $this->sql_log($sql);
        if (mysqli_connect_errno()) {
            return $option['on_error_return'];
        }
        
        $sql_tpl = preg_replace('/in \([\d\w\'\s,"]+\)/i', 'in (?)', $sql);
        $sql_tpl = preg_replace('/\d+/', '?', $sql_tpl);
        $sql_tpl = str_replace("\'", '', $sql_tpl);
        $sql_tpl = preg_replace('/\'[\S\s]+?\'/', '?', $sql_tpl);
        #isset($this->ci->cat) && $this->ci->cat->start('Old MySQL', $sql_tpl);
        
        
        #isset($this->ci->cat) && $this->ci->cat->start('SQL', $sql, $bind_params);
        isset($this->ci->cat) && $this->ci->cat->start('SQL', strtoupper($this->_op));
        isset($this->ci->cat) && $this->ci->cat->event('SQL.Method', strtoupper($this->_op));
        $host = $this->_config['hostname'][$this->sign][$this->_set_db[$this->sign]];
        isset($this->ci->cat) && $this->ci->cat->event('SQL.Database', "jdbc:mysql://{$host}:{$this->port}/{$this->_config['database']}?useUnicode=true&characterEncoding={$this->option['charset']}&autoReconnect=true");
        isset($this->ci->cat) && $this->ci->cat->event('SQL.name', $sql);
        
        $this->rs = $this->_now->query($sql);
        
        $query_status = $this->rs === false ? $this->get_error_msg() : 0;
        isset($this->ci->cat) && $this->ci->cat->end(null, $query_status);
        
        $this->error_code = $this->_now->errno;
        $this->error_msg = $this->_now->error;
        if ($this->_op == 'insert') {
            if (empty($this->_now->insert_id)) {
                return $this->rs;
            } else {
                return $this->_now->insert_id;
            }
        } elseif ($this->_op == 'update' || $this->_op == 'delete') {
            if ($this->_now->affected_rows <= 0) {
                return $this->rs;
            } else {
                return $this->_now->affected_rows;
            }
        } else {
            return $this->rs;
        }
    }

    public function get_error_code() {
        if (is_object($this->_stmt)) {
            return mysqli_stmt_errno($this->_stmt);
        } else {
            return $this->error_code;
        }
    }

    public function get_error_msg() {
        if (is_object($this->_stmt)) {
            return mysqli_stmt_error($this->_stmt);
        } else {
            return $this->error_msg;
        }
    }

    /**
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * @deprecated
     * @author jxt
     */
    public function get_row($sql, array $option = []) {
        $default_option = [
            'force_read_master' => false, #从主库读取(可用于对实时性要求高的后台和作业)
            'on_error_return' => false, #查询报错后,自定义本函数返回的值
        ];
        $option = array_merge($default_option, $option);

        $row = array();
        if (mysqli_connect_errno()) {
            return $option['on_error_return'];
        }
        $this->query($sql, $option);
        if (is_object($this->rs)) {
            $row = mysqli_fetch_array($this->rs, MYSQLI_ASSOC);
            if (is_null($row)) {
                $row = array();
            }
        } else {
            $row = $option['on_error_return'];
        }
        return $row;
    }

    /**
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     *
     * 查询列表
     * @deprecated
     * @param type $sql
     * @param type $option
     *     force_read_master bool从主库读取(可用于对实时性要求高的后台和作业)
     * @return boolean
     */
    public function get_all($sql, array $option = []) {
        $default_option = [
            'force_read_master' => false, #从主库读取(可用于对实时性要求高的后台和作业)
            'on_error_return' => false, #查询报错后,自定义本函数返回的值
        ];
        $option = array_merge($default_option, $option);

        $row = $rows = array();
        if (mysqli_connect_errno()) {
            return $option['on_error_return'];
        }
        $this->query($sql, $option);
        if (is_object($this->rs)) {
            while ($row = mysqli_fetch_array($this->rs, MYSQLI_ASSOC)) {
                $rows[] = $row;
            }
        } else {
            $rows = $option['on_error_return'];
        }

        return $rows;
    }

    /**
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     * 将要废弃 请不要再使用！！！！！！！！！！！！！！！！！！！！！！！！
     *
     *
     * 一次查询多个SQL
     * @deprecated
     */
    public function multi_get_all($multi_sql = [], array $option = []) {
        $default_option = [
            'sign' => 'read', #选择操作主库还是从库
            'on_error_return' => false, #查询报错后,自定义本函数返回的值
        ];
        $option = array_merge($default_option, $option);

        $this->sign = $option['sign'];

        foreach ($multi_sql as $k => $v) {
            $multi_sql[$k] = trim($v);
            $multi_sql[$k] = trim($v, ';');
        }
        $sql = implode(';', $multi_sql);

        if (false === $this->_select_link()) {
            return $option['on_error_return'];
        }

        $all_rows = $row = [];

        if (mysqli_connect_errno()) {
            return $option['on_error_return'];
        }
        
        $result = $this->_now->multi_query($sql);
        $this->error_code = $this->_now->errno;
        $this->error_msg = $this->_now->error;

        if ($result) {
            do {
                $rows = [];
                /* store first result set */
                if ($result = $this->_now->store_result()) {
                    while ($row = $result->fetch_assoc()) {
                        $rows[] = $row;
                    }
                    $result->free();
                }
                $all_rows[] = $rows;
                /* print divider */
                #if ($this->_now->more_results()) {
                #printf("-----------------\n");
                #}
            } while ($this->_now->next_result());
        } else {
            $all_rows = $option['on_error_return'];
        }
        if ($this->error_code) {
            $all_rows = $option['on_error_return'];
        }

        return $all_rows;
    }

    
    /**
     * 查询单行
     *   如果需要打印出 SQL 使用 debug(1) 或者 get_sql(bool) 或者 附加参数'debug' => true 均可以
     * @param string $table 表名 变量严禁从外界传入
     * @param map $where_conds 查询条件。 例：['age<=' => 16, 'time>' => '2016-06-12', 'name LIKE' => '刘%' , 'id IN' => [1, 3, 5], 'mobile IS NOT' => null]  ps:between可使用">=" + "<="方式  变量可从外界传入
     * @param map $other_conds 其他查询字段，有以下几项：
     *  'fields' => '*',  #数据表字段名，不传默认为*。 例： 'fields' => 'DISTINCT(mobile),COUNT(1) AS num' 变量严禁从外界传入
     *  'group_by' => null, #group by 的值，不传不进行group操作。 例1：'group_by' => 'age' 例2： 'group_by' => 'age HAVING COUNT(1) > 2' 变量严禁从外界传入
     *  'order_by' => null, #order by 的值，不传不进行group操作。 例1：'order_by' => 'time' 例2： 'order_by' => 'age DESC,time ASC' 变量严禁从外界传入
     *  'limit' => null, #limit值， 不传不作限制。 例1： 'limit' => 10 例2： 'limit' => '30,30' 变量可从外界传入
     * @param map $option 附加参数
     *  'force_read_master' => true 强制从主库读数据 默认为false
     *  'debug' => true 附带输出SQL语句 默认为false
     *
     * @return row_map  失败返回false
     *
     * @example $this->db->select_row('user', ['user_id' => 404263]);
     *
     * @author jxt
     */
    public function select_row($table, $where_conds = [], $other_conds = [], $option = []) {
        $option['get'] = 'row';
        return $this->select($table, $where_conds, $other_conds, $option);
    }

    /**
     * 查询
     *
     *  如果需要打印出 SQL 使用 debug(1) 或者 get_sql(bool) 或者 附加参数'debug' => true 均可以
     * @param string $table 表名 变量严禁从外界传入
     * @param map $where_conds 查询条件。 例：['age<=' => 16, 'time>' => '2016-06-12', 'name LIKE' => '刘%' , 'id IN' => [1, 3, 5], 'mobile IS NOT' => null]  ps:between可使用">=" + "<="方式  变量可从外界传入
     * @param map $other_conds 其他查询字段，有以下几项：
     *  'fields' => '*',  #数据表字段名，不传默认为*。 例： 'fields' => 'DISTINCT(mobile),COUNT(1) AS num' 变量严禁从外界传入
     *  'group_by' => null, #group by 的值，不传不进行group操作。 例1：'group_by' => 'age' 例2： 'group_by' => 'age HAVING COUNT(1) > 2' 变量严禁从外界传入
     *  'order_by' => null, #order by 的值，不传不进行group操作。 例1：'order_by' => 'time' 例2： 'order_by' => 'age DESC' 变量严禁从外界传入
     *  'limit' => null, #limit值， 不传不作限制。 例1： 'limit' => 10 例2： 'limit' => '30,30' 变量可从外界传入
     * @param map $option 附加参数
     *  'force_read_master' => true 强制从主库读数据 默认为false
     *  'debug' => true 附带输出SQL语句 默认为false
     *
     * @return rows_list 失败返回false
     *
     * @example $this->db->select('user', ['is_gold_bl=' => 1], ['fields' => 'user_id,nickname', 'order_by' => 'creation_date DESC', 'limit' => 5]);
     *
     * @author jxt
     */
    public function select($table, $where_conds = [], $other_conds = [], $option = []) {
        $default_option = [
            'force_read_master' => FALSE, #从主库读取(可用于对实时性要求高的后台和作业)
            'on_error_return'   => FALSE, #查询报错后,自定义本函数返回的值
            'get'               => 'all', # all：取多行 row：取一行
            'debug'             => FALSE|| $this->_debug == 1, #debug 默认为false
        ];
        $default_other_conds = [
            'fields'   => '*',
            'group_by' => NULL,
            'order_by' => NULL,
            'limit'    => NULL,
        ];
        if (empty($table)) {
            $this->error_code = 1;
            $this->error_msg = 'Invalid table name';
            return FALSE;
        }
        $option = array_merge($default_option, $option);
        $other_conds = array_merge($default_other_conds, $other_conds);
        $bind_params = [];
        $where_conds_str = $this->build_where_cond($bind_params, $where_conds);
        $group_by_str = '';
        if (!empty($other_conds['group_by'])) {
            $other_conds['group_by'] = $this->escape($other_conds['group_by']);
            $group_by_str .= "GROUP BY {$other_conds['group_by']}";
        }
        $order_by_str = '';
        if (!empty($other_conds['order_by'])) {
            $other_conds['order_by'] = $this->escape($other_conds['order_by']);
            $order_by_str .= "ORDER BY {$other_conds['order_by']}";
        }
        $limit_str = '';
        if (!empty($other_conds['limit'])) {
            $limit_set = explode(',', $other_conds['limit']);
            $n = count($limit_set);
            if ($n == 2) {
                $bind_params[] = intval($limit_set[0]);
                $bind_params[] = intval($limit_set[1]);
                $limit_str = "LIMIT ?,?";
            } elseif ($n == 1) {
                $bind_params[] = intval($limit_set[0]);
                $limit_str = "LIMIT ?";
            }
        }
        $sql = "SELECT {$other_conds['fields']} FROM {$table} {$where_conds_str} {$group_by_str} {$order_by_str} {$limit_str}";
        #echo $sql;var_dump($bind_params);exit;
        $result = $this->prepare_query($sql, $bind_params, $option);
        return $result;
    }

    /**
     * 更新
     *   如果需要打印出 SQL 使用 debug(1) 或者 get_sql(bool) 或者 附加参数'debug' => true 均可以
     * @param string $table 表名 变量严禁从外界传入
     * @param map $update_map 更新数据。 例：['category' => 'children', 'age+' => 1]  变量可从外界传入
     * @param map $where_conds 查询条件。 例：['age<=' => 16, 'time>' => '2016-06-12']  ps:between可使用">=" + "<="方式  变量可从外界传入
     * @param map $option 附加参数
     *  'debug' => true 附带输出SQL语句 默认为false
     *
     * @return int 更新影响的行数 失败返回false
     *
     * @author jxt
     */
    public function update($table, $update_map = [], $where_conds = NULL, $option = []) {
        $default_option = [
            'on_error_return' => FALSE, #查询报错后,自定义本函数返回的值
            'debug'           => FALSE|| $this->_debug == 1, #debug 默认为false
        ];
        if (empty($update_map) || empty($table)) {
            $this->error_code = 1;
            $this->error_msg = 'Invalid update_map or table name';
            return FALSE;
        }
        #保护措施 即便没有条件 也要传一个空数组[]
        if (NULL === $where_conds || !is_array($where_conds)) {
            $this->error_code = 1;
            $this->error_msg = 'Invalid where_conds';
            return FALSE;
        }
        $option = array_merge($default_option, $option);
        $bind_params = [];
        $update_str = $this->build_update_cond($bind_params, $update_map);
        $where_conds_str = $this->build_where_cond($bind_params, $where_conds);
        $sql = "UPDATE {$table} SET {$update_str} {$where_conds_str}";
        #echo $sql;var_dump($bind_params);exit;
        $result = $this->prepare_query($sql, $bind_params, $option);
        return $result;
    }

    /**
     * 插入
     *    如果需要打印出 SQL 使用 debug(1) 或者 get_sql(bool) 或者 附加参数'debug' => true 均可以
     * @param string $table 表名 变量严禁从外界传入
     * @param map $insert_map 更新数据。 例：['category' => 'children', 'role' => 'guest']  变量可从外界传入
     * @param map $option 附加参数
     *  'debug' => true 附带输出SQL语句 默认为false
     * @return int 插入的行数 失败返回false
     *
     * @author jxt
     */
    public function insert($table, $insert_map = [], $option = []) {
        $default_option = [
            'on_error_return' => FALSE, #查询报错后,自定义本函数返回的值
            'debug'           => FALSE|| $this->_debug == 1, #debug 默认为false
        ];
        if (empty($table)) {
            $this->error_code = 1;
            $this->error_msg = 'Invalid table name';
            return FALSE;
        }
        $option = array_merge($default_option, $option);
        $insert_key_str = [];
        $insert_value_str = [];
        $bind_params = [];
        foreach ($insert_map as $k => $v) {
            $k = $this->escape($k);
            if ('NOW()' === $v) {
                $insert_key_str[] = "{$k}";
                $insert_value_str[] = 'NOW()';
            } elseif (is_null($v)) {
                $insert_key_str[] = "{$k}";
                $insert_value_str[] = 'NULL';
            } else {
                $bind_params[] = $v;
                $insert_key_str[] = "{$k}";
                $insert_value_str[] = '?';
            }
        }
        $insert_key_str = implode(',', $insert_key_str);
        $insert_value_str = implode(',', $insert_value_str);
        $sql = "INSERT INTO {$table} ({$insert_key_str}) VALUES ({$insert_value_str})";
        #echo $sql;var_dump($bind_params);exit;
        $result = $this->prepare_query($sql, $bind_params, $option);
        return $result;
    }

    /**
     * 删除
     *   如果需要打印出 SQL 使用 debug(1) 或者 get_sql(bool) 或者 附加参数'debug' => true 均可以
     * @param string $table 表名 变量严禁从外界传入
     * @param map $where_conds 查询条件。 例：['age<=' => 16, 'time>' => '2016-06-12']  ps:between可使用">=" + "<="方式  变量可从外界传入
     * @param map $option 附加参数
     *  'debug' => true 附带输出SQL语句 默认为false
     * @return int 删除的行数 失败返回false
     *
     * @author jxt
     */
    public function delete($table, $where_conds = NULL, $option = []) {
        $default_option = [
            'on_error_return' => FALSE, #查询报错后,自定义本函数返回的值
            'debug'           => FALSE|| $this->_debug == 1, #debug 默认为false
        ];
        if (empty($table)) {
            $this->error_code = 1;
            $this->error_msg = 'Invalid table name';
            return FALSE;
        }
        #保护措施 即便没有条件 也要传一个空数组[]
        if (NULL === $where_conds || !is_array($where_conds)) {
            $this->error_code = 1;
            $this->error_msg = 'Invalid where_conds';
            return FALSE;
        }
        $option = array_merge($default_option, $option);
        $bind_params = [];
        $where_conds_str = $this->build_where_cond($bind_params, $where_conds);
        $sql = "DELETE FROM {$table} {$where_conds_str}";
        #echo $sql;var_dump($bind_params);exit;
        $result = $this->prepare_query($sql, $bind_params, $option);
        return $result;
    }

    /**
     * 有则更新无则插入
     *       如果需要打印出 SQL 使用 debug(1) 或者 get_sql(bool) 或者 附加参数'debug' => true 均可以
     * @param string $table 表名 变量严禁从外界传入
     * @param map $replace_map 更新数据。传入的参数必须有唯一索引或主键 例：['cert_id' => '3701010101', 'visit_num+' => 1, 'aviliable_num-' => 1, 'type' => 'liangmin']  变量可从外界传入
     * @param map $option 附加参数
     *  'debug' => true 附带输出SQL语句 默认为false
     *
     * @return int 更新影响的行数 失败返回false
     *
     * @author jxt
     */
    public function replace($table, $replace_map = [], $option = []) {
        $default_option = [
            'on_error_return' => FALSE, #查询报错后,自定义本函数返回的值
            'debug'             => FALSE || $this->_debug == 1, #debug 默认为false
        ];
        if (empty($replace_map) || empty($table)) {
            $this->error_code = 1;
            $this->error_msg = 'Invalid replace_map or table name';
            return FALSE;
        }
        $option = array_merge($default_option, $option);
        $bind_params = [];
        $insert_key_str = [];
        $insert_value_str = [];
        foreach ($replace_map as $k => $v) {
            $k = $this->escape($k);
            if (mb_strrichr(strtolower($k), '-') == '-') {#字段自减
                $k = rtrim($k, '-');
                $bind_params[] = -$v;
                $insert_value_str[] = '?';
            } elseif ('NOW()' === $v) {
                $insert_value_str[] = 'NOW()';
            } elseif (is_null($v)) {
                $insert_value_str[] = 'NULL';
            } else {
                $k = rtrim($k, '+');
                $bind_params[] = $v;
                $insert_value_str[] = '?';
            }
            $insert_key_str[] = "{$k}";
        }
        $insert_key_str = implode(',', $insert_key_str);
        $insert_value_str = implode(',', $insert_value_str);
        $sql = "INSERT INTO {$table} ({$insert_key_str}) VALUES ({$insert_value_str})";
        $update_str = $this->build_update_cond($bind_params, $replace_map);
        $sql = "INSERT INTO {$table} ({$insert_key_str}) VALUES ({$insert_value_str}) 
                ON DUPLICATE KEY UPDATE {$update_str}";
        #echo $sql;var_dump($bind_params);exit;
        $result = $this->prepare_query($sql, $bind_params, $option);
        return $result;
    }

    /**
     * 构建更新语句
     *
     * @author jxt
     */
    private function build_update_cond(&$bind_params, $update_map = []) {
        $update_str = [];
        foreach ($update_map as $k => $v) {
            $k = $this->escape($k);
            if (mb_strrichr(strtolower($k), '+') == '+') {#字段自增
                $k = rtrim($k, '+');
                $bind_params[] = $v;
                $update_str[] = "{$k}={$k}+?";
            } elseif (mb_strrichr(strtolower($k), '-') == '-') {#字段自增
                $k = rtrim($k, '-');
                $bind_params[] = $v;
                $update_str[] = "{$k}={$k}-?";
            } elseif (is_null($v)) {
                $update_str[] = "{$k}=NULL";
            } elseif ($v === 'NOW()') {
                $update_str[] = "{$k}=NOW()";
            } else {
                $bind_params[] = $v;
                $update_str[] = "{$k}=?";
            }
        }
        $update_str = implode(',', $update_str);
        return $update_str;
    }

    /**
     * 构建查询条件
     *
     * @author jxt
     */
    private function build_where_cond(&$bind_params, $where_conds = []) {
        $where_conds_str = [];
        foreach ($where_conds as $k => $v) {
            $k = trim($k);
            $k = $this->escape($k);
            if (mb_strrichr(strtolower($k), 'in') == 'in' && is_array($v)) {#以 IN 结尾
                $v_str = [];
                foreach ($v as $vv) {
                    $bind_params[] = $vv;
                    $v_str[] = '?';
                }
                $v_str = implode(',', $v_str);
                $where_conds_str[] = "{$k} ({$v_str})";
            } elseif (is_null($v) && (mb_strrichr(strtolower($k), 'is') == 'is' || mb_strrichr(strtolower($k), 'is not') == 'is not')) {
                $where_conds_str[] = "{$k} NULL";
            } else {
                $bind_params[] = $v;
                $where_conds_str[] = "{$k}?";
            }
        }
        $where_conds_str = implode(' AND ', $where_conds_str);
        if (!empty($where_conds_str)) {
            $where_conds_str = ' WHERE ' . $where_conds_str;
        }
        return $where_conds_str;
    }

    /**
     * 预编译查询 当select_x() update() delete() insert() replace() 无法满足需求时, 使用此方法
     *     如果需要打印出 SQL 使用 debug(1) 或者 get_sql(bool) 或者 附加参数'debug' => true 均可以
     * 
     * @param string $sql SQL语句 变量使用?代替 例： SELECT * FROM user WHERE user_id=? AND username=?  注： 支持？的场景： where limit update和insert设置的变量
     * @param list $bind_params 绑定变量 针对上面例子，例：[404263, 'solorush'] 表字段如果是整型，PHP变量可传int和string；如果表字段是字符型，PHP变量务必传string
     * @param map $option 附加参数
     *  'force_read_master' => true 强制从主库读数据 默认为false
     *  'debug' => true 附带输出SQL语句 默认为false
     * @author jxt
     */
    public function prepare_query($sql, $bind_params = [], $option = []) {

        // var_dump($sql);
        // exit;
        $default_option = [
            'force_read_master' => FALSE, #从主库读取(可用于对实时性要求高的后台和作业)
            'on_error_return'   => FALSE, #查询报错后,自定义本函数返回的值
            'get'               => 'all', # all：取结果中所有行 row：取结果中一行
            'debug'             => true || $this->_debug == 1, #debug 默认为false
        ];
        $option = array_merge($default_option, $option);
        // 事务强制走master
        if (!$this->autocommit) {
            $option['force_read_master'] = true;
        }
        $this->_is_write($sql, $option['force_read_master']);
        if (FALSE === $this->_select_link()) {
            $this->error_code = 1;
            $this->error_msg = 'mysql link is empty';
            return $option['on_error_return'];
        }

        $this->_stmt = mysqli_prepare($this->_now, $sql);
        // var_dump($this->_now);
        // echo $sql;
        // var_dump($bind_params);
        // // exit;

        if (FALSE === $this->_stmt) {
            $this->error_code = 1;
            $this->error_msg = 'mysqli_prepare  error occurred';
            return FALSE;
        }
        $ref = [];
        if (!empty($bind_params) && is_array($bind_params)) {
            $ref[0] = '';
            foreach ($bind_params as $v) {
                $ref[0] .= is_int($v) ? 'i' : 's';
            }
            foreach ($bind_params as $k => $v) {
                $ref[] = &$bind_params[$k];
            }
            #记录sql
            $this->_record_sql($sql, $ref, $option['debug']);
            //mysqli_stmt::bind_param 方法没有检测返回值
            //建议加上返回值检测
            //http://php.net/manual/zh/mysqli-stmt.bind-param.php
            $is_binded = call_user_func_array([$this->_stmt, 'bind_param'], $ref);
            if (!$is_binded) {
                //如果返回false,会导致 $this->_stmt = null, $this->_stmt->execute() 无法执行，所以这里中断执行
                $this->error_code = -1;
                $this->error_msg = 'bind_param failed';
                return FALSE;
            }
        } else {
            #记录sql
            $this->_record_sql($sql, $ref, $option['debug']);
        }
        
        #isset($this->ci->cat) && $this->ci->cat->start('SQL', $sql, $bind_params);
        isset($this->ci->cat) && $this->ci->cat->start('SQL', strtoupper($this->_op));
        isset($this->ci->cat) && $this->ci->cat->event('SQL.Method', strtoupper($this->_op));
        $host = $this->_config['hostname'][$this->sign][$this->_set_db[$this->sign]];
        isset($this->ci->cat) && $this->ci->cat->event('SQL.Database', "jdbc:mysql://{$host}:{$this->port}/{$this->_config['database']}?useUnicode=true&characterEncoding={$this->option['charset']}&autoReconnect=true");
        isset($this->ci->cat) && $this->ci->cat->event('SQL.name', $sql);
        $query_error_msg = 0;
        $result = $this->_stmt->execute();
        if (FALSE === $result) {
            $query_error_msg = $this->get_error_msg();
            goto ARCHOR_RESULT;
        }
        // 事务中的修改操作只有在commit后才生效
        if (in_array($this->_op ,['insert', 'update', 'delete'])) {
            if ($this->_op == 'insert') {
                $insert_id = mysqli_stmt_insert_id($this->_stmt);
                if (!empty($insert_id)) {
                    $result = $insert_id;
                }
            } elseif ($this->_op == 'update' || $this->_op == 'delete') {
              
                $num_rows = mysqli_stmt_affected_rows($this->_stmt);
                if ($num_rows >= 0) {
                    $result = $num_rows;
                }
            }
        } else {
            #select
            $rs = $this->_stmt->get_result();
            $result = [];
            if ($option['get'] == 'all') {
                while ($row = $rs->fetch_assoc()) {
                    $result[] = $row;
                }
            } else {
                $result = $rs->fetch_assoc();
                if (is_null($result)) {
                    $result = [];
                }
            }
        }
        #goto ARCHOR_RESULT;
        ARCHOR_RESULT:
        
        isset($this->ci->cat) && $this->ci->cat->end(null, $query_error_msg);
        
        if (is_object($this->_stmt)) {
            #$this->_stmt->close();
        }
        return $result;
    }

    /**
     * 字符转义
     * @author jxt
     */
    public function escape($var) {
        if (is_scalar($var)) {
            return addslashes($var);
        } else {
            return $var;
        }
    }

    public function array_escape($arr) {
        if (!is_array($arr)) {
            return $arr;
        }
        foreach ($arr as $k => $v) {
            $arr[$k] = $this->escape($v);
        }
        return $arr;
    }

    /**
     * 开始事务
     * @return type
     */
    public function begin() {
        return $this->autocommit(false); // 关闭自动提交
    }

    /**
     * 事务提交
     * @return type
     */
    public function commit() {
        $this->sign = 'write';
        $this->_select_link();
        $result = $this->_now->commit();
        $this->autocommit(true); // 恢复自动提交
        return $result;
    }

    /**
     * 回滚
     * @return type
     */
    public function rollback() {
        $this->sign = 'write';
        $this->_select_link();
        $result = $this->_now->rollback();
        $this->autocommit(true); // 恢复自动提交
        return $result;
    }

    /**
     * 选择是否自动提交(默认开启,事务要关闭)
     * @param type $switch
     * @return type
     */
    public function autocommit($switch = true) {
        $this->sign = 'write';
        $this->_select_link();
        $this->autocommit = $switch;
        return $this->_now->autocommit($switch);
    }
    
    public function close() {
        $db_config = $this->_config;
        foreach ($this->_link as $op => $inst_list) {
            foreach ($inst_list as $node => $inst) {
                if ($inst instanceof mysqli && isset($inst->thread_id) && $inst->thread_id > 0) {
                    #echo "{$inst->thread_id} ";
                    isset($this->ci->cat) && $this->ci->cat->start('MySQL Close', $this->conn . ':' . $op . $node . ' => ' .$db_config['hostname'][$this->sign][$this->_set_db[$this->sign]] . ' => ' . $db_config['database']);
                    #echo $this->conn . ':' . $op . $node . ' => ' .$db_config['hostname'][$this->sign][$this->_set_db[$this->sign]] . ' => ' . $db_config['database'] ."\n";
                    $inst->close();
                    isset($this->ci->cat) && $this->ci->cat->end();
                }
                $this->_link[$op][$node] = null;
                unset($this->_link[$op][$node]);
            }
        }
        self::$container[$this->conn] = null;
        unset(self::$container[$this->conn]);
        $this->_now = null;
    }
    
    static public function close_all() {
        foreach (self::$container as $obj) {
            $obj->close();
        }
    }

    public function sql_log($sql) {
        $this->sql_list[] = $sql;
    }

    public function debug($sign = 0) {
        $this->_debug = $sign;
    }

    function __destruct() {
        $this->close();
        if (is_object($this->_stmt)) {
            $this->_stmt->close();
        }
    }

    /**
     * @var string 记录当前SQL指令 的 预编译 字符串
     */
    protected $query_pre = '';
    /***
     * @var array  记录当前SQL指令 的 bind params
     */
    protected $query_params = array();

    /***
     * 取的最近的一条sql语句的bind params
     * @return array
     * @author      Wentao.Ye <709808807@qq.com>
     * @time        2016-06-22
     */
    public function get_params() {
        return $this->query_params;
    }

    /***
     * 取的最近的一条sql语句
     *      当执行完 update() insert() select() select_row() delete() prepare_query() 方法后
     *      可以使用 get_sql 输出 预编译前 或者预编译 后的SQL语句
     * @param bool $is_real_sql 默认true
     *        true： 编译后sql  SELECT * FROM table_a WHERE field_a ='121212' AND field_b IN (1,3,5)
     *        false: 编译前sql  SELECT * FROM table_a WHERE field_a=? AND field_b IN (?,?,?)
     * @author Wentao YE
     * @return string
     */
    public function get_sql($is_real_sql = TRUE) {
        if (!$is_real_sql) {
            return $this->query_pre;
        }
        $query_str = $this->query_pre;
        foreach ($this->query_params as $k => $value) {
            if ($k == 0) continue;
            if (is_string($value)) {
                $value = '\'' . addslashes($value) . '\'';
            } elseif (is_bool($value)) {
                $value = $value ? '1' : '0';
            } elseif (is_null($value)) {
                $value = 'null';
            } elseif (is_array($value) || is_object($value)) {
                //其他情况，数组，Class等，为非法绑定，无法解析
                $value = 'PARAMS_ERROR';
            }
            $query_str = preg_replace('/\?/', $value, $query_str, 1);
        }
        return $query_str;
    }

    /***
     * 数据库调试 记录当前SQL
     * @param string $prepare_sql
     * @param array $bind_params
     * @param bool $is_debug
     * @access protected
     * @author      Wentao.Ye <709808807@qq.com>
     * @time        2016-06-23
     */
    private function _record_sql($prepare_sql = '', $bind_params = [], $is_debug = FALSE) {
        $this->query_pre = $prepare_sql;
        $this->query_params = $bind_params;
        // if($is_debug){
        //    echo  $this->get_sql(TRUE)."\n";
        // }
    }

     /**
     * 批量插入
     *    如果需要打印出 SQL 使用 debug(1) 或者 get_sql(bool) 或者 附加参数'debug' => true 均可以
     * @param string $table 表名 变量严禁从外界传入
     * @param map $insert_map 更新数据。 例：['category' => 'children', 'role' => 'guest']  变量可从外界传入
     * @param map $option 附加参数
     *  'debug' => true 附带输出SQL语句 默认为false
     * @return int 插入的行数 失败返回false
     *
     * @author jxt
     */
    public function insert_batch($table, $insert_data = [], $option = []) {
        $default_option = [
            'on_error_return' => FALSE, #查询报错后,自定义本函数返回的值
            'debug'           => FALSE|| $this->_debug == 1, #debug 默认为false
        ];
        if (empty($table)) {
            $this->error_code = 1;
            $this->error_msg = 'Invalid table name';
            return FALSE;
        }
        $option = array_merge($default_option, $option);
        $insert_key_str = [];
        $insert_value_str = [];
        $bind_params = [];
        $insert_value_arr = [];
       $insert_sql = '';
        foreach($insert_data as $key=>$insert_map){
            $insert_value_str = [];
            foreach ($insert_map as $k => $v) {
                $k = $this->escape($k);
                if(!in_array( "{$k}",$insert_key_str)){
                    $insert_key_str[] = "{$k}";
                }
                if ('NOW()' === $v) {
                    $insert_value_str[] = 'NOW()';
                } elseif (is_null($v)) {
                    $insert_value_str[] = 'NULL';
                } else {
                    $bind_params[] = $v;
                    $insert_value_str[] = '?';
                }
            }

            $insert_value_str = implode(',', $insert_value_str);
            $insert_sql .= "({$insert_value_str})";
            if($key<count($insert_data)-1){
                $insert_sql .= ",";
            }
        }
        
        $insert_key_str = implode(',', $insert_key_str);
        $sql = "INSERT INTO {$table} ({$insert_key_str})  VALUES ".$insert_sql;
        $result = $this->prepare_query($sql, $bind_params, $option);
        return $result;
    }

    	// --------------------------------------------------------------------

	/**
	 * The "set_insert_batch" function.  Allows key/value pairs to be set for batch inserts
	 *
	 * @param	mixed
	 * @param	string
	 * @param	boolean
	 * @return	object
	 */
	public function set_insert_batch($key, $value = '', $escape = TRUE)
	{
		$key = $this->_object_to_array_batch($key);

		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		$keys = array_keys(current($key));
		sort($keys);

		foreach ($key as $row)
		{
			if (count(array_diff($keys, array_keys($row))) > 0 OR count(array_diff(array_keys($row), $keys)) > 0)
			{
				// batch function above returns an error on an empty array
				$this->ar_set[] = array();
				return;
			}

			ksort($row); // puts $row in the same order as our keys

			if ($escape === FALSE)
			{
				$this->ar_set[] =  '('.implode(',', $row).')';
			}
			else
			{
				$clean = array();

				foreach ($row as $value)
				{
					$clean[] = $this->escape($value);
				}

				$this->ar_set[] =  '('.implode(',', $clean).')';
			}
		}

		foreach ($keys as $k)
		{
			$this->ar_keys[] = $this->_protect_identifiers($k);
		}

		return $this;
	}

    /**
	 * Object to Array
	 *
	 * Takes an object as input and converts the class variables to array key/vals
	 *
	 * @param	object
	 * @return	array
	 */
	public function _object_to_array_batch($object)
	{
		if ( ! is_object($object))
		{
			return $object;
		}

		$array = array();
		$out = get_object_vars($object);
		$fields = array_keys($out);

		foreach ($fields as $val)
		{
			// There are some built in keys we need to ignore for this conversion
			if ($val != '_parent_name')
			{

				$i = 0;
				foreach ($out[$val] as $data)
				{
					$array[$i][$val] = $data;
					$i++;
				}
			}
		}

		return $array;
	}


	/**
	 * Protect Identifiers
	 *
	 * This function is used extensively by the Active Record class, and by
	 * a couple functions in this class.
	 * It takes a column or table name (optionally with an alias) and inserts
	 * the table prefix onto it.  Some logic is necessary in order to deal with
	 * column names that include the path.  Consider a query like this:
	 *
	 * SELECT * FROM hostname.database.table.column AS c FROM hostname.database.table
	 *
	 * Or a query with aliasing:
	 *
	 * SELECT m.member_id, m.member_name FROM members AS m
	 *
	 * Since the column name can include up to four segments (host, DB, table, column)
	 * or also have an alias prefix, we need to do a bit of work to figure this out and
	 * insert the table prefix (if it exists) in the proper position, and escape only
	 * the correct identifiers.
	 *
	 * @access	private
	 * @param	string
	 * @param	bool
	 * @param	mixed
	 * @param	bool
	 * @return	string
	 */
	function _protect_identifiers($item, $prefix_single = FALSE, $protect_identifiers = NULL, $field_exists = TRUE)
	{
		if ( ! is_bool($protect_identifiers))
		{
			$protect_identifiers = $this->_protect_identifiers;
		}

		if (is_array($item))
		{
			$escaped_array = array();

			foreach ($item as $k => $v)
			{
				$escaped_array[$this->_protect_identifiers($k)] = $this->_protect_identifiers($v);
			}

			return $escaped_array;
		}

		// Convert tabs or multiple spaces into single spaces
		$item = preg_replace('/[\t ]+/', ' ', $item);

		// If the item has an alias declaration we remove it and set it aside.
		// Basically we remove everything to the right of the first space
		if (strpos($item, ' ') !== FALSE)
		{
			$alias = strstr($item, ' ');
			$item = substr($item, 0, - strlen($alias));
		}
		else
		{
			$alias = '';
		}

		// This is basically a bug fix for queries that use MAX, MIN, etc.
		// If a parenthesis is found we know that we do not need to
		// escape the data or add a prefix.  There's probably a more graceful
		// way to deal with this, but I'm not thinking of it -- Rick
		if (strpos($item, '(') !== FALSE)
		{
			return $item.$alias;
		}

		// Break the string apart if it contains periods, then insert the table prefix
		// in the correct location, assuming the period doesn't indicate that we're dealing
		// with an alias. While we're at it, we will escape the components
		if (strpos($item, '.') !== FALSE)
		{
			$parts	= explode('.', $item);

			// Does the first segment of the exploded item match
			// one of the aliases previously identified?  If so,
			// we have nothing more to do other than escape the item
			if (in_array($parts[0], $this->ar_aliased_tables))
			{
				if ($protect_identifiers === TRUE)
				{
					foreach ($parts as $key => $val)
					{
						if ( ! in_array($val, $this->_reserved_identifiers))
						{
							$parts[$key] = $this->_escape_identifiers($val);
						}
					}

					$item = implode('.', $parts);
				}
				return $item.$alias;
			}

			// Is there a table prefix defined in the config file?  If not, no need to do anything
			if ($this->dbprefix != '')
			{
				// We now add the table prefix based on some logic.
				// Do we have 4 segments (hostname.database.table.column)?
				// If so, we add the table prefix to the column name in the 3rd segment.
				if (isset($parts[3]))
				{
					$i = 2;
				}
				// Do we have 3 segments (database.table.column)?
				// If so, we add the table prefix to the column name in 2nd position
				elseif (isset($parts[2]))
				{
					$i = 1;
				}
				// Do we have 2 segments (table.column)?
				// If so, we add the table prefix to the column name in 1st segment
				else
				{
					$i = 0;
				}

				// This flag is set when the supplied $item does not contain a field name.
				// This can happen when this function is being called from a JOIN.
				if ($field_exists == FALSE)
				{
					$i++;
				}

				// Verify table prefix and replace if necessary
				if ($this->swap_pre != '' && strncmp($parts[$i], $this->swap_pre, strlen($this->swap_pre)) === 0)
				{
					$parts[$i] = preg_replace("/^".$this->swap_pre."(\S+?)/", $this->dbprefix."\\1", $parts[$i]);
				}

				// We only add the table prefix if it does not already exist
				if (substr($parts[$i], 0, strlen($this->dbprefix)) != $this->dbprefix)
				{
					$parts[$i] = $this->dbprefix.$parts[$i];
				}

				// Put the parts back together
				$item = implode('.', $parts);
			}

			if ($protect_identifiers === TRUE)
			{
				$item = $this->_escape_identifiers($item);
			}

			return $item.$alias;
		}

		// Is there a table prefix?  If not, no need to insert it
		if ($this->dbprefix != '')
		{
			// Verify table prefix and replace if necessary
			if ($this->swap_pre != '' && strncmp($item, $this->swap_pre, strlen($this->swap_pre)) === 0)
			{
				$item = preg_replace("/^".$this->swap_pre."(\S+?)/", $this->dbprefix."\\1", $item);
			}

			// Do we prefix an item with no segments?
			if ($prefix_single == TRUE AND substr($item, 0, strlen($this->dbprefix)) != $this->dbprefix)
			{
				$item = $this->dbprefix.$item;
			}
		}

		if ($protect_identifiers === TRUE AND ! in_array($item, $this->_reserved_identifiers))
		{
			$item = $this->_escape_identifiers($item);
		}

		return $item.$alias;
	}



}
