<?php
class Media extends Http{
    protected $cache="cache/";
    protected $donor;
    protected $donor_pattern;
    public function __construct($a){
        $this->donor = isset($a["host"])?$a["host"]:"www.kik.de";
        $this->donor_pattern = preg_replace("/www\./i","",$this->donor);
        $this->donor_pattern = preg_quote($this->donor_pattern);
        $this->cache = isset($a["cache"])?$a["cache"]:"cache/";
    }
    public function get($in){
        $out = $in;
        $t = $this;
        if(!is_dir($t->cache))mkdir($t->cache);
        if(!is_dir($t->cache."css"))mkdir($t->cache."css");
        if(!is_dir($t->cache."img"))mkdir($t->cache."img");
        if(!is_dir($t->cache."js"))mkdir($t->cache."js");
        $patterns = [
            "/(?<quoteStart>['\"\(])\/(?<path>[a-z0-9\/\-\_\.]+?)\.(?<extension>png|jpg|gif|svg|css|js|ico|cur|php|html|htm|ttf)(?<frag>\?[a-z-0-9\=\%\&\_\;\#]*)?(?<quoteEnd>['\"\)])/im",
            "/(?<quoteStart>['\"])".$t->donor_pattern."\/(?<path>[a-z0-9\/\-\_\.]+?)\.(?<extension>png|jpg|gif|svg|css|js|ico|cur|php|html|htm|ttf)(?<frag>\?[a-z-0-9\=\%\&\_\;\#]*)?(?<quoteEnd>['\"])/im",
            "/(?<quoteStart>['\"\(])(http|https)?\:?\/?\/?(www\.)".$t->donor_pattern."?(?<path>.+?)(?<quoteEnd>['\"\)])/im"
        ];
        //['\"](http\:)?\/\/(www\.)?kik\.de([a-z0-9\/\-\_\.]+?)\.(png|jpg|gif|svg|css|js|ico|cur|php|html|htm)(\?[a-z-0-9\=\%\&\_\;\#]*)?(['\"])
        $replacements = function($m) use ($t){
            $ret = $m[0];
            // remove two dots
            $file = preg_replace("/\.{2}\//im","",$m["path"]).".".$m["extension"];
            $src = $t->donor."/".$file.$m["frag"];
            $file_full = $this->_filename($file);
            if(preg_match("/^['\"]?\/\//i",$ret)) return $ret;
            if(in_array($m["extension"],["php","html","htm"])) return $m["quoteStart"].$src.$m["quoteEnd"];
            $t->_load($file_full,$src);
            $ret = $m["quoteStart"]."/".$file_full.$m["frag"].$m["quoteEnd"];
            Log::debug($m[0]."=>".$ret);
            return $ret;
        };
        $out = preg_replace_callback($patterns,$replacements,$out);
        return $out;
    }
    protected function _load($f,$s){
        if(!file_exists($f)){
            try{
                $this->fetch($s);
                $e = parse_url($s);
                $e = $e["extension"];
                //$this->fetch($src);$file_data=$this->result;
                if(in_array($e,["js","css","map"])) $file_data=$this->get($this->results);
                file_put_contents($f,$this->results);
            }
            catch(Exception $e){
                Log::error($e);
            }
        }
    }
    protected function _filename($f){
        $pi = pathinfo($f);
        $hi = parse_url($this->donor);
        $dir = $hi["host"]."/";
        if(isset($pi["extension"])){
            if($pi["extension"]=="js"){$dir.="js/";}
            elseif(in_array($pi["extension"],["css","map"])){$dir.="css/";}
            elseif(in_array($pi["extension"],["jpg","gif","png","ico","svg","jpeg"])){$dir.="img/";}
            elseif(in_array($pi["extension"],["ttf","woft"])){$dir.="css/";}
        }
        $ret = $this->cashe.$dir.preg_replace("/[\:\/\-\\\]/m","_",$p);
        return $ret;
    }
}
?>
