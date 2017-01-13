<?php
namespace g;
class DBConnector{
    protected $host;
    protected $user;
    protected $pass;
    protected $schema;
    protected $prefix;
    protected $connected=false;
    protected $conn=null;
    public function __construct(){
        $this->host="127.0.0.1";
        $this->user="gpars";
        $this->pass="gpars";
        $this->schema="gpars";
        $this->prefix="xr_";
        $this->connected=false;
        $this->conn=null;
    }
    public function __destruct(){
        if($this->connected) $this->conn->close();
    }
    protected function connect(){
        $this->conn = new \mysqli($this->host,$this->user,$this->pass,$this->schema);
        //GARAN24::debug($this->_dbdata);
        if($this->conn->connect_errno) throw new Exception("No db connection. Error:".$this->conn->connect_error);
        $this->connected = true;
        $this->conn->set_charset('utf8');
    }
    protected function prepare($sql){
        if(!$this->connected) $this->connect();
        $sql = $this->_prefixed($sql);
        //$result = $this->conn->query($sql,MYSQLI_USE_RESULT);
        $result = $this->conn->query($sql);
        if(!$result) throw new Exception("Fail to execute {$sql}. Error:".$this->conn->error);
        return $result;
    }
    protected function _prefixed($sql){
        $r = $sql;
        $r = preg_replace("/(from|join|into|update)\s+([a-z0-9_]+)/im","$1 ".$this->prefix."$2",$r);
        //$r = preg_replace("/join\s+([a-z0-9_]+)/im","join ".$this->prefix."$1",$r);
        //$r = preg_replace("/into\s+([a-z0-9_]+)/im","into ".$this->prefix."$1",$r);
        //$r = preg_replace("/update\s+([a-z0-9_]+)/im","update ".$this->prefix."$1",$r);
        return $r;
    }
    public function select($sql){
        $result = $this->prepare($sql);
        if(!$result->num_rows) throw new Exception("Sync failed no data is retrieved.");
        $ret = $result->fetch_array(MYSQLI_ASSOC);
        $result->close();
        return $ret;
    }
    public function selectAll($sql){
        $result = $this->prepare($sql);
        for ($ret = []; $tmp = $result->fetch_array(MYSQLI_ASSOC);) $ret[] = $tmp;
        $result->close();
        return $ret;
    }
    public function insert($sql){
        if(!$this->connected) $this->connect();
        $sql = $this->_prefixed($sql);
        if(!$this->conn->query($sql)){
            throw new Exception($sql." execution error: "
                .$this->conn->error);
        }
        return (isset($this->conn->insert_id))?$this->conn->insert_id:true;
    }
    public function update($sql){
        $this->insert($sql);
    }
    public function exists($sql){
        $result = $this->prepare($sql);
        return ($result->num_rows>0);
    }
};
?>
