<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('convert_smilies'))
{
    function convert_smilies($text) {
        $wp_smiliessearch = get_smiliessearch();
        $output = '';
        // HTML loop taken from texturize function, could possible be consolidated
        $textarr = preg_split("/(<.*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
        $stop = count($textarr);// loop stuff
        for ($i = 0; $i < $stop; $i++) {
            $content = $textarr[$i];
            if ((strlen($content) > 0) && ('<' != $content[0])) { // If it's not a tag
                $content = preg_replace_callback($wp_smiliessearch,'translate_smiley', $content);
            }
            $output .= $content;
        }
        return $output;
    }
}

if ( ! function_exists('translate_smiley'))
{
    /** 
    * 转换拼接表情图片
    * @param type $smiley
    * @return string
    */
    function translate_smiley($smiley) {
        $CI = &get_instance();
        $CI->load->config('smilies');
        $wpsmiliestrans = SmiliesConfig::$smiliestrans;
        $smilies_class = SmiliesConfig::$smilies_class;
        $smilies_url = SmiliesConfig::$smilies_url;
        if (count($smiley) == 0) {
            return '';
        }

        $smiley = trim(reset($smiley));
        $img = $wpsmiliestrans[$smiley];
        $smiley_masked = $smiley;
        return " <img src='".$smilies_url.$img."' alt='$smiley_masked' class='$smilies_class' /> ";
    }
}

if ( ! function_exists('get_smiliessearch'))
{
    /**
    * 获取表情匹配正则
    * @return string
    */
    function get_smiliessearch(){
        $CI = &get_instance();
        $CI->load->config('smilies');
        $wpsmiliestrans = SmiliesConfig::$smiliestrans;
        $wp_smiliessearch = '/';
        $subchar = '';
        foreach ( (array) $wpsmiliestrans as $smiley => $img ) {
            $firstchar = substr($smiley, 0, 1);
            $rest = substr($smiley, 1);

            // new subpattern?
            if ($firstchar != $subchar) {
                if ($subchar != '') {
                    $wp_smiliessearch .= ')|(?:\s|^)';
                }
                $subchar = $firstchar;
                $wp_smiliessearch .= preg_quote($firstchar, '/') . '(?:';
            } else {
                $wp_smiliessearch .= '|';
            }
            $wp_smiliessearch .= preg_quote($rest, '/');
        }
        $wp_smiliessearch .= ')/m';
        return $wp_smiliessearch;
    }
}

/**
 * 过滤掉表情
 * 
 * @param   string      $text           内容
 * @return  string
 * @author  Dacheng Chen
 * @time    2014-5-4
 */
if(!function_exists('filter_smilies')){
    function filter_smilies($text){
        $CI = &get_instance();
        $CI->load->config('smilies');
        $wpsmiliestrans = SmiliesConfig::$smiliestrans;
        foreach($wpsmiliestrans as $facekey => $faceimg){
            $text = str_replace($facekey, '', $text);
        }
        return $text;
    }
}
