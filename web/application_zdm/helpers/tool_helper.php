<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 转换为非负数
 * 
 * @param mixed $maybeint 可能是int,string
 * @return int
 * @author Dacheng Chen
 * @time   2014-4-29
 */
function absint( $maybeint ) {
    return abs( intval( $maybeint ) );
}

if ( ! function_exists('process_val'))
{
	function process_val($origin = false ,$default =''){
		if($origin === false){
			return $default;
		}else{
			return $origin;
		}
	}
}

/**
 * Escapes text for SQL LIKE special characters % and _.
 *
 * @since 2.5.0
 *
 * @param string $text The text to be escaped.
 * @return string text, safe for inclusion in LIKE query.
 */
function like_escape($text) {
	return str_replace(array("%", "_"), array("\\%", "\\_"), $text);
}

/**
 * 轻便可逆加密
 * @param type $string
 * @param type $key
 * @return type
 */
if (!function_exists('lite_encrypt')) {

	function lite_encrypt($string, $key) {
		$string = trim($string);
		$iv = substr(md5($key), 0, mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB));
		$string = mcrypt_cfb(MCRYPT_CAST_256, $key, $string, MCRYPT_ENCRYPT, $iv);
		return trim(chop(base64_encode($string)));
	}

}

/**
 * 轻便解密
 * @param type $string
 * @param type $key
 * @return type
 */
if (!function_exists('lite_decrypt')) {

	function lite_decrypt($string, $key) {
		$string = base64_decode($string);
		$iv = substr(md5($key), 0, mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB));
		$p_t = mcrypt_cfb(MCRYPT_CAST_256, $key, $string, MCRYPT_DECRYPT, $iv);
		return trim(chop($p_t));
	}

}

/**
 * 分页输出类
 * 
 * @param $paged 当前分页
 * @param $total_num 结果集总数
 * @param $per_num  每页显示数
 * @param $show_num  页码显示个数
 * @param $is_zhongce  是否是众测分页
 * @param $anchor      用户 锚点 添加 2014-12-17 xml
 * @param $ajax        ajax 方法名
 * @return string  分页html
 * @author zhaolu
 * @time   2014-5-17
 */
function page_format($paged,$total_num,$per_num,$show_num = 6,$is_zhongce = false,$anchor='',$ajax='') {
    $CI = &get_instance();
    $uri = $CI->uri->uri_string;
    $href = '';
    $reg = '#(.*)p([\d]+)#';
    preg_match($reg,$uri,$matches);
    if(empty($matches)){
        $href = $uri."/p";
    }else{
        $href = preg_replace($reg,"$1p" ,$uri);
    }
    $href =  base_url($href);

    $max_page = ceil($total_num/$per_num);
    if(empty($paged) || $paged == 0) {
        $paged = 1;
    }

    $pages_to_show = $show_num;

    $pages_to_show_minus_1 = $pages_to_show-1;
    $half_page_start = floor($pages_to_show_minus_1/2);
    $half_page_end = ceil($pages_to_show_minus_1/2);
    $start_page = $paged - $half_page_start;
    if($start_page <= 0) {
        $start_page = 1;
    }
    $end_page = $paged + $half_page_end;
    if(($end_page - $start_page) != $pages_to_show_minus_1) {
        $end_page = $start_page + $pages_to_show_minus_1;
    }
    if($end_page > $max_page) {
        $start_page = $max_page - $pages_to_show_minus_1;
        $end_page = $max_page;
    }
    if($start_page <= 0) {
        $start_page = 1;
    }

    $page_str = '';
    $anchor_str = '';
    if(!empty($anchor)){
        $anchor_str = '#'.$anchor;
    }
    if($max_page > 1) {
        $page_str .= '<ul class="pagination">';
        $previous_page = $paged - 1;
        if($previous_page > 0){
            if($is_zhongce == true){
                if(!empty($ajax)){
                    $page_str .=  '<li class="pageup"><a href="javascript:void(0)" onclick="'.$ajax.'('.$previous_page.')">上一页</a></li>';
                }else{
                    $page_str .=  '<li class="pageup"><a href="'.$href.$previous_page.$anchor_str.'">上一页</a></li>';
                }
            }else{
                if(!empty($ajax)){
                    $page_str .=  '<li class="pageup"><span>&lt;</span><a href="javascript:void(0)" onclick="'.$ajax.'('.$previous_page.')">上一页</a></li>';
                }else{
                    $page_str .=  '<li class="pageup"><span>&lt;</span><a href="'.$href.$previous_page.$anchor_str.'">上一页</a></li>';
                }
            }
        }
        if ($start_page >= 2 && $pages_to_show < $max_page) {
            if(!empty($ajax)){
                $page_str .=  '<li><a href="javascript:void(0)" onclick="'.$ajax.'(1)">1</a></li>';
            }else{
                $page_str .=  '<li><a href="'.$href.$anchor_str.'1">1</a></li>';
            }
            $page_str .=  '<li><span class="dotStyle">...</span></li>';
        }
        //可以点击页面链接
        for($i = $start_page; $i  <= $end_page; $i++) {
            if($i == $paged){
                if(!empty($ajax)){
                    $page_str .=  '<li><a href="javascript:void(0)" class="pageCurrent" onclick="'.$ajax.'('.$i.')">'.$i.'</a></li>';
                }else{
                    $page_str .=  '<li><a href="'.$href.$i.$anchor_str.'" class="pageCurrent">'.$i.'</a></li>';
                }
            }else{
                if(!empty($ajax)){
                    $page_str .=  '<li><a href="javascript:void(0)" onclick="'.$ajax.'('.$i.')">'.$i.'</a></li>';
                }else{
                    $page_str .=  '<li><a href="'.$href.$i.$anchor_str.'">'.$i.'</a></li>';
                }
            }
            
        }
        if ($end_page < $max_page) {
            $page_str .=  '<li><span class="dotStyle">...</span></li>';
        }
        //next_posts_link
        if($paged < $end_page){
            $next_page= $paged + 1 ;
            if($is_zhongce == true){
                if(!empty($ajax)){
                    $page_str .=  '<li class="pagedown"><a href="javascript:void(0)" onclick="'.$ajax.'('.$next_page.')">下一页</a></li>';
                }else{
                    $page_str .=  '<li class="pagedown"><a href="'.$href.$next_page.$anchor_str.'">下一页</a></li>';
                }
            }else{
                if(!empty($ajax)){
                    $page_str .=  '<li class="pagedown"><a href="javascript:void(0)" onclick="'.$ajax.'('.$next_page.')">下一页</a><span>&gt;</span></li>';
                }else{
                    $page_str .=  '<li class="pagedown"><a href="'.$href.$next_page.$anchor_str.'">下一页</a><span>&gt;</span></li>';
                }
            }
        }
        $page_str .=  '</ul>';
    }
    return $page_str;
}

