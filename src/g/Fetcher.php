<?php
namespace g;
use \Log as Log;
use \g\G as G;
class Fetcher{
    protected $headers = [];
    protected $url;
    protected $cfg;
    protected $is_ssl;
    public function __construct(){
        //$donor_cfg = ["host" => "brandalley.fr","schema" => "https","subdomain" => "www-v6."];
        //$donor_cfg = ["host" => "ctshirts.com","schema" => "https","subdomain" => "www."];
        //$donor_cfg = ["host" => "geox.com","schema" => "https","subdomain" => "www.","cookie"=>["preferredCountry"=>"AT","preferredLanguage"=>"EN","countrySelected"=>"true"]];
        //$this->cfg = ["url"=>"https://www.ctshirts.com"];

        $this->cfg = ["url"=>"https://www-v6.brandalley.fr"];
        $this->cfg["host"] = preg_replace("/(http|https):\/\/(.+)/im","$2",$this->cfg["url"]);
        $this->cfg["schema"] = preg_replace("/(https|http).+/im","$1",$this->cfg["url"]);
        $this->cfg["domain"] = preg_replace("/www(.*?)\./im","",$this->cfg["host"]);
        $this->cfg["subdomain"] = preg_replace("/(www[^\.]*\.).+/im","$1",$this->cfg["host"]);
        $domains = preg_split("/\./",$this->cfg["host"]);
        if(count($domains)>1)$this->cfg["domain"] = $domains[count($domains)-2].".".$domains[count($domains)-1];


        $this->cfg["local"]["domain"] = "xray.bs2";
        $domains = preg_split("/\./",$_SERVER["HTTP_HOST"]);
        $local_domains = preg_split("/\./",$this->cfg["local"]["domain"]);
        if(count($domains)>count($local_domains)){
            $this->cfg["subdomain"] = "";
            for($i=0;$i<count($domains)-count($local_domains);++$i)
                $this->cfg["subdomain"] .= $domains[$i].".";
        }
        $this->cfg["local"]["is_ssl"] = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? true : false;
        $this->cfg["local"]["url"] = "//".$this->cfg["local"]["domain"];

        //$this->url = $this->cfg["url"].preg_replace("/#g_/i","",$_SERVER["REQUEST_URI"]);
        $this->url = $this->cfg["schema"]."://".$this->cfg["subdomain"].$this->cfg["domain"].preg_replace("/#g_/i","",preg_replace("/#_xg_subdomain=([^&]+)&*/i","",$_SERVER["REQUEST_URI"]));

        //print_r($this->cfg);echo "<br/>";
        //print_r($this->url);exit;
    }
    public function get(){
        Log::debug();
        $s = "";
        $curl = curl_init();
        Log::debug("REQUEST ".$this->url);
        $curlOptions = [
            CURLOPT_URL => $this->url,
            CURLOPT_HTTPHEADER=>$this->requestHeaders(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "", // обрабатывает все кодировки
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HEADER => true,
            CURLOPT_VERBOSE => 1,
            CURLOPT_STDERR => fopen('logs/curl-'.date("Y-m-d").'.log', 'a+'),
            CURLINFO_HEADER_OUT => 1,
        ];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $curlOptions[CURLOPT_POST]=1;
            $postData = http_build_query($_POST);
            $curlOptions[CURLOPT_POSTFIELDS]=$postData;
        }
        curl_setopt_array($curl, $curlOptions);
        $s = curl_exec($curl);
        $info = curl_getinfo($curl);
        Log::debug("RESPONSE ".$this->url);
        //Log::debug("RESPONSE INFO: \n".json_encode($info,JSON_PRETTY_PRINT));
        $this->responseHeaders(substr($s,0,$info["header_size"]));
        $s = substr($s,$info["header_size"]);
        $s = $this->replaceresponse($s);
        return $s;
    }
    public function pull($s){
        foreach($this->headers as $key => $value) {
            if($key == 'Content-Encoding')continue;
            if($key == 'Transfer-Encoding')continue;
            if($key == 'Content-Length')$value = strlen($s);
            header("{$key}: {$value}");
            //Log::debug("SET header: "."{$key}: {$value}");
        }
        //Log::debug($this->headers);
        echo $s;
    }
    protected function responseHeaders($h){
        Log::debug("responseHeaders");
        if(preg_match_all("/^(.+?):\s*(.+?)\r*$/im",$h,$ms)){
            for($i=0; $i< count($ms[0]); $i++){
                $this->headers[$ms[1][$i]] = $this->replaceresponse($ms[2][$i]);
            }
        }
        Log::debug($this->headers);
    }
    protected function requestHeaders(){
        Log::debug("requestHeaders");
        $headers = [];
        //getting all headers
        foreach (getallheaders() as $name => $value) {
            $headers[]="{$name}: ".$this->replacerequest($value);
        }
        Log::debug($headers);
        return $headers;
    }
    protected function replacerequest($s){
        Log::debug("replacerequest");$r=$s;
        //$r = preg_replace("/(http|https):\/\/".preg_quote($_SERVER["HTTP_HOST"])."/im",$this->cfg["url"],$s);
        $r = preg_replace("/(http|https)*:*(\/\/)*".preg_quote($this->cfg["local"]["domain"])."/im",$this->cfg["host"],$r);
        return $r;
    }
    protected function replaceresponse($s){
        Log::debug("replaceresponse");$r=$s;
        //$r = preg_replace("/(http|https):\/\/".preg_quote($this->cfg["host"])."/im","//".$_SERVER["HTTP_HOST"],$s);
        $r = preg_replace("/".preg_quote($this->cfg["host"])."/im",$this->cfg["local"]["url"],$r);
        //$r = preg_replace("/".preg_quote($this->cfg["domain"])."/im",$this->cfg["local"]["domain"],$r);
        //$r = preg_replace("/([\s\"']+\.*)".preg_quote($this->cfg["domain"])."/im","$1".$this->cfg["local"]["domain"],$r);
        //$r = preg_replace("/([a-z0-9\-_]+\.)".preg_quote($this->cfg["domain"])."/im",$this->cfg["local"]["domain"]."?_xg_subdomain=$1",$r);
        $t = $this;
        $r = preg_replace_callback("/".preg_quote($this->cfg["domain"])."/im",function($m)use($t){
            return $t->cfg["local"]["domain"];
        },$s);
        return $r;
    }
};
?>
