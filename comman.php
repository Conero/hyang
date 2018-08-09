<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/9 0009 15:08
 * Email: brximl@163.com
 * Name:
 */

/**
 * @return null|PDO
 */
function getPDO(){
    static $pdo = null;
    if(empty($pdo)){
        $pdo = new PDO('oci:dbname=127.0.0.1/mci600a;charset=utf8', 'py', 'sroooo');
        $pdo->exec('alter session set nls_date_format = \'yyyy-mm-dd hh24:mi:ss\'');

        // 错误提醒
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
}

/**
 * @return Closure
 */
function sec()
{
    $fn = function (){
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    };
    $star = call_user_func($fn);
    return function () use ($fn, $star){
        return call_user_func($fn) - $star;
    };
}

/**
 * @return Closure
 */
function getByte(){
    $mer = memory_get_usage();
    return function () use ($mer){
        return memory_get_usage() - $mer;
    };
}