/**
 * 分页输出类
 * 
 * @param int|string $paged 当前分页
 * @param int|string $total_num 结果集总数
 * @param int|string $per_num  每页显示数
 * @param $loc      默认'#comments'定位到评论版块
 * @return string  分页html
 * @author zhaolu
 * @time   2014-5-17
 */
function page_format_detail($paged,$total_num,$per_num, $loc='',$is_zhongce = false) {
    $CI = &get_instance();
    $uri = $CI->uri->uri_string;
    $href = '';
    $reg = '#(.*)p([\d]+)#';
    preg_match($reg,$uri,$matches);
    if(empty($matches)){
        $url = $uri.'/';
        $href = $uri."/p";
    }else{
        $url = preg_replace($reg,"$1" ,$uri);
        $href = preg_replace($reg,"$1p" ,$uri);
    }
    $href =  base_url($href);
    $url = base_url($url);
    $max_page = ceil($total_num/$per_num);
    if(empty($paged) || $paged == 0) {
        $paged = 1;
    }

    $pages_to_show = 6;

    $pages_to_show_minus_1 = $pages_to_show-1;
    $half_page_start = floor($pages_to_show_minus_1/2);
    $half_page_end = ceil($pages_to_show_minus_1/2);
    $start_page = $paged - $half_page_start;
    if($start_page <= 0) {
        $start_page = 1;
    }
    $end_page = $paged + $half_page_end;
    if(($end_page - $start_page) != $pages_to_show_minus_1) {
        $end_page = $start_page + $pages_to_show_minus_1;
    }
    if($end_page > $max_page) {
        $start_page = $max_page - $pages_to_show_minus_1;
        $end_page = $max_page;
    }
    if($start_page <= 0) {
        $start_page = 1;
    }
    $page_str = '';
    if($max_page > 1) {
        $page_str .= '<ul class="pagination">';
        $previous_page = $paged - 1;
        if($previous_page > 0){
            $pre_uri = (1 == $previous_page) ? $url.$loc : $href.$previous_page.'/'.$loc;
            if($is_zhongce == true){
                $page_str .=  '<li class="pageup"><a href="'.$pre_uri.'">上一页</a></li>';
            }else{
                $page_str .=  '<li class="pageup"><span>&lt;</span><a href="'.$pre_uri.'">上一页</a></li>';
            }
            
        }
        if ($start_page >= 2 && $pages_to_show < $max_page) {
            $page_str .=  '<li><a href="'.$url.$loc.'">1</a></li>';
            $page_str .=  '<li><span class="dotStyle">...</span></li>';
        }
        //可以点击页面链接
        for($i = $start_page; $i  <= $end_page; $i++) {
            $li_uri = (1 == $i) ? $url.$loc : $href.$i.'/'.$loc;
            if($i == $paged){
                $page_str .=  '<li><a href="'.$li_uri.'" class="pageCurrent">'.$i.'</a></li>';
            }else{
                $page_str .=  '<li><a href="'.$li_uri.'">'.$i.'</a></li>';
            }
            
        }
        if ($end_page < $max_page) {
            $page_str .=  '<li><span class="dotStyle">...</span></li>';
             $page_str .=  '<li><a href="'.$href.$max_page.'/'.$loc.'">'.$max_page.'</a></li>';
        }
        //next_posts_link
        if($paged < $end_page){
            $next_page= $paged + 1 ;
            $suf_uri = $href.$next_page.'/'.$loc;
            if($is_zhongce == true){
                $page_str .=  '<li class="pagedown"><a href="'.$suf_uri.'">下一页</a></li>';
            }else{
                $page_str .=  '<li class="pagedown"><a href="'.$suf_uri.'">下一页</a><span>&gt;</span></li>';
            }
            
        }
        $page_str .=  '<li class="jumpToPage">转至<input type="text" class="input_num" id="beginpage">页</li>';
        $page_str .=  '<li><a href="javascript:void(0);" class="a_jumpTo" onclick="return oncheckpage('.$max_page.', \''.$url.'\''.', this)">GO</a></li>';
        $page_str .=  '</ul>';
    }
    return $page_str;
}

/**
 * @param $paged
 * @param $total_num
 * @param $per_num
 * @param int $show_num
 * @param bool $is_zhongce
 * @param string $anchor
 * @return string
 */
function new_page_format($paged,$total_num,$per_num,$show_num = 6,$is_zhongce = false,$anchor='') {
    $CI = &get_instance();
    $uri = $CI->uri->uri_string;
    $href = '';
    $reg = '#(.*)p([\d]+)#';
    preg_match($reg,$uri,$matches);
    if(empty($matches)){
        $url = $uri.'/';
        $href = $uri."/p";
    }else{
        $url = preg_replace($reg,"$1" ,$uri);
        $href = preg_replace($reg,"$1p" ,$uri);
    }
    $url = base_url($url);
    $href =  base_url($href);
    $max_page = ceil($total_num/$per_num);
    if(empty($paged) || $paged == 0) {
        $paged = 1;
    }

    $pages_to_show = $show_num;

    $pages_to_show_minus_1 = $pages_to_show-1;
    $half_page_start = floor($pages_to_show_minus_1/2);
    $half_page_end = ceil($pages_to_show_minus_1/2);
    $start_page = $paged - $half_page_start;
    if($start_page <= 0) {
        $start_page = 1;
    }
    $end_page = $paged + $half_page_end;
    if(($end_page - $start_page) != $pages_to_show_minus_1) {
        $end_page = $start_page + $pages_to_show_minus_1;
    }
    if($end_page > $max_page) {
        $start_page = $max_page - $pages_to_show_minus_1;
        $end_page = $max_page;
    }
    if($start_page <= 0) {
        $start_page = 1;
    }

    $page_str = '';
    $anchor_str = '/';
    if(!empty($anchor)){
        $anchor_str = '/#'.$anchor;
    }
    if($max_page > 1) {
        $page_str .= '<div class="feed-pagenation"><ul class="pagenation-list">';
        $previous_page = $paged - 1;
        if($previous_page > 0){
            $pre_uri = (1 == $previous_page) ? $url : $href.$previous_page.'/';
            $page_str .=  '<li><i class="z-icon-arrow-left"></i><a href="'.$pre_uri.'" class="page-turn">上一页</a></li>';
        }
        if ($start_page >= 2 && $pages_to_show < $max_page) {
            $page_str .=  '<li><a href="'.$url.'">1</a></li>';
            $page_str .=  '<li><span>...</span></li>';
        }
        //可以点击页面链接
        for($i = $start_page; $i  <= $end_page; $i++) {
            $li_uri = (1 == $i) ? $url : $href.$i.'/';
            if($i == $paged){
                $page_str .=  '<li class="current" ><a href="'.$li_uri.'">'.$i.'</a></li>';
            }else{
                $page_str .=  '<li><a href="'.$li_uri.'">'.$i.'</a></li>';
            }
        }
        if ($end_page < $max_page) {
            $page_str .=  '<li><span>...</span></li>';
        }
        //next_posts_link
        if($paged < $end_page){
            $next_page= $paged + 1 ;
            $suf_uri = $href.$next_page.'/';
            $page_str .=  '<li><a href="'.$suf_uri.'" class="page-turn">下一页</a><i class="z-icon-arrow-right"></i></li>';
        }
        $page_str .=  '</ul></div>';
    }
    return $page_str;
}
/**
 * ajax翻页，同上面的new_page_format一样的html结构
 * @param int $paged
 * @param type $total_num
 * @param type $per_num
 * @param type $show_num
 * @return string
 */
