<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');



function get_cn_num_by_arab($num) {
    $convert_array = array(0 => '零', 1 => '一', 2 => '二', 3 => '三', 4 => '四', 5 => '五', 6 => '六', 7 => '七', 8 => '八', 9 => '九', 10 => '十',
        11 => '十一', 12 => '十二', 13 => '十三', 14 => '十四', 15 => '十五', 16 => '十六', 17 => '十七', 18 => '十八', 19 => '十九', 20 => '二十',
        21 => '二十一', 22 => '二十二', 23 => '二十三', 24 => '二十四', 25 => '二十五', 26 => '二十六', 27 => '二十七', 28 => '二十八', 29 => '二十九', 30 => '三十',
        31 => '三十一', 32 => '三十二', 33 => '三十三', 34 => '三十四', 35 => '三十五', 36 => '三十六', 37 => '三十七', 38 => '三十八', 39 => '三十九', 40 => '四十',
        41 => '四十一', 42 => '四十二', 43 => '四十三', 44 => '四十四', 45 => '四十五', 46 => '四十六', 47 => '四十七', 48 => '四十八', 49 => '四十九', 50 => '五十',
        51 => '五十一', 52 => '五十二', 53 => '五十三', 54 => '五十四', 55 => '五十五', 56 => '五十六', 57 => '五十七', 58 => '五十八', 59 => '五十九', 60 => '六十',
        61 => '六十一', 62 => '六十二', 63 => '六十三', 64 => '六十四', 65 => '六十五', 66 => '六十六', 67 => '六十七', 68 => '六十八', 69 => '六十九', 70 => '七十',
        71 => '七十一', 72 => '七十二', 73 => '七十三', 74 => '七十四', 75 => '七十五', 76 => '七十六', 77 => '七十七', 78 => '七十八', 79 => '七十九', 80 => '八十',
        81 => '八十一', 82 => '八十二', 83 => '八十三', 84 => '八十四', 85 => '八十五', 86 => '八十六', 87 => '八十七', 88 => '八十八', 89 => '八十九', 90 => '九十',
        91 => '九十一', 92 => '九十二', 93 => '九十三', 94 => '九十四', 95 => '九十五', 96 => '九十六', 97 => '九十七', 98 => '九十八', 99 => '九十九', 100 => '一百');
    if (isset($convert_array[$num])) {
        return $convert_array[$num];
    } else {
        return $num;
    }
}

/**
 * 截取UTF8编码字符串从首字节开始指定宽度(非长度), 适用于字符串长度有限的如新闻标题的等宽度截取 
 * 中英文混排情况较理想. 全中文与全英文截取后对比显示宽度差异最大,且截取宽度远大越明显. 
 * @param string $str   UTF-8 encoding 
 * @param int[option] $width 截取宽度 
 * @param string[option] $end 被截取后追加的尾字符 
 * @param float[option] $x3<p> 
 *  3字节（中文）字符相当于希腊字母宽度的系数coefficient（小数） 
 *  中文通常固定用宋体,根据ascii字符字体宽度设定,不同浏览器可能会有不同显示效果</p> 
 * 
 * @return string 
 * @author waiting 
 * http://waiting.iteye.com 
 */
function utf_substr($str, $width = 0, $end = '...', $x3 = 0) {
    global $CFGX3; // 全局变量保存 x3 的值  

    if ($width <= 0 || $width >= strlen($str)) {  
        return $str;  
    }  
    $e = '';
    $arr = str_split($str);  
    $len = count($arr);  
    $w = 0;  
    $width *= 10;  

    // 不同字节编码字符宽度系数  
    $x1 = 11;   // ASCII  
    $x2 = 16;
    $x3 = $x3 === 0 ? ( $CFGX3['cf3'] > 0 ? $CFGX3['cf3'] * 10 : $x3 = 21 ) : $x3 * 10;
    $x4 = $x3;

    // http://zh.wikipedia.org/zh-cn/UTF8  
    for ($i = 0; $i < $len; $i++) {
        if ($w >= $width) {
            $e = $end;
            break;
        }
        $c = ord($arr[$i]);
        if ($c <= 127) {
            $w += $x1;
        } elseif ($c >= 192 && $c <= 223) { // 2字节头  
            $w += $x2;
            $i += 1;
        } elseif ($c >= 224 && $c <= 239) { // 3字节头  
            $w += $x3;
            $i += 2;
        } elseif ($c >= 240 && $c <= 247) { // 4字节头  
            $w += $x4;
            $i += 3;
        }
    }

    return implode('', array_slice($arr, 0, $i)) . $e;
}

