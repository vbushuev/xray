<?php
class Config extends Common{
    protected $host="http://www.ctshirts.com";
    protected $section = "ctshirts";
    protected $donor;
    protected $donor_pattern;
    protected $cache="cache";
    protected $use_cache=true;
    protected $cookie = [];
    protected $js;
    protected $css;
    protected $template;
    protected $proxy = false;
    protected $site = [];
    protected $counters = [];
    protected $lang = "de";
    protected $secure = false;
    protected $engine = [
        "encode_cookie" => true,
        "restricted_headers" =>false,
        "client_cookie" => [ "use"=>false ]
    ];
    public function __construct($a=[]){
        if(!isset($a["hosts"]))return;
        $_=$_SERVER["HTTP_HOST"];
        $_a=preg_split("/\./",$_);
        $this->section = $_a[0];
        if(isset($a["cache"])){
            $this->cache = isset($a["cache"]["path"])?$a["cache"]["path"]:$this->cache;
            $this->use_cache = isset($a["cache"]["use"])?$a["cache"]["use"]:$this->use_cache;
        }
        if(count($_a)&&isset($a["hosts"][$_a[0]])){
            $cs = $a["hosts"][$_a[0]];
            $this->host = (isset($cs["url"]))?$cs["url"]:$this->host;
            $this->proxy = (isset($cs["proxy"]))?$cs["proxy"]:false;
            $this->cookie = (isset($cs["cookie"]))?$cs["cookie"]:[];
            $this->js = (isset($cs["js"]))?$cs["js"]:$_a[0].".js";
            $this->css = (isset($cs["css"]))?$cs["css"]:$_a[0].".css";
            //$this->lang = (isset($cs["lang"]))?$cs["lang"]:$this->lang;
            $this->template = (isset($cs["template"]))?$cs["template"]:$_a[0].".php";
            $this->template = file_exists("templates/".$this->template)?$this->template:"default.php";
            $this->site = (isset($cs["site"]))?$cs["site"]:[
                "title" => "GauzyMALL - удобные покупки",
                "lang" => "de"
            ];
            if(isset($cs["cache"])){
                $this->cache = isset($cs["cache"]["path"])?$cs["cache"]["path"]:$this->cache;
                $this->use_cache = isset($cs["cache"]["use"])?$cs["cache"]["use"]:$this->use_cache;

            }
            $this->engine = array_merge($this->engine,isset($cs["engine"])?$cs["engine"]:$this->engine);
        }
        $this->donor = preg_replace("/(http|https):\/\//i","",$this->host);
        //Log::debug("host:".$this->host." donor:".$this->donor);
        $this->donor_pattern = preg_replace("/(\/\/)?www\./i","",$this->donor);
        $this->donor_pattern = preg_replace("/\/*$/","",$this->donor_pattern);
        $this->donor_pattern = preg_quote($this->donor_pattern);

        //check headers use cache
        foreach (getallheaders() as $name => $value) {
            if($name=="Cache-Control"&&in_array($value,["no-cache",'no-store'])){
                //Log::debug("request header $name:$value");
                $this->use_cache = false;
            }
        }
        if(preg_match("/demandware\.store(.+?)cart\-show/i",$_SERVER["REQUEST_URI"])){
            if(!preg_match("/https\:\/\//i",$this->host)){
                $this->host = preg_replace("/http/i","https",$this->host);
                Log::debug("Host to secure.");
            }
        }
        if(preg_match("/https\:\/\//i",$this->host))$this->secure = true;
    }

};
?>
