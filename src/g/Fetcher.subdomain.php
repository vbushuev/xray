<?php
namespace g;
use \Log as Log;
class Fetcher{
    protected $headers = [];
    protected $url;
    protected $donor;
    protected $cfg;
    public function __construct(){
        $donor_cfg = ["host" => "brandalley.fr","schema" => "https","subdomain" => "www-v6."];
        //$donor_cfg = ["host" => "ctshirts.com","schema" => "https","subdomain" => "www."];
        //$donor_cfg = ["host" => "geox.com","schema" => "https","subdomain" => "www.","cookie"=>["preferredCountry"=>"AT","preferredLanguage"=>"EN","countrySelected"=>"true"]];

        $this->cfg = $donor_cfg;
        //get subdomain
        $pu = parse_url($_SERVER["HTTP_HOST"]);
        $ds = preg_split("/\./",$pu["path"]);
        if(count($ds)>2)$this->cfg["subdomain"] = $ds[0].".";
        $this->url = $this->cfg["schema"]."://".$this->cfg["subdomain"].$this->cfg["host"].preg_replace("/#g_/i","",$_SERVER["REQUEST_URI"]);
        $this->donor = parse_url($this->url);
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
        Log::debug("RESPONSE INFO: \n".json_encode($info,JSON_PRETTY_PRINT));
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
                //$this->headers[$ms[1][$i]] = $ms[2][$i];
            }
        }

    }
    protected function requestHeaders(){
        Log::debug("requestHeaders");
        $headers = [];
        $cookie = "";
        $need_append_cookie = true;
        if(isset($this->cfg["cookie"])){
            foreach ($this->cfg["cookie"] as $c => $v) {
                $cookie.=" {$c}={$v};";
            }
        }
        //getting all headers
        foreach (getallheaders() as $name => $value) {
            if($name=="Cookie"){
                $value .= $cookie;
                $need_append_cookie = false;
            }
            $headers[]="{$name}: ".$this->replacerequest($value);
        }
        if($need_append_cookie)$headers[]="Cookie: ".$cookie;
        Log::debug($headers);
        return $headers;
    }
    protected function replacerequest($s){
        Log::debug("replacerequest");
        $r = $s;
        $r = preg_replace("/".preg_quote($_SERVER["HTTP_HOST"])."/im",$this->donor["host"],$r);
        return $r;
    }
    protected function replaceresponse($s){
        $localhost = preg_replace("/www(.*?)\./im","",$_SERVER["HTTP_HOST"]);
        $domain = preg_replace("/www(.*?)\./im","",$this->donor["host"]);
        $search = "/".preg_quote($domain)."/im";
        $r = preg_replace($search,$localhost,$s);
        $r = preg_replace("/(https|http)\:\/\//im","//",$r);
        return $r;
    }
};
?>
