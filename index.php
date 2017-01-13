<?php
/*
 * ServerAlias *.gauzymall.com
 * VirtualDocumentRoot /var/www/www/data/www/%0
 */
require_once("autoload.php");
date_default_timezone_set('Europe/Moscow');
session_start();
ob_start();
use \g\Fetcher4 as Fetcher;
use \g\Enviroment as Enviroment;
use \g\Filter as Filter;
use \Log as Log;
use \g\Translator as Translator;
$env = json_decode(file_get_contents("xray.json"),true);
$Enviroment = new Enviroment($env);
$Fetcher = new Fetcher($Enviroment->url);
$Fetcher->cookie = $Enviroment->cookie;
$Filter = new Filter($Enviroment);
$Translator = new Translator(["lang"=>"fr"]);
$data = $Fetcher->fetch();
$data = $Filter->fetch($data,$Fetcher->headers["Content-Type"]);
if(preg_match("'text/html'ixs",$Fetcher->headers["Content-Type"])){
    $data = $Translator->translateHtml($data);
}
if($Enviroment->greenline["show"]=="1" || $Enviroment->greenline["show"] == 'true'  || $Enviroment->greenline["show"] == 'true')$data = preg_replace("/\<\/body>/i","<script src='/js/x.js'></script></body>",$data);
Log::debug(ob_get_clean());
$Fetcher->pull($data);
?>
