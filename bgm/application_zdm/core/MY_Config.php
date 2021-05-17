<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Config extends CI_Config {
    function __construct(){
        parent::__construct();
    }

    // -------------------------------------------------------------

    /**
     * Static URL
     * Returns static [. uri_string]
     *
     * @access public
     * @param string $uri
     * @return string
     */
    public function static_url($uri ='',$host='')
    {
        // 检查uri
        if (defined('ENVIRONMENT') && ENVIRONMENT == 'development'){  //开发环境下
            if(strpos($uri,'.css') !== false){
                $uri = str_replace('.css', '_dev.css', $uri);
            }elseif(strpos($uri,'.js') !== false){
                $uri = str_replace('.js', '_dev.js', $uri);
            }
        }
        // 设置文件版本
        if(isset(Config::$constant['js_css_v'])&& $uri !=''){
            $version = '?v='.Config::$constant['js_css_v'];
        }else{
            $version = '';
        }
        if($host ==''){
            if(isset(Config::$url['static_url'])){
                $url_prefix = trim(Config::$url['static_url']);
                $url_prefix = rtrim($url_prefix, '/').'/';
            }else{
                $url_prefix = '/';
            }
        }else{
            $url_prefix = rtrim(trim($host), '/').'/';
        }
        return $url_prefix.ltrim($this->_uri_string($uri), '/').$version;
    }

}