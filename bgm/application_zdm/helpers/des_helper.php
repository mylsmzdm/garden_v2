<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('des_encrypt')) {

    function des_encrypt($string, $key = '') {
        if (empty($key)) {
            $key = 'geZm53XAspb02exN';
        }
        $CI = & get_instance();
        $CI->load->library('des');
        return $CI->des->encrypt($key, $string);
    }

}

if (!function_exists('des_decrypt')) {

    function des_decrypt($string, $key = '') {
        if (empty($key)) {
            $key = 'geZm53XAspb02exN';
        }
        $CI = & get_instance();
        $CI->load->library('des');
        return $CI->des->decrypt($key, $string);
    }

}