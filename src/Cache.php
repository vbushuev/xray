<?php
class Cache {
    protected $cache="cache/";
    protected $donor;
    protected $donor_pattern;
    public function __construct($a=[]){
        $this->donor = isset($a["host"])?$a["host"]:"www.kik.de";
        $this->donor_pattern = preg_replace("/www\./i","",$this->donor);
        $this->donor_pattern = preg_quote($this->donor_pattern);
        $this->cache = isset($a["cache"])?$a["cache"]:"cache/";

        if(!is_dir($this->cache))mkdir($this->cache);
        if(!is_dir($this->cache."/".$this->donor))mkdir($this->cache."/".$this->donor);
        if(!is_dir($this->cache."/".$this->donor."/css"))mkdir($this->cache."/".$this->donor."/css");
        if(!is_dir($this->cache."/".$this->donor."/img"))mkdir($this->cache."/".$this->donor."/img");
        if(!is_dir($this->cache."/".$this->donor."/js"))mkdir($this->cache."/".$this->donor."/js");
    }
    public function get($f){
        $cf = $this->_filename($f);
        Log::debug("File search in cache ...");
        //if(is_dir($cf))
        if(file_exists($cf)) return file_get_contents($cf);
        return false;
    }
    public function save($f,$d){
        $cf = $this->_filename($f);
        Log::debug("File save in cache ...");
        file_put_contents($cf,$d);
    }
    protected function _filename($f){
        $f = preg_replace("/(www\.)?".$this->donor_pattern."/i","",$f);
        $pi = pathinfo($f);
        $pi["extension"] = preg_replace("/\?.+/i","",$pi["extension"]);
        $dir = $this->cache."/".$this->donor."/";
        if(isset($pi["extension"])){
            if($pi["extension"]=="js"){$dir.="js/";}
            elseif(in_array($pi["extension"],["css","map"])){$dir.="css/";}
            elseif(in_array($pi["extension"],["jpg","gif","png","ico","svg","jpeg"])){$dir.="img/";}
            elseif(in_array($pi["extension"],["ttf","woft"])){$dir.="css/";}
        }
        $ret = $dir.preg_replace("/[\:\/\-\\\]/m","_",$pi["basename"].".".$pi["extension"]);
        Log::debug("File cache name [".$pi["extension"]."]".$f." => ".$ret);
        return $ret;
    }
}
?>
