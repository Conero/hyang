<?php
/*
 * 业务逻辑处理
*/
namespace hyang;
use think\Db;
abstract class Logic
{
    public $center_id;
    public $uid;
    public $code;
    public $name;
    public $nick;
    public $admin;
    private $loginData = array();
    public function __construct()
    {
        $uInfo = uInfo();
        if(count($uInfo)>0){
            $this->center_id = $uInfo['cid'];
            $this->uid = $uInfo['uid'];
            $this->code = $uInfo['code'];
            $this->name = $uInfo['name'];
            $this->nick = $uInfo['nick'];
            $this->admin = $uInfo['admin'];
        }
        $this->init();
    }
    public function __get($key)
    {
        if(array_key_exists($key,$this->loginData)) return $this->loginData[$key];
        return '';
    }
    public function __set($key,$value=null)
    {
        if(is_string($key) && $value){
            $this->loginData[$key] = $value;
            return true;
        }
        elseif(is_array($key)){
            $data = $this->loginData;
            $this->loginData = array_merge($data,$key);
            return true;
        }
        return false;
    }
    public function init(){}
    // 数据是否存在
    public function dataExist($wh,$tb=null)
    {
        $tb = $tb? $tb:$this->get('table');
        if(empty($tb)) return false;
        $ctt = Db::table($tb)->where($wh)->count();
        if($ctt) return true;
        return false;
    }
    // 数据保存
    public function save($data,$tb=null){
        $tb = $tb? $tb:$this->get('table');
        if(empty($tb)) return false;
        return Db::table($tb)->insert($data);
    }    
}