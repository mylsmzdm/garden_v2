<?php
/**
 * Created by PhpStorm.
 * User: dengpeng.zdp
 * Date: 2015/9/28
 * Time: 19:11
 */

class RSAUtil{

    /**
     * 加签
     * @param $data 要加签的数据
     * @param $privateKeyContent 私钥正文
     * @return string 签名
     */
    public static function sign($data, $privateKeyContent) {
        $priKey = file_get_contents($privateKeyContent);#var_dump($priKey);exit;
        #$priKey = $privateKeyContent;
        $res = openssl_get_privatekey($priKey);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 验签
     * @param $data 用来加签的数据
     * @param $sign 加签后的结果
     * @param $rsaPublicKeyContent 公钥正文
     * @return bool 验签是否成功
     */
    public static function verify($data, $sign, $rsaPublicKeyContent) {
        //读取公钥文件
        $pubKey = file_get_contents($rsaPublicKeyContent);
        #$pubKey = $rsaPublicKeyContent;

        //转换为openssl格式密钥
        $res = openssl_get_publickey($pubKey);

        //调用openssl内置方法验签，返回bool值
        $result = (bool)openssl_verify($data, base64_decode($sign), $res);

        //释放资源
        openssl_free_key($res);

        return $result;
    }


    /**
     * rsa加密
     * @param $data 要加密的数据
     * @param $pubKeyContent 公钥正文
     * @return string 加密后的密文
     */
    public static function rsaEncrypt($data, $pubKeyContent){
        //读取公钥文件
        $pubKey = file_get_contents($pubKeyContent);
        #$pubKey = $pubKeyContent;
        //转换为openssl格式密钥
        $res = openssl_get_publickey($pubKey);

        $maxlength=117;
        $output='';
        while($data){
            $input= substr($data,0,$maxlength);
            $data=substr($data,$maxlength);
            openssl_public_encrypt($input,$encrypted,$pubKey);
            $output.= $encrypted;
        }
        $encryptedData =  base64_encode($output);
        return $encryptedData;
    }

    /**
     * 解密
     * @param $data 要解密的数据
     * @param $privateKeyContent 私钥正文
     * @return string 解密后的明文
     */
    public static function rsaDecrypt($data, $privateKeyContent){
        //读取私钥文件
        $priKey = file_get_contents($privateKeyContent);
        #$priKey = $privateKeyContent;
        //转换为openssl格式密钥
        $res = openssl_get_privatekey($priKey);
        $data = base64_decode($data);
        $maxlength=128;
        $output='';
        while($data){
            $input = substr($data,0,$maxlength);
            $data = substr($data,$maxlength);
            openssl_private_decrypt($input,$out,$res);
            $output.=$out;
        }
        return $output;
    }
}