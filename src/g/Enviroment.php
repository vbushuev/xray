<?php
namespace g;
use \Log as Log;
class Enviroment{
    protected $cfg = [];
    public function __construct($cfg){
        $this->cfg = $cfg;
        if(isset($_SESSION["current_url"]))$this->cfg["url"]= $_SESSION["current_url"];
        if(!isset($this->cfg["url"])) throw new \Exception("no url");
        $ui = parse_url($this->cfg["url"]);

        $this->cfg["localhost"] = $_SERVER["SERVER_NAME"];
        $sd = preg_split("/\./",$_SERVER["SERVER_NAME"]);
        $sdc = count($sd);
        if(preg_match("/\.bs2/i",$_SERVER["SERVER_NAME"])){
            $this->cfg["localhost"] = "xray.bs2";
            $this->cfg["subdomain"] = ($sdc==3)?$sd[0]:"www";
        }else{
            $this->cfg["localhost"] = $sd[$sdc-3].".".$sd[$sdc-2].".".$sd[$sdc-1];
            $this->cfg["subdomain"] = ($sdc==4)?$sd[0]:"www";
        }
        $this->cfg["localschema"] = preg_match("/\.bs2/i",$_SERVER["SERVER_NAME"])?"http":"https";
        //$this->cfg["domain"] = preg_replace("/^[^\.]*\./i","",$ui["host"]);
        $this->cfg["domain"] = preg_replace("/^([^\.]*)\.?([^\.]+)\.(.+)$/i","$2.$3",$ui["host"]);
        $this->cfg["scheme"] = isset($ui["scheme"])?$ui["scheme"]:false;
        //$this->cfg["mainhost"] = isset($ui["host"])?$ui["host"]:false;
        $this->cfg["mainhost"] = $this->cfg["subdomain"].".".$this->cfg["domain"];
        $this->cfg["port"] = isset($ui["port"])?$ui["port"]:false;
        $this->cfg["user"] = isset($ui["user"])?$ui["user"]:false;
        $this->cfg["pass"] = isset($ui["pass"])?$ui["pass"]:false;
        $this->cfg["path"] = isset($ui["path"])?$ui["path"]:false;
        $this->cfg["query"] = isset($ui["query"])?$ui["query"]:false;
        $this->cfg["fragment"] = isset($ui["fragment"])?$ui["fragment"]:false;
        //$this->cfg["subdomain"] = preg_replace("/^([^\.]*)\.?([^\.]+)\.(.+)$/i","$1",$ui["host"]);
        //if(preg_match("/^([^\.]+)\.".preg_quote($this->cfg["localhost"],'/')."/ixs",$_SERVER["SERVER_NAME"],$m)){
        //    $this->cfg["subdomain"] = $m[1];
        //    $this->cfg["mainhost"] = $this->cfg["subdomain"].".".$this->cfg["domain"];
        //}
        if($this->cfg["subdomain"]=="eulerian")$this->cfg["scheme"]="http";
        if(isset($_REQUEST["_xg_u"])){
            $this->url = urldecode($_REQUEST["_xg_u"]);
            $ui = parse_url($this->url);
            $this->cfg["url"] = $this->url;
            $this->cfg["scheme"] = isset($ui["scheme"])?$ui["scheme"]:false;
            $this->cfg["host"] = isset($ui["host"])?$ui["host"]:false;
            $this->cfg["port"] = isset($ui["port"])?$ui["port"]:false;
            $this->cfg["user"] = isset($ui["user"])?$ui["user"]:false;
            $this->cfg["pass"] = isset($ui["pass"])?$ui["pass"]:false;
            $this->cfg["path"] = isset($ui["path"])?$ui["path"]:false;
            $this->cfg["query"] = isset($ui["query"])?$ui["query"]:false;
            $this->cfg["fragment"] = isset($ui["fragment"])?$ui["fragment"]:false;
            $pi = pathinfo($this->cfg["path"]);
            $this->cfg["dirname"] = isset($pi["dirname"])?$pi["dirname"]:false;
            $this->cfg["basename"] = isset($pi["basename"])?$pi["basename"]:false;
            $this->cfg["filename"] = isset($pi["filename"])?$pi["filename"]:false;
            $this->cfg["extension"] = isset($pi["extension"])?$pi["extension"]:false;
        }
        else {
            Log::debug("query: ".$_SERVER["REQUEST_URI"]);
            $query = preg_replace("/#g_/i","",$_SERVER["REQUEST_URI"]);

            $query = preg_replace("/(http|https)?(%3A)?(%2F%2F)?".preg_quote($this->cfg["localhost"],'/')."/i",urlencode($this->cfg["mainhost"]),$query);
            $this->cfg["url"] = $this->cfg["scheme"]."://".$this->cfg["mainhost"].$query;
            Log::debug("query: ".$query);
        }

        Log::debug($this->cfg);
    }
    public function __get($n){
        if(!isset($this->cfg[strtolower($n)])) throw new \Exception("no such parameter {$n}");
        return $this->cfg[strtolower($n)];
    }
};
?>
