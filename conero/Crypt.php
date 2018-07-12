<?php
/*  2017年2月21日 星期二
 *  Crypt 加密扩展
*/
namespace hyang;
use Exception;
class Crypt{
    public $debug = true;
    public function Algorithm($name){
        $name = '\\hyang\\crypt\\'.$name;
        if(class_exists($name)) return new $name;
        if($this->$debug) throw new Exception($name.'类不存在!!');
    }
    // 链式
    public function setDebug($debug){
        $this->debug = $debug;
        return $this;
    }
}