<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('array_column')) {

    /**
     * 获取数组指定的一栏值,返回列表 例如:从数据库查询结果中返回指定的一栏数据
     * @param type $array
     * @param type $column_key
     * @return array
     */
    function array_column($array, $column_key) {
        if (!is_array($array) && !is_object($array)) {
            return [];
        }
        $return = array();
        foreach ($array as $row) {
            if (isset($row[$column_key])) {
                $return[] = $row[$column_key];
            }
        }
        return $return;
    }

}

if (!function_exists('array_to_hash')) {

    /**
     * 指定的字段为key(key是唯一键), 生成 key => value格式
     * @param type $array
     * @param type $key_field_name
     * @return type
     */
    function array_to_hash($array, $key_field_name, $value_field_name) {
        if (false === $array)
            return false;
        $return = array();
        foreach ($array as $row) {
            isset($row[$key_field_name]) && isset($row[$value_field_name]) && $return[$row[$key_field_name]] = $row[$value_field_name];
        }
        return $return;
    }

}

if (!function_exists('array_to_hash_table')) {

    /**
     * 指定的字段为key(key是唯一键), 生成 key => row格式
     * @param type $array
     * @param type $key_field_name
     * @return type
     */
    function array_to_hash_table($array, $key_field_name) {
        if (false === $array)
            return false;
        $return = array();
        foreach ($array as $row) {
            isset($row[$key_field_name]) && $return[$row[$key_field_name]] = $row;
        }
        return $return;
    }

}

if (!function_exists('hash_table_remap_key')) {

    /**
     * 指定二维数组中某个key作为根索引
     * @param type $array
     * @param type $remap_key
     * @return boolean]
     */
    function hash_table_change_key($array, $remap_key) {
        if (false === $array)
            return false;
        $return = array();
        foreach ($array as $row) {
            isset($row[$remap_key]) && $return[$row[$remap_key]] = $row;
        }
        return $return;
    }

}

if (!function_exists('array_to_hash_list_table')) {

    /**
     * 指定的字段为key(key不是唯一键), 生成 key => array(row)格式
     * @param type $array
     * @param type $key_field_name
     * @return type
     */
    function array_to_hash_list_table($array, $key_field_name) {
        if (false === $array)
            return false;
        $return = array();
        foreach ($array as $row) {
            isset($row[$key_field_name]) && $return[$row[$key_field_name]][] = $row;
        }
        return $return;
    }

}

if (!function_exists('array_trim_key')) {

    function array_trim_key($array, $key, $default = '') {
        isset($array[$key]) || $array[$key] = $default;
        $array[$key] = trim($array[$key]);
        return $array;
    }

}

if (!function_exists('array_trim')) {

    /**
     * 批量对数组或对象的值trim
     * @param type $array
     * @return type
     */
    function array_trim($array) {
        if (is_string($array)) {
            $array = trim($array);
        } else if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = array_trim($value);
            }
        } else if (is_object($array)) {
            foreach ($array as $key => $value) {
                $array->$key = array_trim($value);
            }
        }

        return $array;
    }

}

if (!function_exists('array_order_by_col')) {

    /**
     * 根据指定的键对数组排序
     *
     * 用法：
     * @code php
     * $rows = array(
     * array('id' => 1, 'value' => '1-1', 'parent' => 1),
     * array('id' => 2, 'value' => '2-1', 'parent' => 1),
     * array('id' => 3, 'value' => '3-1', 'parent' => 1),
     * array('id' => 4, 'value' => '4-1', 'parent' => 2),
     * array('id' => 5, 'value' => '5-1', 'parent' => 2),
     * array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * );
     *
     * $rows = array_order_by_col($rows, 'id', SORT_DESC);
     * dump($rows);
     * // 输出结果为：
     * // array(
     * // array('id' => 6, 'value' => '6-1', 'parent' => 3),
     * // array('id' => 5, 'value' => '5-1', 'parent' => 2),
     * // array('id' => 4, 'value' => '4-1', 'parent' => 2),
     * // array('id' => 3, 'value' => '3-1', 'parent' => 1),
     * // array('id' => 2, 'value' => '2-1', 'parent' => 1),
     * // array('id' => 1, 'value' => '1-1', 'parent' => 1),
     * // )
     * @endcode
     *
     * @param array $array 要排序的数组
     * @param string $keyname 排序的键
     * @param int $dir 排序方向
     *
     * @return array 排序后的数组
     */
    function array_order_by_col($array, $keyname, $dir = SORT_ASC) {
        return array_order_by_cols($array, array($keyname => $dir));
    }

}

/**
 * 将一个二维数组按照多个列进行排序，类似 SQL 语句中的 ORDER BY
 *
 * 用法：
 * @code php
 * $rows = array_order_by_cols($rows, array(
 * 'parent' => SORT_ASC,
 * 'name' => SORT_DESC,
 * ));
 * @endcode
 *
 * @param array $rowset 要排序的数组
 * @param array $args 排序的键
 *
 * @return array 排序后的数组
 */
function array_order_by_cols($rowset, $args) {
    $sortArray = array();
    $sortRule = '';
    foreach ($args as $sortField => $sortDir) {
        foreach ($rowset as $offset => $row) {
            $sortArray[$sortField][$offset] = $row[$sortField];
        }
        $sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
    }
    if (empty($sortArray) || empty($sortRule)) {
        return $rowset;
    }
    eval('array_multisort(' . $sortRule . '$rowset);');
    return $rowset;
}

/**
 * 递归将所有内层object转为array
 * 
 * @param   array|object        $obj
 * @return  array
 * @author  Dacheng Chen
 * @time    2014-10-12
 */
