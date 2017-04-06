<?php

/*
 * ServerAlias *.gauzymall.com
 * VirtualDocumentRoot /var/www/www/data/www/%0
 */
require_once("autoload.php");

date_default_timezone_set('Europe/Moscow');
ini_set('max_execution_time', 60*60*12);
ob_start();
session_start();

use \g\Fetcher4 as Fetcher;
use \g\Enviroment as Enviroment;
use \g\Filter as Filter;
use \Log as Log;
use \g\Translator as Translator;
use \g\Cache as Cache;
$tick = time();
$monstat = "Time stat (".date("H:i:s")."):\n";

$env = json_decode(file_get_contents("xray.json"),true);
$Enviroment = new Enviroment($env);
$Fetcher = new Cache($Enviroment->url);

$Fetcher->cookie = $Enviroment->cookie;
$Filter = new Filter($Enviroment);
$Translator = new Translator($Enviroment->translate);
$monstat.="\tdictionary loaded in ".(time()-$tick)."\n";

$data = $Fetcher->fetch();
Log::debug(($data===false)?"no cache data":"from cache file");
Log::debug(($env->cache===false)?"no cache use option":$env->cache);
if($data===false || $env->cache["use"]===false || $env->cache["use"]=="false" || $env->cache["use"] =="0"){
    Log::debug("NOT USING CACHE:");
    $Cacher = $Fetcher;
    $Fetcher = new Fetcher($Enviroment->url);
    $data = $Fetcher->fetch();
    $monstat.="\tpage fetched in ".(time()-$tick)."\n";
    $data = $Filter->fetch($data,isset($Fetcher->headers["Content-Type"])?$Fetcher->headers["Content-Type"]:"all",$env->url);
    $monstat.="\tpage filtered in ".(time()-$tick)."\n";
    if($Enviroment->translate["use"]=="true"){
        if(preg_match("'text/html'ixs",$Fetcher->headers["Content-Type"])){
            $data = $Translator->translateHtml($data);
            //$data = $Translator->translateText($data);
        }
        if(preg_match("'application/json'ixs",$Fetcher->headers["Content-Type"])){
            $jdata = json_decode($data,true);
            if(array_walk_recursive($jdata,function(&$v,$k,$t){
                $v = $t->translateText($v,false);
            },$Translator)){
                $data = json_encode($jdata);
            }

        }
        $monstat.="\tpage translated in ".(time()-$tick)."\n";
    }
    if($Enviroment->greenline["show"]=="1" || $Enviroment->greenline["show"] == 'true' ) {
        //$data = preg_replace("/<body([^>]*)>/i","<body$1>".'<script src="js/cover.js"></script>',$data);
        //if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest"){
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) ){
            Log::debug("Ajax request - no greenline");
        }
        else if(preg_match("'text/html'ixs",$Fetcher->headers["Content-Type"])){
            $data = preg_replace("/<body([^>]*)>/i","<body $1>".file_get_contents('css/cover.html'),$data);
            $data = preg_replace("/\<\/body>/i","<script src='/js/x.js'></script></body>",$data);
        }
        $monstat.="\tgreenline added in ".(time()-$tick)."\n";
    }
    if($Enviroment->script["use"]=="true" ){
        Log::debug($Enviroment->script["name"]);
        $data = preg_replace("/\<\/body>/i","<script src='".$Enviroment->script["name"]."'></script></body>",$data);
        //$data = preg_replace("/\<\/body>/i",file_get_contents("css/com.html")."</body>",$data);
    }
    $Cacher->save($Fetcher->headers,$data);
}
Log::debug(ob_get_clean());
$Fetcher->pull($data);
$monstat.="\tall page [".$Enviroment->url."] in ".(time()-$tick)." seconds.\n";
Log::debug($monstat);
?>
