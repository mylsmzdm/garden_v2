<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 缓存配置
 */
Class CacheConfig {  
    #cache有效时间,如果为0表示永久有效. 以"_list"结尾表示多值缓存时间. 以"_null"结尾表示之前key对于空结果的缓存时间

    public static $ttl = array(#前缀命名以系统开头
        'no_cache' => 0, #通用的不缓存key
    );
}

$config['cache_config_data'] = 1;


