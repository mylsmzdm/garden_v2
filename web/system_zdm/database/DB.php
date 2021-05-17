<?php

function &DB($db_key = '', $option = []) {
    if (!defined('ENVIRONMENT') OR ! file_exists($file_path = APPPATH . 'config/' . ENVIRONMENT . '/service/database.php')) {
        if (!file_exists($file_path = APPPATH . 'config/service/database.php')) {
            show_error('The configuration file database.php does not exist.');
        }
    }
    include($file_path);
    if ($db_key == '') {
        $db_key = 'default';
    }

    global $db;
    $db_config = $db[$db_key];
    $this_db = new mysqli_drv($db_config, $option);
    return $this_db;
}

class mysqli_drv {

    private $_link = array();
    private $_config = null;
    private $_now = null;
    private $_set_db = array('write' => null, 'read' => null);
    private $_op = null;
    private $_debug = 0;
    public $error = '';
    public $sql_list = array();
    public $option = [
        'pconnect' => false,
    ];
    private $ci = null;

    public function __get($key) {
        $CI = & get_instance();
        return $CI->$key;
    }

    public function __construct($db_config = null, $option = []) {
        if (null == $db_config) {
            exit('-1');
        }
        $this->ci = &get_instance();
        if (isset($db_config['write']) && isset($db_config['read'])) {
            if (isset($db_config['set'])) {
                list($this->_set_db['write'], $this->_set_db['read']) = explode(',', $db_config['set']);
            }
            if (is_null($this->_set_db['write'])) {
                $_write_len = count($db_config['write']);
                $this->_set_db['write'] = $_write_len == 1 ? '0' : rand(0, $_write_len - 1);
            }
            if (is_null($this->_set_db['read'])) {
                $_read_len = count($db_config['read']);
                $this->_set_db['read'] = $_read_len == 1 ? '0' : rand(0, $_read_len - 1);
            }
        } else {
            $this->_set_db['write'] = '0';
            $this->_set_db['read'] = &$this->_set_db['write'];
        }
        $this->_config = $db_config;
        $this->option = array_merge($this->option, $option);
    }

    private function _connect() {
        $db_config = null;
        if (isset($this->_config['write']) && isset($this->_config['read'])) {
            $db_config = &$this->_config[$this->sign][$this->_set_db[$this->sign]];
        } else {
            $db_config = &$this->_config;
        }

        if (is_null($db_config)) {
            return false;
        } else {
            if ($this->option['pconnect']) {
                $pconnect = 'p:';
            } else {
                $pconnect = '';
            }
            isset($this->ci->cat) && $this->ci->cat->start('Very Old MySQL Connect', $db_config['hostname'] . ' => ' . $db_config['database']);
            $this->_link[$this->sign][$this->_set_db[$this->sign]] = mysqli_connect($pconnect . $db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);
            isset($this->ci->cat) && $this->ci->cat->end();
            
            if ($this->_link[$this->sign][$this->_set_db[$this->sign]]) {
                mysqli_set_charset($this->_link[$this->sign][$this->_set_db[$this->sign]], $db_config['char_set']);
            } else {
                return false;
            }
        }
        return true;
    }

    private function _is_write($sql, $force_read_master = false) {
        if (true === $force_read_master) {
            $this->sign = 'write';
        } else {
            $this->_op = strtolower(substr($sql, 0, 6));
            $this->sign = in_array($this->_op, array('select')) ? 'read' : 'write';
        }

        return $this->sign;
    }

    private function _select_link() {
        $result = true;
        if (!isset($this->_link[$this->sign][$this->_set_db[$this->sign]])) {
            $result = $this->_connect();
        }
        $this->_now = &$this->_link[$this->sign][$this->_set_db[$this->sign]];
        if (empty($this->_now)) {
            $result = false;
        }
        return $result;
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
        $sql_tpl = preg_replace('/in \([\d\w\'\s,"]+\)/i', 'in (?)', $sql);
        $sql_tpl = preg_replace('/\d+/', '?', $sql_tpl);
        $sql_tpl = preg_replace('/\'[\S\s]+?\'/', '?', $sql_tpl);
        isset($this->ci->cat) && $this->ci->cat->start('Very Old MySQL', $sql_tpl);
        $this->rs = $this->_now->query($sql);
        isset($this->ci->cat) && $this->ci->cat->end();
        if ($this->_op == 'insert') {
            if (empty($this->_now->insert_id)) {
                return $this->rs;
            } else {
                return $this->_now->insert_id;
            }
        } else {
            return $this->rs;
        }
    }

    public function get_row($sql, array $option = []) {
        $default_option = [
            'force_read_master' => false, #从主库读取(可用于对实时性要求高的后台和作业)
            'on_error_return' => false, #查询报错后,自定义本函数返回的值
        ];
        $option = array_merge($default_option, $option);

        $row = array();
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
     * 查询列表
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
     * 字符转义
     * @author jxt
     */
    public function escape($var) {
        return addslashes($var);
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

    public function close() {
        if (isset($this->_now->thread_id) && $this->_now->thread_id > 0) {
            $this->_now->close();
        }
    }

    public function sql_log($sql) {
        $this->sql_list[] = $sql;
    }

    public function debug($sign = 0) {
        $this->_debug = $sign;
    }

}
