<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/13 0013 11:24
 * Email: brximl@163.com
 * Name:
 */

namespace app\common;


class Util
{
    /**
     * 带*号字符串转化为正则表达式
     * @param string $str
     * @return string
     */
    static function str2preg($str){
        $preg = '';
        if($str){
            $rep = '__JC__';
            $str = str_replace('*', $rep, $str);
            $str = preg_quote($str);
            $preg = str_replace($rep, '.*', $str);     // *
            $preg = '/'.$preg.'/';
        }
        return $preg;
    }
}