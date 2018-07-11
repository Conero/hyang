<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 11:35
 * Email: brximl@163.com
 * Name: ppm php 包管理工具
 */
use \hyang\surong\cmd\Cmd;
require_once __DIR__.'/vendor/ppm.php';


Cmd::Init(__DIR__.'/');

$RouterFn = function ($cmd, $act=null){
    $app = 'app\\cmd\\'.$cmd;
    if(class_exists($app)){
        $ins = new $app;
        if($act){
            $action = ucfirst($act) . 'Action';
            if(method_exists($ins, $action)){
                call_user_func([$ins, $action]);
            }else{
                echo $action.'不存在! '. "\n";
            }
        }else{
            $action = 'DefaultAction';
            if(method_exists($ins, $action)){
                call_user_func([$ins, $action]);
            }else{
                echo $action.'不存在! '. "\n";
            }
        }

    }
};
// 运行路由
Cmd::router('[command]/[action]', $RouterFn);
// 默认路由
Cmd::router('[command]', $RouterFn);

Cmd::Run($argv);