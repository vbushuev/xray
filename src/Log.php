<?php
class Log{
    public static $config = [
        "path" => "logs/",
        "type" => "daily",
        "class" => "",
        "level" => "all"
    ];
    public static function info(){
        if(!in_array(self::$config["level"],["all"])) return;
        $r = "INFO:\t".self::_backtrace(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT));
        foreach (func_get_args() as $a) {
            $r .= (is_array($a)||is_object($a))?json_encode($a,JSON_PRETTY_PRINT):$a;
        }
        self::_put($r);
    }
    public static function debug(){
        if(!in_array(self::$config["level"],["all","debug"])) return;
        $r = "DEBUG:\t".self::_backtrace(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT));
        foreach (func_get_args() as $a) {
            $r .= (is_array($a)||is_object($a))?json_encode($a,JSON_PRETTY_PRINT):$a;
        }
        self::_put($r);
    }
    public static function error(){
        $r = "ERROR:\t".self::_backtrace(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT));
        foreach (func_get_args() as $a) {
            $r .= is_string($a)?$a:json_encode($a,JSON_PRETTY_PRINT);
        }
        self::_put($r);
    }
    protected static function _backtrace($d){
        $d = isset($d[1])?$d[1]:$d[0];
        $s =  date("Y-m-d H:i:s")."  ".(isset($d["file"])?$d["file"]:"").":".(isset($d["line"])?$d["line"]:"")."  ";
        self::$config["class"]=(isset($d["class"]))?strtolower(preg_replace("/.*\\\(\w+)$/ixsm","$1",$d["class"])):"";
        return $s;
    }
    protected static function _put($s){
        if(!is_dir(self::$config["path"]))mkdir(self::$config["path"]);
        $f = self::$config["path"]."log";
        $f_common = self::$config["path"]."log";
        if(self::$config["type"]=="daily"){
            if(!empty(self::$config["class"]))$f.="-".self::$config["class"];
            $f.="-".date("Y-m-d");
            $f_common.="-".date("Y-m-d");
        }
        $f.=".log";
        $f_common.=".log";
        file_put_contents($f,$s."\n",FILE_APPEND);
        //file_put_contents($f_common,$s."\n",FILE_APPEND);
    }
};
?>
