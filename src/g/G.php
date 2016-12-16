<?php
namespace g;
class G{
    public static function escape($s,$m="im"){
        $s = preg_quote($s);
        $s = str_replace("//",'\/\/',$s);
        return $s;
    }
    public static function reg_escape($s,$m="im"){
        $s = self::escape($s);
        $s = "/".$s."/".$m;
        return $s;
    }
};
?>
