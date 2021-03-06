<?php
class Http extends Common{
    protected $results;
    protected $response;
    protected $cookies=[];
    protected $headers=[];
    protected $cookieFile ='';
    protected $config;
    protected $_html_extensions = ['html','htm','-','aspx'];
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
        $cookies = $this->outCookie();
        //if(preg_match("/basket\.aspx/i",$url)){
        //    $url = preg_replace("/http/i","https",$url);
        //}
        $headers = [
            'Host:'.preg_replace("/^(http|https)\:\/\//i","",$this->config->host),

            //'Referer: '.$refer,
            //"Cache-Control: no-cache",
            //"Pragma: no-cache",
            //"Connection: keep-alive"
        ];
        if(strlen($cookies))$headers[]='Cookie: '.$cookies;
        foreach (getallheaders() as $name => $value) {
            $countryDomain = preg_replace("/^.+\.([a-z]+)$/","$1",$this->config->host);
            //if($name == "Origin")array_push($headers,"$name: ".preg_replace("/^(http|https)\:\/\//i","",$this->config->host));
            if($name == "Origin"){
                array_push($headers,"$name: ".$this->config->host);
                //array_push($headers,"$name: ".preg_replace("/http/i","https",$this->config->host));
            }
            else if($name == "Referer"){
                //$v = preg_replace("/(\.xray\.bs2|\.gauzymall\.com)/i",".".$countryDomain,$value);
                $v = preg_replace("/".preg_quote($_SERVER["SERVER_NAME"])."/i",preg_replace("/^(http|https)\:\/\//i","",$this->config->host),$value);

                //if($this->config->secure)$v=preg_replace("/http\:\/\//i","https://www.",$v);
                array_push($headers,"$name: ".$v);
            }/*
            else if($name == "Pragma"){
                array_push($headers,"Pragma: no-cache");
            }
            else if($name == "Cache-Control"){
                array_push($headers,"Cache-Control: no-cache");
            }*/
            else if(!in_array($name,["Host","Cookie","Referer","X-Requested-With","Content-Length"])){
                if(($this->config->engine["restricted_headers"]===false)||(is_array($this->config->engine["restricted_headers"])&&in_array($name,$this->config->engine["restricted_headers"])))
                    array_push($headers,"$name: $value");
            }
        }
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))array_push($headers, "X-Requested-With: ".$_SERVER['HTTP_X_REQUESTED_WITH']);
        $verbose = fopen('logs/curl-'.date("Y-m-d").'.log', 'wa');
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER=>$headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER => true,
            //CURLOPT_MAXREDIRS =>20, // останавливаться после 10-ого редиректа
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "", // обрабатывает все кодировки
            CURLOPT_SSL_VERIFYHOST => 2,
            //CURLOPT_CERTINFO => true,


            //CURLOPT_FRESH_CONNECT => 1,
            //CURLOPT_FORBID_REUSE => 1,

            CURLOPT_HEADER => true,
            CURLOPT_VERBOSE => 1,
            CURLOPT_STDERR => $verbose,
            CURLINFO_HEADER_OUT => 1,

            //CURLOPT_POSTREDIR=> 3

        ];
        $ext = "html";
        if(isset($host["path"])){
            $upi = pathinfo($host["path"]);
            $ext = (isset($upi["extension"]))?preg_split("/\?/",$upi["extension"],1)[0]:"-";
        }
        $method = $_SERVER['REQUEST_METHOD'];
        if($this->config->proxy!==false){
            $curlOptions[CURLOPT_PROXY] = $this->config->proxy;
        }
        //if(in_array($ext,$this->_html_extensions))Log::debug("Fetching by ".$method." [".$url."] with headers: ".json_encode($headers,JSON_PRETTY_PRINT)).(($method == 'POST')?" ".json_encode($_POST,JSON_PRETTY_PRINT):"");
        if($method == 'POST'){
            $curlOptions[CURLOPT_POST]=1;
            //print_r($_POST);
            $postData = http_build_query($_POST);
            /*
            $postData = ($this->config->engine["encode_cookie"]) ? http_build_query($_POST) : "";
            if(!$this->config->engine["encode_cookie"]){
                foreach($_POST as $n=>$v)
                    $postData .= ((strlen($postData)==0)?"":"&").$n."=".$v;
            }*/
            $curlOptions[CURLOPT_POSTFIELDS]=$postData;
            Log::debug("POST data: ".$postData);
        }
        curl_setopt_array($curl, $curlOptions);
        $response = curl_exec($curl);
        $this->response = curl_getinfo($curl);
        $this->results = $this->stripHeaders($response,$this->response);
        //if(in_array($ext,$this->_html_extensions))Log::debug(json_encode($this->headers,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        //Log::debug("Response: ".json_encode($this->response,JSON_PRETTY_PRINT));
        //if($method == 'POST'){
            //if(in_array($ext,$this->_html_extensions))Log::debug("Response: ".json_encode($this->response,JSON_PRETTY_PRINT));
            //Log::debug("Response DATA: ".$this->results);
        //}
        //if(in_array($ext,['html','htm']) && count($this->cookies))Log::debug('got COOKIES:['.json_encode($this->cookies,JSON_PRETTY_PRINT)."]");
        curl_close($curl);
        return $this->results;
    }
    protected function stripHeaders($r,$h){
        $_h = substr($r,0,$h["header_size"]);
        $_r = substr($r,$h["header_size"]);
        $this->cookies = ($this->cookies==false)?[]:$this->cookies;
        if(preg_match_all("/set\-cookie\:\s*(?<c>.+?)=(?<v>.+?);/i",$_h,$_mm)){
            for($i=0;$i<count($_mm[0]);++$i){
                $this->cookies[$_mm["c"][$i]]=htmlspecialchars_decode(urldecode($_mm["v"][$i]));
            }
        }
        if(preg_match_all("/(?<h>.+?)\:(?<v>.+?)[\r\n]+/i",$_h,$_mm)) {
            for($i=0;$i<count($_mm[0]);$i++){
                if($_mm["h"][$i]!="Set-Cookie")$this->headers[$_mm["h"][$i]] = $_mm["v"][$i];
            }
        }
        Log::debug("response_cookies: ".json_encode($this->cookies,JSON_PRETTY_PRINT));
        //Log::debug("response_content: ".$_r);
        return $_r;
    }
    protected function stripHeaders_2($_){
        $_r = $_;
        if(preg_match("/^HTTP\/1\.1\s*\d+\s*.+/",$_r)){
            $_a = preg_split("/\r\n\r\n/i",$_,2);
            if(is_array($_a)){
                $_r=isset($_a[1])?$_a[1]:"";
                if(strlen(trim($_a[0]))){
                    if(preg_match_all("/set\-cookie\:\s*(?<c>.+?)=(?<v>.+?);/i",$_a[0],$_mm)){
                      for($i=0;$i<count($_mm[0]);++$i){
                        $this->cookies[$_mm["c"][$i]]=htmlspecialchars_decode(urldecode($_mm["v"][$i]));
                      }
                    }
                    elseif (preg_match_all("/(?<h>.+?)\:(?<v>.+?)\\r\\n/i",$_a[0],$_mm)) {
                        //Log::debug(json_encode($_mm,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
                        for($i=0;$i<count($_mm[0]);$i++){
                            $this->headers[$_mm["h"][$i]] = $_mm["v"][$i];
                        }

                    }
                    /*if(preg_match_all("/set\-cookie\:\s*(?<c>.+?)=(?<v>.+?);(?<e>expires=(.+?);)?/i",$_a[0],$_mm)){
                        for($i=0;$i<count($_mm[0]);++$i){
                            $this->cookies[$_mm["c"][$i]]=[
                                "v" => htmlspecialchars_decode(urldecode($_mm["v"][$i])),
                                "e" => 100,//(isset($_mm["e"][$i]))?$_mm["e"][$i]:gmdate(DATE_COOKIE, mktime(0, 0, 0, 1, 1, 2998)),
                                "p" => (isset($_mm["p"][$i]))?$_mm["p"][$i]:"/",
                                "d" => (isset($_mm["d"][$i]))?$_mm["d"][$i]:$_SERVER['HTTP_HOST'],
                                "s" => (isset($_mm["s"][$i]))?true:false,
                                "h" => (isset($_mm["h"][$i]))?true:false
                            ];
                        }
                    }*/
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
        $_coo = array_merge($_coo,$this->config->cookie);
        if($this->config->engine["client_cookie"]["use"]){
            if(isset($this->config->engine["client_cookie"]["list"])&&count($this->config->engine["client_cookie"]["list"])){
                foreach ($this->config->engine["client_cookie"]["list"] as $k) {
                    if(isset($_COOKIE[$c]))$_coo[$k] = $v;
                }
            }else $_coo = array_merge($_coo,$_COOKIE);
        }else{
            if(file_exists($this->cookieFile)) {
                $this->cookies = json_decode(file_get_contents($this->cookieFile),true);
                $_coo = array_merge($_coo, $this->cookies);
            }
        }
        $this->cookies = $_coo;
        foreach($_coo as $k=>$v){
            if($this->config->engine["encode_cookie"])$v = urlencode($v);
            $c.="{$k}={$v}; ";
        }
        return $c;
    }
    public function inCookie(){
        foreach ($this->cookies as $key => $value) {
            if($key!="googtrans")
                if($this->config->engine["encode_cookie"]) setcookie($key,$value,time()+60*60*24*30,'/',$_SERVER["HTTP_HOST"]);
                else {
                    if(preg_match("/[\r\n\t;,]/",$value)){
                        Log::debug("wrong cookie - ".$key."=".$value);
                        $value = urlencode($value);
                    }
                    setrawcookie($key,$value,time()+60*60*24*30,'/',$_SERVER["HTTP_HOST"]);
                }
        }
        file_put_contents($this->cookieFile,json_encode($this->cookies,JSON_PRETTY_PRINT));
    }
    public function inHeaders(){
        //Log::debug(json_encode($this->headers,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        foreach ($this->headers as $h=>$v) {
            if($h=="Location"){
                if(preg_match("/(http|https)\:\/\/(.+?)\//i",$v,$m)){
                    $this->config->host = $m[1]."://".$m[2];
                    Log::debug("redirect detected - location: ".json_encode($m,JSON_PRETTY_PRINT));
                }
                setrawcookie("xray_host",urlencode($this->config->host),time()+60*60*24*30,'/',$_SERVER["HTTP_HOST"]);
            }
            if(!in_array($h,[
                "Content-Length",
                "Content-Encoding",
                "Transfer-Encoding",
                "Location"
            ]))header("{$h}: {$v}");
        }
    }
};
?>
