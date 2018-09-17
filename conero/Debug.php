<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/9/17 0017 13:27
 * Email: brximl@163.com
 * Name: 系统调试
 */

namespace hyang;


class Debug
{
    /**
     * @var DebugImpl
     */
    static $impl;

    /**
     * @param DebugImpl $impl
     */
    static function register($impl){
        self::$impl = $impl;
    }

    /**
     * print_r 打印格式
     * @param array ...$array
     */
    static function debug(...$array){
        if(is_array($array) && count($array) == 1){
            $array = $array[0];
        }
        (self::$impl)->saveLog(print_r($array, true));
    }

    /**
     * @param mixed $array
     * @param $if
     */
    static function debugif($if, ...$array){
        if(is_callable($if)){
            $if = call_user_func($if);
        }
        if($if){
            self::debug($array);
        }
    }

    /**
     * 调试并抛出异常
     * @param mixed ...$array
     * @throws \Exception
     */
    static function debugThrow(...$array){
        self::debug($array);
        self::error(null);
    }
    /**
     * json 格式程序打印
     * @param array ...$array
     */
    static function json(...$array){
        if(!is_array($array)){
            $array = [$array];
        }
        self::debug(json_encode($array, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 抛出错误异常
     * @param null|string $msg
     * @throws \Exception
     */
    static function error($msg=null){
        $msg = $msg? $msg : 'Zon::throwError -> '.random_int(100, 999999999);
        (self::$impl)->error($msg);
    }

    /**
     * 数据打印时
     * @param array ...$array
     */
    static function println(...$array){
        if(is_array($array) && count($array) == 1){
            $array = $array[0];
        }
        echo '<pre>'.print_r($array, true).'</pre>';
    }

    /**
     * @return string
     */
    static function trace(){
        $data = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $traceStr = '';
        $count = 1;
        foreach ($data as $row){
            $args = $row['args'] ?? '';
            if(is_array($args)){
                $args = implode(',', $args);
            }
            $cls = $row['class'] ?? '';
            $fnc = $row['function'] ?? '';
            if($cls && $fnc){
                $fnc = $cls.($row['type'] ?? ''). $fnc;
            }
            $traceStr .= '  '.$count.'.'. $fnc. '('.$args.') >> ' . $row['file']. '('.$row['line'].')'. "\n";
            $count++;
        }
        return $traceStr;
    }

    /**
     * 单次调试
     * @param mixed $name
     * @param mixed ...$datas
     */
    static function once($name, ...$datas){
        static $onceDick = [];
        $key = sha1($name);
        if(!isset($onceDick[$key])){
            $onceDick[$key] = true;
            self::debug(...$datas);
        }
    }
}