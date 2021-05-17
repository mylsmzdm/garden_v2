<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter URL Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/url_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('site_url'))
{
	function site_url($uri = '')
	{
		$CI =& get_instance();
		return $CI->config->site_url($uri);
	}
}

// ------------------------------------------------------------------------

/**
 * Base URL
 * 
 * Create a local URL based on your basepath.
 * Segments can be passed in as a string or an array, same as site_url
 * or a URL to a file can be passed in, e.g. to an image file.
 *
 * @access	public
 * @param string
 * @return	string
 */
if ( ! function_exists('base_url'))
{
	function base_url($uri = '')
	{
		$CI =& get_instance();
		return $CI->config->base_url($uri);
	}
}

// ------------------------------------------------------------------------

/**
 * static URL
 *
 * Create a local URL based on your staticpath.
 * Segments can be passed in as a string or an array, same as site_url
 * or a URL to a file can be passed in, e.g. to an image file.
 *
 * @access	public
 * @param string
 * @return	string
 */
if ( ! function_exists('static_url'))
{
    function static_url($uri = '',$host = '')
    {
        $CI =& get_instance();
        $url=$CI->config->static_url($uri,$host);
        if ((isset($_SERVER['HTTP_X_Forwarded-Proto'])&&"https"==$_SERVER['HTTP_X_Forwarded-Proto']) 
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])&&"https"==$_SERVER['HTTP_X_FORWARDED_PROTO'])){#https访问http替换为https
            $url=str_replace("http://","https://",$url);
        }
        return $url;
    }
}

if ( ! function_exists('is_https')) {
    function is_https() {
        return (isset($_SERVER['HTTP_X_Forwarded-Proto'])&&"https"==$_SERVER['HTTP_X_Forwarded-Proto']) 
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])&&"https"==$_SERVER['HTTP_X_FORWARDED_PROTO']);
    }
}    
// ------------------------------------------------------------------------

/**
 * Current URL
 *
 * Returns the full URL (including segments) of the page where this
 * function is placed
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('current_url'))
{
	function current_url()
	{
		$CI =& get_instance();
		return $CI->config->site_url($CI->uri->uri_string());
	}
}

/**
 * 获取伪静态url
 */
