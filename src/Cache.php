<?php
class Cache {
    protected $cache="cache/";
    protected $donor;
    protected $donor_pattern;
    public function __construct($cfg){
        $this->donor = $cfg->donor;
        $this->donor_pattern = $cfg->donor_pattern;
        $this->cache = $cfg->cache;

        if(!is_dir($this->cache))mkdir($this->cache);
        if(!is_dir($this->cache."/".$this->donor))mkdir($this->cache."/".$this->donor);
        //if(!is_dir($this->cache."/".$this->donor."/css"))mkdir($this->cache."/".$this->donor."/css");
        //if(!is_dir($this->cache."/".$this->donor."/img"))mkdir($this->cache."/".$this->donor."/img");
        //if(!is_dir($this->cache."/".$this->donor."/js"))mkdir($this->cache."/".$this->donor."/js");
    }
    public function get($f){
        $cf = $this->_filename($f);
        if(file_exists($cf)) return file_get_contents($cf);
        return false;
    }
    public function save($f,$d){
        $cf = $this->_filename($f);
        file_put_contents($cf,$d);
    }
    protected function _filename($f){
        $f= htmlspecialchars_decode(urldecode($f));
        $f = preg_replace("/(http|https)?\:?(\/\/)?(www\.)?".$this->donor_pattern."/i","",$f);
        //$f = preg_replace("/\?.+/i","",$f);
        $f = preg_replace("/^\//","",$f);
        $f = preg_replace("/\/$/","",$f);
        $f = preg_replace("/\/\/\:/","_",$f);
        $f = preg_replace("/\?.+/","",$f);
        if(strlen($f)>360)$f = substr($f,0,360);
        $dir = $this->cache."/".$this->donor."/";
        $pi = pathinfo($f);
        if(!isset($pi["extension"])||trim($pi["extension"])=="") $f.=".html";
        if(!strlen(trim($f)))$f="index.html";

        $ret = $dir.$f;

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
    protected function _filename2($f){
        $f = preg_replace("/(www\.)?".$this->donor_pattern."/i","",$f);
        $f = preg_replace("/\?.+/i","",$f);
        $pi = pathinfo($f);
        if(!isset($pi["extension"])||trim($pi["extension"])==""){
            $pi = pathinfo($f.".html");
        }
        //$pi["extension"] = preg_replace("/\?.+/i","",$pi["extension"]);
        $dir = $this->cache."/".$this->donor."/";

        if(isset($pi["extension"])){
            if($pi["extension"]=="js"){$dir.="js/";}
            elseif(in_array($pi["extension"],["css","map"])){$dir.="css/";}
            elseif(in_array($pi["extension"],["jpg","gif","png","ico","svg","jpeg"])){$dir.="img/";}
            elseif(in_array($pi["extension"],["ttf","woft"])){$dir.="css/";}
        }
        //$ret = $dir.preg_replace("/[\:\/\-\\\]/m","_",$pi["basename"].".".$pi["extension"]);
        $ret = $dir.preg_replace("/[\:\/\-\\\]/m","_",$pi["basename"]);
        $ret = is_dir($ret)?$dir."index.html":$ret;
        //Log::debug("File cache name [".$pi["extension"]."]".$f." => ".$ret);
        return $ret;
    }
}
?>
