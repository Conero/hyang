<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/6/28 0028 9:32
 * Email: brximl@163.com
 * Name: 入口函数
 */
use \hyang\surong\cmd\Cmd;
use \hyang\surong\cmd\Fmt;

// Report simple running errors
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

define('ROOT_DIR', str_replace('\\', '/', __DIR__).'/');
require_once ROOT_DIR.'version.php';
require_once ROOT_DIR.'common.php';

require_once ROOT_DIR.'vendor/ppm.php';
Cmd::Init(ROOT_DIR);
//Cmd::Init(__DIR__.'/');
//Cmd::Init();
require_once ROOT_DIR.'require.php';

define('RUN_DIR', Cmd::getDir());
define('Cli_BR', "\n");     // 换行
define('Cli_LogD', RUN_DIR.'log/');
define('Cli_OutPutD', RUN_DIR.'bist/');

// 路由函数
$RouterFn = function ($cmd, $act=null){
    $app = 'app\\cmd\\'.$cmd;
    //if(class_exists($app) || 1){
    if(class_exists($app)){
        $ins = new $app;
        if($act){
            $action = ucfirst($act) . 'Action';
            if(method_exists($ins, $action)){
                call_user_func([$ins, $action]);
            }else{
                //echo $action.'不存在! '. "\n";
                call_user_func([$ins, 'Main']);
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
    Fmt::line('欢迎使用 SrPhar');
    Fmt::line(Sr_Name.' v'.Sr_Version. '/'.Sr_Build);
    Fmt::line('since@20180628');
});

Cmd::Run($argv);