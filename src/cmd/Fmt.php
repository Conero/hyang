<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/12 0012 16:22
 * Email: brximl@163.com
 * Name: 命令行输出，名字方式来自于golang
 */

namespace hyang\surong\cmd;


class Fmt
{
    /**
     * 换行输出
     * @param mixed $msg
     * @param null $pref
     */
    static function line($msg, $pref=null){
        $pref = empty($pref)? "  ": $pref;
        if(is_array($msg) || is_object($msg)){
            $msg = (array)$msg;
            $msg = print_r($msg, true);
        }
        print $pref. $msg. "\r\n";
    }

    /**
     * 错误信息
     * @param $msg
     */
    static function error($msg){
        self::line($msg, ' -(:  ');
    }

    /**
     * 成功信息
     * @param $msg
     */
    static function success($msg){
        self::line($msg, ' (:-  ');
    }
}