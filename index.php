<?php
ob_start();
include("config.php");

$url = preg_replace("/#g_/i","",$_SERVER["REQUEST_URI"]);
$g = new Http($env);
$f = new Filter($env);
$m = new Cache($env);

$u = $env["host"].$url;
$ui = parse_url($u);
if(!isset($ui["path"]))exit;
$upi = pathinfo($ui["path"]);
$upi["extension"] = split("/\?/",$upi["extension"],1)[0];
$ch = $m->get($u);
if($ch!==false){
    Log::debug("Fetching from cache [".$upi["extension"]."]".$u." ...");
    echo $ch;
    exit;
}
Log::debug("Fetching [".$upi["extension"]."]".$u." ...");
$g->fetch($env["host"].$url);
$h = $g->results;
if(!is_string($h)||!strlen(trim($h)))exit;
if(!in_array($upi["extension"],["png","svg","jpeg","jpg","gif","ico"])){
    Log::debug("Filtering media: ".$u);
    $h = $f->filter($h);
}
$m->save($u,$h);
Log::debug("warns data: ".ob_get_clean());
echo $h;
?>