if (!function_exists('current_static_url')) {

    function current_static_url() {
        return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

}

// ------------------------------------------------------------------------
/**
 * URL String
 *
 * Returns the URI segments.
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('uri_string'))
{
	function uri_string()
	{
		$CI =& get_instance();
		return $CI->uri->uri_string();
	}
}

// ------------------------------------------------------------------------

/**
 * Index page
 *
 * Returns the "index_page" from your config file
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('index_page'))
{
	function index_page()
	{
		$CI =& get_instance();
		return $CI->config->item('index_page');
	}
}
// ------------------------------------------------------------------------
/**
 * Header Redirect
 *
 * Header redirect in two flavors
 * For very fine grained control over headers, you could use the Output
 * Library's set_header() function.
 *
 * @access  public
 * @param   string  the URL
 * @param   string  the method: location or redirect
 * @return  string
 */
if ( ! function_exists('redirect'))
{
    function redirect($uri = '', $method = 'location', $http_response_code = 302)
    {
        if ( ! preg_match('#^https?://#i', $uri))
        {
            $uri = site_url($uri);
        }

        switch($method)
        {
            case 'refresh'  : header("Refresh:0;url=".$uri);
                break;
            default         : header("Location: ".$uri, TRUE, $http_response_code);
                break;
        }
        exit;
    }
}

function app_url_handle($url, $need_other = false, $source = "",$article_id=''){
    $url = trim($url);
    $default = [
        'link' => $url,
        'link_type' => '',
        'sub_type' => '',
        'link_val' => '',
        'link_title' => '',
    ];
    #二维码扫描链接替换
    preg_match_all('/http:\/\/www.smzdm.com\/qr\/(.*)\/p\/([0-9]*)/', $url, $matches);
    if(isset($matches[1][0])&&isset($matches[2][0])){
        switch ($matches[1][0]){
            case 'youhui':
                $url = Config::$big_youhui_url_map[1]['url'].$matches[2][0];
                break;
            case 'haitao':
                $url = Config::$big_youhui_url_map[5]['url'].'p/'.$matches[2][0];
                break;
            case 'faxian':
                $url = Config::$big_youhui_url_map[2]['url'].'p/'.$matches[2][0];
                break;
            case 'yuanchuang':
                $url = Config::$channel[11]['url'].'p/'.$matches[2][0];
                break;
            case 'news':
                $url = Config::$channel[6]['url'].'p/'.$matches[2][0];
                break;
            case '2':
                $url = 'http://h5.smzdm.com/user/second/p/'.$matches[2][0];
                break;
        }
    }

    #app九块九链接当做每日白菜标签跳转特殊处理
    if($url == 'http://faxian.smzdm.com/9kuai9'){
        $url = 'http://www.smzdm.com/tag/%E6%AF%8F%E6%97%A5%E7%99%BD%E8%8F%9C/faxian';
    }
    $result = api_url_check_listlink_6_0($url);
    $data = api_url_6_0($url);

    $result = array_merge($result,$data);
    $result = array_merge($default,$result);
    $other_info = operate_other_url($url, Config::$from,$article_id);
    if(Config::$from=='iphone'){
        $result = array_merge($other_info,$result);
    }
    $result['link_type'] = $result['link_type']?$result['link_type']:'other';
    $result['link'] = $url;
    if($result['link_type']=='other' && $other_info){
//        $mall_client = get_mall_client_info('',$url,true);
//        if($other_info){
        $result['link_val'] = $other_info['product_id'];
        if($other_info['b2c'] == 'taobao'){
            $result['sub_type'] = 'taobao';
        }
        if($other_info['b2c'] == 'tmall'){
            $result['sub_type'] = 'tmall';
        }
        if($other_info['b2c'] == 'jdwx'){
            $result['sub_type'] = 'wechat';
            $result['link'] = $other_info['product_id'];
            $result['link_val'] = $result['link'];
        }
        if($other_info['b2c'] == 'jd'){
            $result['sub_type'] = 'jd';
        }

//        }
    }

    $in_urls = false;
    if($result['link_type']=='other'){
        foreach(Config::$no_bar_urls as $k=>$v){
            if(strstr($url,$v)){
                $in_urls = true;
                break;
            }
        }
    }
    if(in_array($url, Config::$no_bar_urls) || $in_urls){
        $result['sub_type'] = 'h5';
    }
    #7.0新增的跳转规则 老客户端统一跳转指定页面
    if (Config::$from == 'android' && Config::$v >= 320 || Config::$from == 'iphone' && compare_client_version(Config::$v, '7.0', '>=')) {

    }else{
        $link_key = $result['link_type']."|".$result['sub_type'];
        $rules_7_0 = [
            'xilie|list','yuanchuang|hot_7','fenlei|wiki','fenlei|haowu','fenlei|second','tag|pingce','tag|wiki','tag|haowu','tag|second','mall|pingce','mall|wiki','mall|haowu','mall|second',
            'haowu/home','user|lipin','user|feedback','pinpai|detail','haojia|','haojia|1','haojia|2','haojia|0','haojia|5','club|','club|6','club|11','club|16','haowu|','zhiyou|','zhiyou|1',
            'zhiyou|2','duihuan|lipin','duihuan|quan','pinpai|jingxuan','pinpai|youhui','pinpai|haitao','pinpai|faxian','pinpai|news','pinpai|yuanchuang','pinpai|pingce','pinpai|wiki','pinpai|haowu','pinpai|second'
        ];
        if(in_array($link_key,$rules_7_0)){
            $result['link'] = 'http://m.smzdm.com/';
            $result['link_type'] = 'other';
            $result['sub_type'] = '';
            $result['link_val'] = '';
            $result['link_title'] = '';
        }
    }
    return $result;
}
function api_url_6_0($url = ''){
    $url_info = array();
    $search = '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i';
    $url = trim(strtolower($url));
    $url = substr($url, -1) == '/'?substr($url, 0,-1):$url; //去除最后一个斜杠
    #客户端6.3排行榜跳转临时处理
    if(strstr($url, '/paihangbang')){
        #排行榜
        $url_info['link_val'] = '';
        $url_info['link_type'] = "paihang";
        $url_info['sub_type'] = "";
    }
    preg_match_all($search, $url ,$rr);
    if(isset($rr[4][0]) && !empty($rr[4][0]) && isset($rr[5][0]) && !empty($rr[5][0])){
        $id_str = urldecode($rr[5][0]);
        if(strstr($url, Config::$channel[0]['url'])){
            if(preg_match('/\/zhuanti\//', $id_str)){
                $url_info['link_val'] = '';
                $url_info['link_type'] = "zhuanti";
                $CI = &get_instance();
                $CI->load->biz('public/zhuanti/zhuanti_biz');
                $path = parse_url($url, PHP_URL_PATH);
                $path_arr = explode('/', $path);
                $project_url = $path_arr[2];
                $project_data = $CI->zhuanti_biz->get_project_data($project_url);
                $url_info['link_title'] = isset($project_data['title'])?$project_data['title']:'';
                $url_info['link_val'] = isset($project_data['id'])?$project_data['id']:'';
                if (Config::$from == 'android' && Config::$v >= 310){ #6.2以后的安卓版本bug修复
                    $url_info['link_val'] = isset($project_data['img'])?$project_data['img']:'';
                }
            }elseif(preg_match('/youhui\/fenlei\//', $id_str)){
                // 优惠分类
                preg_match_all('/youhui\/fenlei\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/product_category_biz');
                    $category = $CI->product_category_biz->get_category_by_url_nicktitle($id_match[1][0]);
                    if(!empty($category)){
                        $url_info['link_val'] = $category['ID'];
                        $url_info['link_type'] = "fenlei";
                        $url_info['sub_type'] = "youhui";
                        $url_info['link_title'] = $category['title'];
                    }
                }
            }elseif(preg_match('/\/fenlei\//', $id_str)){
                // 分类
                preg_match_all('/fenlei\/(.*)\/(.*)/',$id_str,$id_match);
                if(!isset($id_match[1][0])){
                    $id_str .= '/youhui';
                    preg_match_all('/fenlei\/(.*)\/(.*)/',$id_str,$id_match);
                }
                if(isset($id_match[1][0])&&!empty($id_match[2][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/product_category_biz');
                    $category = $CI->product_category_biz->get_category_by_url_nicktitle($id_match[1][0]);
                    if(!empty($category)){
                        $id_match[2][0] = $id_match[2][0]?$id_match[2][0]:'youhui';
                        $id_match[2][0] = $id_match[2][0]=='post'?'yuanchuang':$id_match[2][0];
                        $id_match[2][0] = $id_match[2][0]=='zhongce'?'pingce':$id_match[2][0];
                        $id_match[2][0] = $id_match[2][0]=='test'?'pingce':$id_match[2][0];
                        $url_info['link_val'] = $category['ID'];
                        $url_info['link_type'] = "fenlei";
                        $url_info['sub_type'] = $id_match[2][0];
                        $url_info['link_title'] = $category['title'];
                    }
                }

            }elseif(preg_match('/youhui\/shangpin\//', $id_str)){
                // 优惠商城
                preg_match_all('/youhui\/shangpin\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/mall/mall_biz');
                    $mall = $CI->mall_biz->get_mall_by_name($id_match[1][0]);
                    if(!empty($mall)){
                        $url_info['link_val'] = $mall['id'];
                        $url_info['link_type'] = "mall";
                        $url_info['sub_type'] = 'youhui';
                        $url_info['link_title'] = $id_match[1][0];
                        $url_info['region'] = $mall['domain_select']==1?0:1;
                    }
                }
            }elseif(preg_match('/mall\//', $id_str)){
                // 商城
                preg_match_all('/mall\/(.*)\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[2][0])){
                    $CI = &get_instance();
                    $CI->load->model('mall/mall_db');
                    $mall = $CI->mall_db->get_mall_info($id_match[1][0]);
                    if(!empty($mall)){
                        $id_match[2][0] = $id_match[2][0]?$id_match[2][0]:'youhui';
                        $id_match[2][0] = $id_match[2][0]=='post'?'yuanchuang':$id_match[2][0];
                        $id_match[2][0] = $id_match[2][0]=='zixun'?'news':$id_match[2][0];
                        $id_match[2][0] = $id_match[2][0]=='test'?'pingce':$id_match[2][0];
                        $url_info['link_val'] = $mall['ID'];
                        $url_info['link_type'] = "mall";
                        $url_info['sub_type'] = $id_match[2][0];
                        $url_info['link_title'] = $mall['name_cn'];
                        $url_info['region'] = $mall['domain_select']==1?0:1;
                    }
                }else{
                    preg_match_all('/mall\/(.*)/',$id_str,$id_match);
                    $CI = &get_instance();
                    $CI->load->model('mall/mall_db');
                    $mall = $CI->mall_db->get_mall_info($id_match[1][0]);
                    if(!empty($mall)){
                        $url_info['link_val'] = $mall['ID'];
                        $url_info['link_type'] = "mall";
                        $url_info['sub_type'] = 'youhui';
                        $url_info['link_title'] = $mall['name_cn'];
                        $url_info['region'] = $mall['domain_select']==1?0:1;
                    }
                }

            }elseif(preg_match('/youhui\/tag\//', $id_str)){
                // 标签
                preg_match_all('/tag\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/tag/tag_biz');
                    $tag = $CI->tag_biz->get_tag_by_name($id_match[1][0]);
                    if(!empty($tag)){
                        $url_info['link_val'] = $tag['id'];
                        $url_info['link_type'] = "tag";
                        $url_info['sub_type'] = 'youhui';
                        $url_info['link_title'] = $tag['name'];
                        $url_info['is_tese'] = in_array($tag['name'],['白菜党','神价格','手慢无'])?'1':'0';
                    }
                }
            }elseif(preg_match('/\/tag\//', $id_str)){
                // 标签
                preg_match_all('/tag\/(.*)\/(.*)/',$id_str,$id_match);
                if(!isset($id_match[1][0])){
                    $id_str .= '/youhui';
                    preg_match_all('/tag\/(.*)\/(.*)/',$id_str,$id_match);
                }
                if(isset($id_match[1][0])&&!empty($id_match[2][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/tag/tag_biz');
                    $tag = $CI->tag_biz->get_tag_by_name($id_match[1][0]);
                    if(!empty($tag)){
                        $id_match[2][0] = $id_match[2][0]?$id_match[2][0]:'youhui';
                        $id_match[2][0] = $id_match[2][0]=='post'?'yuanchuang':$id_match[2][0];
                        $id_match[2][0] = $id_match[2][0]=='zhongce'?'pingce':$id_match[2][0];
                        $id_match[2][0] = $id_match[2][0]=='test'?'pingce':$id_match[2][0];
                        $url_info['link_val'] = $tag['id'];
                        $url_info['link_type'] = "tag";
                        $url_info['sub_type'] = $id_match[2][0];
                        $url_info['link_title'] = $tag['name'];
                        $url_info['is_tese'] = in_array($tag['name'],['白菜党','神价格','手慢无'])?'1':'0';
                    }
                }
            }elseif(preg_match('/\/top\//', $id_str)){
                #排行榜 7.0
                preg_match_all('/top\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $url_info['link_val'] = '';
                    $url_info['link_type'] = "top";
                    $url_info['sub_type'] = '';
                    $url_info['link_title'] = '';
                    switch($id_match[1][0]){
                        case 'haojia':
                            $url_info['sub_type'] = 'haojia';
                            $url_info['link_title'] = '好价';
                            break;
                        case 'haojia/guonei':
                            $url_info['sub_type'] = 'haojia';
                            $url_info['link_title'] = '国内';
                            $url_info['link_val'] = '1';
                            break;
                        case 'haojia/haitao':
                            $url_info['sub_type'] = 'haojia';
                            $url_info['link_title'] = '海淘';
                            $url_info['link_val'] = '5';
                            break;
                        case 'haojia/faxian':
                            $url_info['sub_type'] = 'haojia';
                            $url_info['link_title'] = '发现';
                            $url_info['link_val'] = '2';
                            break;
                        case 'haojia/jingxuan':
                            $url_info['sub_type'] = 'haojia';
                            $url_info['link_title'] = '精选';
                            $url_info['link_val'] = '0';
                            break;
                        case 'club':
                            $url_info['sub_type'] = 'club';
                            $url_info['link_title'] = '社区';
                            break;
                        case 'club/post':
                            $url_info['sub_type'] = 'club';
                            $url_info['link_title'] = '原创';
                            $url_info['link_val'] = '11';
                            break;
                        case 'club/news':
                            $url_info['sub_type'] = 'club';
                            $url_info['link_title'] = '资讯';
                            $url_info['link_val'] = '6';
                            break;
                        case 'club/2':
                            $url_info['sub_type'] = 'club';
                            $url_info['link_title'] = '闲置';
                            $url_info['link_val'] = '16';
                            break;
                        case 'haowu':
                            $url_info['sub_type'] = 'haowu';
                            $url_info['link_title'] = '好物';
                            break;
                        case 'zhiyou':
                            $url_info['sub_type'] = 'zhiyou';
                            $url_info['link_title'] = '值友';
                            break;
                        case 'zhiyou/post':
                            $url_info['sub_type'] = 'zhiyou';
                            $url_info['link_title'] = '原创达人';
                            $url_info['link_val'] = '1';
                            break;
                        case 'zhiyou/tip':
                            $url_info['sub_type'] = 'zhiyou';
                            $url_info['link_title'] = '打赏达人';
                            $url_info['link_val'] = '2';
                            break;
                    }
                }
            }else{
                preg_match_all('/^\/(youhui|meiri|quan|huodong|gonggao|tishi|gongyi|xianhua|zixun|p)\\/([0-9]+)/',$id_str,$id_match);
                if(isset($id_match[1][0]) && !empty($id_match[2][0])){
//                    $CI = &get_instance();
//                    $CI->load->model('youhui/youhui_db');
//                    $detail = $CI->youhui_db->get_youhui_detail(['article_id'=>$id_match[2][0]]);
                    $url_info['link_val'] = $id_match[2][0];
                    $url_info['link_type'] = "faxian";
//                    if(isset($detail['data']['channel'])){
//                        if($detail['data']['channel'] == 1){
//                            $url_info['link_type'] = "youhui";
//                        }elseif($detail['data']['channel'] == 5){
//                            $url_info['link_type'] = "haitao";
//                        }
//                    }
                    $url_info['sub_type'] = "detail";
                }
            }
        }else if(strstr($url, Config::$big_youhui_url_map[2]['url']) || strstr($url, 'fx.smzdm.com')){
            if(preg_match('/\/tag\//', $id_str)){
                // 标签
                preg_match_all('/tag\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/tag/tag_biz');
                    $tag = $CI->tag_biz->get_tag_by_name($id_match[1][0]);
                    if(!empty($tag)){
                        $url_info['link_val'] = $tag['id'];
                        $url_info['link_type'] = "tag";
                        $url_info['sub_type'] = "faxian";
                        $url_info['link_title'] = $tag['name'];
                        $url_info['is_tese'] = in_array($tag['name'],['白菜党','神价格','手慢无'])?'1':'0';
                    }
                }
            }elseif(preg_match('/\/fenlei\//', $id_str)){
                // 分类
                preg_match_all('/fenlei\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/product_category_biz');
                    $category = $CI->product_category_biz->get_category_by_url_nicktitle($id_match[1][0]);
                    if(!empty($category)){
                        $url_info['link_val'] = $category['ID'];
                        $url_info['link_type'] = "fenlei";
                        $url_info['sub_type'] = "faxian";
                        $url_info['link_title'] = $category['title'];
                    }
                }
            }elseif(preg_match('/mall\//', $id_str)){
                // 优惠商城
                preg_match_all('/mall\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/mall/mall_biz');
                    $mall = $CI->mall_biz->get_mall_by_name($id_match[1][0]);
                    if(!empty($mall)){
                        $url_info['link_val'] = $mall['id'];
                        $url_info['link_type'] = "mall";
                        $url_info['sub_type'] = "faxian";
                        $url_info['link_title'] = $id_match[1][0];
                        $url_info['region'] = $mall['domain_select']==1?0:1;
                    }
                }
            }else{
                preg_match_all('/(detail|p)\\/([0-9]+)/',$id_str,$id_match);
                if(isset($id_match[1][0]) && !empty($id_match[1][0])){
                    $url_info['link_val'] = $id_match[2][0];
                    $url_info['link_type'] = "faxian";
                    $url_info['sub_type'] = "detail";
                }
            }
        }else if(strstr($url, Config::$channel[3]['url']) || strstr($url, 'show.smzdm.com') ){
            preg_match_all('/(detail|p)\\/([0-9]+)/',$id_str,$id_match);
            if(isset($id_match[1][0]) && !empty($id_match[1][0])){
                $url_info['link_val'] = $id_match[2][0];
                $url_info['link_type'] = "shaiwu";
            }
        }else if(strstr($url, Config::$channel[6]['url'])){
            if(preg_match('/\/tag\//', $id_str)){
                // 标签
                preg_match_all('/tag\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/tag/tag_biz');
                    $tag = $CI->tag_biz->get_tag_by_name($id_match[1][0]);
                    if(!empty($tag)){
                        $url_info['link_val'] = $tag['id'];
                        $url_info['link_type'] = "tag";
                        $url_info['sub_type'] = "news";
                        $url_info['link_title'] = $tag['name'];
                        $url_info['is_tese'] = in_array($tag['name'],['白菜党','神价格','手慢无'])?'1':'0';
                    }
                }
            }elseif(preg_match('/\/fenlei\//', $id_str)){
                // 分类
                preg_match_all('/fenlei\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/product_category_biz');
                    $category = $CI->product_category_biz->get_category_by_url_nicktitle($id_match[1][0]);
                    if(!empty($category)){
                        $url_info['link_val'] = $category['ID'];
                        $url_info['link_type'] = "fenlei";
                        $url_info['sub_type'] = "news";
                        $url_info['link_title'] = $category['title'];
                    }
                }
            }elseif(preg_match('/\/(dianshang|haitao|xinpin|qita)$/', $id_str)){
                // 日志类型
                preg_match_all('/\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $url_info['link_val'] = $id_match[1][0];
                    $url_info['link_type'] = "rzlx";
                    $url_info['sub_type'] = "news";
                    $rzlx_list = ['dianshang'=>'电商','haitao'=>'海淘','xinpin'=>'新品','qita'=>'其他',];
                    $url_info['link_title'] = $rzlx_list[$id_match[1][0]];
                }
            }else{
                preg_match_all('/(p|detail)\\/([0-9]+)/',$id_str,$id_match);
                if(isset($id_match[1][0]) && !empty($id_match[1][0])){
                    $url_info['link_val'] = $id_match[2][0];
                    $url_info['link_type'] = "news";
                    $url_info['sub_type'] = "detail";
                }
            }
        }else if(strstr($url, Config::$big_youhui_url_map[5]['url'])){
            if(preg_match('/\/tag\//', $id_str)){
                // 标签
                preg_match_all('/tag\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/tag/tag_biz');
                    $tag = $CI->tag_biz->get_tag_by_name($id_match[1][0]);
                    if(!empty($tag)){
                        $url_info['link_val'] = $tag['id'];
                        $url_info['link_type'] = "tag";
                        $url_info['sub_type'] = "haitao";
                        $url_info['link_title'] = $tag['name'];
                        $url_info['is_tese'] = in_array($tag['name'],['白菜党','神价格','手慢无'])?'1':'0';
                    }
                }
            }elseif(preg_match('/\/fenlei\//', $id_str)){
                // 分类
                preg_match_all('/fenlei\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/product_category_biz');
                    $category = $CI->product_category_biz->get_category_by_url_nicktitle($id_match[1][0]);
                    if(!empty($category)){
                        $url_info['link_val'] = $category['ID'];
                        $url_info['link_type'] = "fenlei";
                        $url_info['sub_type'] = "haitao";
                        $url_info['link_title'] = $category['title'];
                    }
                }

            }elseif(preg_match('/mall\//', $id_str)){
                // 优惠商城
                preg_match_all('/mall\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/mall/mall_biz');
                    $mall = $CI->mall_biz->get_mall_by_name($id_match[1][0]);
                    if(!empty($mall)){
                        $url_info['link_val'] = $mall['id'];
                        $url_info['link_type'] = "mall";
                        $url_info['sub_type'] = "haitao";
                        $url_info['link_title'] = $id_match[1][0];
                        $url_info['region'] = $mall['domain_select']==1?0:1;
                    }
                }
            }else{
                #海淘优惠
                preg_match_all('/(youhui|huodong|meiri|zhuanyun|gonglve|quan|tishi|p)\\/([0-9]+)/',$id_str,$id_match);
                if(isset($id_match[2][0]) && !empty($id_match[2][0])){
                    $url_info['link_val'] = $id_match[2][0];
//                    $url_info['link_type'] = "haitao";
                    $url_info['link_type'] = "faxian";
                    $url_info['sub_type'] = "detail";
                }
            }
        }else if(strstr($url, Config::$channel[7]['url'].'p/')){
            #众测
            preg_match_all('/(p)\\/([0-9]+)/',$id_str,$id_match);
            if(isset($id_match[2][0]) && !empty($id_match[2][0])){
                $url_info['link_val'] = $id_match[2][0];
                $url_info['link_type'] = "test";
                $url_info['sub_type'] = "detail";
            }
        }else if(strstr($url, Config::$channel[8]['url'].'/p')){
            #评测
            preg_match_all('/(pingce\/p)\\/([0-9]+)/',$id_str,$id_match);
            if(isset($id_match[2][0]) && !empty($id_match[2][0])){
                $url_info['link_val'] = $id_match[2][0];
                $url_info['link_type'] = "pingce";
                $url_info['sub_type'] = "detail";
            }
        }else if(strstr($url, Config::$channel[8]['url'])){
            if(preg_match('/(pingce)\\/([0-9]+)/', $id_str)){
                #众测单品评测
                preg_match_all('/(pingce)\\/([0-9]+)/',$id_str,$id_match);
                if(isset($id_match[2][0]) && !empty($id_match[2][0])){
                    $url_info['link_val'] = $id_match[2][0];
                    $url_info['link_type'] = "pingce";
                    $url_info['sub_type'] = "test";
                    if(Config::$from == 'iphone' && in_array(Config::$v, ['6.0','6.0.1'])){ #6.0兼容
                        $url_info['link_type'] = 'test/pingce';
                    }
                }
            }else{
                // 评测分类
                preg_match_all('/pingce\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/product_category_biz');
                    $category = $CI->product_category_biz->get_category_by_url_nicktitle($id_match[1][0]);
                    if(!empty($category)){
                        $url_info['link_val'] = $category['ID'];
                        $url_info['link_type'] = "fenlei";
                        $url_info['sub_type'] = "pingce";
                        $url_info['link_title'] = $category['title'];
                    }
                }
            }
        }else if(strstr($url, Config::$channel[11]['url'])){
            if(preg_match('/\/tag\//', $id_str)){
                // 标签
                preg_match_all('/tag\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/tag/tag_biz');
                    $tag = $CI->tag_biz->get_tag_by_name($id_match[1][0]);
                    if(!empty($tag)){
                        $url_info['link_val'] = $tag['id'];
                        $url_info['link_type'] = "tag";
                        $url_info['sub_type'] = "yuanchuang";
                        $url_info['link_title'] = $tag['name'];
                        $url_info['is_tese'] = in_array($tag['name'],['白菜党','神价格','手慢无'])?'1':'0';
                    }
                }
            }elseif(preg_match('/\/fenlei\//', $id_str)){
                // 分类
                preg_match_all('/fenlei\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/product_category_biz');
                    $category = $CI->product_category_biz->get_category_by_url_nicktitle($id_match[1][0]);
                    if(!empty($category)){
                        $url_info['link_val'] = $category['ID'];
                        $url_info['link_type'] = "fenlei";
                        $url_info['sub_type'] = "yuanchuang";
                        $url_info['link_title'] = $category['title'];
                    }
                }
            }elseif(preg_match('/\/xilie\//', $id_str)){
                // 系列
                preg_match_all('/xilie\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $url_info['link_val'] = $id_match[1][0];
                    $url_info['link_type'] = "xilie";
                    $url_info['sub_type'] = "list";
                    $url_info['link_title'] = '';
                }
            }else{
                #原创
                preg_match_all('/(p)\\/([0-9]+)/',$id_str,$id_match);
                if(isset($id_match[2][0]) && !empty($id_match[2][0])){
                    $url_info['link_val'] = $id_match[2][0];
                    $url_info['link_type'] = "yuanchuang";
                    $url_info['sub_type'] = "detail";
                }
            }
        }else if(stripos($url, "http://quan.smzdm.com")!==false){
            #优惠券
            if(Config::$from == 'iphone' && compare_client_version(Config::$v ,'5.8','>=') || Config::$from == 'android' && Config::$v >= '255'){
                if(strstr($url, Config::$constant['quan_url'].'content/')){
                    preg_match_all('/(content)\\/([0-9]+)/',$id_str,$id_match);
                    if(isset($id_match[2][0]) && !empty($id_match[2][0])){
                        $url_info['link_val'] = $id_match[2][0];
                        $url_info['link_type'] = "quan";
                        $url_info['sub_type'] = "detail";
                    }
                }
            }
        }else if(strstr($url, Config::$channel[12]['url'].'dianping/')){
            #百科点评
            preg_match_all('/(dianping)\\/([0-9A-Za-z]+)/',$id_str,$id_match);
            if(isset($id_match[2][0]) && !empty($id_match[2][0])){
                #百科
                // $CI = &get_instance();
                // $CI->load->model('wiki/wiki_api');
                // $wiki_model = new wiki_api();
                // $data = $wiki_model->get_dianping_detail(array('url'=>$url));
                // if($data['data']){
                $url_info['link_val'] = $id_match[2][0];
                $url_info['link_type'] = "wiki";
                $url_info['sub_type'] = "dianping_detail";
                // }
            }
        }else if(strstr($url, Config::$channel[12]['url'].'huati/')){
            #百科话题
            preg_match_all('/(huati)\\/([0-9]+)/',$id_str,$id_match);
            if(isset($id_match[2][0]) && !empty($id_match[2][0])){
                $url_info['link_val'] = $id_match[2][0];
                $url_info['link_type'] = "wiki";
                $url_info['sub_type'] = "huati_detail";
            }
        }else if(strstr($url, Config::$channel[12]['url'])||strstr($url, 'http://m.wiki.smzdm.com/')){
            if(preg_match('/(p)\\/([0-9A-Za-z]+)\/(zhongce|news|jiage|dianping|yuanchuang)/', $id_str)){
                preg_match_all('/(p)\\/([0-9A-Za-z]+)\/(zhongce|news|jiage|dianping|yuanchuang)/',$id_str,$id_match);
                if(isset($id_match[2][0])&&!empty($id_match[2][0])){
                    #百科
//                    $CI = &get_instance();
//                    $CI->load->model('wiki/wiki_api');
//                    $wiki_model = new wiki_api();
//                    $data = $wiki_model->get_detail(array('pro_url'=>$url,'fields'=>'id'));
//                    if($data['data']){
                    $url_info['link_val'] = $id_match[2][0];
                    $url_info['link_type'] = "wiki";
                    $url_info['sub_type'] = $id_match[3][0];
//                    }
                }
            }elseif(preg_match('/\/(.*)\/you/', $id_str)){
                preg_match_all('/\/(.*)\/you/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/product_category_biz');
                    $category = $CI->product_category_biz->get_category_by_url_nicktitle($id_match[1][0]);
                    if(!empty($category)){
                        $url_info['link_val'] = $category['ID'];
                        $url_info['link_type'] = "fenlei";
                        $url_info['sub_type'] = "wiki";
                        $url_info['link_title'] = $category['title'];
                    }
                }
            }elseif(preg_match('/\/youxuan?/', $id_str)){
                $query = explode('&',parse_url($url,PHP_URL_QUERY));
                $c = $s  = '';
                foreach($query as $val){
                    $v = explode('=',$val);
                    if(isset($v[0])&&$v[0] == 'c'){
                        $c = $v[1];
                    }
                    if(isset($v[0])&&$v[0] == 's'){
                        $s = urldecode($v[1]);
                    }
                }
                //preg_match_all('/http\:\/\/search\.smzdm\.com\/\?c\=(.*)&s\=(.*)/', $url, $param);
                if($c&&$s){
                    #6.1.1版本增加综合搜索
                    if(Config::$from == 'android' && Config::$v >= 300 || Config::$from == 'iphone' && compare_client_version(Config::$v, '6.1.1', '>=')){

                    }else{
                        $c = $c == 'home'?'youhui':$c;
                    }
                    $c = $c=='post'?'yuanchuang':$c;
                    $c = $c=='zhongce'?'pingce':$c;
                    $url_info['link_val'] = 0;
                    $url_info['link_type'] = "search";
                    $url_info['sub_type'] = 'wiki';
                    $url_info['link_title'] = $s;
                }
            }else{
                #百科
//                $CI = &get_instance();
//                $CI->load->model('wiki/wiki_api');
//                $wiki_model = new wiki_api();
//                $data = $wiki_model->get_detail(array('pro_url'=>$url,'fields'=>'id'));
//                if($data['data']){
                preg_match_all('/(p)\\/([0-9A-Za-z]+)/',$id_str,$id_match);
                if(isset($id_match[2][0])&&!empty($id_match[2][0])){
                    $url_info['link_val'] = $id_match[2][0];
                    $url_info['link_type'] = "wiki";
                    $url_info['sub_type'] = 'detail';
                }
            }
        }else if(strstr($url, 'http://search.smzdm.com/')){
            #搜索
            $query = explode('&',parse_url($url,PHP_URL_QUERY));
            $c = $s  = '';
            foreach($query as $val){
                $v = explode('=',$val);
                if(isset($v[0])&&$v[0] == 'c'){
                    $c = $v[1];
                }
                if(isset($v[0])&&$v[0] == 's'){
                    $s = urldecode($v[1]);
                }
            }
            //preg_match_all('/http\:\/\/search\.smzdm\.com\/\?c\=(.*)&s\=(.*)/', $url, $param);
            if($c&&$s){
                #6.1.1版本增加综合搜索
                if(Config::$from == 'android' && Config::$v >= 300 || Config::$from == 'iphone' && compare_client_version(Config::$v, '6.1.1', '>=')){

                }else{
                    $c = $c == 'home'?'youhui':$c;
                }
                $c = $c=='post'?'yuanchuang':$c;
                $c = $c=='zhongce'?'pingce':$c;
                $url_info['link_val'] = 0;
                $url_info['link_type'] = "search";
                $url_info['sub_type'] = $c;
                $url_info['link_title'] = $s;
            }
        }else if(stripos($url, 'http://duihuan.smzdm.com')!==false){
            #兑换详情
            if(preg_match('/(product|p)\\/([0-9]+)/', $id_str)){
                preg_match_all('/(product|p)\\/([0-9]+)/',$id_str,$id_match);
                if(isset($id_match[2][0])&&!empty($id_match[2][0])){
                    $CI = &get_instance();
                    $CI->load->model('duihuan/duihuan_api_v2');
                    $data = $CI->duihuan_api_v2->get_duihuan_info(['id'=>$id_match[2][0]]);
                    if(isset($data['data'][0]) && $data['data'][0]){
                        $url_info['link_val'] = $id_match[2][0];
                        if(in_array($data['data'][0]['type_id'],[1,2,4])){
                            $url_info['link_type'] = "quan";
                            $url_info['sub_type'] = "detail";
                        }elseif(in_array($data['data'][0]['type_id'],[5])){
                            $url_info['link_type'] = "duihuan";
                            $url_info['sub_type'] = "lipinka_detail";
                        }else{
                            $url_info['link_type'] = "duihuan";
                            $url_info['sub_type'] = "shiwu_detail";
                        }
                    }
                }
            }
        }else if(strstr($url, Config::$channel[17]['url'].'qingdan/')){
            #好物清单列表
            preg_match_all('/qingdan\/([0-9]+)/',$id_str,$id_match);
            if(isset($id_match[1][0]) && !empty($id_match[1][0])){
                $url_info['link_val'] = $id_match[1][0];
                $url_info['link_type'] = "haowu";
                $url_info['sub_type'] = "qingdan";
            }
        }else if(strstr($url, Config::$channel[17]['url'])){
            #好物详情
            preg_match_all('/p\/([0-9]+)/',$id_str,$id_match);
            if(isset($id_match[1][0]) && !empty($id_match[1][0])){
                $url_info['link_val'] = $id_match[1][0];
                $url_info['link_type'] = "haowu";
                $url_info['sub_type'] = "detail";
            }
        }else if(strstr($url, Config::$channel[16]['url'])){
            #闲置
            if(preg_match('/\/fenlei\/t[0-9]*/', $id_str)){
                // 标签
                preg_match_all('/\/fenlei\/t([0-9]*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/tag/tag_biz');
                    $tag = $CI->tag_biz->get_tag_info_by_id($id_match[1][0]);
                    if(!empty($tag)){
                        $url_info['link_val'] = $id_match[1][0];
                        $url_info['link_type'] = "tag";
                        $url_info['sub_type'] = "second";
                        $url_info['link_title'] = $tag['name'];
                        $url_info['is_tese'] = in_array($tag['name'],['白菜党','神价格','手慢无'])?'1':'0';
                    }
                }
            }elseif(preg_match('/\/fenlei\/\?search\=(.*)/', $url)){
                // 关键词搜索
                preg_match_all('/\/fenlei\/\?search\=(.*)/',$url,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $url_info['link_val'] = 0;
                    $url_info['link_type'] = "search";
                    $url_info['sub_type'] = "second";
                    $url_info['link_title'] = urldecode($id_match[1][0]);
                }
            }elseif(preg_match('/\/fenlei\/(.*)/', $id_str)){
                // 分类
                preg_match_all('/\/fenlei\/(.*)/',$id_str,$id_match);
                if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                    $CI = &get_instance();
                    $CI->load->biz('public/product_category_biz');
                    $category = $CI->product_category_biz->get_category_by_url_nicktitle($id_match[1][0]);
                    if(!empty($category)){
                        $url_info['link_val'] = $category['ID'];
                        $url_info['link_type'] = "fenlei";
                        $url_info['sub_type'] = "second";
                        $url_info['link_title'] = $category['title'];
                    }
                }
            }else{
                #闲置详情
                preg_match_all('/(p)\\/([0-9]+)/',$id_str,$id_match);
                if(isset($id_match[2][0]) && !empty($id_match[2][0])){
                    $url_info['link_val'] = $id_match[2][0];
                    $url_info['link_type'] = "second";
                    $url_info['sub_type'] = "detail";
                }
            }
        }else if(strstr($url, 'http://zhiyou.smzdm.com/member/')){
            // 他的个人主页
            preg_match_all('/\/member\/(.*)/',$id_str,$id_match);
            if(isset($id_match[1][0])&&!empty($id_match[1][0])){
                $url_info['link_val'] = $id_match[1][0];
                $url_info['link_type'] = "zhiyou";
                $url_info['sub_type'] = "index";
                $url_info['link_title'] = '';
            }
        }else if(strstr($url, 'http://pinpai.smzdm.com/') || strstr($url, 'http://m.pinpai.smzdm.com/')){
            #品牌库7.0
            preg_match_all('/([0-9]+)/',$id_str,$id_match);
            if(isset($id_match[1][0]) && !empty($id_match[1][0])){
                $CI = &get_instance();
                $CI->load->model('brand/brand_db');
                $brand = $CI->brand_db->get_brand_by_id($id_match[1][0]);
                $url_info['link_val'] = $id_match[1][0];
                $url_info['link_type'] = "pinpai";
                $url_info['sub_type'] = "detail";
                $url_info['link_title'] = isset($brand['associate_title'])?stripslashes($brand['associate_title']):'';
            }

            preg_match_all('/([0-9]+)\/(.*)/',$id_str,$id_match);
            if(isset($id_match[1][0]) && isset($id_match[2][0])){
                $url_info['sub_type'] = $id_match[2][0];
                switch ($id_match[2][0]){
                    case 'youhui':
                        $url_info['sub_type'] = 'jingxuan';
                        break;
                    case 'youhui/guonei':
                        $url_info['sub_type'] = 'youhui';
                        break;
                    case 'youhui/faxian':
                        $url_info['sub_type'] = 'faxian';
                        break;
                    case 'youhui/haitao':
                        $url_info['sub_type'] = 'haitao';
                        break;
                    case 'news':
                        $url_info['sub_type'] = 'news';
                        break;
                    case 'post':
                        $url_info['sub_type'] = 'yuanchuang';
                        break;
                    case 'test':
                        $url_info['sub_type'] = 'pingce';
                        break;
                    case 'wiki':
                        $url_info['sub_type'] = 'wiki';
                        break;
                    case '2':
                        $url_info['sub_type'] = 'second';
                        break;
                }
                $url_info['link_val'] = $id_match[1][0];
                $url_info['link_type'] = "pinpai";
            }
        }
    }
    return $url_info;
}

