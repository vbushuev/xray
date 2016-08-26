<?php
class Filter extends Common{
    protected $donor;
    protected $donor_pattern;
    public function __construct($a){
        $this->donor = isset($a["host"])?$a["host"]:"www.kik.de";
        $this->donor_pattern = preg_replace("/www\./i","",$this->donor);
        $this->donor_pattern = preg_quote($this->donor_pattern);
    }
    public function filter($in){
        $out = $in;
        $t = $this;
        $out = preg_replace_callback("/((http|https):)?(\/\/)?(www\.)?".$this->donor_pattern."/im",function($m)use($t){
            $ret = "//".$_SERVER["HTTP_HOST"];
            //Log::debug("Replaced[0]: ".$m[0]." => ".$ret);
            return $ret;
        },$out);

        //$patterns = "/(?<qs>[\"'\(])\/?([a-z0-9]\S+?)(?<file>[a-z0-9_]+\.(jpg|png|gif)(?<qe>[\"'\(]))/im";
        /*
        $patterns = [
            "/(?<qs>url\()(?<file>((.+?)[^\/]+?)\.(jpg|png|gif))(?<qe>\))/im"
        ]; //"/(?<qs>[\"'\(])\/?([a-z0-9]\S+?)(?<file>[a-z0-9_]+\.(jpg|png|gif)(?<qe>[\"'\(]))/im";
        $out = preg_replace_callback($patterns,function($m)use($t){
            //$ret = $m["qs"]."/cache/".$t->donor."img/".$m["file"].$m["qe"];
            $ret = $m["qs"]."//".$_SERVER["HTTP_HOST"]."/".$m["file"].$m["qe"];
            Log::debug("Replaced: ".$m[0]." => ".$ret);
            return $ret;
        },$out);
        */
        return $out;
    }
    public function match($in){
        $out = $in;
        return $out;
    }
};
?>
