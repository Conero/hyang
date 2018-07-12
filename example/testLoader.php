<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/12 0012 10:27
 * Email: brximl@163.com
 * Name:文件加载器
 */

$workdir = str_replace('\\', '/', dirname(__DIR__)). '/';
$composer = json_decode(file_get_contents($workdir.'composer.json'), true);

define('Basedir', $workdir);
define('Composer', $composer);
define('Br', "\r\n");

spl_autoload_register(function ($class){
    foreach (Composer['autoload']['psr-4'] as $ns => $path){
        $class2 = Basedir. str_replace($ns, $path, $class). '.php';
        if(is_file($class2)){
            require_once $class2;
        }
    }
});