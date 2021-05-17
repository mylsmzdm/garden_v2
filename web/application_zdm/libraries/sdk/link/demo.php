<?php
/**
 * Created by PhpStorm.
 * User: wind
 * Date: 15/11/18
 * Time: 上午10:50
 */


include_once("link_generate.php");
$link = new LinkGenerate();
echo smzdm_cps_get_top_domain("http://www.ly.com ");
$parm_tmp = array(
    "url" => "http://item.m.jd.com/product/10044437476.html",
    "platform" => "ca",//平台
    "source" => "aa",//来源
    "channel" => "wx",//频道
    "category" => "57",//一级分类ID
    "category_level2" => "133",//二级分类ID
    "article_id" => "0",//文章ID
    "author_id" => "0",//作者ID
    "brand_id" => "0"//品牌ID
);
$link_info = $link->generate_url($parm_tmp);


$parm_tmp['content'] = '<p>
    <a href="http://dujia.lvmama.com/package/423463" target="_blank" rel="nofollow">驴妈妈旅游网</a>
    推出珠海周边游，外伶仃岛，全程2天1夜，10月26日出发，价格为376元。包含往返船票，岛上住宿1晚。
</p>';
$gtm = array();
$obj['object'] = "azaadafsdfasdfasdfasdfasdf";
$parm_tmp['gtm'] = $obj;
$link_info = $link->replace_content_link($parm_tmp);
print_r($link_info);

echo "OK";

