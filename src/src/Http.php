<?php
class Http extends Common{
    protected $results;
    protected $response;
    protected $cookies=[];
    protected $cookieFile ='';
    protected $config;
    public function __construct($a=[]){
        $this->config = $a;
        if(!isset($_COOKIE["PHPSESSID"])) Log::debug("!!!! NO PHPSESSID ". json_encode($_COOKIE,JSON_PRETTY_PRINT));
        $this->cookieFile = $_SERVER['DOCUMENT_ROOT'].'/cache/'.preg_replace("/^(http|https)\:\/\//i","",$this->config->host).'/cookies/'
            .session_id()
            .'/cookie';
        $this->checkPath($this->cookieFile);
    }
    public function fetch($url){
        $curl = curl_init();
        $host = parse_url($url);
        $refer = $this->config->host.$_SERVER["REQUEST_URI"];
        //Log::debug("parse_url ".json_encode($host,JSON_PRETTY_PRINT));
        $cookies = $this->outCookie();
        //Log::debug("sending COOKIES=[".$cookies."]");
        $headers = [
            'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Cookie: '.$cookies,
            'Referer: '.$refer,
            'Accept-Language:en-US,en;q=0.8,ru;q=0.6',
            'Accept-Charset: utf-8;q=0.7,*;q=0.7',
            'Accept-Encoding:gzip, deflate, sdch',
            'Cache-Control:max-age=0',
            'Connection:keep-alive',
            'DNT:1',
            'Host:'.preg_replace("/^(http|https)\:\/\//i","",$this->config->host),
            'Pragma:no-cache',
            'Upgrade-Insecure-Requests:1',
            'User-Agent: '.$_SERVER['HTTP_USER_AGENT']
        ];
        //$cookie = 'cookie.txt';

        //if(!file_exists($this->cookieFile))file_put_contents($this->cookieFile,"");
        //$verbose = fopen('logs/curl-'.date('Y-m-d').'.log', 'a+');

        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => "User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36",
            CURLOPT_HTTPHEADER=>$headers,
            CURLOPT_RETURNTRANSFER => true,
            //CURLOPT_REFERER => $host["host"],
            CURLOPT_HEADER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_ENCODING => "", // обрабатывает все кодировки
            CURLOPT_MAXREDIRS =>10, // останавливаться после 10-ого редиректа
            //CURLOPT_COOKIE => $cookie,
            //CURLOPT_COOKIEFILE => $this->cookieFile,
            //CURLOPT_COOKIEJAR =>  $this->cookieFile,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_FORBID_REUSE => 1,
            //CURLOPT_AUTOREFERER => 1,
            CURLOPT_FOLLOWLOCATION => 1,

            //CURLOPT_VERBOSE => 1,
            //CURLOPT_STDERR => $verbose,
            CURLOPT_CERTINFO => 1,
            CURLINFO_HEADER_OUT => 1,
        ];
        $ext = "html";
        if(isset($host["path"])){
            $upi = pathinfo($host["path"]);
            $ext = (isset($upi["extension"]))?preg_split("/\?/",$upi["extension"],1)[0]:"";
        }
        if(in_array($ext,['html','htm']))Log::debug("Fetching [".$url."] with headers: ".json_encode($headers,JSON_PRETTY_PRINT));
        $method = $_SERVER['REQUEST_METHOD'];
        if($this->config->proxy!==false){
            $curlOptions[CURLOPT_PROXY] = $this->config->proxy;
        }
        if($method == 'POST'){
            $curlOptions[CURLOPT_POST]=1;
            $curlOptions[CURLOPT_POSTFIELDS]=http_build_query($_POST);
        }
        curl_setopt_array($curl, $curlOptions);
        $this->results = $this->stripHeaders(curl_exec($curl));
        $this->response = curl_getinfo($curl);
        if(in_array($ext,['html','htm']) && count($this->cookies))Log::debug('got COOKIES:['.json_encode($this->cookies,JSON_PRETTY_PRINT)."]");
        curl_close($curl);
        return $this->results;
    }
    protected function stripHeaders($_){
        $_r = $_;
        if(preg_match("/^HTTP\/1\.1\s*\d+\s*.+/",$_r)){
            $_a = preg_split("/\r\n\r\n/i",$_,2);
            //Log::debug('HTTP HEADER:'.json_encode($_a,JSON_PRETTY_PRINT));
            if(is_array($_a)){
                $_r=isset($_a[1])?$_a[1]:"";
                if(strlen(trim($_a[0]))){
                    if(preg_match_all("/set\-cookie\:\s*(?<c>.+?)=(?<v>.+?);/i",$_a[0],$_mm)){
                        for($i=0;$i<count($_mm[0]);++$i){
                            $this->cookies[$_mm["c"][$i]]=htmlspecialchars_decode(urldecode($_mm["v"][$i]));
                        }
                    }
                }
                if(preg_match("/^HTTP\/1\.1\s*\d+\s*.+/",$_r))$_r=$this->stripHeaders($_r);
            }
        }
        return $_r;
    }
    protected function stripHeaders_1($_){
        $_r = $_;
        $_a = preg_split("/[\r\n]+/i",$_,2);
        if(is_array($_a)&&strlen(trim($_a[0]))){
            Log::debug('got HEADRs:'.$_a[0]);
            if(preg_match("/set\-cookie\:\s*(?<c>.+?)=(?<v>.+?);/i",$_a[0],$_m)){
                $this->cookies[$_m["c"]]=htmlspecialchars_decode(urldecode($_m["v"]));
            }
            $_r=isset($_a[1])?$_a[1]:"";
            if(!preg_match("/^\</i",$_r))$_r=$this->stripHeaders($_r);
        }
        return $_r;
    }
    protected function outCookie(){
        $c='';
        $_coo = [];

        if(file_exists($this->cookieFile)) {
            $this->cookies = json_decode(file_get_contents($this->cookieFile),true);
            $_coo = array_merge($_coo, $this->cookies);
        }
        $_coo = array_merge($_coo,$this->config->cookie);
        foreach($_coo as $k=>$v){
            //$v = urlencode($v);
            $c.="{$k}={$v};";
        }
        return $c;
    }
    public function inCookie(){
        foreach ($this->cookies as $key => $value) {
            //if(!isset($_COOKIE[$key])){
                //Log::debug("setcookie $key = $value");
                //if(!isset($_COOKIE[$key]))setcookie($key,$value);//,time()+60*60*24,"/",$_SERVER["HTTP_HOST"]);
            //}
        }
        file_put_contents($this->cookieFile,json_encode($this->cookies,JSON_PRETTY_PRINT));
        /*
        foreach ($this->config->cookie as $key => $value) {
            //if(!isset($_COOKIE[$key])){
                //Log::debug("setcookie $key = $value");
                setcookie($key,$value);//,time()+60*60*24,"/",$_SERVER["HTTP_HOST"]);
            //}
        }*/
    }
};
?>
