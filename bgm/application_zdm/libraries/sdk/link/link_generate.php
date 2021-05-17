<?php

/**
 * User: wind
 * Date: 15/9/4
 * Time: 下午12:15
 */
//date_default_timezone_set('PRC');

class LinkGenerate
{
    private $redis_data;
    private $parameter;
    private $has_taobao_url = 0;

    //跳转域名列表
    private $goto_domain_list = array();

    function __construct()
    {
        require("link_redis.php");
        require("link_helper.php");
        $this->redis_data = new LinkRedis("dr");
        $link_domain_list = json_decode($this->redis_data->get("DOMAIN_RULE_LIST_FOR_SDK"), true);
        if (isset($link_domain_list['data'])) {
            $this->goto_domain_list = $link_domain_list['data'];
        }
    }


    /**
     * 生成链接
     * @param $parm
     * @return array
     */
    public function generate_url($parm)
    {
        $parm_tmp = array(
            'url' => "",//URL
            "platform" => "ca",//平台
            "source" => "aa",//来源
            "channel" => "ot",//频道
            "category" => "0",//一级分类ID
            "category_level2" => "0",//二级分类ID
            "article_id" => "0",//文章ID
            "author_id" => "0",//作者ID
            "brand_id" => "0",//品牌ID
            "tags" => ""//标签
        );
        $parm = array_merge($parm_tmp, $parm);
        //$base_url = trim($parm['url']);
        $base_url = trim(htmlspecialchars_decode($parm['url']));
        $ret_info = array(
            'href' => $base_url,
            'clear_url' => $base_url,
            'domain' => '',
            'need_rel' => false,
            'isconvert' => 0
        );
        if (empty($base_url)) {
            return $ret_info;
        }
        if(stripos($base_url, ".yixun.com")||stripos($base_url, ".51buy.com")){//易迅地址换为京东首页
            $base_url = "http://www.jd.com";
        }
        $link_id = $this->generate_linkid($base_url);
        $parm['link_id'] = $link_id;
        $redis_key = 'smzdm_link_base_' . $link_id;
        $link_data_json = $this->redis_data->get($redis_key);

        if ($link_data_json) {
            $link_data = json_decode($link_data_json, TRUE);
            $ret_info['isconvert'] = isset($link_data['isconvert']) ? $link_data['isconvert'] : 0;
            if ($ret_info['isconvert'] && $parm['platform'] == "ca") {
                $parm['channel'] = "ad";//ad block
            }
            $ret_info['href'] = $this->concat_url($parm);
            $ret_info['domain'] = isset($link_data['domain']) ? $link_data['domain'] : "";
            $ret_info['clear_url'] = isset($link_data['clear_url']) ? $this->replace_taoxi_url($link_data['clear_url']) : "";
            $ret_info['need_rel'] = isset($link_data['need_rel']) ? $link_data['need_rel'] : 0;
        } else {
            $clear_url = smzdm_cps_clean_cps_info($base_url);
            $base_domain = smzdm_cps_get_top_domain($clear_url);
            $ret_info['clear_url'] = $clear_url;
            if ($base_domain) {
                $ret_info['domain'] = $base_domain;
            } else {
                return $ret_info;
            }

            if (in_array($base_domain, SmzdmLinkCpsConfig::$link_ignore_domain_list)) {
                return $ret_info;
            }
            $need_cps = FALSE;
            if (isset($this->goto_domain_list[$base_domain])) {
                $rule_list = $this->goto_domain_list[$base_domain];
                if (count($rule_list) > 0) {
                    if (key_exists("*", $rule_list)) {
                        $need_cps = TRUE;
                    } else {
                        foreach ($rule_list as $k => $v) {
                            if (strpos($clear_url, $k)) {
                                $need_cps = TRUE;
                                break;
                            }
                        }
                    }
                } else {
                    $need_cps = TRUE;
                }
            }
            if ($base_domain == "amazon.cn" && strpos($parm['tags'], "Z秒杀") !== FALSE) {
                $parm['channel'] = "ms";
            }
            $ret_info = $this->taobao_url($ret_info, $parm['platform']);
            if ($need_cps) {
                if ($ret_info['isconvert'] && $parm['platform'] == "ca") {
                    $parm['channel'] = "ad";//ad block
                }
                $ret_info['href'] = $this->concat_url($parm);
                $ret_info['need_rel'] = true;
                $data['link_id'] = $link_id;
                $data['base_url'] = $base_url;
                $data['domain'] = $base_domain;
                $data['create_date'] = date('Y-m-d H:i:s', time());
                $data['need_insert'] = 1;
                $data['clear_url'] = $ret_info['clear_url'];
                $data['need_rel'] = $ret_info['need_rel'];
                $data['isconvert'] = $ret_info['isconvert'];
                $this->redis_data->change_connect("dw");
                $this->redis_data->save($redis_key, json_encode($data), 43200);//缓存0.5天
            }
        }

        return $ret_info;
    }