/**
 * 字符串截取
 */
function cutstr($string, $length, $dot = '') {
    $discuz_charset = 'utf8';

    if (strlen($string) <= $length) {
        return $string;
    }
    $strcut = '';
    if (strtolower($discuz_charset) == 'utf8') {

        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {

            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t < 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }

            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);
    } else {
        for ($i = 0; $i < $length - strlen($dot) - 1; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }
    return $strcut . $dot;
}

/**
 * 计算字符串的长度(包括中英数字混合情况)
 * 
 * @param type $str
 * @return int
 */
function count_string_len($str) {
    $name_len = strlen($str);
    $temp_len = 0;
    for ($i = 0; $i < $name_len;) {
        if (strpos('abcdefghijklmnopqrstvuwxyz0123456789ABCDEFGHIJKLMNOPQRSTVUWXYZ_-', $str [$i]) === false) {
            $i = $i + 3;
            $temp_len += 2;
        } else {
            $i = $i + 1;
            $temp_len += 1;
        }
    }
    return $temp_len;
}

/**
 * 评论内容文字出现频率判断
 *      1. 评论内容少于$max_length个字，直接return不处理
 *      2. 评论内容>=$max_length个字，评论内容中前$max_length文本频率是否超过限定频率$the_rate
 * 
 * @param   string      $content        待分析内容
 * @param   int         $max_length     前50个汉字
 * @param   float       $the_rate       频率。默认20%即0.2
 * @param   int         $length         1表示一个汉字
 * @return  bool        TRUE频率超过限制；FALSE未超过
 * @author  Dacheng Chen
 * @time    2014-5-10
 */
function get_exceed_rate($content, $max_length = 50, $the_rate =  20, $length = 1){
    $cnt_tmp = 0;
    $cnt = 0;
    $str = '';
    $str_tmp = array();
    $str_arr = array();
    mb_internal_encoding("utf8");
    $temp_length = (mb_strlen($content) - $length);

    $boo_more = FALSE;
    if($temp_length < $max_length){
        return $boo_more;
    }

    // 只处理前$max_length个汉字
    $content = mb_substr($content, 0, $max_length);
    //取得子串集
    for($i=0; $i<$max_length; $i++){
        $str_tmp[] = mb_substr($content, $i, $length);
    }
    $str_tmp_length = count($str_tmp);

    //去除重复子串 
    $str_tmp = array_unique($str_tmp);

    //计算出现次数
    foreach($str_tmp as $key => $value){
        $cnt_tmp = mb_substr_count($content, $value);
        if($cnt_tmp >= $cnt){
            $str_arr[$value] = $cnt_tmp;   
        }
    }

    //频率
    foreach($str_arr as $key => $val){
        if(($val / $str_tmp_length) >= $the_rate/100){
            $boo_more = TRUE;
            break;
        }
    }
    return $boo_more;
}

/**
 * 增加晒单内容过滤规则，根据需求可增加
 * @param   string      $str_content  晒单内容
 * @param   string      $str_len      保留字符串长度
 * @return  string      
 * @author  zhaolu
 */
function handle_content($str_content, $str_len, $link = "") {
    //转移反斜杠
    $str_content = stripslashes($str_content);
    //过滤html代码
    $str_content = strip_tags($str_content);
    //过滤空格&nbsp;
    $str_content = str_replace("&nbsp;", '', $str_content);
    //过滤空格
    $str_content = str_replace(" ", '', $str_content);
    $str_content = str_replace("\r\n", '', $str_content);
    $str_content = str_replace("\n", '', $str_content);
    $str_content = str_replace("\t", '', $str_content);
    if ($link == "link") {
        return utf_substr($str_content, $str_len);
    } else {
        echo utf_substr($str_content, $str_len);
    }
}