function ajax_page_format($paged,$total_num,$per_num,$show_num = 6) {
    $CI = &get_instance();
    $uri = $CI->uri->uri_string;
    $reg = '#(.*)p([\d]+)#';
    $max_page = ceil($total_num/$per_num);
    if(empty($paged) || $paged == 0) {
        $paged = 1;
    }

    $pages_to_show = $show_num;

    $pages_to_show_minus_1 = $pages_to_show-1;
    $half_page_start = floor($pages_to_show_minus_1/2);
    $half_page_end = ceil($pages_to_show_minus_1/2);
    $start_page = $paged - $half_page_start;
    if($start_page <= 0) {
        $start_page = 1;
    }
    $end_page = $paged + $half_page_end;
    if(($end_page - $start_page) != $pages_to_show_minus_1) {
        $end_page = $start_page + $pages_to_show_minus_1;
    }
    if($end_page > $max_page) {
        $start_page = $max_page - $pages_to_show_minus_1;
        $end_page = $max_page;
    }
    if($start_page <= 0) {
        $start_page = 1;
    }

    $page_str = '';
    if($max_page > 1) {
        $page_str .= '<div class="feed-pagenation"><ul class="pagenation-list" id="J_feed_pagenation">';
        $previous_page = $paged - 1;
        if($previous_page > 0){
            $page_str .=  '<li><i class="z-icon-arrow-left"></i><a href="#" data-page="'.$previous_page.'" class="page-turn">上一页</a></li>';
        }
        if ($start_page >= 2 && $pages_to_show < $max_page) {
            $page_str .=  '<li><a href="#" data-page="1">1</a></li>';
            $page_str .=  '<li><span>...</span></li>';
        }
        //可以点击页面链接
        for($i = $start_page; $i  <= $end_page; $i++) {
            if($i == $paged){
                $page_str .=  '<li class="current" ><a href="#" data-page="'.$i.'">'.$i.'</a></li>';
            }else{
                $page_str .=  '<li><a href="#" data-page="'.$i.'">'.$i.'</a></li>';
            }
        }
        if ($end_page < $max_page) {
            $page_str .=  '<li><span>...</span></li>';
        }
        //next_posts_link
        if($paged < $end_page){
            $next_page= $paged + 1 ;
            $page_str .=  '<li><a href="#" data-page="'.$next_page.'" class="page-turn">下一页</a><i class="z-icon-arrow-right"></i></li>';
        }
        $page_str .=  '</ul></div>';
    }
    return $page_str;
}

/**
 * 根据$_GET中是否含有callback参数（即是否为跨域调用）来返回不同的json数据
 * @param  [object]         $data       要返回到ajax调用者的原始数据
 * @param  [string]         $callback   $_GET['callback']的值
 * @return [string]                     数据json后的值
 */
function jsonp_encode($data, $callback=''){
    header('Content-type: application/x-javascript');//for removing the warning of Resource interpreted as Script but transferred with MIME type text/html
    if(empty($callback)){ 
        return json_encode($data);
    }
    else
        return $callback .'('.json_encode($data).')';
}

/**
 * 判断两个一维数组的元素值是否相等(忽略索引)
 *      例如 array(1, 2, 3, 4, 5) 与 array(5, 3, 2, 1, 4) 返回TRUE
 *      当多个时in_array()会比较多时可以用该方法，比如角色里的权限判断
 * 
 * @param   array       $array1         一维数组
 * @param   array       $array2         一维数组
 * @return  bool
 * @author  Dacheng Chen
 * @time    2014-7-18
 */
function bool_array_equal($array1, $array2){
    $result1 = array_diff($array1, $array2);
    $result2 = array_diff($array2, $array1);
    if(empty($result1) && empty($result2)){
        return TRUE;
    }else{
        return FALSE;
    }
}

/**
 * 时间格式化(评论列表、个人中心等用该格式)
 *      2012-12-4 17:53:25 => 12-4 17:53
 *      2011-12-4 17:53:25 => 2011-12-4
 *
 * @param   int         $timestamp      时间（时间戳格式）
 * @param   boolean     $is_gm          是否GMT时间，默认不是。（文章打分是）
 * @return  string
 * @author  Dacheng Chen
 */
