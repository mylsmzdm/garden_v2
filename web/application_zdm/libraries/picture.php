<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Picture {


    /**
     * 图片等比缩放 宽高相等(正方形)
     * @param type $thumb_size 需要宽高
     * @param type $height     实际高度
     * @param type $width      实际宽度
     * @param type $marginTop  marginTop 
     * @return string          样式（宽高、marginTop ）
     * (2014-04-11 移动自主站 -相明亮)
     */
    function  pic_to_thumb($thumb_size, $height, $width, $marginTop = 0){
        if(empty($height)||empty($width)||$height==0||$width==0){
            return 'width="'.$thumb_size.'px" height="'.$thumb_size.'px"';
        }
        if ($height <= $thumb_size && $width <= $thumb_size) {
            return ' width="'.$width.'px" height="'.$height.'px" style="margin-top:'.(round(($thumb_size-$height)/2) + $marginTop).'px" ';
        } else if ($height > $width) {
            $new_width = round(($width/$height)*$thumb_size);
            return ' height="'.$thumb_size.'px" width="'.$new_width.'px" style="margin-top:'.$marginTop.'px" ';
        } else if ($height < $width) {
            $new_height = round(($height/$width)*$thumb_size);
            return ' width="'.$thumb_size.'px" height="'.$new_height.'px" style="margin-top:'.(round(($thumb_size-$new_height)/2)+$marginTop).'px" ';
        } else {
            return ' width="'.$thumb_size.'px" height="'.$thumb_size.'px" style="margin-top:'.$marginTop.'px" ';
        }
    }
    /**
     * 等比缩放需要宽高不相等(长方形)
     * @param type $limit_width   需要宽度
     * @param type $limit_height  需要高度
     * @param type $source_width  原始宽度
     * @param type $source_height 原始高度
     * @return string             图片宽高样式（图片未居中时需要返回margin-top）
     */
    function pic_to_thumb_two($limit_width, $limit_height, $source_width, $source_height){
        $wh=array("style='width:".$limit_width."px;height:".$limit_height."px;");
        if(empty($source_width) && empty($source_height)){
            return "";
        } else if($source_width<=$limit_width && $source_height<=$limit_height){
            $wh["width"]=$source_width;
            $wh["height"]=$source_height;
            $wh['m-top'] = round(($limit_height-$source_height)/2);
        }else{
            $w=$source_width/$limit_width;
            $h=$source_height/$limit_height;
            if($w>$h){
                $wh["width"]=$limit_width;
                $wh["height"]=($w>=1?($source_height/$w):($source_height*$w));
                $wh['m-top'] = ($limit_height-$wh["height"])/2;
            }elseif($w<$h){
                $wh["width"]=($h>=1?($source_width/$h):($source_width*$h));
                $wh["height"]=$limit_height;
                $wh['m-top'] = ($limit_height-$wh["height"])/2;
            }else{
                $wh["width"]=$limit_width;
                $wh["height"]=$limit_height;
                $wh['m-top'] = ($limit_height-$wh["height"])/2;
            }
        }
        return "style='width:".$wh['width']."px;height:".$wh['height']."px;margin-top:".$wh['m-top']."px;'";
    }

    /**
     * 获取商品中图图片。
     * 		目前（2012年10月19日）仅供文章列表页、热门日志，使用。找一个稍微大于250px的图片。
     *
     * @param   string    $thumbnail     图片路径(大图)
     * @return  string
     */
    function classic_the_post_middle_thumbnail($custome_thumbnail_name ) {
        if (($custome_thumbnail_name == "")||($custome_thumbnail_name == "logo")||($custome_thumbnail_name == "blank")){
            $custome_thumbnail_name = Config::$constant['default_smzdm_icon_178'];
        }
        // 新浪微博
        else if (stripos($custome_thumbnail_name, 'sinaimg') != false){
            if (stripos($custome_thumbnail_name, 'large') != false){
                $custome_thumbnail_name = str_ireplace('large','small',$custome_thumbnail_name);
            } elseif (stripos($custome_thumbnail_name, 'bmiddle') != false){
                $custome_thumbnail_name = str_ireplace('bmiddle','small',$custome_thumbnail_name);
            }
            // 新浪图片URL替换。把ww3更换为ww2，ww4更换为ww1。——2013年1月21日
            if (stripos($custome_thumbnail_name, 'ww3.sinaimg.cn/') > 0) {
                $custome_thumbnail_name = str_ireplace('ww3.sinaimg.cn/', 'ww2.sinaimg.cn/', $custome_thumbnail_name);
            }
            if (stripos($custome_thumbnail_name, 'ww4.sinaimg.cn/') > 0) {
                $custome_thumbnail_name = str_ireplace('ww4.sinaimg.cn/', 'ww1.sinaimg.cn/', $custome_thumbnail_name);
            }
        }
        // 京东商城
        else if (stripos($custome_thumbnail_name, '360buyimg') != false){
            if (stripos($custome_thumbnail_name, '/n0/') != false){
                $custome_thumbnail_name = str_ireplace('/n0/','/n2/',$custome_thumbnail_name);
            } elseif (stripos($custome_thumbnail_name, '/n1/') != false){
                $custome_thumbnail_name = str_ireplace('/n1/','/n2/',$custome_thumbnail_name);
            }
        }
        // 新蛋网
        else if (stripos($custome_thumbnail_name, 'neweggimages') != false){
            if (stripos($custome_thumbnail_name, '/P640/') != false){
                $custome_thumbnail_name = str_ireplace('/P640/','/P220/',$custome_thumbnail_name);
            } elseif (stripos($custome_thumbnail_name, '/P800/') != false){
                $custome_thumbnail_name = str_ireplace('/P800/','/P220/',$custome_thumbnail_name);
            } elseif (stripos($custome_thumbnail_name, '/P380/') != false){
                $custome_thumbnail_name = str_ireplace('/P380/','/P220/',$custome_thumbnail_name);
            }
        }
        // 当当网
        else if (stripos($custome_thumbnail_name, 'ddimg') != false){
            if (stripos($custome_thumbnail_name, 'e.jpg') != false) {
                $custome_thumbnail_name = str_ireplace('e.jpg', 'b.jpg', $custome_thumbnail_name);
            } elseif (stripos($custome_thumbnail_name, 'w.jpg') != false) {
                $custome_thumbnail_name = str_ireplace('w.jpg', 'b.jpg', $custome_thumbnail_name);
            } elseif (stripos($custome_thumbnail_name, 'h.jpg') != false) {
                $custome_thumbnail_name = str_ireplace('h.jpg', 'b.jpg', $custome_thumbnail_name);
            }
        }
        // 淘宝
        else if (stripos($custome_thumbnail_name, 'taobaocdn') != false){
            $temp_taobal_length = strlen($custome_thumbnail_name);
            $_temp = strrpos($custome_thumbnail_name, '_');
            if($_temp > 0 && $_temp < $temp_taobal_length - 4){
                $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $_temp).'_210x210.jpg';
            }else{
                $temp_taobao = stripos($custome_thumbnail_name, '.jpg');
                if ($temp_taobao > 0 && $temp_taobao < $temp_taobal_length - 4) {
                    $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao).'.jpg_210x210.jpg';
                } else {
                    $temp_taobao_png = stripos($custome_thumbnail_name, '.png');
                    if ($temp_taobao_png > 0 && $temp_taobao_png < $temp_taobal_length - 4) {
                        $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_png).'.png_210x210.jpg';
                    } else {
                        $temp_taobao_gif = stripos($custome_thumbnail_name, '.gif');
                        if ($temp_taobao_gif > 0 && $temp_taobao_gif < $temp_taobal_length - 4) {
                            $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_gif).'.gif_210x210.jpg';
                        } else {
                            $custome_thumbnail_name = $custome_thumbnail_name.'_210x210.jpg';
                        }
                    }
                }
            }
        }
        // 亚马逊??
        else if (stripos($custome_thumbnail_name, 'images-amazon') != false){
            if (stripos($custome_thumbnail_name, '_SL1500_') != false){
                $custome_thumbnail_name = str_ireplace('_SL1500_','_SL200_',$custome_thumbnail_name);
            } elseif (stripos($custome_thumbnail_name, '_SL1000_') != false){
                $custome_thumbnail_name = str_ireplace('_SL1000_','_SL200_',$custome_thumbnail_name);
            } elseif (stripos($custome_thumbnail_name, '_SL500_') != false){
                $custome_thumbnail_name = str_ireplace('_SL500_','_SL200_',$custome_thumbnail_name);
            } elseif (stripos($custome_thumbnail_name, '_SL300_') != false){
                $custome_thumbnail_name = str_ireplace('_SL300_','_SL200_',$custome_thumbnail_name);
            } elseif (stripos($custome_thumbnail_name, '_SL250_') != false){
                $custome_thumbnail_name = str_ireplace('_SL250_','_SL200_',$custome_thumbnail_name);
            }
        }
        // 易迅网
        else if (stripos($custome_thumbnail_name, 'icson') != false){
            if (stripos($custome_thumbnail_name, 'mpic') != false){
                $custome_thumbnail_name = str_ireplace('mpic','mm',$custome_thumbnail_name);
            }
        }
        return $custome_thumbnail_name;
    }


    /**
     * 获取自有图床图片的缩略图
     * @param   string      $image_url 	     图片地址
     * @param   string      $thumb_version  缩略图版本号，如： n1.jpg
     * @param   string      $from           默认为空。"post"主站图片，"fx"发现频道图片。
     * @return  string      
     * @author  zhaolu
     */
    function image_url_to_thumb($image_url , $thumb_version = "", $from = "") {
        $image_url = trim($image_url);
        if(stripos($image_url, "zdmimg") !== false){
                // 主站图片
            if($from == Config::$constant['zdmimg_from_post'] && (stripos($image_url, "f.zdmimg") == true || stripos($image_url, "fn.zdmimg") == true || stripos($image_url, "s.zdmimg") == true)){
                return $image_url;
            // 发现频道图片
            }else if($from == Config::$constant['zdmimg_from_fx'] && (stripos($image_url, "p.zdmimg") == true || stripos($image_url, "pn.zdmimg") == true || stripos($image_url, "s.zdmimg") == true)){
                return $image_url;
            }
            if(stripos($image_url, "_") !== false){
                $img_arr = explode("_", $image_url);
                $image_url = $img_arr[0];
            }
            //返回原图url
            if($thumb_version == ""){
                return $image_url;
            }
            if(Config::$constant['img_is_thumb']){
                $image_url = $image_url."_".$thumb_version.".jpg";
            }
        }
        return $image_url;
    }

    /**
     * 获取eimg图床图片的缩略图
     * @param   string      $image_url       图片地址
     * @param   string      $thumb_version  缩略图版本号，如： n1.jpg
     * @return  string      
     * @author  xuxueyong
     */
    function eimg_url_to_thumb($image_url , $thumb_version = "") {
        $image_url = trim($image_url);
        if(stripos($image_url, "eimg.smzdm") !== false)
        {
            if(stripos($image_url, "_") !== false){
                $img_arr = explode("_", $image_url);
                $image_url = $img_arr[0];
            }
            //返回原图url
            if($thumb_version == ""){
                return $image_url;
            }

            $image_url = $image_url."_".$thumb_version.".jpg";

        }
        return $image_url;
    }

    /**
    * 根据官网要求获取商品图片img标签
    *       官网图片：55*55；80*80；160*160；204*204；
    *
    * @param   int      $height                     官网图片规格高度
    * @param   int      $width                      官网图片规格宽度
    * @param   int      $custome_thumbnail_name     数据库图片路径(大图)
    * @param   boolean  $all_limit                  是否限制宽度并且限制高度（发现频道，只限制宽度）
    * @param   int      $source_height              数据库图片规格高度
    * @param   int      $source_width               数据库图片规格宽度
    * @return  string
    * @author  Dacheng Chen
    */
    function classic_the_thumbnail($height, $width, $custome_thumbnail_name, $all_limit = true, $source_height=0, $source_width=0) {
        // 一、限制宽度，并且限制高度
        if ($all_limit) {
            // 1. 找到相应的大、中、小图片
            if (($custome_thumbnail_name == "")||($custome_thumbnail_name == "logo")||($custome_thumbnail_name == "blank")){
                $custome_thumbnail_name = Config::$constant['default_smzdm_icon_178'];
            }
            // 新浪微博
            else if (stripos($custome_thumbnail_name, 'sinaimg') != false) {
                if ($width <= 120 && $height <= 120) {
                    if (stripos($custome_thumbnail_name, 'large') != false) {
                        $custome_thumbnail_name = str_ireplace('large', 'thumbnail', $custome_thumbnail_name);
                    } else if (stripos($custome_thumbnail_name, 'bmiddle') != false) {
                        $custome_thumbnail_name = str_ireplace('bmiddle', 'thumbnail', $custome_thumbnail_name);
                    } else if (stripos($custome_thumbnail_name, 'small') != false) {
                        $custome_thumbnail_name = str_ireplace('small', 'thumbnail', $custome_thumbnail_name);
                    }
                } elseif ($width <= 200 && $height <= 200) {
                    if (stripos($custome_thumbnail_name, 'large') != false) {
                        $custome_thumbnail_name = str_ireplace('large', 'small', $custome_thumbnail_name);
                    } else if (stripos($custome_thumbnail_name, 'bmiddle') != false) {
                        $custome_thumbnail_name = str_ireplace('bmiddle', 'small', $custome_thumbnail_name);
                    }
                } else {
                    if (stripos($custome_thumbnail_name, 'large') != false) {
                        $custome_thumbnail_name = str_ireplace('large', 'bmiddle', $custome_thumbnail_name);
                    }
                }
                // 新浪图片URL替换。把ww3更换为ww2，ww4更换为ww1。——2013年1月21日
                if (stripos($custome_thumbnail_name, 'ww3.sinaimg.cn/') > 0) {
                    $custome_thumbnail_name = str_ireplace('ww3.sinaimg.cn/', 'ww2.sinaimg.cn/', $custome_thumbnail_name);
                }
                if (stripos($custome_thumbnail_name, 'ww4.sinaimg.cn/') > 0) {
                    $custome_thumbnail_name = str_ireplace('ww4.sinaimg.cn/', 'ww1.sinaimg.cn/', $custome_thumbnail_name);
                }
        }
        // 京东商城
        else if (stripos($custome_thumbnail_name, '360buyimg') != false) {
            if ($width <= 100 && $height <= 100) {
                if (stripos($custome_thumbnail_name, '/n0/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n0/', '/n4/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/n1/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n1/', '/n4/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/n2/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n2/', '/n4/', $custome_thumbnail_name);
                }
            } elseif ($width <= 160 && $height <= 160) {
                if (stripos($custome_thumbnail_name, '/n0/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n0/', '/n2/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/n1/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n1/', '/n2/', $custome_thumbnail_name);
                }
            } elseif ($width <= 350 && $height <= 350) {
                if (stripos($custome_thumbnail_name, '/n0/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n0/', '/n1/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/n2/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n2/', '/n1/', $custome_thumbnail_name);
                }
            }
        }
        // 新蛋网
        else if (stripos($custome_thumbnail_name, 'neweggimages') != false) {
            if ($width <= 80 && $height <= 80) {
                if (stripos($custome_thumbnail_name, '/P640/') != false) {
                    $custome_thumbnail_name = str_ireplace('/P640/', '/P80/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P800/') != false){
                    $custome_thumbnail_name = str_ireplace('/P800/', '/P80/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P380/') != false){
                    $custome_thumbnail_name = str_ireplace('/P380/', '/P80/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P220/') != false){
                    $custome_thumbnail_name = str_ireplace('/P220/', '/P80/', $custome_thumbnail_name);
                }
            } else if ($width <= 220 && $height <= 220) {
                if (stripos($custome_thumbnail_name, '/P640/') != false) {
                    $custome_thumbnail_name = str_ireplace('/P640/', '/P220/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P800/') != false){
                    $custome_thumbnail_name = str_ireplace('/P800/', '/P220/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P380/') != false){
                    $custome_thumbnail_name = str_ireplace('/P380/', '/P220/', $custome_thumbnail_name);
                }
            } else if ($width <= 380 && $height <= 380) {
                if (stripos($custome_thumbnail_name, '/P640/') != false) {
                    $custome_thumbnail_name = str_ireplace('/P640/', '/P380/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P800/') != false){
                    $custome_thumbnail_name = str_ireplace('/P800/', '/P380/', $custome_thumbnail_name);
                }
            }
        }
        // 当当网
        else if (stripos($custome_thumbnail_name, 'ddimg') != false) {
            if ($width <= 100 && $height <= 100) {
                if (stripos($custome_thumbnail_name, 'e.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('e.jpg', 'a.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'w.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('w.jpg', 'a.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'h.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('h.jpg', 'a.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'b.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('b.jpg', 'a.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'f.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('f.jpg', 'a.jpg', $custome_thumbnail_name);
                }
            } else if ($width <= 120 && $height <= 120) {
                if (stripos($custome_thumbnail_name, 'e.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('e.jpg', 'f.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'w.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('w.jpg', 'f.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'h.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('h.jpg', 'f.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'b.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('b.jpg', 'f.jpg', $custome_thumbnail_name);
                }
            } else if ($width <= 200 && $height <= 200) {
                if (stripos($custome_thumbnail_name, 'e.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('e.jpg', 'b.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'w.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('w.jpg', 'b.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'h.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('h.jpg', 'b.jpg', $custome_thumbnail_name);
                }
            } else if ($width <= 250 && $height <= 250) {
                if (stripos($custome_thumbnail_name, 'e.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('e.jpg', 'h.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'w.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('w.jpg', 'h.jpg', $custome_thumbnail_name);
                }
            }
        }
        // 淘宝
        else if (stripos($custome_thumbnail_name, 'taobaocdn') != false){
            $temp_taobal_length = strlen($custome_thumbnail_name);
            $_temp = strrpos($custome_thumbnail_name, '_');
            if ($width <= 80 && $height <= 80) {
                if($_temp > 0 && $_temp < $temp_taobal_length - 4){
                    $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $_temp).'_80x80.jpg';
                }else{
                    $temp_taobao = stripos($custome_thumbnail_name, '.jpg');
                    if ($temp_taobao > 0 && $temp_taobao < $temp_taobal_length - 4) {
                        $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao).'.jpg_80x80.jpg';
                    } else {
                        $temp_taobao_png = stripos($custome_thumbnail_name, '.png');
                        if ($temp_taobao_png > 0 && $temp_taobao_png < $temp_taobal_length - 4) {
                            $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_png).'.png_80x80.jpg';
                        } else {
                            $temp_taobao_gif = stripos($custome_thumbnail_name, '.gif');
                            if ($temp_taobao_gif > 0 && $temp_taobao_gif < $temp_taobal_length - 4) {
                                $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_gif).'.gif_80x80.jpg';
                            } else {
                                $custome_thumbnail_name = $custome_thumbnail_name.'_80x80.jpg';
                            }
                        }
                    }
                }
            } elseif ($width <= 160 && $height <= 160) {
                if($_temp > 0 && $_temp < $temp_taobal_length - 4){
                    $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $_temp).'_160x160.jpg';
                }else{
                    $temp_taobao = stripos($custome_thumbnail_name, '.jpg');
                    if ($temp_taobao > 0 && $temp_taobao < $temp_taobal_length - 4) {
                        $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao).'.jpg_160x160.jpg';
                    } else {
                        $temp_taobao_png = stripos($custome_thumbnail_name, '.png');
                        if ($temp_taobao_png > 0 && $temp_taobao_png < $temp_taobal_length - 4) {
                            $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_png).'.png_160x160.jpg';
                        } else {
                            $temp_taobao_gif = stripos($custome_thumbnail_name, '.gif');
                            if ($temp_taobao_gif > 0 && $temp_taobao_gif < $temp_taobal_length - 4) {
                                $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_gif).'.gif_160x160.jpg';
                            } else {
                                $custome_thumbnail_name = $custome_thumbnail_name.'_160x160.jpg';
                            }
                        }
                    }
                }
            } elseif ($width <= 210 && $height <= 210) {
                if($_temp > 0 && $_temp < $temp_taobal_length - 4){
                    $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $_temp).'_210x210.jpg';
                }else{
                    $temp_taobao = stripos($custome_thumbnail_name, '.jpg');
                    if ($temp_taobao > 0 && $temp_taobao < $temp_taobal_length - 4) {
                        $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao).'.jpg_210x210.jpg';
                    } else {
                        $temp_taobao_png = stripos($custome_thumbnail_name, '.png');
                        if ($temp_taobao_png > 0 && $temp_taobao_png < $temp_taobal_length - 4) {
                            $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_png).'.png_210x210.jpg';
                        } else {
                            $temp_taobao_gif = stripos($custome_thumbnail_name, '.gif');
                            if ($temp_taobao_gif > 0 && $temp_taobao_gif < $temp_taobal_length - 4) {
                                $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_gif).'.gif_210x210.jpg';
                            } else {
                                $custome_thumbnail_name = $custome_thumbnail_name.'_210x210.jpg';
                            }
                        }
                    }
                }
            } elseif ($width <= 250 && $height <= 250) {
                if($_temp > 0 && $_temp < $temp_taobal_length - 4){
                    $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $_temp).'_250x250.jpg';
                }else{
                    $temp_taobao = stripos($custome_thumbnail_name, '.jpg');
                    if ($temp_taobao > 0 && $temp_taobao < $temp_taobal_length - 4) {
                        $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao).'.jpg_250x250.jpg';
                    } else {
                        $temp_taobao_png = stripos($custome_thumbnail_name, '.png');
                        if ($temp_taobao_png > 0 && $temp_taobao_png < $temp_taobal_length - 4) {
                            $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_png).'.png_250x250.jpg';
                        } else {
                            $temp_taobao_gif = stripos($custome_thumbnail_name, '.gif');
                            if ($temp_taobao_gif > 0 && $temp_taobao_gif < $temp_taobal_length - 4) {
                                $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_gif).'.gif_250x250.jpg';
                            } else {
                                $custome_thumbnail_name = $custome_thumbnail_name.'_250x250.jpg';
                            }
                        }
                    }
                }
            } elseif ($width <= 310 && $height <= 310) {
                if($_temp > 0 && $_temp < $temp_taobal_length - 4){
                    $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $_temp).'_310x310.jpg';
                }else{
                    $temp_taobao = stripos($custome_thumbnail_name, '.jpg');
                    if ($temp_taobao > 0 && $temp_taobao < $temp_taobal_length - 4) {
                        $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao).'.jpg_310x310.jpg';
                    } else {
                        $temp_taobao_png = stripos($custome_thumbnail_name, '.png');
                        if ($temp_taobao_png > 0 && $temp_taobao_png < $temp_taobal_length - 4) {
                            $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_png).'.png_310x310.jpg';
                        } else {
                            $temp_taobao_gif = stripos($custome_thumbnail_name, '.gif');
                            if ($temp_taobao_gif > 0 && $temp_taobao_gif < $temp_taobal_length - 4) {
                                $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_gif).'.gif_310x310.jpg';
                            } else {
                                $custome_thumbnail_name = $custome_thumbnail_name.'_210x210.jpg';
                            }
                        }
                    }
                }
            }
        }
        // 亚马逊??
        else if (stripos($custome_thumbnail_name, 'images-amazon') != false) {
            if ($width <= 80 && $height <= 80) {
                if (stripos($custome_thumbnail_name, '_SL1500_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL1500_', '_SL80_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL1000_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL1000_', '_SL80_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL500_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL500_', '_SL80_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL300_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL300_', '_SL80_', $custome_thumbnail_name);
                }
                if (stripos($custome_thumbnail_name, '_AA300_') != false) {
                    $custome_thumbnail_name = str_ireplace('_AA300_', '_AA80_', $custome_thumbnail_name);
                }
            } elseif ($width <= 160 && $height <= 160) {
                if (stripos($custome_thumbnail_name, '_SL1500_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL1500_', '_SL160_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL1000_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL1000_', '_SL160_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL500_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL500_', '_SL160_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL300_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL300_', '_SL160_', $custome_thumbnail_name);
                }
                if (stripos($custome_thumbnail_name, '_AA300_') != false) {
                    $custome_thumbnail_name = str_ireplace('_AA300_', '_AA160_', $custome_thumbnail_name);
                }
            } elseif ($width <= 200 && $height <= 200) {
                if (stripos($custome_thumbnail_name, '_SL1500_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL1500_', '_SL200_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL1000_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL1000_', '_SL200_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL500_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL500_', '_SL200_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL300_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL300_', '_SL200_', $custome_thumbnail_name);
                }
                if (stripos($custome_thumbnail_name, '_AA300_') != false) {
                    $custome_thumbnail_name = str_ireplace('_AA300_', '_AA200_', $custome_thumbnail_name);
                }
            } elseif ($width <= 250 && $height <= 250) {
                if (stripos($custome_thumbnail_name, '_SL1500_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL1500_', '_SL250_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL1000_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL1000_', '_SL250_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL500_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL500_', '_SL250_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL300_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL300_', '_SL250_', $custome_thumbnail_name);
                }
                if (stripos($custome_thumbnail_name, '_AA300_') != false) {
                    $custome_thumbnail_name = str_ireplace('_AA300_', '_AA250_', $custome_thumbnail_name);
                }
            } elseif ($width <= 300 && $height <= 300) {
                if (stripos($custome_thumbnail_name, '_SL1500_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL1500_', '_SL300_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL1000_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL1000_', '_SL300_', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '_SL500_') != false) {
                    $custome_thumbnail_name = str_ireplace('_SL500_', '_SL300_', $custome_thumbnail_name);
                }
            }
        }
        // 易迅网
        else if (stripos($custome_thumbnail_name, 'icson') != false) {
            if ($width <= 80 && $height <= 80) {
                if (stripos($custome_thumbnail_name, 'mpic') != false) {
                    $custome_thumbnail_name = str_ireplace('mpic', 'small', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'mm') != false) {
                    $custome_thumbnail_name = str_ireplace('mm', 'small', $custome_thumbnail_name);
                }
            } elseif ($width <= 300 && $height <= 300) {
                if (stripos($custome_thumbnail_name, 'mpic') != false) {
                    $custome_thumbnail_name = str_ireplace('mpic', 'mm', $custome_thumbnail_name);
                }
            }
        }
        // 二、只限制宽度，不限制高度
        } else {
        // 1. 找到相应的大、中、小图片
        if (($custome_thumbnail_name == "")||($custome_thumbnail_name == "logo")||($custome_thumbnail_name == "blank")){
            $custome_thumbnail_name = Config::$constant['default_smzdm_icon_178'];
        }
        // 新浪微博
        else if (stripos($custome_thumbnail_name, 'sinaimg') != false) {
            if ($width <= 120) {
                if (stripos($custome_thumbnail_name, 'large') != false) {
                    $custome_thumbnail_name = str_ireplace('large', 'thumbnail', $custome_thumbnail_name);
                } else if (stripos($custome_thumbnail_name, 'bmiddle') != false) {
                    $custome_thumbnail_name = str_ireplace('bmiddle', 'thumbnail', $custome_thumbnail_name);
                } else if (stripos($custome_thumbnail_name, 'small') != false) {
                    $custome_thumbnail_name = str_ireplace('small', 'thumbnail', $custome_thumbnail_name);
                }
            } elseif ($width <= 200) {
                if ($source_width > $source_height) {
                    if (stripos($custome_thumbnail_name, 'large') != false) {
                        $custome_thumbnail_name = str_ireplace('large', 'small', $custome_thumbnail_name);
                    } else if (stripos($custome_thumbnail_name, 'bmiddle') != false) {
                        $custome_thumbnail_name = str_ireplace('bmiddle', 'small', $custome_thumbnail_name);
                    }
                } else {
                    if (stripos($custome_thumbnail_name, 'large') != false) {
                        $custome_thumbnail_name = str_ireplace('large', 'bmiddle', $custome_thumbnail_name);
                    }
                }
            } else {
                if (stripos($custome_thumbnail_name, 'large') != false) {
                    $custome_thumbnail_name = str_ireplace('large', 'bmiddle', $custome_thumbnail_name);
                }
            }
            // 新浪图片URL替换。把ww3更换为ww2，ww4更换为ww1。——2013年1月21日
            if (stripos($custome_thumbnail_name, 'ww3.sinaimg.cn/') > 0) {
                $custome_thumbnail_name = str_ireplace('ww3.sinaimg.cn/', 'ww2.sinaimg.cn/', $custome_thumbnail_name);
            }
            if (stripos($custome_thumbnail_name, 'ww4.sinaimg.cn/') > 0) {
                $custome_thumbnail_name = str_ireplace('ww4.sinaimg.cn/', 'ww1.sinaimg.cn/', $custome_thumbnail_name);
            }
        }
        // 京东商城
        else if (stripos($custome_thumbnail_name, '360buyimg') != false) {
            if ($width <= 100) {
                if (stripos($custome_thumbnail_name, '/n0/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n0/', '/n4/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/n1/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n1/', '/n4/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/n2/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n2/', '/n4/', $custome_thumbnail_name);
                }
            } elseif ($width <= 160) {
                if (stripos($custome_thumbnail_name, '/n0/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n0/', '/n2/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/n1/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n1/', '/n2/', $custome_thumbnail_name);
                }
            } elseif ($width <= 350) {
                if (stripos($custome_thumbnail_name, '/n0/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n0/', '/n1/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/n2/') != false) {
                    $custome_thumbnail_name = str_ireplace('/n2/', '/n1/', $custome_thumbnail_name);
                }
            }
        }
        // 新蛋网
        else if (stripos($custome_thumbnail_name, 'neweggimages') != false) {
            if ($width <= 80) {
                if (stripos($custome_thumbnail_name, '/P640/') != false) {
                    $custome_thumbnail_name = str_ireplace('/P640/', '/P80/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P800/') != false){
                    $custome_thumbnail_name = str_ireplace('/P800/', '/P80/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P380/') != false){
                    $custome_thumbnail_name = str_ireplace('/P380/', '/P80/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P220/') != false){
                    $custome_thumbnail_name = str_ireplace('/P220/', '/P80/', $custome_thumbnail_name);
                }
            } else if ($width <= 220) {
                if (stripos($custome_thumbnail_name, '/P640/') != false) {
                    $custome_thumbnail_name = str_ireplace('/P640/', '/P220/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P800/') != false){
                    $custome_thumbnail_name = str_ireplace('/P800/', '/P220/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P380/') != false){
                    $custome_thumbnail_name = str_ireplace('/P380/', '/P220/', $custome_thumbnail_name);
                }
            } else if ($width <= 380) {
                if (stripos($custome_thumbnail_name, '/P640/') != false) {
                    $custome_thumbnail_name = str_ireplace('/P640/', '/P380/', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, '/P800/') != false){
                    $custome_thumbnail_name = str_ireplace('/P800/', '/P380/', $custome_thumbnail_name);
                }
            }
        }
        // 当当网
        else if (stripos($custome_thumbnail_name, 'ddimg') != false) {
            if ($width <= 100) {
                if (stripos($custome_thumbnail_name, 'e.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('e.jpg', 'a.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'w.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('w.jpg', 'a.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'h.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('h.jpg', 'a.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'b.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('b.jpg', 'a.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'f.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('f.jpg', 'a.jpg', $custome_thumbnail_name);
                }
            } else if ($width <= 120) {
                if (stripos($custome_thumbnail_name, 'e.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('e.jpg', 'f.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'w.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('w.jpg', 'f.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'h.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('h.jpg', 'f.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'b.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('b.jpg', 'f.jpg', $custome_thumbnail_name);
                }
            } else if ($width <= 200) {
                if (stripos($custome_thumbnail_name, 'e.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('e.jpg', 'b.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'w.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('w.jpg', 'b.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'h.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('h.jpg', 'b.jpg', $custome_thumbnail_name);
                }
            } else if ($width <= 250) {
                if (stripos($custome_thumbnail_name, 'e.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('e.jpg', 'h.jpg', $custome_thumbnail_name);
                } elseif (stripos($custome_thumbnail_name, 'w.jpg') != false) {
                    $custome_thumbnail_name = str_ireplace('w.jpg', 'h.jpg', $custome_thumbnail_name);
                }
            }
        }
        // 淘宝
        else if (stripos($custome_thumbnail_name, 'taobaocdn') != false){
            $temp_taobal_length = strlen($custome_thumbnail_name);
            $_temp = strrpos($custome_thumbnail_name, '_');
            if ($width <= 80) {
                if($_temp > 0 && $_temp < $temp_taobal_length - 4){
                    $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $_temp).'_80x80.jpg';
                }else{
                    $temp_taobao = stripos($custome_thumbnail_name, '.jpg');
                    if ($temp_taobao > 0 && $temp_taobao < $temp_taobal_length - 4) {
                        $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao).'.jpg_80x80.jpg';
                    } else {
                        $temp_taobao_png = stripos($custome_thumbnail_name, '.png');
                        if ($temp_taobao_png > 0 && $temp_taobao_png < $temp_taobal_length - 4) {
                            $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_png).'.png_80x80.jpg';
                        } else {
                            $temp_taobao_gif = stripos($custome_thumbnail_name, '.gif');
                            if ($temp_taobao_gif > 0 && $temp_taobao_gif < $temp_taobal_length - 4) {
                                $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_gif).'.gif_80x80.jpg';
                            } else {
                                $custome_thumbnail_name = $custome_thumbnail_name.'_80x80.jpg';
                            }
                        }
                    }
                }
            } elseif ($width <= 160) {
                if($_temp > 0 && $_temp < $temp_taobal_length - 4){
                    $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $_temp).'_160x160.jpg';
                }else{
                    $temp_taobao = stripos($custome_thumbnail_name, '.jpg');
                    if ($temp_taobao > 0 && $temp_taobao < $temp_taobal_length - 4) {
                        $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao).'.jpg_160x160.jpg';
                    } else {
                        $temp_taobao_png = stripos($custome_thumbnail_name, '.png');
                        if ($temp_taobao_png > 0 && $temp_taobao_png < $temp_taobal_length - 4) {
                            $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_png).'.png_160x160.jpg';
                        } else {
                            $temp_taobao_gif = stripos($custome_thumbnail_name, '.gif');
                            if ($temp_taobao_gif > 0 && $temp_taobao_gif < $temp_taobal_length - 4) {
                                $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_gif).'.gif_160x160.jpg';
                            } else {
                                $custome_thumbnail_name = $custome_thumbnail_name.'_160x160.jpg';
                            }
                        }
                    }
                }
            } elseif ($width <= 210) {
                if($_temp > 0 && $_temp < $temp_taobal_length - 4){
                    $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $_temp).'_210x210.jpg';
                }else{
                    $temp_taobao = stripos($custome_thumbnail_name, '.jpg');
                    if ($temp_taobao > 0 && $temp_taobao < $temp_taobal_length - 4) {
                        $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao).'.jpg_210x210.jpg';
                    } else {
                        $temp_taobao_png = stripos($custome_thumbnail_name, '.png');
                        if ($temp_taobao_png > 0 && $temp_taobao_png < $temp_taobal_length - 4) {
                            $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_png).'.png_210x210.jpg';
                        } else {
                            $temp_taobao_gif = stripos($custome_thumbnail_name, '.gif');
                            if ($temp_taobao_gif > 0 && $temp_taobao_gif < $temp_taobal_length - 4) {
                                $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_gif).'.gif_210x210.jpg';
                            } else {
                                $custome_thumbnail_name = $custome_thumbnail_name.'_210x210.jpg';
                            }
                        }
                    }
                }
            } elseif ($width <= 250) {
                if($_temp > 0 && $_temp < $temp_taobal_length - 4){
                    $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $_temp).'_250x250.jpg';
                }else{
                    $temp_taobao = stripos($custome_thumbnail_name, '.jpg');
                    if ($temp_taobao > 0 && $temp_taobao < $temp_taobal_length - 4) {
                        $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao).'.jpg_250x250.jpg';
                    } else {
                        $temp_taobao_png = stripos($custome_thumbnail_name, '.png');
                        if ($temp_taobao_png > 0 && $temp_taobao_png < $temp_taobal_length - 4) {
                            $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_png).'.png_250x250.jpg';
                        } else {
                            $temp_taobao_gif = stripos($custome_thumbnail_name, '.gif');
                            if ($temp_taobao_gif > 0 && $temp_taobao_gif < $temp_taobal_length - 4) {
                                $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_gif).'.gif_250x250.jpg';
                            } else {
                                $custome_thumbnail_name = $custome_thumbnail_name.'_250x250.jpg';
                            }
                        }
                    }
                }
            } elseif ($width <= 310) {
                if($_temp > 0 && $_temp < $temp_taobal_length - 4){
                    $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $_temp).'_310x310.jpg';
                }else{
                    $temp_taobao = stripos($custome_thumbnail_name, '.jpg');
                    if ($temp_taobao > 0 && $temp_taobao < $temp_taobal_length - 4) {
                        $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao).'.jpg_310x310.jpg';
                    } else {
                        $temp_taobao_png = stripos($custome_thumbnail_name, '.png');
                        if ($temp_taobao_png > 0 && $temp_taobao_png < $temp_taobal_length - 4) {
                            $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_png).'.png_310x310.jpg';
                        } else {
                            $temp_taobao_gif = stripos($custome_thumbnail_name, '.gif');
                            if ($temp_taobao_gif > 0 && $temp_taobao_gif < $temp_taobal_length - 4) {
                                $custome_thumbnail_name = substr($custome_thumbnail_name, 0, $temp_taobao_gif).'.gif_310x310.jpg';
                            } else {
                                $custome_thumbnail_name = $custome_thumbnail_name.'_210x210.jpg';
                            }
                        }
                    }
                }
            }
        }
        // 亚马逊??
        else if (stripos($custome_thumbnail_name, 'images-amazon') != false) {
            if ($width <= 80) {
                    if (stripos($custome_thumbnail_name, '_SL1500_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL1500_', '_SL80_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL1000_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL1000_', '_SL80_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL500_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL500_', '_SL80_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL300_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL300_', '_SL80_', $custome_thumbnail_name);
                    }
                    if (stripos($custome_thumbnail_name, '_AA300_') != false) {
                        $custome_thumbnail_name = str_ireplace('_AA300_', '_AA80_', $custome_thumbnail_name);
                    }
                } elseif ($width <= 160) {
                    if (stripos($custome_thumbnail_name, '_SL1500_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL1500_', '_SL160_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL1000_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL1000_', '_SL160_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL500_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL500_', '_SL160_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL300_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL300_', '_SL160_', $custome_thumbnail_name);
                    }
                    if (stripos($custome_thumbnail_name, '_AA300_') != false) {
                        $custome_thumbnail_name = str_ireplace('_AA300_', '_AA160_', $custome_thumbnail_name);
                    }
                } elseif ($width <= 200) {
                    if (stripos($custome_thumbnail_name, '_SL1500_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL1500_', '_SL200_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL1000_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL1000_', '_SL200_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL500_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL500_', '_SL200_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL300_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL300_', '_SL200_', $custome_thumbnail_name);
                    }
                    if (stripos($custome_thumbnail_name, '_AA300_') != false) {
                        $custome_thumbnail_name = str_ireplace('_AA300_', '_AA200_', $custome_thumbnail_name);
                    }
                } elseif ($width <= 250) {
                    if (stripos($custome_thumbnail_name, '_SL1500_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL1500_', '_SL250_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL1000_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL1000_', '_SL250_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL500_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL500_', '_SL250_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL300_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL300_', '_SL250_', $custome_thumbnail_name);
                    }
                    if (stripos($custome_thumbnail_name, '_AA300_') != false) {
                        $custome_thumbnail_name = str_ireplace('_AA300_', '_AA250_', $custome_thumbnail_name);
                    }
                } elseif ($width <= 300) {
                    if (stripos($custome_thumbnail_name, '_SL1500_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL1500_', '_SL300_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL1000_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL1000_', '_SL300_', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, '_SL500_') != false) {
                        $custome_thumbnail_name = str_ireplace('_SL500_', '_SL300_', $custome_thumbnail_name);
                    }
                }
            }
            // 易迅网
            else if (stripos($custome_thumbnail_name, 'icson') != false) {
                if ($width <= 80) {
                    if (stripos($custome_thumbnail_name, 'mpic') != false) {
                        $custome_thumbnail_name = str_ireplace('mpic', 'small', $custome_thumbnail_name);
                    } elseif (stripos($custome_thumbnail_name, 'mm') != false) {
                        $custome_thumbnail_name = str_ireplace('mm', 'small', $custome_thumbnail_name);
                    }
                } elseif ($width <= 300) {
                    if (stripos($custome_thumbnail_name, 'mpic') != false) {
                        $custome_thumbnail_name = str_ireplace('mpic', 'mm', $custome_thumbnail_name);
                    }
                }
            }
        }
        return $custome_thumbnail_name;
    }

    /**
     * “每日签到”图片等比例缩放（长方形图片）
     *
     * @param   int      $thumb_size_height     缩放后高度
     * @param   int      $thumb_size_width      缩放后宽度
     * @param   int      $height                图片高度
     * @param   int      $width                 图片宽度
     * @return  string
     */
    function daily_pic_to_thumb_simple($thumb_size_height, $thumb_size_width, $height, $width) {
        if($thumb_size_width/$thumb_size_height > $width /$height){
            //以高度缩放
            if($height > $thumb_size_height){
                //高度为标准高度，宽度缩小，不用定上下居中
                return 'height="'.$thumb_size_height.'px" width="'.round(($width/$height)*$thumb_size_height).'px" style="margin-top:0px"';
            }else{
                //高度不够，需要计算居中位置，图片高度是他本身
                $margin = round((150 - $height)/2);
                return 'height="'.$height.'px" width="'.$width.'px" style="margin-top:'.round($margin).'px"' ;

            }
        }else{
            //以宽度缩放
            if($width > $thumb_size_width){
                //宽度为标准宽度，高度缩小，需要计算上下居中
                $new_height = round(($height/$width)*$thumb_size_width);
                $margin = round((150-$new_height)/2);
                return 'height="'.$new_height.'px" width="'.$thumb_size_width.'px" style="margin-top:'.round($margin).'px"';
            }else{
                //宽度不够，高度，需要计算居中位置，图片高度是他本身
                $margin = round((150-$height)/2);
                return 'height="'.$height.'px" width="'.$width.'px" style="margin-top:'.round($margin).'px"' ;
            }
        }
    }

}

