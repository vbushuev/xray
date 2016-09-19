<?php
class Config extends Common{
//<<<<<<< Updated upstream
    protected $host="http://www.baby-walz.fr";
    protected $donor;
    protected $donor_pattern;
    protected $cache="cache";
    public function __construct($a=[]){
        if(!isset($a["hosts"]))return;
        $_=$_SERVER["HTTP_HOST"];
        $_a=preg_split("/\./",$_);
        //Log::debug($_." -> ".json_encode($_a));
        if(count($_a)){
            if(isset($a["hosts"][$_a[0]])) $this->host = $a["hosts"][$_a[0]]["url"];
        }
        $this->donor = preg_replace("/(http|https):\/\//i","",$this->host);
        //Log::debug("host:".$this->host." donor:".$this->donor);
        $this->donor_pattern = preg_replace("/(\/\/)?www\./i","",$this->donor);
        $this->donor_pattern = preg_quote($this->donor_pattern);
        $this->cache = isset($a["cache"])?$a["cache"]:$this->cache;
    }
/*
=======
    protected $host;
    protected $cache = "cache";
    protected $schema = "http://";
    public function __construct($a = []){
        $_ = $_SERVER["HTTP_HOST"];
        $_ = preg_replace("/([a-z0-9\-]+)\.(xray\.bs2|xrayshopping\.ru|xrayshopping\.com|xray\.garan24\.ru)/i","$1",$_);
        $_ = (isset($a["stores"])&&isset($a["stores"][$_]))?$a["stores"][$_]["host"]:"www.baby-walz.fr";
        $this->host = $_;
        Log::debug("Detect host:".$_);
        $this->cache = (isset($a["cache"]))?$a["cache"]:$this->cache;
    }
>>>>>>> Stashed changes
*/
};
?>
