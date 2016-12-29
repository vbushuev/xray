<?php
namespace g;
use \Log as Log;
class Filter{
    protected $cfg;
    protected $s;
    public function __construct($cfg){
        $this->cfg = $cfg;

    }
    public function fetch($s = "",$contentType){
        $this->s = $s;
        if(preg_match("'text/css'i",$contentType)) {//Cascading Style sheets
            // local url replace
            $this->s = preg_replace_callback('#(["\'])(/[a-z0-9\\-]+)+["\']#ixs',function($r)use($t){
                $res = $this->_expandlinks($r[2]);
                Log::debug("filtered [".$r[2]."] >> [".$res."]");
                return $res;
            },$this->s);
        }
        if(preg_match("'text/html'i",$contentType)){//HTML
            $this->_stripurls();
            $this->_striphost();
        }
        if(preg_match("'json|javascript'i",$contentType)) {//javascript
            //$this->_striphost();
        }

        return $this->s;
    }
    protected function _stripurls(){
        $t=$this;
        //preg_match_all("/(?:https?:)?\/{2}[^\s\?\"'\>]+/ixs",$this->s,$m1);
        //preg_match_all('#(["\'])(/[a-z0-9\\-]*)+["\']#ixs',$this->s,$m2);
        //preg_match_all('/'.preg_quote($this->cfg["domain"],'/').'/ixs',$this->s,$m3);
        //$m = array_merge($m1[0],$this->_expandlinks($m2[2]),$m3[0]);

        // full urls replace
        $this->s = preg_replace_callback("/(?:https?:)?\/{2}[^\s\?\"'\>]+/ixs",function($r)use($t){
            $res = "//".$this->cfg["localhost"]."?".REQUEST_PARAMETER_NAME."=".base64_encode($r[0]);
            Log::debug("filtered [".$r[0]."] >> [".$res."]");
            return $res;
        },$this->s);
        // local url replace
        $this->s = preg_replace_callback('#(["\'])(/[a-z0-9\\-]+)+["\']#ixs',function($r)use($t){
            $res = "//".$this->cfg["localhost"]."?".REQUEST_PARAMETER_NAME."=".base64_encode($this->_expandlinks($r[2]));
            Log::debug("filtered [".$r[2]."] >> [".$res."]");
            return $res;
        },$this->s);

    }
    protected function _striphost(){
        $t=$this;
        // donor domain in scripts context
        $this->s = preg_replace_callback('/'.preg_quote($this->cfg["mainhost"],'/').'/ixs',function($r)use($t){
            $res = $this->cfg["localhost"];
            Log::debug("filtered [".$r[0]."] >> [".$res."]");
            return $res;
        },$this->s);
        // donor domain in scripts context
        $this->s = preg_replace_callback('/'.preg_quote($this->cfg["domain"],'/').'/ixs',function($r)use($t){
            $res = $this->cfg["localhost"];
            Log::debug("filtered [".$r[0]."] >> [".$res."]");
            return $res;
        },$this->s);
    }
    protected function _striplinks(){
        preg_match_all("'(href|src|link)\s*=\s*			# find <a href=
						([\"\'])?					# find single or double quote
						(?(1) (.*?)\\1 | ([^\s\>]+))		# if quote found, match up to next matching
													# quote, otherwise match up to next space
						'isx", $this->s, $links);


        // catenate the non-empty matches from the conditional subpattern

        while (list($key, $val) = each($links[3])) {
            if (!empty($val))
                $match[] = $val;
        }

        while (list($key, $val) = each($links[3])) {
            if (!empty($val))
                $match[] = $val;
        }
        $match = $this->_expandlinks($match);
        // return the links
        return $match;
    }
    protected function _expandlinks($links){
        $URI = $this->cfg["lasturl"];
        preg_match("/^[^\?]+/", $URI, $match);
        $match = preg_replace("|/[^\/\.]+\.[^\/\.]+$|", "", $match[0]);
        $match = preg_replace("|/$|", "", $match);
        $match_part = parse_url($match);
        $match_root = $match_part["scheme"] . "://" . $match_part["host"];

        $search = array("|^http://" . preg_quote($this->host) . "|i",
            "|^(\/)|i",
            "|^(?!http://)(?!mailto:)|i",
            "|/\./|",
            "|/[^\/]+/\.\./|"
        );

        $replace = array("",
            $match_root . "/",
            $match . "/",
            "/",
            "/"
        );

        $expandedLinks = preg_replace($search, $replace, $links);

        return $expandedLinks;
    }
};
?>
