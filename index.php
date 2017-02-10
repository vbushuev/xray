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
$tick = time();
$monstat = "Time stat (".date("H:i:s")."):\n";
$env = json_decode(file_get_contents("xray.json"),true);
$Enviroment = new Enviroment($env);
$Fetcher = new Fetcher($Enviroment->url);
$Fetcher->cookie = $Enviroment->cookie;
$Filter = new Filter($Enviroment);
$Translator = new Translator($Enviroment->translate);
$monstat.="\tdictionary loaded in ".(time()-$tick)."\n";
$data = $Fetcher->fetch();
$monstat.="\tpage fetched in ".(time()-$tick)."\n";
$data = $Filter->fetch($data,isset($Fetcher->headers["Content-Type"])?$Fetcher->headers["Content-Type"]:"all");
$monstat.="\tpage filtered in ".(time()-$tick)."\n";
if($Enviroment->translate["use"]=="true"){
    if(preg_match("'text/html'ixs",$Fetcher->headers["Content-Type"])){
        $data = $Translator->translateHtml($data);
        //$data = $Translator->translateText($data);
        $monstat.="\tpage translated in ".(time()-$tick)."\n";
    }
    if(preg_match("'application/json'ixs",$Fetcher->headers["Content-Type"])){
        $jdata = json_decode($data,true);
        if(array_walk_recursive($jdata,function(&$v,$k,$t){
            $v = $t->translateHtml($v);
        },$Translator)){
            $data = json_encode($jdata);
        }

        $monstat.="\tpage translated in ".(time()-$tick)."\n";
    }
}
if($Enviroment->greenline["show"]=="1" || $Enviroment->greenline["show"] == 'true'  || $Enviroment->greenline["show"] == 'true') {
    //$data = preg_replace("/<body([^>]*)>/i","<body$1>".'<script src="js/cover.js"></script>',$data);
    $data = preg_replace("/<body([^>]*)>/i","<body$1>".file_get_contents('css/cover.html'),$data);
    $data = preg_replace("/\<\/body>/i","<script src='/js/x.js'></script></body>",$data);
    $monstat.="\tgreenline added in ".(time()-$tick)."\n";
}
Log::debug(ob_get_clean());
$Fetcher->pull($data);
$monstat.="\tall page [".$Enviroment->url."] in ".(time()-$tick)." seconds.\n";
Log::debug($monstat);
?>
