<?php
namespace g;
use \Log as Log;
class Translator{
    protected $_d = [];
    protected $db;
    public function __construct($cfg){
        $this->db = new \g\DBConnector();
        $this->setLang($cfg);
    }
    public function setLang($cfg){
        $lang = isset($cfg["lang"])?$cfg["lang"]:"fr";
        $this->_d = $this->db->selectAll("select * from g_dictionary where lang='".$lang."' order by priority desc");
    }
    public function translateText($in){
        $out = $in;
        foreach($this->_d as $di){
            $out = preg_replace("/\b".preg_quote($di["original"])."\b/im",$di["translate"],$out);
        }
        return $out;
    }
    public function translateHtml($in){
        $out = $in;
        $doc = \phpQuery::newDocument($in);
        $text = pq(":text");
        //Log::debug("Found ".json_encode($text[0],JSON_PRETTY_PRINT). " text tag");return $in;
        foreach ($text as $tag) {
            $qtag =pq($tag);
            if(in_array(strtolower($tag->tagName),["li","div","p","h1","h2","h3","h4"])){
                $t=$qtag->text();
                if(!empty(trim($t))){
                    Log::debug($tag->tagName." >>> ".$t);continue;
                    $t = $this->translateText($t);
                    $qtag->text($t);
                }
            }
        }
        $out = $doc->htmlOuter();
        return $in;
    }
};
?>
