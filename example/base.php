<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 11:36
 * Email: brximl@163.com
 * Name: 基础程序
 */

// 程序加载器
spl_autoload_register(function ($cls){
    $file = '../src/'. str_replace('hyang\\surong\\', '', $cls).'.php';
    require_once $file;
});



\hyang\surong\cmd\Cmd::router('[command]/[action]', function ($cmd, $action){
    print '[command]/[action]: '.$cmd .'/'.$action. "\r\n";
});


\hyang\surong\cmd\Cmd::router('[command]', function ($cmd){
    print '[command]: '.$cmd . "\r\n";
});

// 未发现
\hyang\surong\cmd\Cmd::router('unfind', function ($cmd, $action){
    print 'unfind: '.$cmd .'/'.$action. "\r\n";
});

// 空
\hyang\surong\cmd\Cmd::router('empty', function (){
    print 'empty: '. "\r\n";
});
\hyang\surong\cmd\Cmd::Run($argv);