/**
 * 标题输出前转义
 *
 * @param    string       $title     标题
 * @return   string
 */
function title_format($title) {
    return stripslashes(trim($title));
}

/**
 * 给a链接增加rel="nofollow"属性
 *
 * @param   string      $text           内容
 * @return  string
 * @author  Dacheng Chen
 * @time    2014-4-29
 */
function _rel_nofollow($text) {
    $text = stripslashes($text);
    $text = preg_replace_callback('|<a (.+?)>|i', '_rel_nofollow_callback', $text);
    //$text = esc_sql($text);
    //$text = addslashes($text);
    return $text;
}

function _rel_nofollow_callback($matches) {
    $text = $matches[1];
    $text = str_replace(array(' rel="nofollow"', " rel='nofollow'"), '', $text);
    return "<a $text rel=\"nofollow\">";
}

/**
 * 判断字符个数
 *      一个中文（包含中文符号）算一个字符
 * 
 * @param   string    $text       内容
 * @return  int
 * @author  Dacheng Chen
 * @time    2013-12-5
 */
function utf8_strlen($text) {
    // 将字符串分解为单元
    preg_match_all("/./us", $text, $match);
    // 返回单元个数
    return count($match[0]);
}

/**
 * 过滤掉标签
 * 
 * @param   string    $text       内容
 * @return  string
 * @author  Dacheng Chen
 * @time    2014-5-5
 */
function filter_tag($text) {
    $text = htmlspecialchars_decode($text);
    //转义 &#39; => ' 注意：为客户端预留。因网页版浏览器会自动转换显示，客户端不会
    $text = htmlspecialchars_decode($text, ENT_QUOTES);
    $text = strip_tags($text);
    return $text;
}


if (!function_exists('is_serialized')) {

    /**
     * 判断是否为序列化串
     * @param type $data
     * @return type
     */
    function is_serialized($data) {
        // if it isn't a string, it isn't serialized
        if (!is_string($data))
            return false;
        $data = trim($data);
        if ('N;' == $data)
            return true;
        $length = strlen($data);
        if ($length < 4)
            return false;
        if (':' !== $data[1])
            return false;
        $lastc = $data[$length - 1];
        if (';' !== $lastc && '}' !== $lastc)
            return false;
        $token = $data[0];
        switch ($token) {
            case 's' :
                if ('"' !== $data[$length - 2])
                    return false;
            case 'a' :
            case 'O' :
                return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b' :
            case 'i' :
            case 'd' :
                return (bool) preg_match("/^{$token}:[0-9.E-]+;\$/", $data);
        }
        return false;
    }

}

if (!function_exists('maybe_unserialize')) {

    /**
     * 反序列化
     * @param type $data
     * @return type
     */
    function maybe_unserialize($original) {
        if (is_serialized($original)) // don't attempt to unserialize data that wasn't serialized going in
            return @unserialize($original);
        return $original;
    }

}

if(!function_exists('handle_content_for_os_ex')){
    /**
     * 增加晒单内容过滤规则，根据需求可增加（晒物经验专用）
     * @param   string      $str_content  晒单内容
     * @param   string      $str_len      保留字符串长度
     * @param string $link
     * @return string
     */
    function handle_content_for_os_ex($str_content,$str_len,$link="")
    {
        //转移反斜杠
        $str_content = stripslashes($str_content);
        //对段落内先添加个空格符，空格符不使用&nbsp; 防止字符截取时出问题
        $str_content = str_replace('<p>','<p> ',$str_content);
        //过滤html代码
        $str_content = strip_tags($str_content);
        //对商品信息进行过滤
        $str_content=str_replace("[商品：",'[ ', $str_content);
        //回车换行符替换为空格
        $str_content=str_replace("\r\n",' ', $str_content);
        $str_content=str_replace("\n",' ', $str_content);
        $str_content=str_replace("\t",' ', $str_content);
        if($link=="link"){
            return utf_substr($str_content,$str_len);
        }else{
            echo utf_substr($str_content,$str_len);
        }
    }
}

