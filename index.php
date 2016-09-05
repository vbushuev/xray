<?php
ob_start();
include("config.php");

$url = preg_replace("/#g_/i","",$_SERVER["REQUEST_URI"]);
$cfg = new Config($env);
$g = new Http($cfg);
$f = new Filter($cfg);
$c = new Cache($cfg);
//Log::debug("cfg:".json_encode($cfg));
$u = $cfg->host.$url;
$ui = parse_url($u);
if(!isset($ui["path"]))exit;
$upi = pathinfo($ui["path"]);
$ext = (isset($upi["extension"]))?preg_split("/\?/",$upi["extension"],1)[0]:"";
$ch = false;
if(in_array($ext,["js","css","png","svg","jpeg","jpg","gif","ico","swg"])){
    $ch = $c->get($u);
}
if($ch!==false){
    Log::debug("Fetching from cache [".$ext."] ".$u." ...");
    $h = $ch;
}
else {
    //Log::debug("Fetching from host [".$ext."] ".$u." ...");
    $g->fetch($cfg->host.$url);
    $h = $g->results;
    //Log::debug(json_encode($g->response,JSON_PRETTY_PRINT));
    //if(!is_string($h)||!strlen(trim($h)))exit;
    if(!in_array($ext,["png","svg","jpeg","jpg","gif","ico"])){
        //Log::debug("Filtering: [".$ext."] ".$u);
        $h = $f->filter($h);
    }
    $c->save($u,$h);
}
$ob_buffer = ob_get_clean();
if(strlen($ob_buffer))Log::debug("warns data: ".$ob_buffer);
//header('Cache-Control: no-cache, no-store, must-revalidate');
//header('Pragma: no-cache');
//header('Expires: 0');
//header('Set-Cookie: '.$g->getcookies());
$g->inCookie();
switch($ext){
    case "css": header('Content-Type: text/css');break;
    case "png": header('Content-Type: image/png');break;
    case "jpg": header('Content-Type: image/jpg');break;
    case "gif": header('Content-Type: image/gif');break;
    case "svg": header('Content-Type: image/svg+xml');break;
    default:    header('Content-Type: text/html');break;
}
echo $h
?>
