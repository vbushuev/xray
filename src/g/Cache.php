<?php
namespace g;
use \Log as Log;
use \g\G as G;
class Cache extends Fetcher4{
    protected $cache="cache/";
    protected $nocachext = ['ttf','woff'];
    protected $nocachpages = ['/basket','/cart'];
    public function fetch(){
        // checks
        if($this->checkNoCache())return false;

        $cf = $this->_filename($this->url);
        if(file_exists($cf)) {
            $r = json_decode(file_get_contents($cf),true);
            $this->headers= $r["headers"];
            Log::debug("use cache file for ".$this->url);
            return $r["data"];
        };
        return false;
    }
    public function save($h,$d){
        if($this->checkNoCache())return;
        $cf = $this->_filename($this->url);
        $out = [
            "headers" => $h,
            "data" => $d
        ];
        file_put_contents($cf,json_encode($out,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }
    protected function _filename($f){
        $f= htmlspecialchars_decode(urldecode($f));
        $donor = $this->_urlinfo["domain"];
        $f = preg_replace("/(http|https)?\:?(\/\/)?(www\.)?".preg_quote($donor,'/')."/i","",$f);
        //$f = preg_replace("/\?.+/i","",$f);
        $f = preg_replace("/^\//","",$f);
        $f = preg_replace("/\/$/","",$f);
        $f = preg_replace("/\/\/\:/","_",$f);
        $f = preg_replace("/\?.+/","",$f);
        if(strlen($f)>360)$f = substr($f,0,360);
        $dir = $this->cache."/".$donor."/";
        $pi = pathinfo($f);
        if(!isset($pi["extension"])||trim($pi["extension"])=="") $f.=".html";
        if(!strlen(trim($f)))$f="index.html";

        $ret = $dir.$f.".json";

        //if($pi["extension"]==""||preg_match("/\/$/",$ret))$ret=preg_replace("/\/$/","",$ret)."/index.html";
        $this->checkdir($ret);
        return $ret;
    }
    protected function checkdir($p){
        $pi = pathinfo($p);
        $dir = $pi["dirname"];
        if(
            !file_exists($dir)
            ||!is_dir($dir)
        ){
            Log::debug("mkdir ".$dir);
            mkdir($dir,0777,true);
        }
    }
    protected function checkNoCache(){
        if(in_array($this->_pathinfo["extension"],$this->nocachext))return true;
        foreach ($this->nocachpages as $page) {
            //Log::debug("preg_match /".preg_quote($page,'/')."/im ".$this->url." = ".preg_match("/".preg_quote($page,'/')."/im",$this->url));
            if(preg_match("/".preg_quote($page,'/')."/im",$this->url))return true;
        }
        $client_headers = $this->getallheaders();
        //Log::debug("Headers:");Log::debug($client_headers);
        if(isset($client_headers["X-Requested-With"])|| (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ) return true;
        Log::debug("use cache for ".$this->url);
        return false;
    }
}
?>