/**
 * 含html截取字符
 * 
 * @param   string    $content       内容
 * @param   int    $maxlen      长度
 * @return  string
 * @author  zhaolu
 * @time    2014-5-13
 */
function html_substr($content, $maxlen = 300) {
    $content = strip_tags($content,"<a><b><strong>");
    $content = str_replace('&nbsp;','',$content);
    //把字符按HTML标签变成数组。
    $content = preg_split("/(<[^>]+?>)/si", $content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $wordrows = 0;   //中英字数
    $outstr = "";     //生成的字串
    $wordend = false;   //是否符合最大的长度
    $beginTags = 0;   //除<img><br><hr>这些短标签外，其它计算开始标签，如<div*>
    $endTags = 0;     //计算结尾标签，如</div>，如果$beginTags==$endTags表示标签数目相对称，可以退出循环。
    //print_r($content);
    foreach ($content as $value) {
        if (trim($value) == "")
            continue;   //如果该值为空，则继续下一个值

        if (strpos(";$value", "<") > 0) {
            //如果与要载取的标签相同，则到处结束截取。
            if (trim($value) == $maxlen) {
                $wordend = true;
                continue;
            }

            if ($wordend == false) {
                $outstr.=$value;
                if (!preg_match("/<img([^>]+?)>/is", $value) && !preg_match("/<param([^>]+?)>/is", $value) && !preg_match("/<!([^>]+?)>/is", $value) && !preg_match("/<br([^>]+?)>/is", $value) && !preg_match("/<hr([^>]+?)>/is", $value)) {
                    $beginTags++; //除img,br,hr外的标签都加1
                }
            } else if (preg_match("/<\/([^>]+?)>/is", $value, $matches)) {
                $endTags++;
                $outstr.=$value;
                if ($beginTags == $endTags && $wordend == true)
                    break;   //字已载完了，并且标签数相称，就可以退出循环。
            }else {
                if (!preg_match("/<img([^>]+?)>/is", $value) && !preg_match("/<param([^>]+?)>/is", $value) && !preg_match("/<!([^>]+?)>/is", $value) && !preg_match("/<br([^>]+?)>/is", $value) && !preg_match("/<hr([^>]+?)>/is", $value)) {
                    $beginTags++; //除img,br,hr外的标签都加1
                    $outstr.=$value;
                }
            }
        } else {
            if (is_numeric($maxlen)) {   //截取字数
                $curLength = getStringLength($value);
                $maxLength = $curLength + $wordrows;
                if ($wordend == false) {
                    if ($maxLength > $maxlen) {   //总字数大于要截取的字数，要在该行要截取
                        $outstr.=subString($value, 0, $maxlen - $wordrows);
                        $wordend = true;
                    } else {
                        $wordrows = $maxLength;
                        $outstr.=$value;
                    }
                }
            } else {
                if ($wordend == false)
                    $outstr.=$value;
            }
        }
    }
    //循环替换掉多余的标签，如<p></p>这一类
    while (preg_match("/<([^\/][^>]*?)><\/([^>]+?)>/is", $outstr)) {
        $outstr = preg_replace_callback("/<([^\/][^>]*?)><\/([^>]+?)>/is", "strip_empty_html", $outstr);
    }
    //把误换的标签换回来
    if (strpos(";" . $outstr, "[html_") > 0) {
        $outstr = str_replace("[html_<]", "<", $outstr);
        $outstr = str_replace("[html_>]", ">", $outstr);
    }
    if($wordend){
        $outstr .= "...";
    }

    //echo htmlspecialchars($outstr);
    return $outstr;
}

//去掉多余的空标签
function strip_empty_html($matches) {
    $arr_tags1 = explode(" ", $matches[1]);
    if ($arr_tags1[0] == $matches[2]) {   //如果前后标签相同，则替换为空。
        return "";
    } else {
        $matches[0] = str_replace("<", "[html_<]", $matches[0]);
        $matches[0] = str_replace(">", "[html_>]", $matches[0]);
        return $matches[0];
    }
}

//取得字符串的长度，包括中英文。
function getStringLength($text) {
    if (function_exists('mb_substr')) {
        $length = mb_strlen($text, 'UTF-8');
    } elseif (function_exists('iconv_substr')) {
        $length = iconv_strlen($text, 'UTF-8');
    } else {
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $text, $ar);
        $length = count($ar[0]);
    }
    return $length;
}

