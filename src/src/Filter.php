<?php
class Filter extends Common{
    protected $donor;
    protected $donor_pattern;
    public function __construct($cfg){
        $this->donor = $cfg->donor;
        $this->donor_pattern = $cfg->donor_pattern;
    }
    public function filter($in){
        $out = $in;
        $t = $this;
        //Log::debug("Donor:".$this->donor." pattern:".$this->donor_pattern);
        $out = preg_replace_callback("/(?<q>[\"'\(])((http|https):)?(\/\/)?(www\.)?".$this->donor_pattern."/im",function($m)use($t){
            $ret = $m["q"]."//".$_SERVER["HTTP_HOST"];
            //Log::debug("Replaced: p["."/((http|https):)?(\/\/)?(www\.)?".$this->donor_pattern."/im"."] ".$m[0]." => ".$ret);
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
