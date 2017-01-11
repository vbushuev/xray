<?php
/*
 * ServerAlias *.gauzymall.com
 * VirtualDocumentRoot /var/www/www/data/www/%0
 */
require_once("autoload.php");
date_default_timezone_set('Europe/Moscow');

$env = (isset($_REQUEST["restore"])&&$_REQUEST["restore"]==1&&file_exists("xray.json.bak"))?json_decode(file_get_contents("xray.json.bak"),true):json_decode(file_get_contents("xray.json"),true);

$env_bak = $env;
$html = new \g\Html([
    "title"=>"Administrator panel",
    "data" => $env
]);
foreach($_REQUEST as $k=>$v){
    if($k=="url")$v=preg_replace("/\/$/","",$v);
    $env[$k] = $v;
}
//if(!isset($_REQUEST["cookie"]))$env["cookie"]=[];
//if(!isset($_REQUEST["hacks"]["substitutions"]))$env["hacks"]["substitutions"]=[];
include("layout.php");
//file_put_contents("xray.json",json_encode($env,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE))
file_put_contents("xray.json",json_encode($env,JSON_PRETTY_PRINT));
file_put_contents("xray.json.bak",json_encode($env_bak,JSON_PRETTY_PRINT));
?>
