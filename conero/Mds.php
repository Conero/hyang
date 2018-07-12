<?php
/*
 * models 
*/
namespace hyang;
use \think\Db;
class Mds{
    public $db;
    private $queryData;
    public function __construct($table=null,$fn=null){
        if($table){
            $this->db = Db::table($table);
            if(is_callable($fn)){
                $this->queryData = $fn($this->db);
            }
        }
        else $this->queryData = null;
    }
    // 获取单列
    public function fetchRow($sql,$bind=[]){
        $data = Db::query($sql,$bind);
        if(isset($data[0])) $data = $data[0];
        return $data;
    }
    // 获取数据字符串
    public function fetchOne($sql,$bind=[]){
        $data = Db::query($sql,$bind);
        $data = isset($data[0])? $data[0]:$data;
        if(is_array($data)){
            $values = array_values($data);
            return $values[0];
        }
        return '';
    }
    public function get($col=null){
        if($this->queryData && $col && array_key_exists($col,$this->queryData)) return $this->queryData[$col];
        elseif($this->queryData && $col) return '';
        elseif($this->queryData){
            $data = array_values($this->queryData);
            if(isset($data[0])) return $data[0];            
        }
        $this->queryData = null;
        return '';
    }
    public function row()
    {
        if($this->queryData && isset($this->queryData[0])) return $this->queryData[0];
        elseif(is_array($this->queryData)) return $this->queryData;
        return [];
    }
}