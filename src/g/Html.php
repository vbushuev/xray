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
        $v = "";
        foreach($fields as $field){
            $field = strtolower($field);
            if($f=="GreenlineShow"){
                //print_r($this->a);
                echo "{$field} isset() = ".(isset($this->a[$field])?"true":"false")." value=".(isset($this->a[$field])?json_encode($this->a[$field]):"undefined")."<br />";
            }
            //$v = isset($this->a[$field])?$this->a[$field]:((is_array($v)&&isset($v[$field]))?$v[$field]:$v);
            $v = isset($this->a[$field])?$this->a[$field]:((is_array($v)&&isset($v[$field]))?$v[$field]:"");
        }
        return is_array($v)?$this->_listvalues($v,$fields):$v;
    }
    protected function _listvalues($v,$field){
        $f= $this->_htmlfieldname($field);
        $id = strtolower($field[count($field)-1]);
        $i=0;
        $s = '<ul class="listvalues" id="'.$id.'" data="'.$f.'">';
        foreach ($v as $key => $value) {
            $s.= '<li class="listvalue">';
            $s.= '<i class="fa fa-square-"></i>&nbsp;';
            $s.= '<div class="editable inline key" data-rel="#'.$id.'-'.$i.'" data-field="name">'.$key.'</div>&nbsp;';
            $s.= '<i class="fa fa-exchange"></i>&nbsp;';
            $s.= '<div class="editable inline value" data-rel="#'.$id.'-'.$i.'" data-field="value">'.$value.'</div>&nbsp;';
            $s.= '<a class="button delete" data-ref=".listvalue" href="javascript:{0}"><i class="fa fa-times"></i></a>';
            $s.= '<input id="'.$id.'-'.$i.'" type="hidden" name="'.$f.'['.$key.']" value="'.$value.'"/>';
            $s.= '</li>';
            ++$i;
        }
        $s.= '</ul>';
        return $s;
    }
    protected function _htmlfieldname($field){
        $r = "";
        if(!is_array($field))return $field;
        for($i=1;$i<count($field);++$i) {
            $f = strtolower($field[$i]);
            $r .= strlen($r)?"[{$f}]":$f;
        }

        return $r;
    }
};
?>