    /**
     * 替换内容里的链接
     * @param $parm
     * @return array
     */
    public function replace_content_link($parm)
    {
        $parm_tmp = array(
            "content" => "",
            "platform" => "ca",//平台
            "source" => "aa",//来源
            "channel" => "ot",//频道
            "category" => "0",//一级分类ID
            "category_level2" => "0",//二级分类ID
            "article_id" => "0",//文章ID
            "author_id" => "0",//作者ID
            "brand_id" => "0",//品牌ID
            "tags" => "",//标签
            "gtm" => array()
        );
        $parm = array_merge($parm_tmp, $parm);

        $this->parameter = $parm;
        // 替换 文章内容中 bgm的地址 为了防止内容里有BGM的链接
        $content = str_replace('bgm.smzdm.com', '', $parm["content"]);

        $ret_info = array(
            "content" => $content,
            'has_taobao_url' => 0
        );
        $content = preg_replace('/<!--\[.*\]-->/', '', $content);
        $matches = '/(<a.+href=[\\\"\']+?)(.+)([\\\"\']+?.*)(>)/iUs';
        $content = preg_replace_callback($matches, array($this, 'replace_content'), $content);

        $ret_info['content'] = $content;
        $ret_info['has_taobao_url'] = $this->has_taobao_url;

        return $ret_info;
    }


    /**
     * 过滤内容中的链接
     */
    private function replace_content($matches)
    {
        $this_url = $matches [2];
        $this->parameter['url'] = $this_url;
        $url_info = $this->generate_url($this->parameter);
        $cps_url = $url_info['href'];
        $rel = $is_convert = '';
        if ($url_info['need_rel']) {
            $rel = ' rel="nofollow" ';
        }
        $clear_url = "";
        $change_url = "";
        if ($url_info['isconvert']) {
            $is_convert = ' isconvert="1" ';
            $this->has_taobao_url = TRUE;
            $cps_url = $url_info['clear_url'];
            $clear_url = " data-url={$url_info['href']}";
            $change_url = "if(typeof change_direct_url != 'undefined' && change_direct_url instanceof Function){change_direct_url(this)}";
        }
        $gtm_str = "";

        if($url_info['domain']!="smzdm.com" && !stripos("onclick", $matches[3])){
            $gtm = $this->parameter['gtm'];
            $add_to_cart = (isset($gtm['object']) && $gtm['object']) ? $gtm['object'] :"";

            $add_to_cart= $add_to_cart ? "gtmAddToCart({$add_to_cart})" : "";
            $add_to_cart = $add_to_cart.";".$change_url;
            $gtm_str = $add_to_cart==";"?"":" onclick=\"{$add_to_cart}\" ";
        }
        return $matches[1] . $cps_url . $matches[3] . $is_convert . $clear_url . $rel . $gtm_str . $matches[4];
    }


