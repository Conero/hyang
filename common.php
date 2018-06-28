<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/6/28 0028 10:35
 * Email: brximl@163.com
 * Name: 共函数
 */

/**
 * 标准地址
 * @param $dir
 * @return mixed|string
 */
function standDir($dir){
    $dir = str_replace('\\', '/', $dir);
    if('/' != substr($dir, -1)){
        $dir .= '/';
    }
    return $dir;
}


/**
 * 统计时间数
 * @return Closure 返回秒数
 */
function getRunTimes(){
    $runTime = function () {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    };
    $start = call_user_func($runTime);
    return function () use($start, $runTime){
        return call_user_func($runTime) - $start;
    };
}