function get_uhome_format_time($timestamp, $is_gm=false) {
    if ($is_gm) {
        $diff = gmmktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"))-$timestamp;
        $format_time_year = gmdate('Y', $timestamp );
        $format_time = gmdate('Y-m-d H:i:s', $timestamp );

    } else {
        if (!is_numeric($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        $diff = time()-$timestamp;
        $format_time = date('Y-m-d H:i:s', $timestamp );
        $format_time_year = date('Y', $timestamp );
        
    }
    if ($format_time_year == date('Y')) {
        if($diff < 60){
            $format_time = "刚刚";
        }else if($diff >= 60 && $diff < 3600){
            $format_time = floor($diff/60)."分钟前";
        }else if($diff >= 3600 && $diff < 86400){
            $format_time = floor($diff/3600)."小时前";
        }else{
            $format_time = substr($format_time, 5, 11);
        }
    }else{
        $format_time = substr($format_time, 0, 10);
    }
    return $format_time; 
}

/**
 * API时间格式化
 *
 * @param unknown_type $publishDate
 * @return unknown
 */
function api_format_date($publishDate,$flag = 'list'){
    if (empty($publishDate)) {
        return false;
    }
    $curDate = date("Y-m-d");
    $curYear = date("Y");
    $publish_Date = substr($publishDate, 0, 10);
    $publish_Year = substr($publishDate, 0, 4);

    if($flag == 'list'){
        if ($curDate === $publish_Date) {
            return date('H:i',strtotime($publishDate));
        } else if($curYear === $publish_Year){
            return date('m-d',strtotime($publishDate));
        }else{
            return date('y-m-d',strtotime($publishDate));
        }
    } else {
        if ($curDate === $publish_Date) {
            return date('H:i',strtotime($publishDate));
        } else if($curYear === $publish_Year){
            return date('m-d H:i',strtotime($publishDate));
        }else{
            return date('y-m-d',strtotime($publishDate));
        }
    }
}

/**
 * 判断客户端可以显示的频道
 *
 * @param   string      $from       客户端来源
 * @param   int         $v          客户端版本
 * @return  array       空表示不可以显示；pingce 可以显示评测评论；yuanchuang 可以显示原创评论
 * @author  Dacheng Chen
 * @time    2015-4-16
 */
function get_show_channel($from, $v) {
    $result = [];   #空表示不可以显示；pingce 可以显示评测评论；yuanchuang 可以显示原创评论

    # iphone从5.4开始显示众测；5.6开始显示原创；6.0开始显示点评
    if($from == 'iphone' || $from == 'iphone_widget'){
        if(compare_client_version($v,'5.4','>=')){
            $result[] = 'pingce';
            if(compare_client_version($v,'5.6','>=')){
                $result[] = 'yuanchuang';
                if(compare_client_version($v,'6.0','>=')){#点评
                    $result[] = 'dianping';
                }
            }
        }
        return $result;
    }

    # ipad 现在还没有
    if($from == 'ipad'){
        return $result;
    }

    # android从230开始显示众测评论，从235开始显示原创评论
    if($from == 'android'){
        if($v >= 230){
            $result[] = 'pingce';
            if($v >= 235){
                $result[] = 'yuanchuang';
                if($v >= 285){#6.0版本
                    $result[] = 'dianping';
                }
            }
        }
        return $result;
    }

    # winphone客户端从2.3版本开始显示众测、原创评论
    if($from == 'wp'){
        if($v >= 2.3){
            $result[] = 'pingce';
            $result[] = 'yuanchuang';
        }
        return $result;
    }

    #windows8客户端
    if($from == 'win'){
//        if($v > 1.8){
//            $result[] = 'pingce';
//            $result[] = 'yuanchuang';
//        }
        return $result;
    }

    return $result;
}

/**
 * 替换老图床图片地址域名换成新图床域名，老图片版本也换成新图床版本
 *
 * @global  array       $pic_server         新图床配置
 * @param   string      $img                老图床图片地址，或者文章内容
 * @param   string      $new_suf            新图床图片版本，默认空返回原数据；e600新图床版本号；source新图床原图地址，可能含有角度，不含密码，不含版本；pure 新图床原图地址，不含角度，不含密码，不含版本，啥都不含有。
 * @param   string      $re_default         如果是默认图片是否返回空值
 * @return  string
 * @author  Dacheng Chen
 * @time    2017-4-12
 */
function replace_img_to_new($img, $new_suf='',$re_default=false){
    $CI = &get_instance();
    $CI->load->config('pic');

    if(empty($new_suf)){
        return $img;
    }
    if($img == Config::$constant['default_smzdm_icon_178'] || in_array($img, Config::$default_smzdm_icon)){
        if($re_default){
            return '';
        }else{
            return $img;
        }
    }

    //新图层原图没有旋转角度
    if($new_suf == 'pure'){
        $img = preg_replace(
            array(
                                '#//((qnym|qny|ym|y|am|a|l|lm|qnl|qnlm)\.zdmimg\.com)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',
                                '#//((qnym|qny|qnam|qna|l|lm|qnl|qnlm)\.smzdm\.com)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',//七牛
                                '#//([p|f]\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',
                                '#//([p|f]n\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',
                                '#//((s|j|zb|n)\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)#i',
                                '#//((s|j|zb|nm-)n\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',
                                '#//((z|zn)\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)([^\"\']*)?#i',#众测产品
            ),
            array(
                                '//$1$3.$4',
                                '//$1$3.$4',
                PicConfig::$youhui['mark']['figureBed']['domain'].'/$2.$3',
                PicConfig::$youhui['normal']['figureBed']['domain'].'/$2.$3',
                PicConfig::$article['mark']['figureBed']['domain'].'/$3.$4',
                PicConfig::$article['normal']['figureBed']['domain'].'/$3.$4',
                                '//$1$3.$4',#众测产品
            ),
            $img);
        return $img;
    }

    //原图，含有旋转角度
    if($new_suf == 'source'){
        $img = preg_replace(
                            array(
                                '#//((qnym|qny|ym|y|am|a|l|lm|qnl|qnlm)\.zdmimg\.com)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',//七牛
                                '#//((qnym|qny|qnam|qna|l|lm|qnl|qnlm)\.smzdm\.com)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',//七牛
                                '#//([p|f]\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',
                                '#//([p|f]n\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',
                                '#//((s|j|zb|n)\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)#i',
                                '#//((s|j|zb|nm-)n\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',
                                '#//((z|zn)\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)#i',#众测产品
                            ),
                            array(
                                '//$1$3.$4$5',
                                '//$1$3.$4$5',
                                PicConfig::$youhui['mark']['figureBed']['domain'].'/$2.$3$4',
                                PicConfig::$youhui['normal']['figureBed']['domain'].'/$2.$3$4',
                                PicConfig::$article['mark']['figureBed']['domain'].'/$3.$4$5',
                                PicConfig::$article['normal']['figureBed']['domain'].'/$3.$4$5',
                                '//$1$3.$4$5',#众测产品
                            ),
            $img);
        return $img;
    }

    $img = preg_replace(
        array(
            '#//((qnym|qny|ym|y|am|a|l|lm|qnl|qnlm)\.zdmimg\.com)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',//七牛
            '#//((qnym|qny|qnam|qna|l|lm|qnl|qnlm)\.smzdm\.com)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',//七牛
            '#//([p|f]\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',
            '#//([p|f]n\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',
            '#//((s|j|zb|n)\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)#i',
            '#//((s|j|zb|nm-)n\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)?#i',
            '#//((z|zn)\.zdmimg\.com/)([^\.]*)\.(jpg|png|jpeg|gif|bmp)(-90|-180|-270)?([^\"\']*)#i',#众测产品
        ),
        array(
            '//$1$3.$4$5_'.$new_suf.'.jpg',
            '//$1$3.$4$5_'.$new_suf.'.jpg',
            PicConfig::$youhui['mark']['figureBed']['domain'].'/$2.$3$4_'.$new_suf.'.jpg',
            PicConfig::$youhui['normal']['figureBed']['domain'].'/$2.$3$4_'.$new_suf.'.jpg',
            PicConfig::$article['mark']['figureBed']['domain'].'/$3.$4$5_'.$new_suf.'.jpg',
            PicConfig::$article['normal']['figureBed']['domain'].'/$3.$4$5_'.$new_suf.'.jpg',
            '//$1$3.$4$5_' . $new_suf . '.jpg',#众测产品
        ),
        $img);
    return $img;
}


/**
 * 获得客户端用户IP
 *
 * @return string
 * @author Dacheng Chen
 */
function get_user_ip()
{
    if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $ip_address = $_SERVER["REMOTE_ADDR"];
    } else {
        $ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    if(strpos($ip_address, ',') !== false) {
        $ip_address = explode(',', $ip_address);
        $ip_address = $ip_address[0];
    }
    return $ip_address;
}

/**
 * 生成唯一bigint值 18位长度
 * @return [type] [description]
 */
function unique_int() {
    list($usec, $mic) = explode(' ', microtime());
    $unique = date("YmdHis",$usec).$mic.str_pad( mt_rand( 1, 99999 ), 5, "0", STR_PAD_LEFT );
    $unique = md5($unique);
    $unique = crc32($unique);
    if($unique < 0 ) {
        $unique *= -1;
    }
    $unique = time().$unique;
    return  substr($unique, -18);
}

/**
 * 把数字格式化为时间
 *      例如： 90 =》 0时 1分 1秒； 121 =》 0时 2分 1秒；
 * @param   int     $time       数字时间间隔
 * @return  FALSE|string
 * @author  Dacheng Chen
 * @time    2016-1-20
 */
function Sec2Time($time) {
    if (!is_numeric($time)) {
        return FALSE;
    }

    $value = array(
        "hours" => 0, "minutes" => 0, "seconds" => 0,
    );
    if ($time >= 3600) {
        $value["hours"] = floor($time / 3600);
        $time = ($time % 3600);
    }
    if ($time >= 60) {
        $value["minutes"] = floor($time / 60);
        $time = ($time % 60);
    }
    $value["seconds"] = floor($time);
    $t = "{$value["hours"]}时 {$value["minutes"]}分 {$value["seconds"]}秒";
    Return $t;
}

/**
 * 虚拟币兑换规则
 * @param  [type] $from_coin_type 虚拟币类型 1金币 2银币 ...
 * @param  [type] $coin           虚拟币金额
 * @param  [type] $to_coin_type   需要转换成的虚拟币类型 1金币 2银币
 * @return [type]                 虚拟币兑换
 */
function coin_exchange($from_coin_type, $coin, $to_coin_type) {
    $coin = intval($coin);
    if($from_coin_type == $to_coin_type) {
        return $coin;
    }

    # gold => silver
    if($from_coin_type == 1 && $to_coin_type == 2) {
        return $coin * Config::$coin_rate['gold2silver'];
    }

    #silver => gold
    if($from_coin_type == 2 && $to_coin_type == 1) {
        return floor($coin * Config::$coin_rate['silver2gold']); 
    }
    
}

/**
 * 判断来源from是否来自客户端小于7.0的客户端版本
 * @return boolean [description]
 */
function is_client_less_than_7() { 
    if(Config::$from == "android" && Config::$v >= 320) {
        return false;
    }
    if((Config::$from == "iphone" || Config::$from == 'iphone_widget') && compare_client_version(Config::$v,'7.0','>=')) {
        return false;
    }
    if(in_array(Config::$from, ["web", "wap"]) ) { 
        return false;
    }
    return true;
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
 * [301跳转 统一入口]
 *
 * @author liangjinyi
 * @date   2016-04-28T10:34:18+0800
 * @return [none]
 */
function pagination_p1_301_jump() {
    $is_https = (isset($_SERVER['HTTP_X_Forwarded-Proto'])&&"https"==$_SERVER['HTTP_X_Forwarded-Proto']) 
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])&&"https"==$_SERVER['HTTP_X_FORWARDED_PROTO']);
    if($is_https) {
        $prefix = 'https://';
    } else {
        $prefix = 'http://';
    }
    $url = $prefix.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    //p1默认301跳第一页不显示p1
    if (preg_match('/\/p1[\/]?$/',$url)) {
        $url = preg_replace('/p1[\/]?/', '',$url);
        header('HTTP/1.1 301 Moved Permanently');
        header('Location:'.$url);
    }
    //不以反斜线结尾的301跳转到以反斜线结尾的地址
    if(preg_match('/(.)+[^\/]$/',$url) && !preg_match('/(\/\?)+/',$url)){
        $pattern = array(
            "/([0-9a-zA-Z\%]+)(\?)/",
        );
        $replacement = array(
            "$1/$2",
        );
        $replace_url = preg_replace($pattern, $replacement, $url);
        if($replace_url == $url){
            $url = $url."/";
        }else{
            $url = $replace_url;
        }
        header('HTTP/1.1 301 Moved Permanently');
        header('Location:'.$url);
    }
}

