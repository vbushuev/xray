<?php
namespace g;
use \Log as Log;
class Fetcher3{
    protected $url;
    protected $snoopy;
    public $headers=[];
    public function __construct($url){
        $this->snoopy = new \Snoopy;
        $this->url = $url;
    }
    public function fetch(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            
        }
        $this->snoopy->fetch($this->url);
        $this->_stripheaders();
        $res = $this->snoopy->results;
        return $res;
    }
    public function pull($s){
        foreach($this->headers as $header => $val){
            if(in_array($header,["Transfer-Encoding","Content-Encoding","Location"]))continue;
            if($header=="Content-Length")$val = strlen($s);
            header($header.":".$val);
        }
        echo $s;
    }
    protected function _stripheaders(){
        foreach($this->snoopy->headers as $rawheader){
            if(preg_match("/^([^\:]+?)\:(.+?)[\r\n]*$/i",$rawheader,$m)){
                $this->headers[$m[1]] = $m[2];
            }
        }
    }
};
?>
