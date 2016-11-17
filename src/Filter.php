<?php
class Filter extends Common{
    protected $donor;
    protected $donor_pattern;
    protected $cfg;
    public function __construct($cfg){
        $this->cfg = $cfg;
        $this->donor = $cfg->donor;
        $this->donor_pattern = $cfg->donor_pattern;
    }
    public function filter($in){
        $out = $in;
        $t = $this;
        $patterns = [
            "common" => "/(?<q>[\"'\(]|\&#039;)((http|https):)?(\/\/)?(www\.)?".$this->donor_pattern."/im",
            "special" => [
                "/https".preg_quote('\x3A\x2F\x2F')."www\.".$this->donor_pattern."/"  => "\\x2F\\x2F".$_SERVER["HTTP_HOST"],
                "/http".preg_quote('\x3A\x2F\x2F')."www\.".$this->donor_pattern."/"  => "\\x2F\\x2F".$_SERVER["HTTP_HOST"],
                "/\<script\s+src\=\".+demandware\..+\/js\/app\.js\"\>/"     => "<script src='/cache/www.ctshirts.com/app.js'></script>",
                "/\<script\>.+\s+.+\s+.+GoogleAnalyticsObject[\s\S]+?\<\/script\>/" => "",
                "/if\s*\(document\.location\.protocol\s*==\s*'http:'\)/" => "if(false)"




            ]
        ];
        //Log::debug("Donor:".$this->donor." pattern:".$this->donor_pattern);
        //$out = preg_replace_callback("/(?<q>[\"'\(])((http|https):)?(\/\/)?(www\.)?".$this->donor_pattern."/im",function($m)use($t){
//<a href="#" class="sizeHelper" onclick="showSizeHelperWindow('https\x3A\x2F\x2Fwww.eduscho.at\x2F\x3Fx\x3DH4sIAAAAAAAAAAHCAD3_ETMsDgAAAVgAnucUABRBRVMvQ0JDL1BLQ1M1UGFkZGluZwCAABAAELeBnK9ooKTsYxgZRb3DhwAAAABw\x2D\x2DaN0zXhgZvrgtqX3z1m5L14LDipE\x2DvZcSDfM_OUQmkAbDgHPmVrmZrfWLdVMabhrw0SWXRUQcfuC3uX0pfK5BSOstSpwBrSJxz5OQKYRq12XKlbVnCIVyLmTPW5zSbL19dI4xD6G9thKoXExbXMmgAUS0pW7Hd2SVbZyDCUnmqlgt7R7fec34sBwgAAAA\x253D\x253D'); fdc.hunter.trackClick('LINK_GROESSENFINDER_HERREN_HERRENBEKLEIDUNG'); return false;"><font><font class="">определить правильный размер</font></font></a>
        $out = preg_replace_callback($patterns["common"],function($m)use($t){
            $ret = $m["q"]."//".$_SERVER["HTTP_HOST"];
            //Log::debug("Replaced: p["."/((http|https):)?(\/\/)?(www\.)?".$this->donor_pattern."/im"."] ".$m[0]." => ".$ret);
            return $ret;
        },$out);
        foreach($patterns["special"] as $p=>$r){
            //Log::debug("replacing p[".$p."]");
            $out = preg_replace_callback($p,function($m)use($p,$r){
                $ret = $r;
                //Log::debug("Replaced: p[".$p."] ".$m[0]." => ".$ret);
                return $ret;
            },$out);
        }
        if(isset($this->cfg->site["title"]))$out = preg_replace("/\<title\>(.*?)\<\/title\>/i","<title>".$this->cfg->site["title"].". $1</title>",$out);

        // Google counter (analytics remover)
        /*
        <!-- begin: GoogleAnalytics (asynchronous) BasicTracking -->

        <script>
            if (typeof(ga) == 'undefined') {
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function()
                { (i[r].q=i[r].q||[]).push(arguments)}
                ,i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
                ga('create', 'UA-8388638-1', 'auto');
            }
            ga('set', 'anonymizeIp', true);
            ga('send', 'pageview');
        </script>

        <!-- end: GoogleAnalytics (asynchronous) BasicTracking -->
        */
        /*$gaPattern =;
        if(preg_match($gaPattern,$out,$m)){
            Log::debug($m[0]);
        }
        $out = preg_replace($gaPattern,"",$out);*/
        return $out;
    }
    public function match($in){
        $out = $in;
        return $out;
    }
};
?>
