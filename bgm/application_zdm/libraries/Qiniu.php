<?php
/**
 * 七牛封装
 */
require_once __DIR__.'/qiniusdk/autoload.php';
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
class Qiniu {
    public function __construct($args = array()) {
        $CI = &get_instance();
        $CI->load->config('pic');
        $this->accessKey = QiniuConfig::$access_key;
        $this->secretKey = QiniuConfig::$secret_key;

        $this->auth = new Auth($this->accessKey, $this->secretKey);
        $this->bucket_normal = QiniuConfig::$mark_set['normal']['bucket']; 
        $this->bucket_mark = QiniuConfig::$mark_set['mark']['bucket']; 
        $this->bucket = $this->bucket_normal;
        $this->domain = QiniuConfig::$mark_set['normal']['domain']; 
        
        /*$this->token = $this->auth->uploadToken($this->bucket_normal, null, 3600);

        $this->uploadMgr = New UploadManager();*/

        $this->eventText = array(
            0 => '上传成功',
            1 => 'Oops!上传图片遇到点问题，再试试看？',
            2 => '您需要上传文件类型为jpg,jpeg,png的图片',
            3 => '文件上传不完整或大小超出限制范围',
            4 => '无法移动文件到指定目录',
            5 => 'Oops!上传图片遇到点问题，再试试看？'
        );
        $this->allowExt = 'jpeg,jpg,png,gif';
        $this->maxSize = '4096000';
        $this->savePath = '';


    }
    function test_stat() {
        /*zdm-youhuis-7n => qny.smzdm.com
        zdm-youhuis-mark-7n => qnym.smzdm.com*/

        $accessKey = $this->accessKey;
        $secretKey = $this->secretKey;
        $auth = new Auth($accessKey, $secretKey);

        $bucket = 'zdm-youhuis-7n';


        $token = $auth->uploadToken($bucket, null, 3600);
        $uploadMgr = New UploadManager();

        $key = "/Users/litong/Downloads/2889649_172655002116_2.jpg";

        list($ret, $err) = $uploadMgr->putFile($token, null, $key);


        if ($err !== null) {
            var_dump($err);
        } else {
            var_dump($ret);
        }
        exit();

    }

    /**
     * 远程上传图片
     * @param type $url 图片url
     * @param type $upyun 图床配置 youhui
     * @param type $with_mark 是否带水印
     * @return type $result Array ( 
     *                  [error] => 0 
     *                  [msg] => 
     *                  [rerut] => Array ( 
     *                      [x-upyun-width] => 150 
     *                      [x-upyun-height] => 150 
     *                      [x-upyun-frames] => 1 
     *                      [x-upyun-file-type] => PNG 
     *                  ) 
     *                  [url] => http://p.zdmimg.com/201410/16/543f5b03536d8.png 
     *                  [width] => 150 
     *                  [height] => 150 ) 
     */
    function upload_pic($url, $type = 'youhui', $with_mark = true) {
        $result = array('error' => 1, 'msg' => '上传失败！');
        if($type == 'youhui') {
            $this->bucket = $with_mark ? $this->bucket_mark : $this->bucket_normal;
            $this->domain = $with_mark ? QiniuConfig::$mark_set['mark']['domain'] : QiniuConfig::$mark_set['normal']['domain'];
        }
        $conf = array('allowExt'=> $this->allowExt, 'savePath' => $this->savePath);
        $local_img = $this->GrabImage($url, $conf);
        if(empty($local_img)) {
            return $result;
        }

        //设置图床路径及文件名
        $prefix = date('Ym/d/');
        $prefix && substr($prefix, -1) !== '/' && $prefix = $prefix . '_';
        $file = $conf['savePath'] . $prefix . uniqid() . '.' . $this->fileExt($local_img, $conf);
        /*$dir_file = strtr(dirname(__FILE__), "\\", "/") . $file;
        is_dir(dirname($dir_file)) || mkdir(dirname($dir_file), 0777, true);*/
        $this->uploadMgr = New UploadManager();
        $this->token = $this->auth->uploadToken($this->bucket);
        list($ret, $err) = $this->uploadMgr->putFile($this->token, $file, $local_img);

        if ($err !== null) {
            return $result;
        } else {

            list($width, $height) = getimagesize($local_img);

            $result['error'] = 0;
            $result['msg'] = '上传成功';
            // $result['rerut'] = $rerut;
            $result['url'] = "{$this->domain}/$file";
            $result['width'] = $width ? $width : 0;
            $result['height'] = $height ? $height : 0;
        }
        //删除本地暂存图片
        unlink($local_img);
        return $result;
    }

