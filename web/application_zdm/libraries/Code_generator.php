<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Code_Generator {

    /**
     * 生成指定长度的code, 移植于wp-includes/pluggable.php的genRandomString()
     * @param type 长度
     * @return string
     */
    function gen_random_string($len) {
        $chars = array(
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9");
        $charsLen = count($chars) - 1;

        shuffle($chars);    // 将数组打乱

        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }

        return $output;
    }

    function get_uniqid_id() {
        return md5(uniqid('', true)) . substr(md5(rand(10000, 99999)), 0, 6);
    }

}
