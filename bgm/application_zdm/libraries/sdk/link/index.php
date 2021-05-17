<?php


$deya_fashion = array(137,133,61,65,89,1693,69,145,589,135,59,63,139,67,143);

include_once("link_generate.php");
$link = new LinkGenerate();

$url = $_GET['url'];
$parm_tmp = array(
    'url' => $url,
    "platform" => "ca",
    "source" => "aa",
    "channel" => "ot",
    "channel" => "ot"
);

$link_info = $link->generate_url($parm_tmp);

print_r($link_info);