    /**
     * 捕获图片
     * @param type $url
     * @param string $filename
     * @return string|boolean
     */
    function GrabImage($url, $conf, $filename = "") {
        if ($url == "")
            return false;
        $ext = $this->fileExt($url, $conf);

        $res = strpos($this->allowExt, $ext);
        if ($res === false) {
            return '';
        }
        //本地需要设置一个临时文件夹，否则需要写自动生成文件夹操作，目前在同级目录下设置了/temp/文件夹
        if ($filename == "") {
            $filename = $_SERVER['DOCUMENT_ROOT'] . '/resources/public/upload_pic_upyun/temp/' . uniqid() . rand(1, 10000) . ".$ext";
        }
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
        $fp2 = fopen($filename, "a");
        fwrite($fp2, $img);
        fclose($fp2);
        return $filename;
    }

    /**
     * 验证文件是否合法
     */
    function fileExt($file, $conf) {
        $ext = '';
        if ($file) {
            // 1. 直接取上传文件路径直观后缀进行判断
            $idx = strrpos($file, '.');
            if(!empty($idx)){
                $ext = strtolower(substr($file, $idx+1));
                $res = strpos($conf['allowExt'], $ext);
                if ($res >= 0) {
                    return $ext;
                }
            }

            // 2. 从上传文件header中取实际文件格式判断
            $img_info = getimagesize($file);
            if (!isset($img_info['mime']) || empty($img_info['mime'])) {
                return '';
            }

            $ext = str_replace('image/', '', $img_info['mime']);
            $ext = strtolower($ext);
            $res = strpos($conf['allowExt'], $ext);
            if ($res !== false) {
                return $ext;
            }
            return '';
        }
        return '';
    }

    

    /**
     * 本地图片上传
     * 
     */
    function upload_pic_local($fileinfo, $type = 'youhui', $with_mark = true) {
        $result = array('error' => 1, 'msg' => '上传失败！');
        
        $conf = array();
        if ($type == 'youhui') {
            $this->bucket = $with_mark ? $this->bucket_mark : $this->bucket_normal;
            $this->domain = $with_mark ? QiniuConfig::$mark_set['mark']['domain'] : QiniuConfig::$mark_set['normal']['domain'];
        }else{
            // $conf = $with_mark ? PicConfig::$article['mark'] : PicConfig::$article['normal'];
        }
       
        $width = 0;
        $height = 0;

        $dat = $this->uploadExt($fileinfo);
        $img = '';
        if ($dat['code'] === 0) {
            $img = $this->domain . '/'. $dat['url'];
            list($width, $height) = getimagesize($fileinfo['tmp_name']);
        }
        $result = array(
            'error' => $dat['code'],
            'msg' => $this->eventText[$dat['code']],
            'url' => $img,
            'width' => $width,
            'height' => $height,
        );
        return $result;
    }

    function uploadExt($fileinfo = '') {
        $data = $this->single($fileinfo, date('Ym/d/'));
        return $data;
    }

    /**
     * 单文件上传
     * @param string $fileinfo 上传文件信息
     * @param string $prefix 文件保存名称前缀
     * @param string $test_flag  测试图片上传返回值 默认是 false 测试 true    xml 2014-08-08
     * @return array(
     *  'code' 上传结果代码
     *  'text' 上传结果描述
     *  'name' 原始文件名称
     *  'type' 原始文件类型
     *  'size' 实际文件大小
     *  'path' 文件保存路径
     * )
     */
    public function single($fileinfo, $prefix = '', $test_flag = false) {
        $data = array('code' => 1, 'text' => '');
        if (isset($fileinfo) && is_array($fileinfo)) {
            $copy = $fileinfo;
            $type = $this->fileType($copy['name']);
            if (!$type) {
                $data['code'] = 2;
            } elseif (in_array($copy['error'], array(1, 2, 3, 4, 5))) {
                $data['code'] = 3;
            } elseif (!$copy['size'] || $copy['size'] > $this->maxSize) {
                $data['code'] = 3;
            } elseif ($copy['error'] === 0) {
                $prefix && substr($prefix, -1) !== '/' && $prefix = $prefix . '_';
                $file = $this->savePath . $prefix . uniqid().rand(1, 10000) ;

                $this->token = $this->auth->uploadToken($this->bucket, null, 3600);
                $this->uploadMgr = New UploadManager();
                list($ret, $err) = $this->uploadMgr->putFile($this->token, $file.'.'. $type, $fileinfo['tmp_name']);
                if ($err !== null) {
                    $data['code'] = 4;
                } else {
                    $data['code'] = 0;
                    // $data['rerut'] = $rerut;
                    $data['name'] = $copy['name'];
                    $data['type'] = $type;
                    $data['size'] = $copy['size'];
                    $data['file'] = $file;
                    $data['url'] = $file.'.'. $type;
                } 
            } else {
                $data['code'] = 5;
            }
        }
        $data['text'] = $this->eventText[$data['code']];
        return $data;
    }

    /**
     * 检查文件类型是否允许上传
     */
    private function fileType($file) {
        if ($file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            if ($ext && strpos($this->allowExt, $ext) !== false)
                return $ext;
            else
                return '';
        }
        return '';
    }


}

?>