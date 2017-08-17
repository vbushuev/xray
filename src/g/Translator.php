<?php
namespace g;
require("vendor/HTML5/Parser.php");
use \Log as Log;
use \simple_html_dom as simple_html_dom;
use \Masterminds\HTML5 as HTML5;
use g\YandexTranslate as YandexTranslate;
class Translator extends \Common{
    protected $_d = [];
    protected $_d_sorted = [];
    protected $db;
    protected $_langDetectPattern = "/[a-zàÉêé]/iu";
    protected $_textTags = ["li","a","span","p","h1","h2","h3","h4","h5","h6","i","cite","code","pre","b","strong","div","button","section","article","td"];
    protected $_currency = [];
    protected $_lang = 'en';
    protected $_use_cache = false;
    protected $yandex;
    protected $_useYandex = false;
    public function __construct($cfg){
        $this->db = new \g\DBConnector();

        $this->setLang($cfg);
        $this->yandex = new YandexTranslate();
    }
    public function setLang($cfg){
        $lang = isset($cfg["lang"])?$cfg["lang"]:"de";
        $this->_lang=$lang;
        $this->_use_cache = isset($cfg["cache"])?$cfg["cache"]:false;
        $this->_use_cache = ($this->_use_cache=="false")?false:$this->_use_cache;
        $this->_lang = $lang;
        //check today hash
        $file_dict = "dicts/".$lang."-".date("Y-m-d").".json";
        $file_dict_sorted = "dicts/".$lang."-".date("Y-m-d")."-sorted.json";
        $file_currencies = "logs/currencies-".date("Y-m-d").".json";

        $this->checkPath($file_dict);
        $this->checkPath($file_dict_sorted);
        $this->checkPath($file_currencies);
        if($this->_use_cache&&file_exists($file_dict)){
            $this->_d = json_decode(file_get_contents($file_dict),true);
        }
        else {
            $this->_d = $this->db->selectAll("select * from g_dictionary where lang='".$lang."' order by length(original) desc,priority desc");
            if($this->_use_cache)file_put_contents($file_dict,json_encode($this->_d,JSON_UNESCAPED_UNICODE));
            //Log::debug(json_encode($this->_d,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        }
        if($this->_use_cache&&file_exists($file_dict_sorted)){
            $this->_d_sorted = json_decode(file_get_contents($file_dict_sorted),true);
        }
        else {
            $this->_d_sorted = [];
            foreach($this->_d as $v){$this->_d_sorted[mb_strtolower(preg_replace(['/\\\u2019/ium','/[\r\n]+/'],["'",""],$v["original"]))]=$v["translate"];}
            if($this->_use_cache)file_put_contents($file_dict_sorted,json_encode($this->_d_sorted,JSON_UNESCAPED_UNICODE));
            //Log::debug(json_encode($this->_d_sorted,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        }

        //currencies
        if(file_exists($file_currencies)){
            $this->_currency= json_decode(file_get_contents($file_currencies),true);
            if(!count($this->_currency)){
                $_currencies= json_decode(file_get_contents("https://l.gauzymall.com/currency"),true);
                foreach($_currencies as $currency){$this->_currency[$currency["iso_code"]] = $currency["value"]*$currency["multiplier"];}
                file_put_contents($file_currencies,json_encode($this->_currency,JSON_UNESCAPED_UNICODE));
            }
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
    public function translateTextYandex($in,$convert=true){
        $out = $this->yandex->get($in,$this->_lang.'-ru');
        if($convert)$this->convert($out);
        return $out;
    }
    public function translateText($in,$convert=true){
        $out = $in;
        $tick = time();
        $circlesTotal = 0;
        $circles = 0;
        if(empty(trim($out))){Log::debug("Looks empty");return $out;}
        if(preg_match("/^\d+$/im",$out)){Log::debug("Looks like digit only");return $out;}
        if(preg_match("/^\d+\.\d+$/im",$out)){Log::debug("Looks like decimal only");return $out;}
        if(preg_match("/^http(s?):/im",$out)){Log::debug("Looks like url");return $out;}
        if(preg_match("/[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}/im",$out)){Log::debug("Looks uid");return $out;}
        if(preg_match("/^\S+[\-_]\S+/im",$out)){Log::debug("Looks slug");return $out;}
        if(preg_match("/^\s*(\[|\{)/xs",$out)){Log::debug("Looks json");return $out;}
        //Log::debug(preg_replace(["/^\s+/mu","/\s+$/mu"],"",$out));
        foreach($this->_d as $di){
            $circlesTotal++;
            if(strlen($out)<$di["priority"]){continue;}
            $circles++;
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
        // currency convert
        if($convert)$this->convert($out);
        if($this->_useYandex){
            if(preg_match($this->_langDetectPattern,preg_replace("/medimax|javascript|&amp;|&|greatelectronic/im","",$out))){
                for(;;){
                    if(preg_match("/\w+_\w+/im",$out))break;
                    if(preg_match("/\{|\[/im",$out))break;
                    if(preg_match("/^\//im",$out))break;
                    if(preg_match("/[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}/im",$out))break;
                    $out = $this->yandex->get($in,$this->_lang.'-ru');
                    if(strlen(trim($out))){
                        $out_low = mb_strtolower($in);
                        $this->_d_sorted[$out_low] = $out;
                        try{
                            if(!$this->db->exists("select 1 from g_dictionary where original='".preg_replace(["/'/m","/\//m"],["''","\/"],$in)."'"))
                                $this->db->insert("insert into g_dictionary (original,translate,priority,lang) values('".preg_replace("/'/m","''",$in)."','".preg_replace("/'/m","''",$out)."',".strlen(trim($in)).",'{$this->_lang}')");
                        }
                        catch(Exception $e){
                            Log::debug($e);
                        }
                    }
                    break;
                }
            }
        }

        Log::debug("Translated ".$in." [".$out."] total circles =".$circlesTotal." usefull=".$circles." in ".(time()-$tick));
        return $out;
    }
    public function translateHtmltags($in){
        $out = ""; $t = $this;
        //Запускаем бесконечный цикл
        for(;;){
            // ищем символ закрывающегося тэга
            $posCloseTag = strpos($in,'>');
            // Если не найден то выходим из цикла
            if($posCloseTag===false)break;
            // Обрезаем строку до найденного символа + 1 это длинна найденного символа >
            $out .= substr($in,0,$posCloseTag+1);$in = substr($in,$posCloseTag+1);
            // ищем символ открывающего тэга
            $posOpenTag = strpos($in,'<');
            // Если не найден то выходим из цикла
            if($posOpenTag===false)break;
            // заносим в переменную значение входной строки между найденными символами
            $whatBetween = substr($in,0,$posOpenTag);

            // если найденное содержит символы - то это текст
            if(!empty(trim(preg_replace("/[\r\n]+/im"," ",$whatBetween)))) {
                $out.= $this->translateText($whatBetween);
            }
            $out.="<";
            // Обрезаем строку до найденного символа + 1 это длинна найденного символа <
            $in = substr($in,$posOpenTag+1);
        }
        $out = preg_replace_callback("/placeholder=(['\"])([^\"']+)(['\"])/im",function($m)use($t){
            $val = trim($m[2]);$he = trim($m[1]);$res=$val;
            if(!empty(trim(preg_replace("/[\r\n]+/"," ",$val)))){
                $res = $t->translateText($val);
                //Log::debug(preg_replace("/[\r\n]+/mu","",$m[0])." >>> ".$res);
            }
            return "placeholder=".$he.$res.$he;
        },$out);
        // возвращаем найденные данные
        return $out;
    }
    public function translateHtml($in){
        //return $this->translateText($in);
        $out = $in; $t = $this;$skip=false;


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
                        $node->setAttribute("value",$new);
                    }
                    if($node->hasAttribute("cart-text")) {
                        $new = $this->translateText($node->getAttribute("cart-text"));
                        $node->setAttribute("cart-text",$new);
                    }
                    if($node->hasAttribute("cart-price")) {
                        $new = $this->translateText($node->getAttribute("cart-price"));
                        //Log::debug($node->nodeName."\t".$node->getAttribute("value")." -> ".$new);
                        $node->setAttribute("cart-price",$new);
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
    protected function price($f,$t = " ",$d = false){
        $fa = preg_split("/\./",$f);
        //Log::debug($fa);
        $a = strrev($fa[0]);
        $r = "";$c=3;
        for($i=0;$i<strlen($a);++$i){
            $r.=$a[$i];
            if((--$c)==0&&($i+1)<strlen($a)){
                $c=3;
                $r.=$t;
            }
        }
        $r = strrev($r).($d===false?"":($d.(count($fa)>1?$fa[1]:"00")));
        return $r;
    }
    protected function toupper($s){
        $fc = mb_strtoupper(mb_substr($s, 0, 1));
        return $fc.mb_substr($s, 1);
    }
    protected function convert(&$out){
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
            return " ".$this->price($res)." ₽";//.htmlspecialchars('<span class="xg_original_converted" style="display:none">'.$m[2].'</span>');
            //return "€ ".$this->price($res,",");//.'<span class="xg_original_converted" style="display:none">'.$m[2].'</span>';
        },$out);
        return $out;
    }
};
?>
