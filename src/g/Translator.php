<?php
namespace g;
require("vendor/HTML5/Parser.php");
use \Log as Log;
use \simple_html_dom as simple_html_dom;
use \Masterminds\HTML5 as HTML5;
class Translator extends \Common{
    protected $_d = [];
    protected $_d_sorted = [];
    protected $db;
    protected $_langDetectPattern = "/[a-zàÉêé]/iu";
    protected $_textTags = ["li","a","span","p","h1","h2","h3","h4","h5","h6","i","cite","code","pre","b","strong","div","button","section","article","td"];
    protected $_currency = [];
    protected $_lang = 'en';
    protected $_use_cashe = false;
    public function __construct($cfg){
        $this->db = new \g\DBConnector();
        $this->setLang($cfg);
    }
    public function setLang($cfg){
        $lang = isset($cfg["lang"])?$cfg["lang"]:"fr";
        $this->_lang = $lang;
        //check today hash
        $file_dict = "dicts/".$lang."-".date("Y-m-d").".json";
        $file_dict_sorted = "dicts/".$lang."-".date("Y-m-d")."-sorted.json";
        $file_currencies = "logs/currencies-".date("Y-m-d").".json";

        $this->checkPath($file_dict);
        $this->checkPath($file_dict_sorted);
        $this->checkPath($file_currencies);
        if($_use_cashe&&file_exists($file_dict)){
            $this->_d = json_decode(file_get_contents($file_dict),true);
        }
        else {
            $this->_d = $this->db->selectAll("select * from g_dictionary where lang='".$lang."' order by length(original) desc,priority desc");
            file_put_contents($file_dict,json_encode($this->_d,JSON_UNESCAPED_UNICODE));
            //Log::debug(json_encode($this->_d,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        }
        if($_use_cashe&&file_exists($file_dict_sorted)){
            $this->_d_sorted = json_decode(file_get_contents($file_dict_sorted),true);
        }
        else {
            $this->_d_sorted = [];
            foreach($this->_d as $v){$this->_d_sorted[mb_strtolower(preg_replace(['/\\\u2019/ium','/[\r\n]+/'],["'",""],$v["original"]))]=$v["translate"];}
            file_put_contents($file_dict_sorted,json_encode($this->_d_sorted,JSON_UNESCAPED_UNICODE));
            //Log::debug(json_encode($this->_d_sorted,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        }

        //currencies
        if(file_exists($file_currencies)){
            $this->_currency= json_decode(file_get_contents($file_currencies),true);
        }
        else {
            $_currencies= json_decode(file_get_contents("https://l.gauzymall.com/currency"),true);
            foreach($_currencies as $currency){$this->_currency[$currency["iso_code"]] = $currency["value"]*$currency["multiplier"];}
            file_put_contents($file_currencies,json_encode($this->_currency,JSON_UNESCAPED_UNICODE));
        }
        if($lang=="en")$this->_langDetectPattern = '/[a-z]/i';
        else if($lang=="de")$this->_langDetectPattern = '/[a-z]/i';
    }
    public function translate($in){
        $out = isset($this->_d_sorted[strtolower(trim($in))])?$this->_d_sorted[strtolower(trim($in))]:$in;
        return $out;
    }
    public function translateText($in){
        $out = $in;
        Log::debug(preg_replace(["/^\s+/mu","/\s+$/mu"],"",$out));
        foreach($this->_d as $di){
            if(strlen($out)<$di["priority"])continue;
            if(preg_match($this->_langDetectPattern,$out)){
                $out = preg_replace("/^[\r\n\s]*(.+)[\s\r\n]*$/mu","$1",$out);
                $out = preg_replace("/&#039;/mu","'",$out);
                $out = preg_replace("/&nbsp;/mu"," ",$out);

                $out_low = mb_strtolower($out);
                if(isset($this->_d_sorted[$out_low])){
                    $out =$this->_d_sorted[$out_low];
                    break;
                }
                else {
                    $original = $di["original"];
                    //$original = mb_strtolower($di["original"]);
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
        // currency convert
        //convert GBP
        $currency = $this->_currency["GBP"];
        $out = preg_replace_callback("/(£|\&pound;|\&#163;)(\d+\.?\d*)/um",function($m)use($currency){
            $res = floor(floatval($m[2])*$currency);
            return "&nbsp;".$res."&#8381;"."<span class='xg_original_converted' style='display:none'>".$m[2].'</span>';
        },$out);

        //convert EUR
        $currency = $this->_currency["EUR"];
        //$out = preg_replace_callback("/(\d+[\.,]\d+)[\s\r\n]*(&euro;|€)/mu",function($m)use($currency){
        //€ 3.199,00
        $out = preg_replace_callback("/(&euro;|€)\s*(\d*\.?\d+,\d+)/mu",function($m)use($currency){
            $val = preg_replace(["/\./","/,/"],["","."],$m[2]);
            $res = floor(floatval($val)*$currency);
            //Log::debug($m[2]." x ".$currency." => ".$res);
            return " ".$this->price($res)."₽";//.'<span class="xg_original_converted" style="display:none">'.$m[2].'</span>';
        },$out);
        return $out;
    }
    public function translateHtml($in){
        //return $this->translateText($in);
        $out = $in; $t = $this;$skip=false;

        // Do your load here original DOM
        /*
        //libxml_use_internal_errors(TRUE);
        //if(!preg_match("/<\/html>/",$out))$out = "<noscript>".$out."</noscript>";
        $doc = new \DOMDocument("1.0","UTF-8");
        if($doc->loadHTML(mb_convert_encoding($out, 'HTML-ENTITIES', 'UTF-8'),LIBXML_HTML_NOIMPLIED|LIBXML_NOWARNING|LIBXML_HTML_NODEFDTD|LIBXML_NOERROR
            |LIBXML_ERR_NONE
            |LIBXML_ERR_WARNING
            |LIBXML_ERR_ERROR
            |LIBXML_ERR_FATAL
        )){*/
        //$doc = \HTML5_Parser::parse($out);
        $in = preg_replace("/<div class=\"js\-product\-content\"\/>/im",'<div class="js-product-content">',$in);
        $html5 = new HTML5(["disable_html_ns"=>true]);
        $doc = $html5->loadHTML($in);
        if($doc){
            //$errors = libxml_get_errors();
            //Log::debug("DOM loaded");
            $node=$doc;
            while ($node) {
                //Log::debug($node->nodeName." child = ".($node->firstChild?"yes":"no")." slibling=".($node->nextSibling?"yes":"no"));
                if ($node->nodeType == 3) {
                    if(!empty(trim($node->nodeValue)) && $node->parentNode->nodeName!="script"){
                        $new = $this->translateText($node->nodeValue);
                        //Log::debug($node->parentNode->nodeName."\t".$node->nodeValue." -> ".$new);
                        $node->nodeValue = $new;
                    }
                }
                else if ($node->nodeType == 1) {
                    if($node->hasAttribute("placeholder")) {
                        $new = $this->translateText($node->getAttribute("placeholder"));
                        //Log::debug($node->nodeName."\t".$node->getAttribute("placeholder")." -> ".$new);
                        $node->setAttribute("placeholder",$new);
                    }
                    if($node->hasAttribute("type")&&$node->getAttribute("type")=="submit") {
                        $new = $this->translateText($node->getAttribute("value"));
                        //Log::debug($node->nodeName."\t".$node->getAttribute("value")." -> ".$new);
                        $node->setAttribute("value",$this->translateText($node->getAttribute("value")));
                    }
                }
                //else {
                    //Log::debug($node->nodeName." T".$node->nodeType."\t".join(" ",$node->attributes));
                //}
                //if($node->nodeName == "script"){$skip=true;}else
                if(!$skip && $node->firstChild) {$node = $node->firstChild;}
                elseif ($node->nextSibling) {$node = $node->nextSibling;$skip = false;}
                else {$node = $node->parentNode;$skip = true;}
            }
            $out = $doc->saveHTML();
            if(!preg_match("/<\/html>/i",$in)){ //for fragments
                $out = preg_replace("/<\/?html[^>]*>/im","",$out);
                $out = preg_replace("/<\!doctype[^>]*>/im","",$out);
            }


            // currency convert
            //convert GBP
            /*
            $currency = $this->_currency["GBP"];
            $out = preg_replace_callback("/(£|\&pound;|\&#163;)(\d+\.?\d*)/um",function($m)use($currency){
                $res = floor(floatval($m[2])*$currency);
                return "&nbsp;".$res."&#8381;"."<span class='xg_original_converted' style='display:none'>".$m[2].'</span>';
            },$out);

            //convert EUR
            $currency = $this->_currency["EUR"];
            //$out = preg_replace_callback("/(\d+[\.,]\d+)[\s\r\n]*(&euro;|€)/mu",function($m)use($currency){
            //€ 3.199,00
            $out = preg_replace_callback("/(&euro;|€)\s*(\d*\.?\d+,\d+)/mu",function($m)use($currency){
                $val = preg_replace(["/\./","/,/"],["","."],$m[2]);
                $res = floor(floatval($val)*$currency);
                //Log::debug($m[2]." x ".$currency." => ".$res);
                return "&nbsp;".$this->price($res)."&#8381;";//.'<span class="xg_original_converted" style="display:none">'.$m[2].'</span>';
            },$out);
            */
            return $out;
        }
        /*
        $pattern = "/(<(?!script|\!|\s)[^>]*>)([^<]+)<\//iuxsm";
        $out = preg_replace_callback($pattern,function($m)use($t){
            $val = trim($m[2]);$he = trim($m[1]);$res=$val;
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
                //Log::debug(preg_replace("/[\r\n]+/mu","",$m[0])." >>> ".$res);
            }
            return $he.$res."</";
        },$out);

        \( ( (?>[^()]+) | (?R) )* \)
        */
        //$pattern = '~<([a-z0-6]+)([^>]*)>(.+?)<(/\1)>~iuxsm';
        //$pattern = "/<([a-z0-6]+)([^>]*)>(.+?)<(\/\S+)>/iuxsm";
        $pattern = "/<([a-z0-6]+)([^>]*)>(.+?)</iuxsm";
        $out = preg_replace_callback($pattern,function($m)use($t){
            $val = trim($m[3]);
            $attr = trim($m[2]);
            $tag = trim($m[1]);
            $res=$val;
            if($tag == "script")return $m[0];
            if(empty(trim(preg_replace("/[\r\n]+/"," ",$val))))return $m[0];
            $res = $t->translateText($val);
            //Log::debug("[{$tag}]\t{$val} >>> {$res}");
            return "<{$tag} {$attr}>{$res}<";
        },$out);


        $pattern = "/<input(.+?)value=['\"]([^\"']+)['\"]/ixsm";

        $out = preg_replace_callback($pattern,function($m)use($t){
            $val = $m[2];$he = $m[1];
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
                //Log::debug("[input]\t".$val." >>> ".$res);
            }
            return "<input".$he."value='".$res."'";
        },$out);

        $out = preg_replace_callback('/placeholder=[\'"](.+?)[\'"]/',function($m)use($t){
            $val = $m[1];;
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
                //Log::debug("[placeholder]\t".$val." >>> ".$res);
            }
            return 'placeholder="'.$res.'""';
        },$out);



        $html = new simple_html_dom();
        $html->load($in);
        //$els = $html->find(":text:not(:has(".join($this->_textTags,", ")."))");
        //$els = $html->find(join($this->_textTags,":not(:has(".join($this->_textTags,", ").")), "));
        $els = $html->find(join($this->_textTags,", "));
        foreach ($els as $el) {
            if(!empty(trim($el->innertext))){
                $tr = $this->translateText($el->innertext);
                //Log::debug($el->tag.": ".$el->innertext." >>> ".$tr);
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
    protected function price($d){
        $a = strrev($d);
        $r = "";$c=3;
        for($i=0;$i<strlen($a);++$i){
            $r.=$a[$i];
            if((--$c)==0){
                $c=3;
                $r.=" ";
            }

        }
        return strrev($r);
    }
    protected function toupper($s){
        $fc = mb_strtoupper(mb_substr($s, 0, 1));
        return $fc.mb_substr($s, 1);
    }
};
?>
