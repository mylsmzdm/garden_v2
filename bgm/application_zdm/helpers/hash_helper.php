<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 获取数组指定的一栏值,返回列表 例如:从数据库查询结果中返回指定的一栏数据
 * @param type $array
 * @param type $column_key
 * @return type
 */
function smzdm_uuid() {
    $mac_match = "/[0-9a-f][0-9a-f]:";
    $mac_match .= "[0-9a-f][0-9a-f]:";
    $mac_match .= "[0-9a-f][0-9a-f]:";
    $mac_match .= "[0-9a-f][0-9a-f]:";
    $mac_match .= "[0-9a-f][0-9a-f]:";
    $mac_match .= "[0-9a-f][0-9a-f]/i";
    $ethernet_match = "/(NAME=.*eth0)|(NAME=.*em1)/i";
    $mac_addr = null;
    static $host_id;
    static $this_pid;

    if (is_null($host_id)) {
        if ('WINNT' == PHP_OS) {
            $host_id = 'aabbccddeeff';
        } else {
            $file_handle = fopen('/etc/udev/rules.d/70-persistent-net.rules', 'r');
            while (!feof($file_handle) && empty($mac_addr)) {
                $line = fgets($file_handle);
                if (preg_match($ethernet_match, $line, $ethernet_name) && preg_match($mac_match, $line, $mac_addr)) {
                    $host_id = str_replace(':', '', $mac_addr[0]);
                }
            }
            fclose($file_handle);
        }
        $host_id = crc32($host_id);
        #echo $host_id;exit;
    }

    list($microsecond, $timestamp) = explode(' ', microtime());
    $c_microtime = $timestamp . '' . substr($microsecond, 2, 6);
    if (is_null($this_pid)) {
        $this_pid = str_pad(getmypid(), 5, '0', STR_PAD_LEFT);
    }

    $uuid = strtolower('' . $c_microtime . '' . $host_id . '' . $this_pid);

    return $uuid;
}

/**
 * 对非整型数据取模
 * 
 * @param type $uuid
 * @param type $delivery
 * @return type
 */
function get_string_mod($uuid, $delivery = 100) {
    $crc = abs(crc32($uuid));
    $mod = $crc % $delivery;
    return $mod;
}

/**
 * 对整型数据取模
 * @param type $id
 * @param type $delivery
 * @return type
 */
function get_number_mod($id, $delivery = 100) {
    return $id % $delivery;
}

/**
 * 对一组字符串id取模 返回应被分配的表 
 * 返回值格式 [table1 => [uuid1, uuid2]]
 * @param array $uuids
 * @param type $table_count 表的总数
 * @param type $prefix 表名
 * @return array
 */
function group_hashname_by_strids($uuids, $table_count, $prefix = '') {
    $result = [];
    if ($table_count <= 0) {
        $uuids = [];
    }
    foreach ($uuids as $uuid) {
        $mod = get_string_mod($uuid, $table_count);
        $result[$prefix . $mod][] = $uuid;
    }
    return $result;
}