/**
 * 根据URL地址获取域名
 * @param type $url url 地址
 * @return string       域名
 */

function get_domain($url, $clear =false) {
    
    $url = str_replace("!", "",trim($url));
    if($clear){
        if (strpos($url, "p.yiqifa.com") !== false) {
            $url = urldecode(preg_replace("/(.*)p.yiqifa.com(.*)[&|&amp;]t=(.*)/", "$3", $url));
        }
        if (strpos($url, "api.viglink.com")) {
            $url = urldecode(preg_replace("/(.*)api.viglink.com(.*)[&|&amp;]out=(.*)/", "$3", $url));
        }

        if (strpos($url, "count.chanet.com.cn") !== false) {
            $url = urldecode(preg_replace("/(.*)count.chanet.com.cn(.*)[&|&amp;]url=(.*)/", "$3", $url));
        }
        if (strpos($url, "weiyi.com")) {
            $url = urldecode(preg_replace("/(.*)weiyi.com(.*)[&|&amp;]t=(.*)/", "$3", $url));
        }
        if (strpos($url, "c.duomai.com")) {
            $url = urldecode(preg_replace("/(.*)c.duomai.com(.*)[&|&amp;]t=(.*)/", "$3", $url));
        }
    }

    $arr_url = parse_url($url);
    if ($arr_url && array_key_exists("host", $arr_url)) {
        $host = parse_url($url)['host'];
    } else {
        return false;
    }

    $validTlds = array(
            'ab.ca', 'bc.ca', 'mb.ca', 'nb.ca', 'nf.ca', 'nl.ca', 'ns.ca', 'nt.ca', 'nu.ca',
            'pe.ca', 'qc.ca', 'sk.ca', 'yk.ca', 'com.cd', 'net.cd', 'org.cd', 'com.ch',
            'org.ch', 'gov.ch', 'co.ck', 'ac.cn', 'com.cn', 'edu.cn', 'gov.cn', 'net.cn',
            'ah.cn', 'bj.cn', 'cq.cn', 'fj.cn', 'gd.cn', 'gs.cn', 'gz.cn', 'gx.cn', 'ha.cn',
            'he.cn', 'hi.cn', 'hl.cn', 'hn.cn', 'jl.cn', 'js.cn', 'jx.cn', 'ln.cn', 'nm.cn',
            'qh.cn', 'sc.cn', 'sd.cn', 'sh.cn', 'sn.cn', 'sx.cn', 'tj.cn', 'xj.cn', 'xz.cn',
            'zj.cn', 'com.co', 'edu.co', 'org.co', 'gov.co', 'mil.co', 'net.co', 'nom.co',
            'edu.cu', 'org.cu', 'net.cu', 'gov.cu', 'inf.cu', 'gov.cx', 'edu.do', 'gov.do',
            'com.do', 'org.do', 'sld.do', 'web.do', 'net.do', 'mil.do', 'art.do', 'com.dz',
            'net.dz', 'gov.dz', 'edu.dz', 'asso.dz', 'pol.dz', 'art.dz', 'com.ec', 'info.ec',
            'fin.ec', 'med.ec', 'pro.ec', 'org.ec', 'edu.ec', 'gov.ec', 'mil.ec', 'com.ee',
            'fie.ee', 'pri.ee', 'eun.eg', 'edu.eg', 'sci.eg', 'gov.eg', 'com.eg', 'org.eg',
            'mil.eg', 'com.es', 'nom.es', 'org.es', 'gob.es', 'edu.es', 'com.et', 'gov.et',
            'edu.et', 'net.et', 'biz.et', 'name.et', 'info.et', 'co.fk', 'org.fk', 'gov.fk',
            'nom.fk', 'net.fk', 'tm.fr', 'asso.fr', 'nom.fr', 'prd.fr', 'presse.fr',
            'gouv.fr', 'com.ge', 'edu.ge', 'gov.ge', 'org.ge', 'mil.ge', 'net.ge', 'pvt.ge',
            'net.gg', 'org.gg', 'com.gi', 'ltd.gi', 'gov.gi', 'mod.gi', 'edu.gi', 'org.gi',
            'ac.gn', 'gov.gn', 'org.gn', 'net.gn', 'com.gr', 'edu.gr', 'net.gr', 'org.gr',
            'com.hk', 'edu.hk', 'gov.hk', 'idv.hk', 'net.hk', 'org.hk', 'com.hn', 'edu.hn',
            'net.hn', 'mil.hn', 'gob.hn', 'iz.hr', 'from.hr', 'name.hr', 'com.hr', 'com.ht',
            'firm.ht', 'shop.ht', 'info.ht', 'pro.ht', 'adult.ht', 'org.ht', 'art.ht',
            'rel.ht', 'asso.ht', 'perso.ht', 'coop.ht', 'med.ht', 'edu.ht', 'gouv.ht',
            'co.in', 'firm.in', 'net.in', 'org.in', 'gen.in', 'ind.in', 'nic.in', 'ac.in',
            'res.in', 'gov.in', 'mil.in', 'ac.ir', 'co.ir', 'gov.ir', 'net.ir', 'org.ir',
            'gov.it', 'co.je', 'net.je', 'org.je', 'edu.jm', 'gov.jm', 'com.jm', 'net.jm',
            'org.jo', 'net.jo', 'edu.jo', 'gov.jo', 'mil.jo', 'co.kr', 'or.kr', 'com.kw',
            'gov.kw', 'net.kw', 'org.kw', 'mil.kw', 'edu.ky', 'gov.ky', 'com.ky', 'org.ky',
            'org.kz', 'edu.kz', 'net.kz', 'gov.kz', 'mil.kz', 'com.kz', 'com.li', 'net.li',
            'gov.li', 'gov.lk', 'sch.lk', 'net.lk', 'int.lk', 'com.lk', 'org.lk', 'edu.lk',
            'soc.lk', 'web.lk', 'ltd.lk', 'assn.lk', 'grp.lk', 'hotel.lk', 'com.lr',
            'gov.lr', 'org.lr', 'net.lr', 'org.ls', 'co.ls', 'gov.lt', 'mil.lt', 'gov.lu',
            'org.lu', 'net.lu', 'com.lv', 'edu.lv', 'gov.lv', 'org.lv', 'mil.lv', 'id.lv',
            'asn.lv', 'conf.lv', 'com.ly', 'net.ly', 'gov.ly', 'plc.ly', 'edu.ly', 'sch.ly',
            'org.ly', 'id.ly', 'co.ma', 'net.ma', 'gov.ma', 'org.ma', 'tm.mc', 'asso.mc',
            'nom.mg', 'gov.mg', 'prd.mg', 'tm.mg', 'com.mg', 'edu.mg', 'mil.mg', 'com.mk',
            'com.mo', 'net.mo', 'org.mo', 'edu.mo', 'gov.mo', 'org.mt', 'com.mt', 'gov.mt',
            'net.mt', 'com.mu', 'co.mu', 'aero.mv', 'biz.mv', 'com.mv', 'coop.mv', 'edu.mv',
            'info.mv', 'int.mv', 'mil.mv', 'museum.mv', 'name.mv', 'net.mv', 'org.mv',
            'com.mx', 'net.mx', 'org.mx', 'edu.mx', 'gob.mx', 'com.my', 'net.my', 'org.my',
            'edu.my', 'mil.my', 'name.my', 'edu.ng', 'com.ng', 'gov.ng', 'org.ng', 'net.ng',
            'com.ni', 'edu.ni', 'org.ni', 'nom.ni', 'net.ni', 'gov.nr', 'edu.nr', 'biz.nr',
            'com.nr', 'net.nr', 'ac.nz', 'co.nz', 'cri.nz', 'gen.nz', 'geek.nz', 'govt.nz',
            'maori.nz', 'mil.nz', 'net.nz', 'org.nz', 'school.nz', 'com.pf', 'org.pf',
            'com.pg', 'net.pg', 'com.ph', 'gov.ph', 'com.pk', 'net.pk', 'edu.pk', 'org.pk',
            'biz.pk', 'web.pk', 'gov.pk', 'gob.pk', 'gok.pk', 'gon.pk', 'gop.pk', 'gos.pk',
            'biz.pl', 'net.pl', 'art.pl', 'edu.pl', 'org.pl', 'ngo.pl', 'gov.pl', 'info.pl',
            'waw.pl', 'warszawa.pl', 'wroc.pl', 'wroclaw.pl', 'krakow.pl', 'poznan.pl',
            'gda.pl', 'gdansk.pl', 'slupsk.pl', 'szczecin.pl', 'lublin.pl', 'bialystok.pl',
            'torun.pl', 'biz.pr', 'com.pr', 'edu.pr', 'gov.pr', 'info.pr', 'isla.pr',
            'net.pr', 'org.pr', 'pro.pr', 'edu.ps', 'gov.ps', 'sec.ps', 'plo.ps', 'com.ps',
            'net.ps', 'com.pt', 'edu.pt', 'gov.pt', 'int.pt', 'net.pt', 'nome.pt', 'org.pt',
            'net.py', 'org.py', 'gov.py', 'edu.py', 'com.py', 'com.ru', 'net.ru', 'org.ru',
            'msk.ru', 'int.ru', 'ac.ru', 'gov.rw', 'net.rw', 'edu.rw', 'ac.rw', 'com.rw',
            'int.rw', 'mil.rw', 'gouv.rw', 'com.sa', 'edu.sa', 'sch.sa', 'med.sa', 'gov.sa',
            'org.sa', 'pub.sa', 'com.sb', 'gov.sb', 'net.sb', 'edu.sb', 'com.sc', 'gov.sc',
            'org.sc', 'edu.sc', 'com.sd', 'net.sd', 'org.sd', 'edu.sd', 'med.sd', 'tv.sd',
            'info.sd', 'org.se', 'pp.se', 'tm.se', 'parti.se', 'press.se', 'ab.se', 'c.se',
            'e.se', 'f.se', 'g.se', 'h.se', 'i.se', 'k.se', 'm.se', 'n.se', 'o.se', 's.se',
            'u.se', 'w.se', 'x.se', 'y.se', 'z.se', 'ac.se', 'bd.se', 'com.sg', 'net.sg',
            'gov.sg', 'edu.sg', 'per.sg', 'idn.sg', 'edu.sv', 'com.sv', 'gob.sv', 'org.sv',
            'gov.sy', 'com.sy', 'net.sy', 'ac.th', 'co.th', 'in.th', 'go.th', 'mi.th',
            'net.th', 'ac.tj', 'biz.tj', 'com.tj', 'co.tj', 'edu.tj', 'int.tj', 'name.tj',
            'org.tj', 'web.tj', 'gov.tj', 'go.tj', 'mil.tj', 'com.tn', 'intl.tn', 'gov.tn',
            'ind.tn', 'nat.tn', 'tourism.tn', 'info.tn', 'ens.tn', 'fin.tn', 'net.tn',
            'gov.tp', 'com.tr', 'info.tr', 'biz.tr', 'net.tr', 'org.tr', 'web.tr', 'gen.tr',
            'dr.tr', 'bbs.tr', 'name.tr', 'tel.tr', 'gov.tr', 'bel.tr', 'pol.tr', 'mil.tr',
            'edu.tr', 'co.tt', 'com.tt', 'org.tt', 'net.tt', 'biz.tt', 'info.tt', 'pro.tt',
            'edu.tt', 'gov.tt', 'gov.tv', 'edu.tw', 'gov.tw', 'mil.tw', 'com.tw', 'net.tw',
            'idv.tw', 'game.tw', 'ebiz.tw', 'club.tw', 'co.tz', 'ac.tz', 'go.tz', 'or.tz',
            'com.ua', 'gov.ua', 'net.ua', 'edu.ua', 'org.ua', 'cherkassy.ua', 'ck.ua',
            'cn.ua', 'chernovtsy.ua', 'cv.ua', 'crimea.ua', 'dnepropetrovsk.ua', 'dp.ua',
            'dn.ua', 'if.ua', 'kharkov.ua', 'kh.ua', 'kherson.ua', 'ks.ua',
            'km.ua', 'kiev.ua', 'kv.ua', 'kirovograd.ua', 'kr.ua', 'lugansk.ua', 'lg.ua',
            'lviv.ua', 'nikolaev.ua', 'mk.ua', 'odessa.ua', 'od.ua', 'poltava.ua', 'pl.ua',
            'rv.ua', 'sebastopol.ua', 'sumy.ua', 'ternopil.ua', 'te.ua', 'uzhgorod.ua',
            'vn.ua', 'zaporizhzhe.ua', 'zp.ua', 'zhitomir.ua', 'zt.ua', 'co.ug', 'ac.ug',
            'go.ug', 'ne.ug', 'or.ug', 'ac.uk', 'co.uk', 'gov.uk', 'ltd.uk', 'me.uk',
            'mod.uk', 'net.uk', 'nic.uk', 'nhs.uk', 'org.uk', 'plc.uk', 'police.uk', 'bl.uk',
            'jet.uk', 'nel.uk', 'nls.uk', 'parliament.uk', 'sch.uk', 'ak.us', 'al.us',
            'az.us', 'ca.us', 'co.us', 'ct.us', 'dc.us', 'de.us', 'dni.us', 'fed.us',
            'ga.us', 'hi.us', 'ia.us', 'id.us', 'il.us', 'in.us', 'isa.us', 'kids.us',
            'ky.us', 'la.us', 'ma.us', 'md.us', 'me.us', 'mi.us', 'mn.us', 'mo.us', 'ms.us',
            'nc.us', 'nd.us', 'ne.us', 'nh.us', 'nj.us', 'nm.us', 'nsn.us', 'nv.us', 'ny.us',
            'ok.us', 'or.us', 'pa.us', 'ri.us', 'sc.us', 'sd.us', 'tn.us', 'tx.us', 'ut.us',
            'va.us', 'wa.us', 'wi.us', 'wv.us', 'wy.us', 'edu.uy', 'gub.uy', 'org.uy',
            'net.uy', 'mil.uy', 'com.ve', 'net.ve', 'org.ve', 'info.ve', 'co.ve', 'web.ve',
            'org.vi', 'edu.vi', 'gov.vi', 'com.vn', 'net.vn', 'org.vn', 'edu.vn', 'gov.vn',
            'ac.vn', 'biz.vn', 'info.vn', 'name.vn', 'pro.vn', 'health.vn', 'com.ye',
            'ac.yu', 'co.yu', 'org.yu', 'edu.yu', 'ac.za', 'city.za', 'co.za', 'edu.za',
            'law.za', 'mil.za', 'nom.za', 'org.za', 'school.za', 'alt.za', 'net.za',
            'tm.za', 'web.za', 'co.zm', 'org.zm', 'gov.zm', 'sch.zm', 'ac.zm', 'co.zw',
            'gov.zw', 'ac.zw', 'com.ac', 'edu.ac', 'gov.ac', 'net.ac', 'mil.ac', 'org.ac',
            'net.ae', 'co.ae', 'gov.ae', 'ac.ae', 'sch.ae', 'org.ae', 'mil.ae', 'pro.ae',
            'com.ag', 'org.ag', 'net.ag', 'co.ag', 'nom.ag', 'off.ai', 'com.ai', 'net.ai',
            'gov.al', 'edu.al', 'org.al', 'com.al', 'net.al', 'com.am', 'net.am', 'org.am',
            'net.ar', 'org.ar', 'e164.arpa', 'ip6.arpa', 'uri.arpa', 'urn.arpa', 'gv.at',
            'co.at', 'or.at', 'com.au', 'net.au', 'asn.au', 'org.au', 'id.au', 'csiro.au',
            'edu.au', 'com.aw', 'com.az', 'net.az', 'org.az', 'com.bb', 'edu.bb', 'gov.bb',
            'org.bb', 'com.bd', 'edu.bd', 'net.bd', 'gov.bd', 'org.bd', 'mil.be', 'ac.be',
            'com.bm', 'edu.bm', 'org.bm', 'gov.bm', 'net.bm', 'com.bn', 'edu.bn', 'org.bn',
            'com.bo', 'org.bo', 'net.bo', 'gov.bo', 'gob.bo', 'edu.bo', 'tv.bo', 'mil.bo',
            'agr.br', 'am.br', 'art.br', 'edu.br', 'com.br', 'coop.br', 'esp.br', 'far.br',
            'g12.br', 'gov.br', 'imb.br', 'ind.br', 'inf.br', 'mil.br', 'net.br', 'org.br',
            'rec.br', 'srv.br', 'tmp.br', 'tur.br', 'tv.br', 'etc.br', 'adm.br', 'adv.br',
            'ato.br', 'bio.br', 'bmd.br', 'cim.br', 'cng.br', 'cnt.br', 'ecn.br', 'eng.br',
            'fnd.br', 'fot.br', 'fst.br', 'ggf.br', 'jor.br', 'lel.br', 'mat.br', 'med.br',
            'not.br', 'ntr.br', 'odo.br', 'ppg.br', 'pro.br', 'psc.br', 'qsl.br', 'slg.br',
            'vet.br', 'zlg.br', 'dpn.br', 'nom.br', 'com.bs', 'net.bs', 'org.bs', 'com.bt',
            'gov.bt', 'net.bt', 'org.bt', 'co.bw', 'org.bw', 'gov.by', 'mil.by', 'ac.cr',
            'ed.cr', 'fi.cr', 'go.cr', 'or.cr', 'sa.cr', 'com.cy', 'biz.cy', 'info.cy',
            'pro.cy', 'net.cy', 'org.cy', 'name.cy', 'tm.cy', 'ac.cy', 'ekloges.cy',
            'parliament.cy', 'com.dm', 'net.dm', 'org.dm', 'edu.dm', 'gov.dm', 'biz.fj',
            'info.fj', 'name.fj', 'net.fj', 'org.fj', 'pro.fj', 'ac.fj', 'gov.fj', 'mil.fj',
            'com.gh', 'edu.gh', 'gov.gh', 'org.gh', 'mil.gh', 'co.hu', 'info.hu', 'org.hu',
            'sport.hu', 'tm.hu', '2000.hu', 'agrar.hu', 'bolt.hu', 'casino.hu', 'city.hu',
            'erotika.hu', 'film.hu', 'forum.hu', 'games.hu', 'hotel.hu', 'ingatlan.hu',
            'konyvelo.hu', 'lakas.hu', 'media.hu', 'news.hu', 'reklam.hu', 'sex.hu',
            'suli.hu', 'szex.hu', 'tozsde.hu', 'utazas.hu', 'video.hu', 'ac.id', 'co.id',
            'go.id', 'ac.il', 'co.il', 'org.il', 'net.il', 'k12.il', 'gov.il', 'muni.il',
            'co.im', 'net.im', 'gov.im', 'org.im', 'nic.im', 'ac.im', 'org.jm', 'ac.jp',
            'co.jp', 'ed.jp', 'go.jp', 'gr.jp', 'lg.jp', 'ne.jp', 'or.jp', 'hokkaido.jp',
            'iwate.jp', 'miyagi.jp', 'akita.jp', 'yamagata.jp', 'fukushima.jp', 'ibaraki.jp',
            'gunma.jp', 'saitama.jp', 'chiba.jp', 'tokyo.jp', 'kanagawa.jp', 'niigata.jp',
            'ishikawa.jp', 'fukui.jp', 'yamanashi.jp', 'nagano.jp', 'gifu.jp', 'shizuoka.jp',
            'mie.jp', 'shiga.jp', 'kyoto.jp', 'osaka.jp', 'hyogo.jp', 'nara.jp',
            'tottori.jp', 'shimane.jp', 'okayama.jp', 'hiroshima.jp', 'yamaguchi.jp',
            'kagawa.jp', 'ehime.jp', 'kochi.jp', 'fukuoka.jp', 'saga.jp', 'nagasaki.jp',
            'oita.jp', 'miyazaki.jp', 'kagoshima.jp', 'okinawa.jp', 'sapporo.jp',
            'yokohama.jp', 'kawasaki.jp', 'nagoya.jp', 'kobe.jp', 'kitakyushu.jp', 'per.kh',
            'edu.kh', 'gov.kh', 'mil.kh', 'net.kh', 'org.kh', 'net.lb', 'org.lb', 'gov.lb',
            'com.lb', 'com.lc', 'org.lc', 'edu.lc', 'gov.lc', 'army.mil', 'navy.mil',
            'music.mobi', 'ac.mw', 'co.mw', 'com.mw', 'coop.mw', 'edu.mw', 'gov.mw',
            'museum.mw', 'net.mw', 'org.mw', 'mil.no', 'stat.no', 'kommune.no', 'herad.no',
            'vgs.no', 'fhs.no', 'museum.no', 'fylkesbibl.no', 'folkebibl.no', 'idrett.no',
            'org.np', 'edu.np', 'net.np', 'gov.np', 'mil.np', 'org.nr', 'com.om', 'co.om',
            'ac.com', 'sch.om', 'gov.om', 'net.om', 'org.om', 'mil.om', 'museum.om',
            'pro.om', 'med.om', 'com.pa', 'ac.pa', 'sld.pa', 'gob.pa', 'edu.pa', 'org.pa',
            'abo.pa', 'ing.pa', 'med.pa', 'nom.pa', 'com.pe', 'org.pe', 'net.pe', 'edu.pe',
            'gob.pe', 'nom.pe', 'law.pro', 'med.pro', 'cpa.pro', 'vatican.va','us.com',
            'com.ar', 'edu.ar', 'gob.ar', 'gov.ar', 'int.ar', 'mil.ar', 'tur.ar','cn.com'
    );
    $parts = explode('.', $host);
    $count = count($parts);
    if ($count < 2)
        return false;
    $topDomain = $parts [$count-2].'.'.$parts [$count-1];
    if (in_array($topDomain, $validTlds)&&$count>=3) {
        $topDomain = $parts [$count-3].'.'.$topDomain;
    }
    return $topDomain;
}