/* * *********按一定长度截取字符串（包括中文）******** */

function subString($text, $start = 0, $limit = 12) {
    if (function_exists('mb_substr')) {
        $more = (mb_strlen($text, 'UTF-8') > $limit) ? TRUE : FALSE;
        $text = mb_substr($text, 0, $limit, 'UTF-8');
        return $text;
    } elseif (function_exists('iconv_substr')) {
        $more = (iconv_strlen($text, 'UTF-8') > $limit) ? TRUE : FALSE;
        $text = iconv_substr($text, 0, $limit, 'UTF-8');
        //return array($text, $more);
        return $text;
    } else {
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $text, $ar);
        if (func_num_args() >= 3) {
            if (count($ar[0]) > $limit) {
                $more = TRUE;
                $text = join("", array_slice($ar[0], 0, $limit));
            } else {
                $more = FALSE;
                $text = join("", array_slice($ar[0], 0, $limit));
            }
        } else {
            $more = FALSE;
            $text = join("", array_slice($ar[0], 0));
        }
        return $text;
    }
}


/**
 * 处理内容中的@TA、email、链接 高亮显示
 * 
 * @param   string      $comment_content        评论内容
 * @param   bool        $convert_smilies        是否转换表情唯一标识为表情图片：TRUE转表情图片；FALSE表情文字标识
 * @param   bool        $convert_atta           是否@TA高亮显示：TRUE高亮显示；FALSE去掉span不高亮显示
 * @return  string      转换后的内容
 * @author  Dacheng Chen
 * @time    2014-5-26
 */
function get_comment_text($content, $convert_smilies=TRUE, $convert_atta=TRUE){
    if (!in_array(Config::$from, ['wp', 'win'])) { #wp不要让其可点
        $content = make_clickable($content);
    }
    if(TRUE == $convert_smilies){
        $content = convert_smilies($content);
    }
    if(TRUE == $convert_atta){
        $content = convert_atta($content);
    }
    if(Config::$from != 'web'){
        // 过滤<a>标签
        $content = preg_replace("#<a[^>]*>(.*?)</a>#is", "$1", $content);
    }
    # wp不替换换行
    if (!in_array(Config::$from, ['wp', 'win'])) {
        $content = str_replace("\n", '<br>', $content);
    }
    # android br替换成换行
    if (in_array(Config::$from, ['android', 'iphone', 'ipad'])) {
        $content = str_replace(array("<br>", "<br/>", "<br />"), "\n", $content);
    }
    return $content;
}
/**
 * 转换评论内容 @TA, email，高亮显示
 * 
 * @param   string      $comment_content        评论内容
 * @return  string      转换后的内容
 * @author  Dacheng Chen
 * @time    2014-5-26
 */
function convert_atta($comment_content) {
    $comment_content .= " ";
    preg_match_all(Config::$constant['comment']['reg_at_displayname'],$comment_content, $users_matches);
    preg_match_all(Config::$constant['comment']['reg_email'], $comment_content, $emails_matches);
    if(!empty($users_matches[0])){
        foreach($users_matches[1] as $match){
            $match = trim($match);
            if(!empty($match) && (empty($emails_matches[3]) || !in_array($match,$emails_matches[3]))){
                $comment_content = str_replace("@".$match, '<span style="color:#ED7E13">@'.$match.'</span>', $comment_content);
            }
        }
    }
    return $comment_content;
}