    //根据URL生成 16位ID
    private function generate_linkid($base_url)
    {
        $md5_id = md5($base_url);
        $md5_16 = substr($md5_id, 8, 16);
        $link_id = strtolower($md5_16);
        return $link_id;
    }

    //拼接URL
    private function concat_url($parm)
    {
        $link_id = empty($parm['link_id']) ? "" : $parm['link_id'];
        $platform = empty($parm['platform']) ? "ot" : $parm['platform'];
        $source = empty($parm['source']) ? "ot" : $parm['source'];
        $channel = empty($parm['channel']) ? "ot" : $parm['channel'];
        $category = empty($parm['category']) ? "0" : $parm['category'];
        $category_level2 = empty($parm['category_level2']) ? "0" : $parm['category_level2'];
        $article_id = empty($parm['article_id']) ? "0" : $parm['article_id'];
        $author_id = empty($parm['author_id']) ? "0" : $parm['author_id'];
        $brand_id = empty($parm['brand_id']) ? "0" : $parm['brand_id'];
        
        $url = SmzdmLinkCpsConfig::SMZDM_GO_DOMAIN . $link_id .
            "/{$platform}_{$source}_{$channel}_{$category}_{$article_id}_{$author_id}_{$brand_id}_{$category_level2}";
        
        return $url;
    }
    
    /**
     * 
     * @param type $ids
     * @return type
     */
    private function genHashId($ids) {
        require_once(__DIR__ . '/vendor/autoload.php');
        $hashids = new Hashids\Hashids('sMzdm', 8);
        $id = $hashids->encode($ids);
        return $id;
    }

    /**
     * 淘宝链接处理
     * @param $url_info
     * @param $platform
     * @return mixed
     */
    private function taobao_url($url_info, $platform)
    {

        $domain = $url_info['domain'];
        $this_url = $url_info['clear_url'];

        if (in_array($domain, array("taobao.com", "tmall.com", "tmall.hk", "alitrip.com", "95095.com"))) {

            if (strpos($this_url, "type=nocps") || strpos($this_url, "s.taobao.com") || strpos($this_url, "s8.taobao.com")
                || strpos($this_url, "search.taobao.com") || strpos($this_url, "s.click.taobao.com")
            ) {
                $url_info['clear_url'] = str_replace(array("&type=nocps", "?type=nocps"), "", $this_url);
            } else {
                $this_url = $this->replace_taoxi_url($this_url);
                //$url_info['href'] = $this_url;
                $url_info['clear_url'] = $this_url;
                $url_info['isconvert'] = 1;
                if($domain=="alitrip.com" && !strpos($this_url, "items.alitrip.com") && !strpos($this_url, "detail.alitrip.com") ){
                    $url_info['isconvert'] = 0;
                }
            }

        }

        return $url_info;
    }

    private function replace_taoxi_url($url)
    {
        //$url = str_replace("detail.tmall.hk/hk/item.htm", "item.taobao.com/item.htm", $url);
        //$url = str_replace("detail.tmall.hk/item.htm", "item.taobao.com/item.htm", $url);
        $url = str_replace("items.alitrip.com/item.htm", "item.taobao.com/item.htm", $url);
        $url = str_replace("detail.alitrip.com/item.htm", "item.taobao.com/item.htm", $url);
        $url = str_replace("detail.yao.95095.com/item.htm", "detail.tmall.com/item.htm", $url);
        if (strpos($url, "taiwan.tmall.com/item/")) {
            $url = preg_replace('/http:\/\/taiwan\.tmall\.com\/item\/(\d+)\.htm(.*)/i', 'http://detail.tmall.com/item.htm?id=$1', $url);
        } else if (strpos($url, "tw.taobao.com/item/")) {
            $url = preg_replace('/http:\/\/tw\.taobao\.com\/item\/(\d+)\.htm(.*)/i', 'http://item.taobao.com/item.htm?id=$1', $url);
        }
        return $url;
    }

}