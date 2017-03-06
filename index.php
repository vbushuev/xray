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
/*?>
<script>
    if(typeof(window.xr_g_loader)=="undefined" || window.xr_g_loader==null){
        var xgl = document.createElement("div");
        xgl.setAttribute("id","xr_g_cover_layer");
        xgl.setAttribute("style","position:fixed;top:0;left:0;background-color: rgba(255,255,255,.94);width:100%;height:100%;z-index: 9999;transition: all .4s ease-in;");
        var img = document.createElement("img");
        img.setAttribute("src","/css/loader.gif");
        img.setAttribute("style","position:fixed;top:0;left:-64px;margin: 30% 50%;z-index: 10000;");
        xgl.appendChild(img);
        document.body.appendChild(xgl);
        window.xr_g_loader = xgl;
    }
</script>

<?php
ob_flush();*/

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
            $v = $t->translateText($v);
        },$Translator)){
            $data = json_encode($jdata);
        }

    }
    $monstat.="\tpage translated in ".(time()-$tick)."\n";
}
if($Enviroment->greenline["show"]=="1" || $Enviroment->greenline["show"] == 'true'  || $Enviroment->greenline["show"] == 'true') {
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
Log::debug(ob_get_clean());
$Fetcher->pull($data);
$monstat.="\tall page [".$Enviroment->url."] in ".(time()-$tick)." seconds.\n";
//Log::debug($monstat);
?>
