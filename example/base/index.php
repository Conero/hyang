<?php
/**
 * 2018年7月13日 星期五
 * 内部程序测试
**/


// 引入文件

//require_once './require.php';       // phar 内部文件无法加载
require_once __DIR__.'/require.php';

// 自动载入
spl_autoload_register(function ($class){
//    $file = '/'. $class.'.php';    // phar 内部文件无法加载
    $file = __DIR__. '/'. $class.'.php';
    require_once $file;
});


$br = "\r\n";
print ' 类引入： T1 '. \auto\T1::Name.$br;
print ' 类引入： T2 '. \auto\T2::Name.$br;

// 自动定义函数
(function(){
	print_r([
        $GLOBALS['author'],
        $GLOBALS['date']
    ]);
})();