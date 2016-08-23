<?php
class Http extends Common{
    protected $results;
    protected $response;
    protected $cookies=[];
    public function __construct($a=[]){
        $this->cookies = isset($a["cookies"])?$a["cookies"]:[];
    }
    public function fetch($url){
        $curl = curl_init();
        $host = parse_url($url);
        $cookie_file = '.cookies';
        $cookies =$this->cookies($url);
        $headers = [
            'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language:en-US,en;q=0.8,ru;q=0.6',
            'Accept-Charset: utf-8;q=0.7,*;q=0.7',
            'Accept-Encoding:gzip, deflate, sdch',
            'Cache-Control:max-age=0',
            'Connection:keep-alive',
            'Cookie: '.$cookies,
            'DNT:1',
            //'Upgrade-Insecure-Requests:1',
            //'Host:www.baby-walz.de',
            //'Referer:http://www.baby-walz.de/'
        ];
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => "User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36",
            CURLOPT_HTTPHEADER=>$headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_REFERER => $host["host"],
            CURLOPT_HEADER => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_ENCODING => "", // обрабатывает все кодировки
            CURLOPT_MAXREDIRS =>10, // останавливаться после 10-ого редиректа
            //CURLOPT_COOKIE => $cookies,
            //CURLOPT_COOKIEJAR =>  $cookies,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_AUTOREFERER => 1,
            //CURLOPT_FOLLOWLOCATION => 1,

            CURLOPT_VERBOSE => 1,
            CURLINFO_HEADER_OUT => 1,
        ];
        curl_setopt_array($curl, $curlOptions);
        $this->results = curl_exec($curl);
        $this->response = curl_getinfo($curl);
        curl_close($curl);
    }
    public function cookies($url){
        $c='';
        if(!is_array($this->cookies))return "";
        foreach($this->cookies as $k=>$v){
            $c.="{$k}={$v};";
        }
        return $c;
    }
};
?>
