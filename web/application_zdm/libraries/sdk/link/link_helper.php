<?php
/**
 * Created by PhpStorm.
 * User: wind
 * Date: 16/3/14
 * Time: 下午12:00
 */
/**
 * 根据URL地址获取根域名
 * @param type $url url 地址
 * @return string       域名
 */

function smzdm_cps_get_top_domain($url)
{
    $arr_url = parse_url($url);
    if ($arr_url && array_key_exists("host", $arr_url)) {
        $host = parse_url($url)['host'];
    } else {
        return false;
    }

    $parts = explode('.', $host);
    $count = count($parts);
    if ($count < 2)
        return false;
    $topDomain = $parts [$count - 2] . '.' . $parts [$count - 1];
    if (isset(SmzdmLinkCpsConfig::$valid_tlds[$topDomain]) && $count >= 3) {
        $topDomain = $parts [$count - 3] . '.' . $topDomain;
    }
    return $topDomain;
}

function smzdm_cps_clean_cps_info($url)
{
    if (strpos($url, "p.yiqifa.com") !== false) {
        $url = smzdm_cps_get_url_parm($url, "t");
    } else if (strpos($url, "api.viglink.com")) {
        $url = smzdm_cps_get_url_parm($url, "out");
    } else if (strpos($url, "count.chanet.com.cn") !== false) {
        $url = smzdm_cps_get_url_parm($url, "url");
    } else if (strpos($url, "weiyi.com")) {
        $url = smzdm_cps_get_url_parm($url, "t");
    } else if (strpos($url, "c.duomai.com")) {
        $url = smzdm_cps_get_url_parm($url, "t");
    } else if (strpos($url, ".tkqlhce.com")) {
        $url = smzdm_cps_get_url_parm($url, "url");
    } else if (strpos($url, "click.linksynergy.com")) {
        $url = smzdm_cps_get_url_parm($url, "RD_PARM1");
        $url = smzdm_cps_get_url_parm($url, "murl");
    } else if (strpos($url, "www.dpbolvw.com")) {
        $url = smzdm_cps_get_url_parm($url, "url");
    } else if (strpos($url, ".kqzyfj.com")) {
        $url = smzdm_cps_get_url_parm($url, "url");
    } else if (strpos($url, ".awin1.com")) {
        $url = urldecode(preg_replace("/(.*)awin1.com(.*)[&|&amp;]p=(.*)/", "$3", $url));
    } else if (strpos($url, ".anrdoezrs.net")) {
        $url = smzdm_cps_get_url_parm($url, "url");
    } else if (strpos($url, ".jdoqocy.com")) {
        $url = smzdm_cps_get_url_parm($url, "url");
    } else if (strpos($url, ".pntrac.com") || strpos($url, ".pjtra.com") || strpos($url, ".pntrs.com")
        || strpos($url, ".gopjn.com") || strpos($url, ".pjatr.com") || strpos($url, ".pntra.com")
    ) {
        $url = smzdm_cps_get_url_parm($url, "url");
    } else if (strpos($url, "www.shareasale.com")) {
        $url = smzdm_cps_get_url_parm($url, "urllink");
    } else if (strpos($url, ".oadz.com")) {
        $url = str_ireplace("http://a1722.oadz.com/link/C/1722/7682/TtHj-cVOwdro-uLRS.AwUc6Ct-w_/p007/0/", "", $url);
    } else if (strpos($url, "api.banggo.com")) {
        $url = smzdm_cps_get_url_parm($url, "url");
    } else if (strpos($url, ".avantlink.com")) {
        $url = smzdm_cps_get_url_parm($url, "url");
    } else if (strpos($url, ".yintai.com")) { // 银泰网
        $url = smzdm_cps_get_url_parm($url, "url");
        if (strpos($url, ".17glink.com")) {
            $url = urldecode(str_ireplace("http://t.17glink.com/c.htm?pv=1&sp=0,189,100021055,22934,0,90,728&target=http://f.17glink.com/Ealliance?m=yintai01,E100021055,E22934,0,1,u_id=,tu=", "", $url));
        }
    } else if (strpos($url, "associates.haituncun.com")) {
        $url = smzdm_cps_get_url_parm($url, "url");
    } else if (strpos($url, ".walmart.com")) {
        $url = smzdm_cps_get_url_parm($url, "RD_PARM1");
    }else if (strpos($url, "aos.prf.hn")) {
        $url = preg_replace("/(.*)aos.prf.hn(.*)destination:(.*)/", "$3", $url);
    }else if (strpos($url, "ad.zanox.com")) {
        $url = smzdm_cps_get_url_parm($url, "ulp");
    }
    return $url;
}

/**
 * 获取URL中某个参数值，不存在则返回原始地址
 * @param String $base_url
 * @param String $parm
 */
function smzdm_cps_get_url_parm($base_url, $parm, $need_decode = true)
{
    $tmp_url = parse_url($base_url);
    if (array_key_exists('query', $tmp_url)) {
        parse_str($tmp_url ['query'], $array_parm);
        if (isset($array_parm[$parm])) {
            if ($need_decode) {
                return urldecode($array_parm[$parm]);
            } else {
                return $array_parm[$parm];
            }
        }
    }
    return $base_url;
}
