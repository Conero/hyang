<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 11:35
 * Email: brximl@163.com
 * Name: ppm php 包管理工具
 */
use \hyang\surong\cmd\Cmd;
use \hyang\surong\cmd\Fmt;
require_once __DIR__.'/vendor/ppm.php';


Cmd::Init(__DIR__.'/');

// 路由函数
$RouterFn = function ($cmd, $act=null){
    $cmd = ucfirst($cmd);
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
    }else{
        return true;
    }
};
// 运行路由
Cmd::router('[command]/[action]', $RouterFn);
// 默认路由
Cmd::router('[command]', $RouterFn);
// 解析失败
Cmd::router(Cmd::rUnfind, function ($cmd){
    Fmt::line($cmd.' 命令不存在！！');
});
// 空解析方式
Cmd::router(Cmd::rEmpty, function (){
    Fmt::line('命令行参数： ');
    Fmt::line('git 从开源的git仓库中获取数据！');
});

Cmd::Run($argv);