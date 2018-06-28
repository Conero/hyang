<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/6/28 0028 9:32
 * Email: brximl@163.com
 * Name: 入口函数
 */

// 常量
//define('ROOT_DIR', str_replace('\\', '/', __DIR__) . '/');
define('RUN_DIR', 'ROOT_DIR', str_replace('\\', '/', __DIR__) . '/');
define('ROOT_DIR', './');
define('Cli_BR', "\n");     // 换行
define('Cli_LogD', ROOT_DIR. 'log/');
define('Cli_OutPutD', ROOT_DIR. 'bist/');


//echo cli_get_process_title();
require_once __DIR__.'/version.php';
require_once __DIR__.'/require.php';
require_once __DIR__.'/common.php';

// 系统命令

echo ' .  欢迎使用 SrPhar 包'. "\n";
echo ' .  '.Sr_Name.' v'.Sr_Version. '/'.Sr_Build."\n";
echo ' .  since@20180628'. "\n\n";


// 启动路由
require_once __DIR__.'/router.php';