/**
 * ID 字符串拼接，去重、去空返回一维数组
 * 
 * @param   string  $ids    ID字符串拼接，英文逗号分隔
 * @return  []
 * @author  Dacheng Chen
 * @time    2017-12-14
 */
function filter_ids_to_array($ids = ''){
    $arr = [];
    if(empty($ids)){
        return $arr;
    }

    $ids = explode(',', $ids);
    foreach($ids as $val){
        $temp = intval($val);
        if($temp > 0 && !in_array($temp, $arr)){
            $arr[] = $temp;
        }
    }
    $arr = !empty($arr) ? array_filter($arr) : [];
    return $arr;
}

/**
    * 生成随机字符串
    * @param  int 		$length  字符串长度，默认8
    * @param  string	$chars		随机字符串集，默认字母和数字
    * @return   string   随机字符串
    * @author  zhaoxinran@smzdm.com
    * @date      2018-03-07 15:11:37
    */
function GenerateRandomNumber(int $length = 8, string $chars = '')
{
	// 密码字符集，可任意添加你需要的字符
	$chars = empty($chars) ? 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' : $chars;
	$password = '';
	for ( $i = 0; $i < $length; $i++ )
	{
	// 这里提供两种字符获取方式
	// 第一种是使用 substr 截取$chars中的任意一位字符；
	// 第二种是取字符数组 $chars 的任意元素
	// $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
		$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
	}
	return $password;
}

/**
    * 判断一个字符串是否是合法的日期模式
    * @param  string $data  日期字符串
    * @param  string $format  日期格式，默认 'Y-m-d H:i:s'
    * @return   boolean   true 合法
    * @author  zhaoxinran@smzdm.com
    * @date      2018-03-12 14:39:20
    */
if (!function_exists('CheckDateTime')) {

	function CheckDateTime(string $data, string$format = 'Y-m-d H:i:s') : bool
	{
		if(date($format,strtotime($data)) == $data)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}
