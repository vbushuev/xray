<?php
session_start();
ob_start();
include("config.php");

$url = preg_replace("/#g_/i","",$_SERVER["REQUEST_URI"]);
$cfg = new Config($env);
$g = new Http($cfg);
$f = new Filter($cfg);
$c = new Cache($cfg);
//Log::debug("cfg:".json_encode($cfg));
$url = ($url=="/")?"":$url;
$u = $cfg->host.$url;
$ui = parse_url($u);
//if(!isset($ui["path"]))exit;
$upi = isset($ui["path"])?pathinfo($ui["path"]):[];
$ext = (isset($upi["extension"]))?preg_split("/\?/",$upi["extension"],1)[0]:"";
$ch = $c->get($u);
//Log::debug("Fetching ".$u." ...");
if(in_array($ext,["js","css","png","svg","jpeg","jpg","gif","ico","swg"]) && $ch!==false && strlen($ch) ){
    $h = $ch;
}
else {
    $h = $g->fetch($u);
    //$h = $g->results;
    //Log::debug(json_encode($g->response,JSON_PRETTY_PRINT));
    //if(!is_string($h)||!strlen(trim($h)))exit;
    if(!in_array($ext,["png","svg","jpeg","jpg","gif","ico","ttf","woff"])){
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
switch($ext){
    case "css": header('Content-Type: text/css');break;
    case "png": header('Content-Type: image/png');break;
    case "jpg": header('Content-Type: image/jpg');break;
    case "gif": header('Content-Type: image/gif');break;
    case "svg": header('Content-Type: image/svg+xml');break;
    case "js": header('Content-Type: text/javascript');break;
    default:
        header('Content-Type: text/html');
        $g->inCookie();
        if(file_exists("js/".$cfg->js))$h = preg_replace("/\<\/body>/i","<script src='/js/".$cfg->js."'></script></body>",$h);
        if(file_exists("css/".$cfg->css))$h = preg_replace("/\<\/body>/i","<link href='/css/".$cfg->css."' rel='stylesheet'/></body>",$h);
        if(file_exists("templates/".$cfg->template))$h = preg_replace("/\<\/body>/i",file_get_contents("templates/".$cfg->template)."</body>",$h);
        $h = preg_replace("/\<\/body>/i",file_get_contents("templates/analytics.php")."</body>",$h);


    break;
}
echo $h;
/*
todo
убрать кнопку. "Добавить в мульти корзину". и написать что происходит
*/
?>
