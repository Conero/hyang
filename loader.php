<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/9 0009 14:25
 * Email: brximl@163.com
 * Name: 包加载器，用于快速加载
 */

spl_autoload_register(function ($cls){
    /**
    "autoload": {
        "psr-4": {
        "hyang\\surong\\cmd\\": "src/cmd"
        }
    },
     */
    $ns = 'hyang\\surong\\cmd\\';
    $dir = 'src/cmd';
    $file = str_replace($ns, __DIR__.'/'. $dir.'/', $cls). '.php';

    // echo $file."\n\r";

    if(is_file($file)){
        require_once $file;
    }
});