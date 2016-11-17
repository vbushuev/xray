<?php
session_start();
ob_start();
include("config.php");

$url = preg_replace("/#g_/i","",$_SERVER["REQUEST_URI"]);
$cfg = new Config($env);
$g = new Http($cfg);
$f = new Filter($cfg);
$c = new Cache($cfg);
$tr = new Translator($cfg);
$url = ($url=="/")?"":$url;
$u = $cfg->host.$url;
$ui = parse_url($u);
$upi = isset($ui["path"])?pathinfo($ui["path"]):[];
$ext = (isset($upi["extension"]))?preg_split("/\?/",$upi["extension"],1)[0]:"";
//$ch = $c->get($u);
//$cached = false;

//if($ch!==false&& strlen($ch)&& $cfg->use_cache&& in_array($ext,["js","css","png","svg","jpeg","jpg","gif","ico","swg"])){$h = $ch;$cached=true;}

$h = $g->fetch($u);
if(!in_array($ext,["png","svg","jpeg","jpg","gif","ico","ttf","woff"])){
    $h = $f->filter($h);
    //$h = $tr->translate($h);
}
//$c->save($u,$h);

$ob_buffer = ob_get_clean();
if(strlen($ob_buffer))Log::debug("warns data: ".$ob_buffer);
/*
switch($ext){
    case "css": header('Content-Type: text/css');break;
    case "png": header('Content-Type: image/png');break;
    case "jpg": header('Content-Type: image/jpg');break;
    case "gif": header('Content-Type: image/gif');break;
    case "svg": header('Content-Type: image/svg+xml');break;
    case "js": header('Content-Type: text/javascript');break;
    default:
        header('Content-Type: text/html');
        $g->inHeaders();
        $g->inCookie();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])&&$_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'){Log::debug("Ajax request - detected");}
        elseif (in_array($ext,["axd"])){Log::debug("Ajax data");}
        else{
            $h = preg_replace("/\<\/body>/i",file_get_contents("templates/styles.php")."</body>",$h);
            //if(file_exists("css/".$cfg->css))$h = preg_replace("/\<\/body>/i","<link href='/css/".$cfg->css."' rel='stylesheet'/></body>",$h);
            if(file_exists("templates/".$cfg->template))$h = preg_replace("/\<\/body>/i",file_get_contents("templates/".$cfg->template)."</body>",$h);
            if(file_exists("js/".$cfg->js))$h = preg_replace("/\<\/body>/i","<script src='/js/".$cfg->js."'></script></body>",$h);
            if(file_exists("js/".$cfg->section.".goals.js"))$h = preg_replace("/\<\/body>/i","<script src='/js/".$cfg->section.".goals.js'></script></body>",$h);
            $h = preg_replace("/\<\/body>/i",file_get_contents("templates/snooper.php")."</body>",$h);
        }
    break;
}*/
$g->inHeaders();
$g->inCookie();
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])&&$_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest'){Log::debug("Ajax request - detected");}
elseif (in_array($ext,["axd"])){Log::debug("Ajax data");}
else{
    $h = preg_replace("/\<\/body>/i",file_get_contents("templates/styles.php")."</body>",$h);
    //if(file_exists("css/".$cfg->css))$h = preg_replace("/\<\/body>/i","<link href='/css/".$cfg->css."' rel='stylesheet'/></body>",$h);
    if(file_exists("templates/".$cfg->template))$h = preg_replace("/\<\/body>/i",file_get_contents("templates/".$cfg->template)."</body>",$h);
    if(file_exists("js/".$cfg->js))$h = preg_replace("/\<\/body>/i","<script src='/js/".$cfg->js."'></script></body>",$h);
    if(file_exists("js/".$cfg->section.".goals.js"))$h = preg_replace("/\<\/body>/i","<script src='/js/".$cfg->section.".goals.js'></script></body>",$h);
    $h = preg_replace("/\<\/body>/i",file_get_contents("templates/snooper.php")."</body>",$h);
}
echo $h;
?>
