<?php
class Config extends Common{
    protected $host="http://www.baby-walz.fr";
    protected $donor;
    protected $donor_pattern;
    protected $cache="cache";
    protected $cookie = [];
    protected $js;
    public function __construct($a=[]){
        if(!isset($a["hosts"]))return;
        $_=$_SERVER["HTTP_HOST"];
        $_a=preg_split("/\./",$_);
        //Log::debug($_." -> ".json_encode($_a));
        if(count($_a)){
            if(isset($a["hosts"][$_a[0]])) $this->host = $a["hosts"][$_a[0]]["url"];
            $this->cookie = (isset($a["hosts"][$_a[0]]["cookie"]))?$a["hosts"][$_a[0]]["cookie"]:[];
            $this->js = (isset($a["hosts"][$_a[0]]["js"]))?$a["hosts"][$_a[0]]["js"]:$_a[0].".js";
        }

        $this->donor = preg_replace("/(http|https):\/\//i","",$this->host);
        //Log::debug("host:".$this->host." donor:".$this->donor);
        $this->donor_pattern = preg_replace("/(\/\/)?www\./i","",$this->donor);
        $this->donor_pattern = preg_replace("/\/*$/","",$this->donor_pattern);
        $this->donor_pattern = preg_quote($this->donor_pattern);
        $this->cache = isset($a["cache"])?$a["cache"]:$this->cache;
    }

};
?>
