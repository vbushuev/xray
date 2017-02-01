<?php
namespace g;
use \Log as Log;
use \simple_html_dom as simple_html_dom;
class Translator{
    protected $_d = [];
    protected $_d_sorted = [];
    protected $db;
    protected $_langDetectPattern = "/[a-zàÉêé]/i";
    protected $_textTags = ["li","a","span","p","h1","h2","h3","h4","h5","h6","i","cite","code","pre","b","strong","div","button","section","article","td"];
    public function __construct($cfg){
        $this->db = new \g\DBConnector();
        $this->setLang($cfg);
    }
    public function setLang($cfg){
        $lang = isset($cfg["lang"])?$cfg["lang"]:"fr";
        $this->_d = $this->db->selectAll("select * from g_dictionary where lang='".$lang."' order by length(original) desc,priority desc");
        $this->_d_sorted = array_map(function($v){return [strtolower($v["original"])=>$v["translate"]];},$this->_d);
        if($lang=="en")$this->_langDetectPattern = '/[a-zA-Z]/i';
    }
    public function translate($in){
        $out = isset($this->_d_sorted[strtolower(trim($in))])?$this->_d_sorted[strtolower(trim($in))]:$in;
        return $out;
    }
    public function translateText($in){
        $out = $in;
        foreach($this->_d as $di){
            if(preg_match($this->_langDetectPattern,$out))$out = preg_replace("/".preg_quote($di["original"],'/')."/im",$di["translate"],$out);
            else return $out;

        }
        return $out;
    }
    public function translateHtml($in){
        //return $this->translateText($in);
        $out = $in; $t = $this;
        //$out = preg_replace("/[\r\n]/m"," ",$out);
        //$pattern = "/(<(".join("|",$this->_textTags).")[^>]*>)([^<]+)/ixsm";
        $pattern = "/(<(?!script|\!|\s)[^>]*>)([^<]+)<\//ixsm";
        Log::debug($pattern);
        $out = preg_replace_callback($pattern,function($m)use($t){
            $val = trim($m[2]);$he = trim($m[1]);
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
                Log::debug("[".preg_replace("/<([^>\s]+).*/i","$1",$he)."] ".$val." >>> ".$res);
            }
            return $he.$res.'</';
        },$out); return $out;
        /*$pattern = "/<input(.+?)value=['\"](.+?)['\"]/ixsm";
        Log::debug($pattern);
        $out = preg_replace_callback($pattern,function($m)use($t){
            $val = $m[2];$he = $m[1];
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
                Log::debug("[input]\t".$val." >>> ".$res);
            }
            return "<input".$he."value='".$res."'";
        },$out); return $out;*/

        $html = new simple_html_dom();
        $html->load($in);
        //$els = $html->find(":text:not(:has(".join($this->_textTags,", ")."))");
        //$els = $html->find(join($this->_textTags,":not(:has(".join($this->_textTags,", ").")), "));
        $els = $html->find(join($this->_textTags,", "));
        foreach ($els as $el) {
            if(!empty(trim($el->innertext))){
                $tr = $this->translateText($el->innertext);
                Log::debug($el->tag.": ".$el->innertext." >>> ".$tr);
                $el->innertext = $tr;
            }
        }
        return $html->save();
        $doc = \phpQuery::newDocument($in);
        $text = $doc[":text:not(:has(".join($this->_textTags,", ")."))"];
        //Log::debug("Found ".json_encode($text[0],JSON_PRETTY_PRINT). " text tag");return $in;
        foreach ($text as $tag) {
            $qtag =pq($tag);
            if(in_array(strtolower($tag->tagName),$this->_textTags)){
                $t=$qtag->text();
                if(!empty(trim($t))){
                    $tr = $this->translateText($t);
                    //$tr = $this->translate($t);
                    //Log::debug($tag->tagName." : ".$t." >>> ".$tr);continue;
                    $qtag->text($tr);
                }
            }
        }
        $out = $doc->__toString();
        return $out;
    }
};
?>
