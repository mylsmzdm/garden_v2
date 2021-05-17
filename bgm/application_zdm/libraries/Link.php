<?php

/**
 * CPS link sdk
 */
require_once __DIR__ . '/sdk/link/link_generate.php';

class Link {
    
    private $link_obj;

    public function __construct() {
        
    }
    
    public function init() {
        $this->link_obj = new LinkGenerate();
    }

    /**
     * 根据url生成cps链接
     * 
     * @author jxt
     */
    function generate_url($params) {
        if (!is_object($this->link_obj)) {
            $this->init();
        }
        return $this->link_obj->generate_url($params);
    }
    
    /**
     * 批量替换文章内链接为CPS链接
     * 
     * @author jxt
     */
    function replace_content_link($params) {
        if (!is_object($this->link_obj)) {
            $this->init();
        }
        return $this->link_obj->replace_content_link($params);
    }

}