function operate_other_url($link, $client,$article_id=''){
    $url_info = array();
    $link = htmlspecialchars_decode($link);
    $url_parse = parse_url($link);
//    if (strpos($url, "item.taobao.com/item.htm") || strpos($url, "detail.tmall.com/item.htm") ) {
//        $product_id = 0;
//        $output = array();
//        parse_str($url_parse['query'], $output);
//        if (key_exists("id", $output)) {
//            $product_id = str_replace("_", "", $output['id']);
//            $taobao_auction_iid = get_taobao_auction_iid($product_id);
//            if(!empty($taobao_auction_iid)){
//                if (strpos($url, "item.taobao.com/item.htm")  ) {
//                    $b2c = "taobao";
//                }else{
//                    $b2c = "tmall";
//                }
//                $url_info['b2c'] = $b2c;
//                $url_info['product_id'] = $taobao_auction_iid;
//            }
//        }
//    }
    $CI = &get_instance();
    $CI->load->helper('tool');
    if(Config::$from == 'android' && Config::$v >= 300 || Config::$from == 'iphone' && compare_client_version(Config::$v, '6.1.1', '>=')){
        if(!empty($link)){
            $link_data = parse_url($link);
            if(isset($link_data['host']) && (strpos($link_data['host'],'.jd.com') !== false || strpos($link_data['host'],'.jd.hk') !== false)){
                $CI->load->library('link');
                $url_info['product_id'] = $CI->link->generate_url(['url'=>$link, 'channel'=>LinkCps::$channel['other']]);
                $url_info['product_id'] = $url_info['product_id']['href'];
                $url_info['b2c'] = 'jd';
            }
        }
    }
    if (strpos($link, "taobao.com") || strpos($link, "item.taobao.com/item.htm") || strpos($link, "yao.95095.com")  || strpos($link, "detail.yao.95095.com/item.htm") || strpos($link, "detail.ju.taobao.com/home.htm")) {
        #正则获取淘宝ID
        preg_match('/(https|http):\/\/(item|detail|items)\.(taobao|tmall|yao.95095|ju.taobao)\.(com|hk)(.*)\/(item|home)\.htm(.*)[&|?](id|item_id)=(\d+)/i', $link,$output);
        $product_id = 0;
        if ($output && isset($output[9]) && !empty($output[9])) {
            $product_id = $output[9];
            $url_info['product_id'] = get_taobao_auction_iid($product_id);
            $url_info['b2c'] = '';
            if(!empty($url_info['product_id'])){
                $url_info['b2c'] = 'taobao';
            }
        }
    }
//    if (in_array($article_id,[6040070]) && strpos($link, "items.alitrip.com/item.htm")) { #指定阿里旅行文章跳转手淘
    if (strpos($link, ".alitrip.com/item.htm")) {
        #正则获取淘宝ID
        preg_match('/(https|http):\/\/(item|detail|items)\.(taobao|tmall|yao.95095|ju.taobao|alitrip)\.(com|hk)(.*)\/(item|home)\.htm(.*)[&|?](id|item_id)=(\d+)/i', $link,$output);
        $product_id = 0;
        if ($output && isset($output[9]) && !empty($output[9])) {
            $product_id = $output[9];
            $url_info['product_id'] = get_taobao_auction_iid($product_id);
            $url_info['b2c'] = '';
            if(!empty($url_info['product_id'])){
                $url_info['b2c'] = 'taobao';
            }
        }
    }
    if (strpos($link, "tmall.com") || strpos($link, "detail.tmall.com/item.htm") || strpos($link, "tmall.hk") || strpos($link, "detail.tmall.hk/hk/item.htm")) {
        #正则获取淘宝ID
        preg_match('/(https|http):\/\/(item|detail|chaoshi.detail)\.(taobao|tmall|yao.95095|ju.taobao)\.(com|hk)(.*)\/(item|home)\.htm(.*)[&|?](id|item_id)=(\d+)/i', $link,$output);

        $product_id = 0;
        if ($output && isset($output[9]) && !empty($output[9])) {
            $product_id = $output[9];
            $url_info['product_id'] = get_taobao_auction_iid($product_id);
            $url_info['b2c'] = '';
            if(!empty($url_info['product_id'])){
                $url_info['b2c'] = 'tmall';
            }
        }
    }
    if (isset($url_parse['host']) && strpos($url_parse['host'],'.jd.com') !== false && isset($url_parse['path']) && strpos($link, "type=wx") && Config::$weixin) {
        $url_info['b2c'] = "jdwx";
        $CI = &get_instance();
        $CI->load->library('link');
        $cps_url = $CI->link->generate_url(['url'=>$link, 'channel'=>LinkCps::$channel['weixin']]);
        // $url_info['product_id'] = substr_replace($cps_url,"WX",28,2);
        $url_info['product_id'] = $cps_url['href'];
    }
    return $url_info;
}

