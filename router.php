<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/6/28 0028 14:10
 * Email: brximl@163.com
 * Name: 程序路由
 */


// 未发现命令
\sr\cmd\Router::on('unfind', function ($cmd){
    echo '  {'. $cmd . '} 命令不存在 '. "\n";
});

// 路由初始化
\sr\cmd\Router::on('init', function ($cmd){
    $cmd = strtolower($cmd);
    switch ($cmd){
        case '?': $cmd = 'help';break;
        case 'b': $cmd = 'build';break;
    }
    return $cmd;
});

// 启动路由
\sr\cmd\Router::Run($argv);