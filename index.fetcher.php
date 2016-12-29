<?php
/*
 * ServerAlias *.gauzymall.com
 * VirtualDocumentRoot /var/www/www/data/www/%0
 */
require_once("autoload.php");
date_default_timezone_set('Europe/Moscow');

$env = json_decode(file_get_contents("xray.json"),true);
$f = new \g\Fetcher($env);
$content =  $f->get();
if($env["greenline"]["show"]=="1" || $env["greenline"]["show"] == 'true'  || $env["greenline"]["show"] == 'true')$content = preg_replace("/\<\/body>/i","<script src='/js/x.js#".$url."'></script></body>",$content);
$f->pull($content);
?>
