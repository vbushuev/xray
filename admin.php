<?php
/*
 * ServerAlias *.gauzymall.com
 * VirtualDocumentRoot /var/www/www/data/www/%0
 */
require_once("autoload.php");
date_default_timezone_set('Europe/Moscow');

$env = json_decode(file_get_contents("xray.json"),true);

$html = new \g\Html([
    "title"=>"Administrator panel",
    "data" => $env
]);
foreach($_REQUEST as $k=>$v){
    if($k=="url")$v=preg_replace("/\/$/","",$v);
    $env[$k] = $v;
}
include("layout.php");
//file_put_contents("xray.json",json_encode($env,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE))
file_put_contents("xray.json",json_encode($env,JSON_PRETTY_PRINT))
?>
