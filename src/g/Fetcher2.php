<?php
namespace g;
use \Log as Log;
class Fetcher2{
    protected $headers = [];
    protected $url;
    protected $cfg;
    protected $is_ssl;
    public function __construct($cfg){
        ob_start();
        $this->cfg = $cfg;
        if(!isset($this->cfg["url"])) throw new \Exception("no url");
        $this->url = (isset($_REQUEST["_xg_u"]))?base64_decode($_REQUEST["_xg_u"]):$this->cfg["url"];
        $this->cfg["host"] = preg_replace("/(http|https):\/\/(.+)/im","$2",$this->url);
        $this->cfg["schema"] = preg_replace("/(https|http).+/im","$1",$this->cfg["url"]);
        $this->cfg["domain"] = preg_replace("/^([^\.]*)\.*(([^.]+)\.(.+))\s*$/im","$2",$this->cfg["host"]);
        $this->cfg["local"]["domain"] = preg_match("/\.bs2/",$_SERVER["SERVER_NAME"])?"xray.bs2":"x.gauzymall.com";
        $this->cfg["local"]["is_ssl"] = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? true : false;
        $this->cfg["local"]["url"] = "//".$this->cfg["local"]["domain"];
    }
    public function get(){
        $s = "";
        $curl = curl_init();
        $this->url = $this->url;
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
        $s = $this->replaceresponse($s);
        foreach($this->headers as $key => $value) {
            if($key == 'Content-Encoding')continue;
            if($key == 'Transfer-Encoding')continue;
            if($key == 'Content-Length')$value = strlen($s);
            if($key == 'Location') {
                $new_value = $r = preg_replace("/(http|https):\/\/www[^\.]*\.".($this->cfg["domain"])."/im",($this->cfg["local"]["is_ssl"]?"https://":"http://").$this->cfg["local"]["domain"],$value);
                //Log::debug("change location: ".$value." >> ".$new_value);
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
            $client_headers["Cookie"] =(isset($client_headers["Cookie"])?$client_headers["Cookie"].$cc:$cc);
        }
        foreach ($client_headers as $name => $value) {
            if($name == "Origin")$value = $this->cfg["host"];
            elseif($name == "Referer") $value = $this->url;
            elseif($name == "Host") $value = $this->cfg["host"];
            $headers[]="{$name}: ".$value;
        }
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
        $patterns = [
            "html" =>[
                "tag" => "/(url|href|data|link|src)\=([\"']?)(.+?)([\"'])/im",
                "url" => "/(https|http)*:?(\/\/)*(([\w\-]*?)\.)*(\w+?\.\w{2,5})([\/\-=+\!@#\$%\^&*\[\]\{\}a-z0-9;:\?\>\<\|]*)/im",
                "path" => "/(\/[\/\-=+\!@#\$%\^&*\[\]\{\}a-z0-9;:\?\>\<\|]*)/im"
            ],
            "js" => [

            ]
        ];
        /* FOR urls*/
        $r = preg_replace_callback($patterns["html"]["tag"],function($mt)use($t,$patterns){
            $mr=$mt[0];
            //Log::debug($t->url."::tags ",$mr);

            $mr = preg_replace_callback($patterns["html"]["url"],function($vs)use($t){
                Log::debug($t->url."::href ",$vs[0] ." >>> ".$t->cfg["local"]["url"]."?_xg_u=base64_encode(".$vs[0].")");
                return $t->cfg["local"]["url"]."?_xg_u=".base64_encode($vs[0]);
            },$mr);
            $mr = preg_replace_callback($patterns["html"]["path"],function($vs)use($t){
                Log::debug($t->url."::path ",$vs[0] ." >>> ".$t->cfg["local"]["url"]."?_xg_u=base64_encode(".$vs[0].")");
                return $t->cfg["local"]["url"]."?_xg_u=".base64_encode($t->cfg["url"].$vs[0]);
            },$mr);
            return $mr;
        },$r);
        return $r;
    }
};
?>
