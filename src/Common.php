<?php
class Common {
    public function __get($n){
        return isset($this->$n)?$this->$n:false;
    }
    public function __set($n,$v){
        if(isset($this->$n))$this->$n=$v;
    }
}
?>
