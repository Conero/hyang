<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/6/28 0028 9:51
 * Email: brximl@163.com
 * Name: 路由器
 */

namespace sr\cmd;


class Router
{
    protected static $_command; // 命令类别
    protected static $_action;
    protected static $_args = [];    // 参数(k = v)
    protected static $_option = [];  // 选择列表 (--v)
    protected static $callbacks = [];      // 回调事件集

    /**
     * @return mixed
     */
    static function getCommand(){
        return self::$_command;
    }
    /**
     * @return mixed
     */
    static function getAction(){
        return self::$_action;
    }
    /**
     * @return mixed
     */
    static function getArgs(){
        return self::$_args;
    }
    /**
     * @return mixed
     */
    static function getOption(){
        return self::$_option;
    }
    /**
     * 路由运行
     * @param array $args
     */
    static function Run($args){
        self::parseArgs($args);
        self::$_command = $args[1] ?? false;

        // 初始化
        if($iniFn = self::_callFun('init')){
            $selfCommand = call_user_func($iniFn, self::$_command);
            if($selfCommand){
                self::$_command = $selfCommand;
            }
        }
        // 路由
        if(self::$_command){
            $ctrl = 'app\\cmd\\'.ucfirst(self::$_command);
            if(class_exists($ctrl)){
                $ctrl = new $ctrl();
                call_user_func([$ctrl, 'Main']);
            }else if(($unfind = self::_callFun('unfind'))){
                call_user_func($unfind, self::$_command);
            }
        }elseif (($empty = self::_callFun('unfind'))){
            call_user_func($empty);
        }
    }
    protected static function parseArgs($args){
        $newArgs = [];
        $option = [];
        $action = null;
        foreach ($args as $k => $v){
            if($k > 1){
                $eq = strpos($v, '=');
                if($eq !== false){
                    $key = substr($v, 0, $eq);
                    $value = substr($v, $eq + 1);
                    if($key && $value){
                        $newArgs[$key] = $value;
                    }
                }elseif (preg_match('/^--/', $v, $matched)){
                    $option[] = str_replace('--', '', $v);
                }
                elseif (empty($action)){
                    $action = $v;
                }
            }
        }
        self::$_args = $newArgs;
        self::$_option = $option;
        self::$_action = $action;
    }
    /**
     * 事件绑定
     * @param $name
     * @param $callback
     */
    static function on($name, $callback){
        if(is_callable($callback)){
            self::$callbacks[$name] = $callback;
        }
    }

    /**
     * 事件调用
     * @param $name
     * @return bool|mixed
     */
    protected  static function _callFun($name){
        return self::$callbacks[$name] ?? false;
    }
}