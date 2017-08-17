<?php
namespace g;
use \Log as Log;
use core\HTTPConnector as Http;
class YandexTranslate {
    protected $_cache_file = 'dicts/ya.json';
    protected $_key_file = 'dicts/ya.key.json';
    protected $keys = [
        "trnsl.1.1.20170424T120324Z.ecffc2978c838451.8089eb046bfa94e80fc42ff1dfafb200e3c58291",
        "trnsl.1.1.20170424T120657Z.8d42cea2cf6ecc61.1163f8484bb2fe9ff2c69c61799ba131f8de68b7",
        "trnsl.1.1.20160808T114104Z.4fca987aa626b8c2.91ed21fc6a7d733075f78f8cca41fcecf4146acd",
        "trnsl.1.1.20170424T115747Z.64d4e5911d244e8d.7323646b435386c1b2b88d150bada412848c3b68",
        "trnsl.1.1.20170424T120243Z.f6d5607d5db67926.d2cbe6053ff1e96c8525c9f0737a8724229d8f8f",
    ];
    protected $currentKeyNumber = 0;
    protected $host = "https://translate.yandex.net/api/v1.5/tr.json/translate";
    protected $dict = [];
    public function __construct(){
        if(file_exists($this->_cache_file))$this->dict = json_decode(file_get_contents($this->_cache_file),true);
        if(file_exists($this->_key_file))$this->currentKeyNumber = file_get_contents($this->_key_file);
    }
    public function get($in,$lang='de-ru'){
        $_in = mb_strtolower(trim($in));
        if(!isset($this->dict[$lang])) $this->dict[$lang]=[];
        if(isset($this->dict[$lang][$_in]))return $this->dict[$lang][$_in];
        $out = $this->request($_in,$lang);
        if(!strlen(trim($out))){
            $this->changeKey();
            $out = $this->request($_in,$lang);
        }
        $this->dict[$lang][$_in] = $out;
        return $out;
    }
    protected function request($in,$lang){
        $h = new Http();$out = $in;
        $u = $this->host."?key=".$this->keys[$this->currentKeyNumber];
        $resp = $h->fetch($u,'POST',["text"=>$in,"lang"=>$lang,'format'=>'plain']);
        Log::debug($in." >>> \n".$resp);
        $resp = json_decode($resp);
        if($resp->code=="200"||$resp->code==200){
            $out = $resp->text[0];
            $this->store();
        }
        $h->close();
        return $out;
    }
    protected function store(){
        file_put_contents($this->_cache_file,json_encode($this->dict,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    }
    protected function changeKey(){
        $this->currentKeyNumber++;
        if($this->currentKeyNumber>=count($this->keys)-1){
            $this->currentKeyNumber=0;
            file_put_contents($this->_key_file,$this->currentKeyNumber);
        }

    }
}
?>