function object_to_array($obj){
    $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    $arr = array();
    foreach ($_arr as $key => $val){
        $val = (is_array($val) || is_object($val)) ? object_to_array($val) : $val;
        $arr[$key] = $val;
    }
    return $arr;
}

/**
 * @param array $data 要排序的二维数组
 * @param $field  排序的字段
 * @param $direction 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
 * @return array
 * @author liubin
 * @Time:2018/5/25 14:11
 */
function array_sort_by_cols(array $data,$field,$direction)
{
    $sort = array(
        'direction' => $direction,
        'field'     => $field,
    );
    $arrSort = [];
    foreach($data AS $uniqid => $row){
        foreach($row AS $key=>$value){
            $arrSort[$key][$uniqid] = $value;
        }
    }
    if($sort['direction']){
        array_multisort($arrSort[$sort['field']], constant($sort['direction']), $data);
    }

    return $data;
}

/**
 * 将数组中的null值转换为''
 * @param $arr
 * @return array|string
 * @author liubin
 * @Time:2018/7/14 16:50
 */
function unsetNull($arr)
{
    if($arr !== NULL) {
        if(is_array($arr))
        {
            if(!empty($arr))
            {
                foreach($arr as $key => $value)
                {
                    if($value === NULL)
                    {
                        $arr[$key] = '';
                    }else{
                        $arr[$key] = unsetNull($value); #递归再去执行
                    }
                }
            }else{
                $arr = '';
            }
        }else{
            if($arr === NULL)
            {
                $arr = '';
            }
        }
    }else{
        $arr = '';
    }
    return $arr;
}

function getQuarterDate($assessment_stage)
{
    #2020开始施行新考核制度半年一评
    # 计算季度时间范围
    if (stripos($assessment_stage, 'H'))
    {
        $quarter = explode('H',$assessment_stage);
        $quarter_start = $quarter[1] * 6 - 5;
        $quarter_end = $quarter[1] * 6;
        $firstday = date('Y-m-01', strtotime($quarter[0].'-'.$quarter_start));
        $firstday_end = date('Y-m-01', strtotime($quarter[0].'-'.$quarter_end));
        $lastday = date('Y-m-d', strtotime("$firstday_end +1 month -1 day"));
        $performance_lastday = date('Y',strtotime($lastday)).'-'. date('m',strtotime($lastday)).'-15';
        return $data = [
            'firstday' => $firstday,
            'lastday' => $lastday,
            'performance_lastday' => $performance_lastday
        ];
    }
    else
    {
        #2020年以前每季度考核一次
        # 计算季度时间范围
        $quarter = explode('Q',$assessment_stage);
        $quarter_start = $quarter[1] * 3 - 2;
        $quarter_end = $quarter[1] * 3;
        $firstday = date('Y-m-01', strtotime($quarter[0].'-'.$quarter_start));
        $firstday_end = date('Y-m-01', strtotime($quarter[0].'-'.$quarter_end));
        $lastday = date('Y-m-d', strtotime("$firstday_end +1 month -1 day"));
        $performance_lastday = date('Y',strtotime($lastday)).'-'. date('m',strtotime($lastday)).'-15';
        return $data = [
            'firstday' => $firstday,
            'lastday' => $lastday,
            'performance_lastday' => $performance_lastday
        ];
    }




}
/**
 * 2020开始施行新考核制度半年一评，从H恢复Q ，与MY_Controller.php的（）相反。
 * 2020H1 => 2020Q2
 * 2020H2 => 2020Q4
 *
 * @param sting $assessment_stage
 * @return void
 */
function revert_change_stage($assessment_stage)
{
    if (!empty($assessment_stage))
    {
        $_t_year = (int)substr($assessment_stage, 0, 4);
        $_t_quarter = substr($assessment_stage, 4);
        if ($_t_year >= 2020)
        {
            $_s_quarter = $_t_quarter;
            if ($_t_quarter=='H1')
            {
                $_s_quarter = 'Q2';
            }
            else if ($_t_quarter=='H2')
            {
                $_s_quarter = 'Q4';
            }
            $assessment_stage = $_t_year . $_s_quarter;
            $assessment_stage = $_t_year . ( $_t_quarter=='H1' ? "Q2" : "Q4" );
        }
    }
    return $assessment_stage;
}
/**
 * 根据年份季度获取文案
 * 2020以及以后的H1或Q2 => 上半年(H1)
 * 2020以及以后的H2或Q4 => 下半年(H2)
 * 
 * 2020年以前的年份 => 季度
 *
 * @param [type] $assessment_stage
 * @return void
 */
function change_chinese_character_quarter($assessment_stage)
{
    $result = '季度';
    if (!empty($assessment_stage))
    {
        $_t_year = (int)substr($assessment_stage, 0, 4);
        $_t_quarter = substr($assessment_stage, 4);
        if ($_t_year >= 2020)
        {
            if ($_t_quarter=='Q2' || $_t_quarter=='H1')
            {
                $result = '上半年(H1)';
            }
            else if ($_t_quarter=='Q4' || $_t_quarter=='H2')
            {
                $result = '下半年(H2)';
            }
        }
    }
    return $result;
}

/**
 * 处理 0000-00-00 为 ''
 * @param $nulldate
 * @return string
 * @author liubin
 * @Time:2018/9/4 15:55
 */
function dateToNull($nulldate){
    $nulldate = !empty($nulldate) && 0 !== strpos($nulldate, '0000-00') ? trim($nulldate) : '';
    return $nulldate;
}



