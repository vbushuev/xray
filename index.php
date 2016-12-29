<?php
/*
 * ServerAlias *.gauzymall.com
 * VirtualDocumentRoot /var/www/www/data/www/%0
 */
require_once("autoload.php");
date_default_timezone_set('Europe/Moscow');
session_start();
use \g\Fetcher4 as Fetcher;
use \g\Enviroment as Enviroment;
use \g\Filter as Filter;
use \Log as Log;

$env = json_decode(file_get_contents("xray.json"),true);
$Enviroment = new Enviroment($env);
$Fetcher = new Fetcher($Enviroment->url);
$Filter = new Filter($Enviroment);
$data = $Fetcher->fetch();
$use_filters = [
    "1"=>["use"=>true,"pattern"=>"/(http|https)\:?\/{0,2}".preg_quote($Enviroment->mainhost,'/')."/ixsm","replacement"=>"//".$Enviroment->localhost],
    "2"=>["use"=>true,"pattern"=>"/".preg_quote($Enviroment->mainhost,'/')."/ixsm","replacement"=>$Enviroment->localhost],
    "3"=>["use"=>true,"pattern"=>"/([\=\"'\s]+\.?)".preg_quote($Enviroment->domain)."/ixsm","replacement"=>"$1".$Enviroment->localhost],
    "4"=>["use"=>false,"pattern"=>"/http(s)?:\/{2}[^\s\?\"'\>]+\.js[^\"'\s\>]*/ixs","replacement"=>"//".$Enviroment->localhost."/ext.php?".REQUEST_PARAMETER_NAME."=$0"],
    "5"=>["use"=>false,"pattern"=>"/\/{2}[^\s\?\"']+?\.js[^\"'\s\>]*/ixs","replacement"=>"//".$Enviroment->localhost."/ext.php?".REQUEST_PARAMETER_NAME."=".urlencode($Enviroment->localschema.":".$m[0])],
    "6"=>["use"=>false,"pattern"=>"/([a-z0-9\.]+)".preg_quote($Enviroment->domain)."/ixsm","replacement"=>$Enviroment->localhost."/ext.php?".REQUEST_PARAMETER_NAME."=".urlencode($m[0])],
    "7"=>["use"=>true,"pattern"=>"/([\"'][^\.]+)\.".preg_quote($Enviroment->domain,'/')."/ixsm","replacement"=>"$1.".$Enviroment->localhost],
    "8"=>["use"=>true,"pattern"=>"/".preg_quote($Enviroment->domain,'/')."/ixsm","replacement"=>$Enviroment->localhost],
];
if(preg_match("'text/html'ixs",$Fetcher->headers["Content-Type"])){
    /** debug for make classes*/
    // filter 1
    $pattern = "/(http|https)\:?\/{0,2}".preg_quote($Enviroment->mainhost,'/')."/ixsm";
    if($use_filters["1"]["use"])$data = preg_replace_callback($pattern,function($m)use($Enviroment){$res = "//".$Enviroment->localhost; Log::debug("filter[1]: [".$m[0]."] >> [".$res."]");return $res;},$data);
    // filter 2
    $pattern = "/".preg_quote($Enviroment->mainhost,'/')."/ixsm";
    if($use_filters["2"]["use"])$data = preg_replace_callback($pattern,function($m)use($Enviroment){$res = $Enviroment->localhost; Log::debug("filter[2]: [".$m[0]."] >> [".$res."]");return $res;},$data);
    // filter 3
    $pattern = "/([\=\"'\s]+\.?)".preg_quote($Enviroment->domain)."/ixsm";
    if($use_filters["3"]["use"])$data = preg_replace_callback($pattern,function($m)use($Enviroment){$res = $m[1].$Enviroment->localhost;Log::debug("filter[3]: [".$m[0]."] >> [".$res."]");return $res;},$data);
    // filter 4
    $pattern = "/http(s)?:\/{2}[^\s\?\"'\>]+\.js[^\"'\s\>]*/ixs";
    if($use_filters["4"]["use"])$data = preg_replace_callback($pattern,function($m)use($Enviroment){
        $res = $m[0];
        if(
            !preg_match("/".preg_quote($Enviroment->localhost)."/ixs",$m[0]) &&
            !preg_match("/jquery/ixs",$m[0]) &&
            !preg_match("/\.googleanalytics/ixs",$m[0])
        ){

            $res = "//".$Enviroment->localhost."/ext.php?".REQUEST_PARAMETER_NAME."=".urlencode($m[0]);
            Log::debug("filter[4]: [".$m[0]."] >> [".$res."]");
        }
        return $res;},$data);
    // filter 5
    $pattern = "/\/{2}[^\s\?\"']+?\.js[^\"'\s\>]*/ixs";
    if($use_filters["5"]["use"])$data = preg_replace_callback($pattern,function($m)use($Enviroment){
        $res = $m[0];
        if(
            !preg_match("/".preg_quote($Enviroment->localhost)."/ixs",$m[0]) &&
            !preg_match("/jquery/ixs",$m[0]) &&
            !preg_match("/\.google/ixs",$m[0])
        ){
            $res = "//".$Enviroment->localhost."/ext.php?".REQUEST_PARAMETER_NAME."=".urlencode($Enviroment->localschema.":".$m[0]);
            Log::debug("filter[5]: [".$m[0]."] >> [".$res."]");
        }
        return $res;},$data);
    // filter 6
    $pattern = "/([a-z0-9\.]+)".preg_quote($Enviroment->domain)."/ixsm";
    if($use_filters["6"]["use"])$data = preg_replace_callback($pattern,function($m)use($Enviroment){
        $res = $m[0];
        if(
            !preg_match("/".preg_quote($Enviroment->localhost)."/ixs",$m[0]) &&
            !preg_match("/jquery/ixs",$m[0]) &&
            !preg_match("/\.google/ixs",$m[0])
        ){
            $res = $Enviroment->localhost."/ext.php?".REQUEST_PARAMETER_NAME."=".urlencode($m[0]);
            Log::debug("filter[6]: [".$m[0]."] >> [".$res."]");
        }
        return $res;},$data);
    // filter 7
    $pattern = "/([\"'][a-z0-9\-]+)\.".preg_quote($Enviroment->domain,'/')."/ixsm";
    if($use_filters["7"]["use"])$data = preg_replace_callback($pattern,function($m)use($Enviroment){
        $res = $m[0];
        $res = $m[1].".".$Enviroment->localhost;
        Log::debug("filter[7]: [".$m[0]."] >> [".$res."]");
        return $res;},$data);

}
else if(preg_match("'javascript'ixs",$Fetcher->headers["Content-Type"])){
    // filter 8
    $pattern = "/".preg_quote($Enviroment->domain,'/')."/ixsm";
    if($use_filters["7"]["use"])$data = preg_replace_callback($pattern,function($m)use($Enviroment){
        $res = $m[0];
        $res = $Enviroment->localhost;
        Log::debug("filter[8]: [".$m[0]."] >> [".$res."]");
        return $res;},$data);
    $pattern = "/https\:\/\/(.+?)".preg_quote($Enviroment->localhost,'/')."/ixsm";
    $data = preg_replace_callback($pattern,function($m)use($Enviroment){
        $res = $m[0];
        $res = "//".$m[1].$Enviroment->localhost;
        Log::debug("filter[8.1]: [".$m[0]."] >> [".$res."]");
        return $res;},$data);
}
foreach($Enviroment->hacks["substitutions"] as $o=>$s){
    $data = preg_replace("/".preg_quote($o,'/')."/ixs",$s,$data);
}
$Fetcher->pull($data);
?>
