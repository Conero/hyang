<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/6/28 0028 9:45
 * Email: brximl@163.com
 * Name: 类引入
 */


spl_autoload_register(function ($cls){
    $file = __DIR__. '/'. $cls. '.php';
    if(is_file($file)){
        require_once $file;
    }else{
        \sr\Log::write($file . '类不存在');
    }
});