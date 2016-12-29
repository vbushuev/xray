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
        Log::debug($this->url.":: ","FLUSH DATA: ".ob_get_flush());
        foreach($this->headers as $key => $value) {
            if($key == 'Content-Encoding')continue;
            if($key == 'Transfer-Encoding')continue;
            if($key == 'Content-Length')$value = strlen($s);
            if($key == 'Location') continue;
            $value =  $this->_replaceresponse($value);
            header("{$key}: {$value}");
        }
        echo $s;
    }
    protected function requestHeaders(){
        $headers = [];
        $client_headers = getallheaders();
        if(isset($this->cookie)){
            $cc = "";
            foreach ($this->cookie as $c => $v) {
                if(!isset($_COOKIE[$c]))$cc .= "{$c}={$v};";
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
        $this->url = preg_replace("/".preg_quote($_SERVER["SERVER_NAME"],'/')."/i",$this->_urlinfo["host"],$value);
        Log::debug($this->url.":: ",$headers);
        return $headers;
    }
    protected function responseHeaders($h){
        if(preg_match_all("/^(.+?):\s*(.+?)\r*$/im",$h,$ms)){
            for($i=0; $i< count($ms[0]); $i++){
                $this->headers[$ms[1][$i]] = $ms[2][$i];
            }
        }
    }
    protected function _replacerequest($s){return $s;}
    protected function _replaceresponse($s,$t = "html"){return $s;}
};
?>
