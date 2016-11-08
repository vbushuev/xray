<?php
class Common {
    public function __get($n){
        return isset($this->$n)?$this->$n:false;
    }
    public function __set($n,$v){
        if(isset($this->$n))$this->$n=$v;
    }
    public function checkPath($p){
        $pi = pathinfo($p);
        $dir = $pi["dirname"];
        //Log::debug("check dir ".$dir);
        if(!file_exists($dir)||!is_dir($dir))mkdir($dir,0777,true);
    }
}
?>
