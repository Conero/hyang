<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/9 0009 14:36
 * Email: brximl@163.com
 * Name:
 */

define('BASE_DIR', __DIR__);

//require_once BASE_DIR.'/vendor/';
require_once BASE_DIR . '/vendor/cmd/loader.php';
require_once BASE_DIR.'/comman.php';

spl_autoload_register(function ($cls){
    $file = BASE_DIR.'/app/'. $cls .'.php';
    if(is_file($file)){
        require_once $file;
    }
});


// 定位器
$funtion  = function ($cmd, $action=null){
    $cls = ucfirst($cmd);
    $ins = new $cls();
    $action = ucfirst($action ?? 'Action');
    call_user_func([$ins, $action]);
};
\hyang\surong\cmd\Cmd::router('[command]/[action]', $funtion);


\hyang\surong\cmd\Cmd::router('[command]', $funtion);

// 未发现
\hyang\surong\cmd\Cmd::router('unfind', function ($cmd, $action){
    print 'unfind: '.$cmd .'/'.$action. "\r\n";
});

// 空
\hyang\surong\cmd\Cmd::router('empty', function (){
    print 'empty: '. "\r\n";
});
\hyang\surong\cmd\Cmd::Run($argv);