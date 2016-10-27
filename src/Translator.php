<?php
class Translator extends Common{
    protected $_d = [];
    public function __construct($cfg){
        $dict_fname = "dicts/".$cfg->lang.".php";
        if(file_exists($dict_fname)){
            include($dict_fname);
            if(isset($_dicts))$this->_d = array_merge($this->_d,$_dicts);
        }
        $dict_fname = "dicts/".$cfg->section.".php";
        if(file_exists($dict_fname)){
            include($dict_fname);
            if(isset($_dicts))$this->_d = array_merge($this->_d,$_dicts);
        }
    }
    public function translate($in){
        $out = $in;
        foreach($this->_d as $s=>$t){
            $out = preg_replace("/".preg_quote($s)."/i",$t,$out);
        }
        return $out;
    }
};
?>
