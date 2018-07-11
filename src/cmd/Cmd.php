<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 11:01
 * Email: brximl@163.com
 * Name: 命令行程序
 */

namespace hyang\surong\cmd;

class Cmd
{
    /**
     * 获取当亲的工作目录
     * @var string
     */
    protected static $_rdo_dir;
    /**
     * @var array
     */
    protected static $_rdo_args = array();
    protected static $_rdo_opt = array();
    protected static $_rdo_command;
    protected static $_rdo_action;
    /**
     * @var array 系统控制器
     */
    protected static $_rdo_cmdCtrls = array();
    protected static $_rdo_routerTpl = array(); // 路由模板

    /**
     * 获取当亲的工作目录
     * @return string
     */
    static function getDir(){
        return self::$_rdo_dir;
    }

    /**
     * 解析以后的参数
     * @return array
     */
    static function getArgs(){
        return self::$_rdo_args;
    }

    /**
     * 属性参数
     * @return array
     */
    static function getOption(){
        return self::$_rdo_opt;
    }
    /**
     * 命令
     * @return array
     */
    static function getCommand(){
        return self::$_rdo_command;
    }
    /**
     * 行为
     * @return array
     */
    static function getAction(){
        return self::$_rdo_action;
    }
    /**
     * 路由器 [command]/[action]
     * @param string $tpl
     * @param callable $callback
     */
    static function router($tpl, $callback){
        if(is_callable($callback)){
            self::$_rdo_routerTpl[$tpl] = $callback;
        }
    }

    /**
     * 可用模板： command/action
     * @return bool
     */
    protected static function parseRouterTpl(){
        $success = false;
        $command = self::$_rdo_command;
        $action = self::$_rdo_action;
        if($action && $command){
            foreach (self::$_rdo_routerTpl as $tpl => $callback){
                $reqQueue = explode('/', $tpl);
                $cmd1 = $reqQueue[0] ?? false;
                $act1 = $reqQueue[1] ?? false;
                // command
                if($cmd1 && $act1 === false){
                    if('[command]' == $cmd1){
                        call_user_func($callback, $command);
                        $success = true;
                    }else if($command === $cmd1){
                        call_user_func($callback);
                        $success = true;
                    }
                }else if($cmd1 && $act1){
                    // [command]/[action]
                    if('[command]' == $cmd1 && '[action]' == $act1){
                        call_user_func($callback, $command, $action);
                        $success = true;
                    }
                    // [command]/action
                    else if('[command]' == $cmd1 && '[action]' != $act1){
                        if($action == $act1){
                            call_user_func($callback, $command);
                            $success = true;
                        }
                    }
                    // command/[action]
                    else if('[command]' != $cmd1 && '[action]' == $act1){
                        if($command == $cmd1){
                            call_user_func($callback, $action);
                            $success = true;
                        }
                    }
                    // command/action
                    else if($cmd1 == $command && $act1 == $action){
                        call_user_func($callback);
                        $success = true;
                    }
                }
                if($success){
                    break;
                }
            }
        }
        return $success;
    }
    /**
     * 运行器
     * @param array $argv
     */
    static function Run($argv){
        self::$_rdo_dir = str_replace('\\', '/', getcwd()). '/';
        self::parseArgs($argv);
        $command = self::$_rdo_command;
        $successMk = false;
        if($command){
            // 存在绑定实际
            if(isset(self::$_rdo_cmdCtrls[$command])){
                call_user_func(self::$_rdo_cmdCtrls[$command]);
                $successMk = true;
            }else if(self::parseRouterTpl()){
                $successMk = true;
            }
        }
        // unfind
        $unfind = 'unfind';
        $empty = 'empty';
        if(!$successMk && $command){
            if(isset(self::$_rdo_cmdCtrls[$unfind])){
                call_user_func(self::$_rdo_cmdCtrls[$unfind], $command, self::$_rdo_action);
            }
            else if(isset(self::$_rdo_routerTpl[$unfind])){
                call_user_func(self::$_rdo_routerTpl[$unfind], $command, self::$_rdo_action);
            }
        }
        else if(!$successMk){   // 空路由
            if(isset(self::$_rdo_cmdCtrls[$empty])){
                call_user_func(self::$_rdo_cmdCtrls[$empty]);
            }
            else if(isset(self::$_rdo_routerTpl[$empty])){
                call_user_func(self::$_rdo_routerTpl[$empty]);
            }
        }
    }

    /**
     * 项目初始化
     * @param null|string $dir
     */
    static function Init($dir=null){
        $dir = $dir ?? './';
        spl_autoload_register(function ($class) use ($dir){
            $file = $dir . $class .'.php';
            if(is_file($file)){
                require_once $file;
            }
        });
    }
    /**
     * 参数解析
     * @param array $args
     */
    protected static function parseArgs($args){
        $newArgs = [];
        $option = [];
        $action = null;
        if($command = ($args[1] ?? false)){
            self::$_rdo_command = $command;
        }
        foreach ($args as $k => $v){
            if($k > 1){
                $eq = strpos($v, '=');
                // k = v
                if($eq !== false){
                    $key = substr($v, 0, $eq);
                    $value = substr($v, $eq + 1);
                    if($key && $value){
                        $newArgs[$key] = $value;
                    }
                }
                // --option
                elseif (preg_match('/^--/', $v, $matched)){
                    $option[] = str_replace('--', '', $v);
                }
                elseif (empty($action)){
                    $action = $v;
                }
            }
        }
        self::$_rdo_args = $newArgs;
        self::$_rdo_opt = $option;
        self::$_rdo_action = $action;
    }
    /**
     * 控制器运行
     * @param string $name
     * @param callable $callback
     */
    static function command($name, $callback){
        if($name && is_callable($callback)){
            self::$_rdo_cmdCtrls[$name] = $callback;
        }
    }
}