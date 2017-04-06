<?php
namespace g;
use \Log as Log;
class Filter{
    protected $_enviroment;
    protected $s;
    public function __construct($env){
        $this->_enviroment = $env;
    }
    public function fetch($data = "",$contentType="text/html",$fileName = "content"){
        $use_filters = [
            "1"=>["use"=>true],
            "2"=>["use"=>true],
            "3"=>["use"=>true],
            "4"=>["use"=>false],
            "5"=>["use"=>false],
            "6"=>["use"=>false],
            "7"=>["use"=>true],
            "8"=>["use"=>true],
            "9"=>["use"=>false],
        ];

        $t = $this;
        if(preg_match("'text/html|application/json'ixs",$contentType)){
            $data = preg_replace("/<html(.*)?lang=(['\"])(\D+?)(['\"])/",'<html$1lang="en"',$data);
            /** debug for make classes*/
            // filter 1
            $pattern = "/(http|https)\:?\/{0,2}".preg_quote($t->_enviroment->mainhost,'/')."/ixsm";
            if($use_filters["1"]["use"])$data = preg_replace_callback($pattern,function($m)use($t){$res = "//".$t->_enviroment->localhost; Log::debug("filter[1] ".$fileName.": [".$m[0]."] >> [".$res."]");return $res;},$data);
            // filter 2
            $pattern = "/".preg_quote($t->_enviroment->mainhost,'/')."/ixsm";
            if($use_filters["2"]["use"])$data = preg_replace_callback($pattern,function($m)use($t){$res = $t->_enviroment->localhost; Log::debug("filter[2] ".$fileName.": [".$m[0]."] >> [".$res."]");return $res;},$data);
            // filter 3
            $pattern = "/([\=\"'\s]+\.?)".preg_quote($t->_enviroment->domain)."/ixsm";
            if($use_filters["3"]["use"])$data = preg_replace_callback($pattern,function($m)use($t){$res = $m[1].$t->_enviroment->localhost;Log::debug("filter[3] ".$fileName.": [".$m[0]."] >> [".$res."]");return $res;},$data);
            // filter 4
            $pattern = "/http(s)?:\/{2}[^\s\?\"'\>]+\.js[^\"'\s\>]*/ixs";
            if($use_filters["4"]["use"])$data = preg_replace_callback($pattern,function($m)use($t){
                $res = $m[0];
                if(
                    !preg_match("/".preg_quote($t->_enviroment->localhost)."/ixs",$m[0]) &&
                    !preg_match("/jquery/ixs",$m[0]) &&
                    !preg_match("/\.googleanalytics/ixs",$m[0])
                ){

                    $res = "//".$t->_enviroment->localhost."/ext.php?".REQUEST_PARAMETER_NAME."=".urlencode($m[0]);
                    Log::debug("filter[4] ".$fileName.": [".$m[0]."] >> [".$res."]");
                }
                return $res;},$data);
            // filter 5
            $pattern = "/\/{2}[^\s\?\"']+?\.js[^\"'\s\>]*/ixs";
            if($use_filters["5"]["use"])$data = preg_replace_callback($pattern,function($m)use($t){
                $res = $m[0];
                if(
                    !preg_match("/".preg_quote($t->_enviroment->localhost)."/ixs",$m[0]) &&
                    !preg_match("/jquery/ixs",$m[0]) &&
                    !preg_match("/\.google/ixs",$m[0])
                ){
                    $res = "//".$t->_enviroment->localhost."/ext.php?".REQUEST_PARAMETER_NAME."=".urlencode($t->_enviroment->localschema.":".$m[0]);
                    Log::debug("filter[5] ".$fileName.": [".$m[0]."] >> [".$res."]");
                }
                return $res;},$data);
            // filter 6
            $pattern = "/([a-z0-9\.]+)".preg_quote($t->_enviroment->domain)."/ixsm";
            if($use_filters["6"]["use"])$data = preg_replace_callback($pattern,function($m)use($t){
                $res = $m[0];
                if(
                    !preg_match("/".preg_quote($t->_enviroment->localhost)."/ixs",$m[0]) &&
                    !preg_match("/jquery/ixs",$m[0]) &&
                    !preg_match("/\.google/ixs",$m[0])
                ){
                    $res = $t->_enviroment->localhost."/ext.php?".REQUEST_PARAMETER_NAME."=".urlencode($m[0]);
                    Log::debug("filter[6] ".$fileName.": [".$m[0]."] >> [".$res."]");
                }
                return $res;},$data);
            // filter 7
            //$pattern = "/([a-z0-9\-]+)\.".preg_quote($t->_enviroment->domain,'/')."/ixsm";
            $pattern = "/((http|https)\:?\/{0,2})([a-z0-9\-]+)\.".preg_quote($t->_enviroment->domain,'/')."/ixsm";
            if($use_filters["7"]["use"])$data = preg_replace_callback($pattern,function($m)use($t){
                $res = $m[0];
                $res = "//".$m[3].".".$t->_enviroment->localhost;
                Log::debug("filter[7] ".$fileName.": [".$m[0]."] >> [".$res."]");
                return $res;},$data);

        }
        else if(preg_match("'javascript'ixs",$contentType)){
            // filter 8
            $pattern = "/".preg_quote($t->_enviroment->domain,'/')."/ixsm";
            if($use_filters["8"]["use"])$data = preg_replace_callback($pattern,function($m)use($t){
                $res = $m[0];
                $res = $t->_enviroment->localhost;
                Log::debug("filter[8] ".$fileName.": [".$m[0]."] >> [".$res."]");
                return $res;},$data);

            $pattern = "/https\:\/\/(.+?)".preg_quote($t->_enviroment->localhost,'/')."/ixsm";
            if($use_filters["9"]["use"])$data = preg_replace_callback($pattern,function($m)use($t){
                $res = $m[0];
                $res = "//".$m[1].$t->_enviroment->localhost;
                Log::debug("filter[9] ".$fileName.": [".$m[0]."] >> [".$res."]");
                return $res;},$data);
        }
        if(isset($t->_enviroment->hacks["substitutions"])){
            foreach($t->_enviroment->hacks["substitutions"] as $o=>$s){
                $data = preg_replace("/".preg_quote($o,'/')."/ixs",$s,$data);
            }
        }
        return $data;
    }
};
?>
