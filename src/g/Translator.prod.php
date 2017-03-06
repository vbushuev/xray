<?php
namespace g;
use \Log as Log;
use \simple_html_dom as simple_html_dom;
class Translator{
    protected $_d = [];
    protected $_d_sorted = [];
    protected $db;
    protected $_langDetectPattern = "/[a-zàÉêé]/iu";
    protected $_textTags = ["li","a","span","p","h1","h2","h3","h4","h5","h6","i","cite","code","pre","b","strong","div","button","section","article","td"];
    public function __construct($cfg){
        $this->db = new \g\DBConnector();
        $this->setLang($cfg);
    }
    public function setLang($cfg){
        $lang = isset($cfg["lang"])?$cfg["lang"]:"fr";
        $this->_d = $this->db->selectAll("select * from g_dictionary where lang='".$lang."' order by length(original) desc,priority desc");
        //Log::debug(json_encode($this->_d,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        $this->_d_sorted = [];
        foreach($this->_d as $v){$this->_d_sorted[mb_strtolower(preg_replace(['/\\\u2019/ium','/[\r\n]+/'],["'",""],$v["original"]))]=$v["translate"];}
        //Log::debug(json_encode($this->_d_sorted,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        if($lang=="en")$this->_langDetectPattern = '/[a-z]/i';
    }
    public function translate($in){
        $out = isset($this->_d_sorted[strtolower(trim($in))])?$this->_d_sorted[strtolower(trim($in))]:$in;
        return $out;
    }
    public function translateText($in){
        $out = $in;
        foreach($this->_d as $di){
            if(strlen($out)<$di["priority"])continue;
            if(preg_match($this->_langDetectPattern,$out)){
                $out = preg_replace("/^[\r\n\s]*(.+)[\s\r\n]*$/mu","$1",$out);
                $out = preg_replace("/&#039;/mu","'",$out);
                $out = preg_replace("/&nbsp;/mu"," ",$out);
                if(isset($this->_d_sorted[mb_strtolower($out)])){
                    $out =$this->_d_sorted[mb_strtolower($out)];
                    break;
                }
                else {
                    $original = mb_strtolower($di["original"]);
                    $original = preg_replace(['/\\\u2019/ium','/[\r\n]+/um','/&amp;/um'],["'","",'&'],$original);
                    $original = preg_quote($original,'/');
                    $out = preg_replace("/\b".$original."\b/ium",$di["translate"],$out);
                }
            }
            else break;
        }
        if(preg_match("/^[A-Z]/m",trim($in))){
            $out = trim($out);
            $out = $this->toupper($out);
        }
        return $out;
    }
    public function translateHtml($in){
        //return $this->translateText($in);
        $out = $in; $t = $this;
        $pattern = "/(<(?!script|\!|\s)[^>]*>)([^<]+)<\//ixsm";
        $out = preg_replace_callback($pattern,function($m)use($t){
            $val = trim($m[2]);$he = trim($m[1]);$res=$val;
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);

            }
            return $he.$res.'</';
        },$out);
        $pattern = "/(<(?!script|\!|\s)[^>]*>)([^<]+)<\/(.+?)>/ixsm";
        $out = preg_replace_callback($pattern,function($m)use($t){
            $val = trim($m[2]);$he = trim($m[1]);$res=$val;
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
            }
            return $he.$res.'</'.$m[3].'>';
        },$out);


        $pattern = "/<input(.+?)value=['\"]([^\"']+)['\"]/ixsm";

        $out = preg_replace_callback($pattern,function($m)use($t){
            $val = $m[2];$he = $m[1];
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
                Log::debug("[input]\t".$val." >>> ".$res);
            }
            return "<input".$he."value='".$res."'";
        },$out);

        $out = preg_replace_callback('/placeholder=[\'"](.+?)[\'"]/',function($m)use($t){
            $val = $m[1];;
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
                Log::debug("[placeholder]\t".$val." >>> ".$res);
            }
            return 'placeholder="'.$res.'""';
        },$out);

        return $out;


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
    protected function toupper($s){
        $fc = mb_strtoupper(mb_substr($s, 0, 1));
        return $fc.mb_substr($s, 1);
    }
};
?>
