<?php
namespace hyang;
class Evil{
    // 头部用于覆盖-默认属性
    private $_optAction=array();
    public function __construct()
    {
        $this->pageInit();
    }
    public function pageInit(){}
    public function OptAction($key=null,$value=null)
    {
        if(is_null($key)) return $this->_optAction;
        if(is_array($key)){// 设置数组值
            $arr = $this->_optAction;
            $this->_optAction = array_merge($arr,$key);
            return;
        }elseif($value && $key){// 设置
            $this->_optAction[$key] = $value;
        }
        elseif($key && is_null($value)){
            if(array_key_exists($this->_optAction,$key)) return $this->_optAction[$key];
            return '';
        }
    }
    // 导航栏
    public function app_nav()
    {
        return null;
    }
    // 首页概述
    public function about_app()
    {
        return null;
    }    
}