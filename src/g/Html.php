<?php
namespace g;
class Html {
    protected $a = ["title"=>"default title"];
    public function __construct($a=[]){
        $this->a = array_change_key_case(array_merge($this->a,$a));
    }
    public function __get($f){
        $fields = $f;
        if(preg_match_all("/([A-Z])([a-z0-9]+)/",$f,$ms))$fields = $ms[0];
        else $fields=[$fields];
        $v = "<pre>no field <b>{".$f."}</b> setted!</pre>";
        foreach($fields as $field){
            $field = strtolower($field);
            if($f=="GreenlineShow"){
                print_r($this->a);
                echo "{$field} isset() = ".(isset($this->a[$field])?"true":"false")." value=".(isset($this->a[$field])?json_encode($this->a[$field]):"undefined")."<br />";
            }
            $v = isset($this->a[$field])?$this->a[$field]:((is_array($v)&&isset($v[$field]))?$v[$field]:$v);
        }
        return $v;
    }
};
?>
