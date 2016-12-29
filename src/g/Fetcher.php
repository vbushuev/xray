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
        if(isset($_REQUEST["_xg_u"])){
            $this->url = base64_decode($_REQUEST["_xg_u"]);
            $this->cfg["host"] = preg_replace("/(http|https):\/\/(.+)/im","$2",$this->url);
            $this->cfg["schema"] = preg_replace("/(https|http).+/im","$1",$this->cfg["url"]);
            $this->cfg["domain"] = preg_replace("/^([^\.]*)\.*(([^.]+)\.(.+))\s*$/im","$2",$this->cfg["host"]);
            $this->cfg["local"]["domain"] = preg_match("/\.bs2/",$_SERVER["SERVER_NAME"])?"xray.bs2":"x.gauzymall.com";
            $this->cfg["local"]["is_ssl"] = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? true : false;
            $this->cfg["local"]["url"] = "//".$this->cfg["local"]["domain"];
        }
        else{
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
            $this->url = (isset($_REQUEST["_xg_u"]))?base64_decode($_REQUEST["_xg_u"]):$this->url;
            //somehacks
            if(isset($this->cfg["hacks"])&&isset($this->cfg["hacks"]["substitutions"])){
                foreach($this->cfg["hacks"]["substitutions"] as $w=>$n){
                    if($n["url"]==preg_replace("/#_xg_subdomain=([^&]+)&*/i","",$_SERVER["REQUEST_URI"])){
                        $this->url = $w;
                        $this->cfg["no_cookie"] = $n["no_cookie"] or false;
                        $this->cfg["host"] = $n["host"] or $this->cfg["host"];
                        break;
                    }
                }
            }
        }
    }
    public function fetch(){
        $s = "";
        $curl = curl_init();
        $this->url = $this->replacerequest($this->url);
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
        //$s = $this->replaceresponse($s);
        foreach($this->headers as $key => $value) {
            if($key == 'Content-Encoding')continue;
            if($key == 'Transfer-Encoding')continue;
            if($key == 'Content-Length')$value = strlen($s);
            if($key == 'Location') {
                $new_value = $r = preg_replace("/(http|https):\/\/www[^\.]*\.".($this->cfg["domain"])."/im",($this->cfg["local"]["is_ssl"]?"https://":"http://").$this->cfg[local]["domain"],$value);
                Log::debug($this->url.":: ","change location: ".$value." >> ".$new_value);
                $value = $new_value;
            }
            $value =  $this->replaceresponse($value);
            header("{$key}: {$value}");
        }
        echo $s;
    }
    protected function responseHeaders($h){
        if(preg_match_all("/^(.+?):\s*(.+?)\r*$/im",$h,$ms)){
            for($i=0; $i< count($ms[0]); $i++){
                $this->headers[$ms[1][$i]] = $ms[2][$i];
            }
        }
    }
    protected function requestHeaders(){
        $headers = [];
        $client_headers = getallheaders();
        if(isset($this->cfg["cookie"])){
            $cc = "";
            foreach ($this->cfg["cookie"] as $c => $v) {
                if(!isset($_COOKIE[$c]))$cc .= "{$c}={$v};";
            }
            $client_headers["Cookie"] =(isset($client_headers["Cookie"])?$client_headers["Cookie"].";".$cc:$cc);
        }
        if(isset($this->cfg["no_cookie"])&&$this->cfg["no_cookie"]===true){
            unset($client_headers["Cookie"]);
        }
        foreach ($client_headers as $name => $value) {
            $headers[]="{$name}: ".$this->replacerequest($value);
        }
        Log::debug($this->url.":: ",$headers);
        return $headers;
    }
    protected function replacerequest($s){
        $r=$s;
        $r = preg_replace("/(http|https)?:?\/\/".($this->cfg["local"]["domain"])."/im",$this->cfg["url"],$s);
        $r = preg_replace("/([\s\"'\=\/\:]|\A)".preg_quote($this->cfg["local"]["domain"])."/im","$1".$this->cfg["host"],$r);
        $r = preg_replace("/".preg_quote($this->cfg["local"]["domain"])."/im","$1".$this->cfg["domain"],$r);
        return $r;
    }
    protected function replaceresponse($s,$t = "html"){
        $r=$s;
        $t = $this;
        $r = preg_replace_callback("/(https|http)*:?(\/\/)*(([\w\-]*?)\.)*(\w+?\.\w{2,5})(\S*)/im",function($m)use($t){
            $res = $m[0];
            if($m[5]==$this->cfg["domain"]){
                $res = "//".$m[3].$t->cfg["local"]["domain"].$m[6];
            }
            //Log::debug($this->url." :: ", "[".$m[0]. "] >>> [".$res."]");
            return $res;
        },$r);
        //somehacks
        if(isset($this->cfg["hacks"])&&isset($this->cfg["hacks"]["substitutions"])){
            foreach($this->cfg["hacks"]["substitutions"] as $w=>$n){
                $r = preg_replace_callback("/".preg_quote($w,'/')."/im",function($m)use($n){
                    Log::debug($this->url.":: replace : {".$m[0]."} >> {".$n["url"]."}" );
                    return $n["url"];
                },$r);
            }
        }
        return $r;
    }
};
?>