/**
 * Callback to convert URI match to HTML A element.
 *
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
 * make_clickable()}.
 *
 * @since 2.3.2
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with URI address.
 * @update    2016-11-30, by Dacheng Chen
 */
function _make_url_clickable_cb($matches) {
    $CI = &get_instance();
    $CI->load->library(['link']);
    $url = $matches[0];
    $suffix = '';

    /** Include parentheses in the URL only if paired **/
    while ( substr_count( $url, '(' ) < substr_count( $url, ')' ) ) {
        $suffix = strrchr( $url, ')' ) . $suffix;
        $url = substr( $url, 0, strrpos( $url, ')' ) );
    }
    if ( empty($url) )
        return '';

    #对某URL进行CPS处理
    $cps_url = $CI->link->generate_url([
            'url' => $url,
            'platform' => LinkConfig::$platform['web'],#Config::$from
            'source' => 'aa',
            'channel' => LinkConfig::$channel['pinglun'],
            'article_id' => 0,
        ]);
    $cps_url_href = !empty($cps_url['href']) ? trim($cps_url['href']) : $url;
    #天猫、淘宝链接web上特殊处理
    $_tmall_html = '';
    if(!empty($cps_url['domain']) && in_array($cps_url['domain'], ['tmall.com', 'taobao.com']) && !empty($cps_url['isconvert'])){
        $url = !empty($cps_url['clear_url']) ? trim($cps_url['clear_url']) : $url;
        $_tmall_html = " isconvert=\"1\" data-url=\"{$cps_url_href}\" onclick=\"if(typeof change_direct_url != 'undefined' && change_direct_url instanceof Function){change_direct_url(this)}\" ";
    }else{
        $url = $cps_url_href;
    }
    return "<a target=\"_blank\" href=\"$url\" rel=\"nofollow\" {$_tmall_html}>$url</a>" . $suffix;
}
/**
 * Callback to convert URL match to HTML A element.
 *
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
 * make_clickable()}.
 *
 * @since 2.3.2
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with URL address.
 */
function _make_web_ftp_clickable_cb($matches) {
    $ret = '';
    $dest = $matches[2];
    $dest = 'http://' . $dest;
    if ( empty($dest) )
        return $matches[0];
    // removed trailing [.,;:)] from URL
    if ( in_array( substr($dest, -1), array('.', ',', ';', ':', ')') ) === true ) {
        $ret = substr($dest, -1);
        $dest = substr($dest, 0, strlen($dest)-1);
    }
    return $matches[1] . "<a target=\"_blank\" href=\"$dest\" rel=\"nofollow\">$dest</a>$ret";
}

/**
 * Callback to convert email address match to HTML A element.
 *
 * This function was backported from 2.5.0 to 2.3.2. Regex callback for {@link
 * make_clickable()}.
 *
 * @since 2.3.2
 * @access private
 *
 * @param array $matches Single Regex Match.
 * @return string HTML A element with email address.
 */
function _make_email_clickable_cb($matches) {
    $email = $matches[2] . '@' . $matches[3];
    return $matches[1] . "<a target=\"_blank\" href=\"mailto:$email\">$email</a>";
}
/**
 * Convert plaintext URI to HTML links.
 *
 * Converts URI, www and ftp, and email addresses. Finishes by fixing links
 * within links.
 *
 * @since 0.71
 *
 * @param string $ret Content to convert URIs.
 * @return string Content with converted URIs.
 */
