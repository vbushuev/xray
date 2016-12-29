<?php
class Log{
    public static $config = [
        "path" => "logs/",
        "type" => "daily"
    ];
    public static function debug(){
        $d = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        $d=$d[0];
        $r = date("Y-m-d H:i:s")."  ".$d["file"].":".$d["line"]."  DEBUG:";

        //foreach($d["args"] as $a=>$v){//$r.=$v;};
        foreach (func_get_args() as $a) {
            $r .= (is_array($a)||is_object($a))?json_encode($a,JSON_PRETTY_PRINT):$a;
        }
        self::_put($r);
    }
    public static function error(){
        $d = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        $r = date("Y-m-d H:i:s")."  ".$d["file"].":".$d["line"]."  ".$d["function"]."  ERROR:";
        //foreach($d["args"] as $a=>$v){//$r.=$v;};
        foreach (func_get_args() as $a) {
            $r .= is_string($a)?$a:json_encode($a,JSON_PRETTY_PRINT);
        }
        self::_put($r);
    }
    protected static function _put($s){
        if(!is_dir(self::$config["path"]))mkdir(self::$config["path"]);
        $f = self::$config["path"]."log";
        if(self::$config["type"]=="daily")$f.="-".date("Y-m-d");
        $f.=".log";
        file_put_contents($f,$s."\n",FILE_APPEND);
    }
};
?>
