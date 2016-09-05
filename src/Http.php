<?php
class Http extends Common{
    protected $results;
    protected $response;
    protected $cookies=[];
    protected $config;
    public function __construct($a=[]){
        $this->config = $a;
    }
    public function fetch($url){
        $curl = curl_init();
        $host = parse_url($url);
        $refer = $a->host.$_SERVER["REQUEST_URI"];
        //Log::debug("parse_url ".json_encode($host,JSON_PRETTY_PRINT));
        $cookies = $this->outCookie();
        //Log::debug("sending COOKIES=[".$cookies."]");
        $headers = [
            'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Cookie: '.$cookies,
            'Referer: '.$refer
            //'Cookie: fe_typo_user=caf3946af581025e806361097ab13a68;mb3pc=%7B%22shoppingbasket%22%3A%7B%22articlesAmount%22%3A0%7D%7D;'
            /*'Accept-Language:en-US,en;q=0.8,ru;q=0.6',
            'Accept-Charset: utf-8;q=0.7,*;q=0.7',
            'Accept-Encoding:gzip, deflate, sdch',
            'Cache-Control:max-age=0',
            'Connection:keep-alive',
            ,
            'DNT:1',
            */
            //'Upgrade-Insecure-Requests:1',
            //'Host:www.baby-walz.de',
            //
        ];
        //$cookieFile = $_SERVER['DOCUMENT_ROOT'].'/cache/'.$host["host"].'/cookie.txt';
        //$cookie = 'cookie.txt';

        //if(!file_exists($cookieFile))file_put_contents($cookieFile,"");
        $verbose = fopen('logs/curl-'.date('Y-m-d').'.log', 'a+');

        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => "User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36",
            CURLOPT_HTTPHEADER=>$headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_REFERER => $host["host"],
            CURLOPT_HEADER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_ENCODING => "", // обрабатывает все кодировки
            CURLOPT_MAXREDIRS =>10, // останавливаться после 10-ого редиректа
            //CURLOPT_COOKIE => $cookie,
            //CURLOPT_COOKIEJAR =>  $cookieFile,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_FORBID_REUSE => 1,
            //CURLOPT_AUTOREFERER => 1,
            CURLOPT_FOLLOWLOCATION => 1,

            CURLOPT_VERBOSE => 1,
            CURLOPT_STDERR => $verbose,
            CURLOPT_CERTINFO => 1,
            CURLINFO_HEADER_OUT => 1,
        ];
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == 'POST'){
            $curlOptions[CURLOPT_POST]=1;
            $curlOptions[CURLOPT_POSTFIELDS]=http_build_query($_POST);
        }
        curl_setopt_array($curl, $curlOptions);
        $this->results = $this->stripHeaders(curl_exec($curl));
        $this->response = curl_getinfo($curl);
        Log::debug('got COOKIES:['.json_encode($this->cookies,JSON_PRETTY_PRINT)."]");
        curl_close($curl);
    }
    protected function stripHeaders($_){
        $_r = $_;
        $_a = preg_split("/[\r\n]+/i",$_,2);
        if(is_array($_a)&&strlen(trim($_a[0]))){
            //Log::debug('got HEADRs:'.$_a[0]);
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
        //if(!is_array($this->cookies))return "";
        foreach($_COOKIE as $k=>$v){
            //$v = urlencode($v);
            $c.="{$k}={$v};";
        }
        return $c;
    }
    public function inCookie(){
        foreach ($this->cookies as $key => $value) {
            //if(!isset($_COOKIE[$key])){
                //Log::debug("setcookie $key = $value");
                setcookie($key,$value);//,time()+60*60*24,"/",$_SERVER["HTTP_HOST"]);
            //}
        }
    }
};
?>
