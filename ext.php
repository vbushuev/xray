<?php
/*
 * ServerAlias *.gauzymall.com
 * VirtualDocumentRoot /var/www/www/data/www/%0
 */
require_once("autoload.php");
date_default_timezone_set('Europe/Moscow');
use \g\Fetcher3 as Fetcher;
use \g\Enviroment as Enviroment;
use \g\Filter as Filter;
use \Log as Log;

$env = json_decode(file_get_contents("xray.json"),true);
$Enviroment = new Enviroment($env);
$Fetcher = new Fetcher($Enviroment->url);
$data = $Fetcher->fetch();

//$pattern = "/([\=\"'\s]+\.?)".preg_quote($Enviroment->domain)."/ixsm";
//$data = preg_replace_callback($pattern,function($m)use($Enviroment){$res = $m[1].$Enviroment->localhost;Log::debug("filter: [".$m[0]."] >> [".$res."]");return $res;},$data);

Log::debug($Enviroment->url);
Log::debug($Fetcher->headers["Content-Type"]);
if(preg_match("'javascript'ixs",$Fetcher->headers["Content-Type"])){
    $pattern = "/(http|https)\:?\/{0,2}".preg_quote($Enviroment->mainhost,'/')."/ixsm";
    $data = preg_replace_callback($pattern,function($m)use($Enviroment){$res = "//".$Enviroment->localhost; Log::debug("filter: [".$m[0]."] >> [".$res."]");return $res;},$data);
    $pattern = "/".preg_quote($Enviroment->mainhost,'/')."/ixsm";
    $data = preg_replace_callback($pattern,function($m)use($Enviroment){$res = $Enviroment->localhost; Log::debug("filter: [".$m[0]."] >> [".$res."]");return $res;},$data);
    $pattern = "/([\=\"'\s]+\.?)".preg_quote($Enviroment->domain)."/ixsm";
    $data = preg_replace_callback($pattern,function($m)use($Enviroment){$res = $m[1].$Enviroment->localhost;Log::debug("filter: [".$m[0]."] >> [".$res."]");return $res;},$data);

}
$Fetcher->pull($data);
?>