function make_clickable($ret) {
    $ret = ' ' . $ret;
    // in testing, using arrays here was found to be faster
    $save = @ini_set('pcre.recursion_limit', 10000);
    #原来的正则有弊端，URL前后必须有空格才能正确匹配到：'#(?<!=[\'"])(?<=[*\')+.,;:!&$\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#%~/?@\[\]-]{1,2000}|[\'*(+.,;:!=&$](?![\b\)]|(\))?([\s]|$))|(?(1)\)(?![\s<.,;:]|$)|\)))+)#is'
    $retval = preg_replace_callback('#(((https|http)://)|(www.))([\w-]+\.)+[\w-]+(/[\w- ./?%&=:_]*)?#is', '_make_url_clickable_cb', $ret);
    if (null !== $retval )
        $ret = $retval;
    @ini_set('pcre.recursion_limit', $save);
    $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', '_make_web_ftp_clickable_cb', $ret);
    $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
    // this one is not in an array because we need it to run last, for cleanup of accidental links within links
    $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
    $ret = trim($ret);
    return $ret;
}

/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 * Serialized data is always a string.
 *
 * @since 2.0.5
 *
 * @param mixed $data Value to check to see if was serialized.
 * @return bool False if not serialized and true if it was.
 */
function is_serialized( $data ) {
    // if it isn't a string, it isn't serialized
    if ( ! is_string( $data ) )
        return false;
    $data = trim( $data );
    if ( 'N;' == $data )
        return true;
    $length = strlen( $data );
    if ( $length < 4 )
        return false;
    if ( ':' !== $data[1] )
        return false;
    $lastc = $data[$length-1];
    if ( ';' !== $lastc && '}' !== $lastc )
        return false;
    $token = $data[0];
    switch ( $token ) {
        case 's' :
            if ( '"' !== $data[$length-2] )
                return false;
        case 'a' :
        case 'O' :
            return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
        case 'b' :
        case 'i' :
        case 'd' :
            return (bool) preg_match( "/^{$token}:[0-9.E-]+;\$/", $data );
    }
    return false;
}

/**
 * 将手机号字符串中间4位替换为*
 * 
 * @param   string      $str        手机号
 * @return  string
 * @author  Dacheng Chen
 * @time    2014-7-30
 */
function str_replace_middle4($str){
    $m_pre = substr($str, 0, 3);
    $m_suf = substr($str, 7);
    return $m_pre.'****'.$m_suf;
}
/**
 * 替换email为l***s@163.com格式
 * @param   string  $email      email地址
 * @return  string
 * @author  Dacheng Chen
 * @time    2015-6-30
 */
function str_replace_email($email){
    $temp_email = explode('@', $email);
    $m_email = substr($temp_email[0], 0, 1).'***'.substr($temp_email[0], -1).'@'.$temp_email[1];
    return $m_email;
}

/**
 * 获取邮件的服务商网址
 * @param  string $email [description]
 * @return [type]        [description]
 */
function get_email_address($email = '') {
    $email_address = '';
    if (strpos($email, '@gmail.com')) {
        $email_address = 'http://mail.google.com';
    } elseif (strpos($email, '@hotmail.com')) {
        $email_address = 'http://www.hotmail.com';
    } else {
        $email_address = 'http://mail.' . substr($email, strpos($email, '@') + 1);
    }

    return $email_address;
}

/**
 * 判断客户端可以显示的频道
 */
