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
        foreach($this->_d as $v){$this->_d_sorted[mb_strtolower(preg_replace('/\\\u2019/ium',"'",$v["original"]))]=$v["translate"];}
        Log::debug(array_keys($this->_d_sorted));
        if($lang=="en")$this->_langDetectPattern = '/[a-z]/i';
    }
    public function translate($in){
        $out = isset($this->_d_sorted[strtolower(trim($in))])?$this->_d_sorted[strtolower(trim($in))]:$in;
        return $out;
    }
    public function translateText($in){
        $out = trim($in);
        $_last='cc';
        foreach($this->_d as $di){
            if(strlen($out)<$di["priority"])continue;
            //if(preg_match("/jusq.*/im",$di["original"])&&preg_match("/jusq.*/im",$out)) Log::debug("replace for "."/\b".preg_quote(strtolower($di["original"]),'/')."\b/ium  -- ".$out);
            if(preg_match($this->_langDetectPattern,$out)){
                $out = preg_replace("/&#039;/mu","'",$out);
                $out = preg_replace("/&nbsp;/mu"," ",$out);
                if(empty($out)) return $in;
                if(isset($this->_d_sorted[mb_strtolower($out)])) {
                    Log::debug("Identical !!! ".$out);
                    $out = $this->_d_sorted[mb_strtolower($out)];
                    $out = mb_strtoupper(mb_substr($out, 0, 1)).mb_substr($out, 1);
                    return $out;
                }
                if($out!=$_last){
                    Log::debug("Try {".$di["original"]."} on {".$out."}");
                    $_last = $out;
                }
                //$out = preg_replace("/\b".preg_quote(strtolower($di["original"]),'/')."\b/ium",$di["translate"],$out);
                $pattern = preg_quote(mb_strtolower(preg_replace("/\\\u2019/ium","'",$di["original"])),'/');
                $out = preg_replace_callback("/\b".$pattern."\b/ium",function($m)use($di){
                    $vin = $m[0];
                    $vou = $di["translate"];
                    if(preg_match("/^\s*[A-Z]/m",trim($vin))){
                        $vou = trim($vou);
                        $vou = mb_strtoupper(mb_substr($vou, 0, 1)).mb_substr($vou, 1);
                    }
                    return $vou;
                },$out);

            }
            else break;
        }
        return $out;
    }
    public function translateHtml($in){
        //return $this->translateText($in);
        $out = $in; $t = $this;
        //$out = preg_replace("/[\r\n]/m"," ",$out);
        //$pattern = "/(<(".join("|",$this->_textTags).")[^>]*>)([^<]+)/ixsm";
        $pattern = "/(<(?!script|\!|\s)[^>]*>)([^<]+)<\//ixsm";
        //Log::debug($pattern);
        $out = preg_replace_callback($pattern,function($m)use($t){
            $val = trim($m[2]);$he = trim($m[1]);$res=$val;
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
                //Log::debug("[".preg_replace("/<([^>\s]+).*/i","$1",$he)."] ".$val." >>> ".$res);
            }
            return $he.$res.'</';
        },$out); return $out;
        $pattern = "/<input(.+?)value=['\"]([^\"']+)['\"]/ixsm";

        $out = preg_replace_callback($pattern,function($m)use($t){
            $val = $m[2];$he = $m[1];
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
                Log::debug("[input]\t".$val." >>> ".$res);
            }
            return "<input".$he."value='".$res."'";
        },$out); return $out;

        $out = preg_replace_callback('/placeholder=[\'"](.+?)[\'"]/',function($m)use($t){
            $val = $m[1];;
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
            }
            return 'placeholder="'.$res.'""';
        },$out); return $out;


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
