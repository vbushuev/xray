<?php
namespace g;
use \Log as Log;
use \g\G as G;
class Fetcher4{
    public $headers = [];
    public $cookie = [];
    protected $url;
    protected $_pathinfo;
    protected $_urlinfo;
    protected $is_ssl;
    public function __construct($url){
        $this->url = $url;
        $this->_urlinfo = parse_url($url);
        $this->_urlinfo["domain"] = preg_replace("/^([^\.]*)\.?([^\.]+)\.(.+)$/i","$2.$3",$this->_urlinfo["host"]);
        $this->_pathinfo = isset($this->_urlinfo["path"])?pathinfo($this->_urlinfo["path"]):[];
    }
    public function fetch(){
        $s = "";
        $curl = curl_init();
        $this->url = $this->_replacerequest($this->url);
        $curlOptions = [
            CURLOPT_URL => $this->url,
            CURLOPT_HTTPHEADER=>$this->requestHeaders(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "", // обрабатывает все кодировки
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HEADER => true,
            CURLINFO_HEADER_OUT => true
        ];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $curlOptions[CURLOPT_POST]=true;
            $postData = http_build_query($_POST);
            $curlOptions[CURLOPT_POSTFIELDS]=$postData;
        }
        curl_setopt_array($curl, $curlOptions);
        $s = curl_exec($curl);
        $info = curl_getinfo($curl);
        $this->responseHeaders(substr($s,0,$info["header_size"]));
        $s = substr($s,$info["header_size"]);
        return $s;
    }
    public function pull($s){
        foreach($this->headers as $key => $value) {
            if($key == 'Content-Encoding')continue;
            if($key == 'Transfer-Encoding')continue;
            if($key == 'Content-Length')$value = strlen($s);
            if($key == 'Set-Cookie') {
                $value = preg_replace("/domain\s?=\s?[^;]+;?/im","",$value);
                $value = preg_replace("/".preg_quote($_SERVER["SERVER_NAME"],'/')."/i",$this->_urlinfo["domain"],$value);
            }
            if($key == 'Location') {
                continue;
                $vpi = parse_url($value);
                if(preg_match("/(m\.|www\.)?".preg_quote($this->_urlinfo["domain"],'/')."/i",$value)){
                    if($vpi["host"]!=$this->_urlinfo["host"]){
                        $_SESSION["current_url"] = $value;
                        Log::debug("Change session main url to {$value}");
                        $value = "http://".$_SERVER["SERVER_NAME"];
                    }

                }
                $value = preg_replace("/(www\.)?".preg_quote($this->_urlinfo["domain"],'/')."/i",$_SERVER["SERVER_NAME"],$value);
            }
            $value =  $this->_replaceresponse($value);
            header("{$key}: {$value}");
        }
        foreach ($this->cookie as $c => $v) {
            if(!isset($_COOKIE[$c])){
                setcookie($c,$v);
            }
        }
        echo $s;
    }
    protected function requestHeaders(){
        $headers = [];
        $client_headers = $this->getallheaders();
        if(isset($this->cookie)){
            $cc = "";
            foreach ($this->cookie as $c => $v) {
                if(!isset($_COOKIE[$c])){
                    $cc .= "{$c}={$v};";
                }
            }
            $client_headers["Cookie"] =(isset($client_headers["Cookie"])?$client_headers["Cookie"].";".$cc:$cc);
        }
        foreach ($client_headers as $name => $value) {
            //Log::debug($_SERVER);
            if($name=="Referer")$value = preg_replace("/http\:\/\/".preg_quote($_SERVER["SERVER_NAME"],'/')."/i",$this->_urlinfo["scheme"]."://".$this->_urlinfo["host"],$value);
            elseif($name=="Host")$value = $this->_urlinfo["host"];
            elseif($name=="Origin")$value = $this->_urlinfo["scheme"]."://".$this->_urlinfo["host"];
            if(!strlen(trim($value)))continue;
            $headers[]="{$name}: ".$value;
        }

        //$this->url = preg_replace("/".preg_quote($_SERVER["SERVER_NAME"],'/')."/i",$this->_urlinfo["host"],$value);
        Log::debug($this->url." HTTP 1.1/ ".$_SERVER['REQUEST_METHOD']." : ",$headers);
        return $headers;
    }
    protected function responseHeaders($h){
        if(preg_match_all("/^(.+?):\s*(.+?)\r*$/im",$h,$ms)){
            for($i=0; $i< count($ms[0]); $i++){
                $this->headers[$ms[1][$i]] = $ms[2][$i];
            }
        }
        Log::debug($this->headers);
    }
    protected function _replacerequest($s){return $s;}
    protected function _replaceresponse($s,$t = "html"){return $s;}
    protected function getallheaders(){
        return getallheaders();
        $headers = [];
        foreach ($_SERVER as $name => $value){
           if (substr($name, 0, 5) == 'HTTP_'){
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
};
?>
