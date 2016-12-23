<?php
namespace g;
use \Log as Log;
use \g\G as G;
class Fetcher{
    protected $headers = [];
    protected $url;
    protected $cfg;
    protected $is_ssl;
    public function __construct($cfg){
        ob_start();
        $this->cfg = $cfg;
        $this->cfg["host"] = preg_replace("/(http|https):\/\/(.+)/im","$2",$this->cfg["url"]);
        $this->cfg["schema"] = preg_replace("/(https|http).+/im","$1",$this->cfg["url"]);
        $this->cfg["domain"] = preg_replace("/^([^\.]*)\.*(([^.]+)\.(.+))\s*$/im","$2",$this->cfg["host"]);

        $this->cfg["subdomain"] = "";
        if(preg_match("/(www[^\.]*\.).+/im",$this->cfg["host"]))$this->cfg["subdomain"] = preg_replace("/(www[^\.]*\.).+/im","$1",$this->cfg["host"]);
        $domains = preg_split("/\./",$this->cfg["host"]);
        if(count($domains)>1)$this->cfg["domain"] = $domains[count($domains)-2].".".$domains[count($domains)-1];

        $this->cfg["local"]["domain"] = preg_match("/\.bs2/",$_SERVER["SERVER_NAME"])?"xray.bs2":"x.gauzymall.com";
        $domains = preg_split("/\./",$_SERVER["HTTP_HOST"]);
        $local_domains = preg_split("/\./",$this->cfg["local"]["domain"]);
        if(count($domains)>count($local_domains)){
            $this->cfg["subdomain"] = "";
            for($i=0;$i<count($domains)-count($local_domains);++$i)
                $this->cfg["subdomain"] .= $domains[$i].".";
        }
        $this->cfg["local"]["is_ssl"] = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? true : false;
        $this->cfg["local"]["url"] = "//".$this->cfg["local"]["domain"];
        $this->url = $this->cfg["schema"]."://".$this->cfg["subdomain"].$this->cfg["domain"].preg_replace("/#g_/i","",preg_replace("/#_xg_subdomain=([^&]+)&*/i","",$_SERVER["REQUEST_URI"]));

        Log::debug("/************************************************************************************************/");
        Log::debug("/**** START {$this->url} ****/");
        Log::debug("/************************************************************************************************/");
        Log::debug("DOMAIN:".$this->cfg["domain"]);
        Log::debug("subDOMAIN:".$this->cfg["subdomain"]);
    }
    public function get(){
        Log::debug();
        $s = "";
        $curl = curl_init();
        $this->url = $this->replacerequest($this->url);
        Log::debug("REQUEST ".$this->url);
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
            CURLINFO_HEADER_OUT => true,

            //CURLOPT_VERBOSE => true,
            //CURLOPT_STDERR => fopen('logs/curl.log','w'),
            //CURLOPT_FRESH_CONNECT=>false,
            //CURLOPT_COOKIESESSION=>false,
        ];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $curlOptions[CURLOPT_POST]=true;
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
        return $s;
    }
    public function pull($s){
        Log::debug("FLUSH DATA: ".ob_get_flush());
        $s = $this->replaceresponse($s);
        foreach($this->headers as $key => $value) {
            if($key == 'Content-Encoding')continue;
            if($key == 'Transfer-Encoding')continue;
            if($key == 'Content-Length')$value = strlen($s);
            if($key == 'Location') {
                $new_value = $r = preg_replace("/(http|https):\/\/www[^\.]*\.".($this->cfg["domain"])."/im",($this->cfg["local"]["is_ssl"]?"https://":"http://").$this->cfg[local]["domain"],$value);
                Log::debug("change location: ".$value." >> ".$new_value);
                //$env = json_decode(file_get_contents("xray.json"),true);
                //$env["url"] = preg_replace("/\/$/","",$value);
                //file_put_contents("xray.json",json_encode($env,JSON_PRETTY_PRINT));
                $value = $new_value;
            }
            $value =  $this->replaceresponse($value);
            header("{$key}: {$value}");
        }
        Log::debug("/************************************************************************************************/");
        Log::debug("/**** END {$this->url} ****/");
        Log::debug("/************************************************************************************************/");
        echo $s;
    }
    protected function responseHeaders($h){
        Log::debug("responseHeaders");
        if(preg_match_all("/^(.+?):\s*(.+?)\r*$/im",$h,$ms)){
            for($i=0; $i< count($ms[0]); $i++){
                $this->headers[$ms[1][$i]] = $ms[2][$i];
            }
        }
        Log::debug($this->headers);
    }
    protected function requestHeaders(){
        Log::debug("requestHeaders");
        $headers = [];
        $client_headers = getallheaders();
        if(isset($this->cfg["cookie"])){
            $cc = "";
            foreach ($this->cfg["cookie"] as $c => $v) {
                if(!isset($_COOKIE[$c]))$cc .= "{$c}={$v};";
            }
            $client_headers["Cookie"] =(isset($client_headers["Cookie"])?$client_headers["Cookie"].$cc:$cc);
        }
        foreach ($client_headers as $name => $value) {
            $headers[]="{$name}: ".$this->replacerequest($value);
        }
        Log::debug($headers);
        return $headers;
    }
    protected function replacerequest($s){
        $r=$s;
        if(preg_match_all("/.*".($this->cfg["local"]["domain"]).".*/im",$s,$m)){
            Log::debug("replacerequest:");
            Log::debug($m[0]);
        }
        $r = preg_replace("/(http|https)?:?\/\/".($this->cfg["local"]["domain"])."/im",$this->cfg["url"],$s);
        $r = preg_replace("/([\s\"'\=\/\:]|\A)".preg_quote($this->cfg["local"]["domain"])."/im","$1".$this->cfg["host"],$r);
        $r = preg_replace("/".preg_quote($this->cfg["local"]["domain"])."/im","$1".$this->cfg["domain"],$r);
        return $r;
    }
    protected function replaceresponse($s){
        $r=$s;
        $t = $this;

        $r = preg_replace_callback("/(\S*)".preg_quote($this->cfg["domain"])."/im",function($m)use($t){
            $res = $m[0];
            if(
                !preg_match("/\-".preg_quote($this->cfg["domain"])."/im",$m[0],$not_m) &&
                !preg_match("/eulerian\.brandalley\.fr\/col1\//im",$m[1],$not_m)
            ){
                $top = preg_replace("/www[^\.]*\.$/im","",$m[1]);
                $top = preg_replace("/(http|https):\/\//im","//",$top);
                $res = $top.$t->cfg["local"]["domain"];
                Log::debug( "replace:". trim($m[0])." >> ".trim($res) );
            }
            return $res;
        },$r);
        return $r;
    }
};
?>