function api_url_check_listlink_6_0($url){
    $result = array();
    $rules = Config::$app_link_rules;
    $url = substr($url, -1) == '/'?substr($url, 0,-1):$url;
    foreach($rules as $key=>$val){
        if(preg_match($val['rule'], $url)){
            if (strrpos($val['link'],$url) !== false) {
                $result = $rules[$key];
                unset($result['rule']);
                break;
            }
        }
//            if (strrpos($val['link'],$url) !== false) {
//                $result = $rules[$key];
//                break;
//            }
    }
    return $result;
}

/**
 * 是否请求reffer来自smzdm
 * @return boolean [description]
 */
function is_reffer_from_smzdm() {
    $CI = & get_instance(); 
    $redirect_to = $CI->input->server('HTTP_REFERER');
    $url_info = parse_url($redirect_to);
    if (!isset($url_info['host']) || strpos($url_info['host'], Config::$url['root']) === false) {
        return false;
    }
    return true; 

}

/***
 * 生成新版静态文件链接地址
 */
if (!function_exists('remote_url')) {
    function remote_url($uri = '', $host = '') {
        $CI =& get_instance();
        $url = $CI->config->remote_url($uri, $host);
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && "https" == $_SERVER['HTTP_X_FORWARDED_PROTO']) {#https访问http替换为https
            $url = str_replace("http://", "https://", $url);
        }
        return $url;
    }
}
// ------------------------------------------------------------------------
