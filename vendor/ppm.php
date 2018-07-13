<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 15:46
 * Email: brximl@163.com
 * Name:php package manage PHP 包管理
 */


spl_autoload_register(function ($class){
    // namespace => src
    $psr4 = [
        'hyang\\surong\\cmd\\' => 'hyang/surong/cmd/src/cmd/'
    ];
    foreach ($psr4 as $ns => $path){
        if(substr_count($class, $ns) > 0){
            $filepath = __DIR__.'/'. str_replace($ns, $path, $class) . '.php';
            if(is_file($filepath)){
                require_once $filepath;
                return;
            }
        }
    }
});