function get_show_channels() { 
    $from = Config::$from;
    $v = isset(Config::$v) ? Config::$v : ''; 

    #TODO:如果以后需要显示新的频道名称,需要在这里更新总的频道数
    $all_channels = ['youhui', 'faxian', 'haitao', 'news', 'test', 'pingce', 'qingdan', 'yuanchuang', 'wiki', 'dianping', 'wiki_topic', '2'];
    $default_channels = ['youhui', 'faxian', 'haitao', 'news'];
    $channels = $default_channels;

    if($from == "web") {
        $channels = $all_channels;
        goto ARCHOR_RESULT;
    }

    if($from == 'iphone' || $from == 'iphone_widget'){
        if(compare_client_version($v,'5.4','>=')) {
            $channels[] = 'pingce';
        } else {
            goto ARCHOR_RESULT;
        }
        if(compare_client_version($v,'5.6','>=')) {
            $channels[] = 'yuanchuang';
        } else {
            goto ARCHOR_RESULT;
        }
        if(compare_client_version($v,'6.0','>=')) {
            $channels[] = 'dianping';
        } else {
            goto ARCHOR_RESULT;
        }
        if(compare_client_version($v,'6.1.2','>=')) {
            $channels[] = 'test';
        } else {
            goto ARCHOR_RESULT;
        }
        if(compare_client_version($v,'6.3','>=')) {
            $channels[] = '2';
        } else {
            goto ARCHOR_RESULT;
        }
        if(compare_client_version($v,'7.2','>=')) {
            $channels[] = 'shai';
        } else {
            goto ARCHOR_RESULT;
        }

        if(compare_client_version($v,'7.4','>=')) {
            $channels[] = 'wiki_topic';
        } else {
            goto ARCHOR_RESULT;
        }
        #TODO:如果有需要增加新的频道， 在这里加上
        
        goto ARCHOR_RESULT;
    }

    if($from == 'ipad'){
        goto ARCHOR_RESULT;
    }

    if($from == 'android'){
        if($v >= 230) {
            $channels[] = 'pingce';
        } else {
            goto ARCHOR_RESULT;
        }
        if($v >= 235) {
            $channels[] = 'yuanchuang';
        } else {
            goto ARCHOR_RESULT;
        }
        if($v >= 285){
            $channels[] = 'dianping';
        } else {
            goto ARCHOR_RESULT;
        }
        if($v >= 305) {
            $channels[] = 'test';
        } else {
            goto ARCHOR_RESULT;
        }
        if($v >= 315) {
            $channels[] = '2';
        } else {
            goto ARCHOR_RESULT;
        }

        if($v >= 335) {
            $channels[] = 'shai';
        } else {
            goto ARCHOR_RESULT;
        }

        if($v >= 355) {
            $channels[] = 'wiki_topic';
        } else {
            goto ARCHOR_RESULT;
        }

        #TODO:如果有需要增加新的频道， 在这里加上
        
        goto ARCHOR_RESULT;
    }

    if($from == 'wp'){
        if($v >= 2.3){
            $channels[] = 'pingce';
            $channels[] = 'yuanchuang';
        }
        goto ARCHOR_RESULT;
    }

    #windows8客户端
    if($from == 'win'){
        goto ARCHOR_RESULT;
    }

    ARCHOR_RESULT:
    $type_arr = [];
    $channel_map = array_map(function($val) { return $val['name']; }, Config::$channel);
    $channel_map = array_flip($channel_map);
    if(empty($channels)) {
        $channels = $all_channels;
    }
    foreach ($channels as $k => $channel) {
        if(isset($channel_map[$channel])) {
            $type_arr[] = $channel_map[$channel];
        }
    }
    $result = implode(',', $type_arr);
    return $result;
}

if ( ! function_exists('compare_client_version'))
{
    function compare_client_version($now,$version,$type){
        $result = false;
        #客户端版本处理
        $a = explode('.', $now);
        $b = explode('.',$version);
        $num = count($a)>count($b)?count($a):count($b);
        #判断结果
        for($i=0;$i<$num;$i++){
            $a[$i] = isset($a[$i])?$a[$i]:0;
            $b[$i] = isset($b[$i])?$b[$i]:0;
        }
        
        $data = '=';
        for($i=0;$i<$num;$i++){
            if($a[$i]!=$b[$i]){
                $res = $a[$i]-$b[$i];
                switch($res){
                    case 0:
                        $data = '=';
                        break;
                    case $res<0:
                        $data = '<';
                        break;
                    case $res>0:
                        $data = '>';
                        break;
                    default :
                        $data = '=';
                        break;
                }
                break;
            }
        }
        $result = strpos($type,$data)!==false?true:false;
        return $result;
    }
}

/**
 * 安全输出 xss过滤，转义html字符
 * @param  string $str [description]
 * @return [type]      [description]
 * @author litongxue 
 */
function safe_output($str = "") {
    if(empty($str)) {
        return $str;
    }
    global $SEC;
    $str = $SEC->xss_clean($str);

    $str = htmlspecialchars($str);

    return $str;

}




