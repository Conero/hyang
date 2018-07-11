<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 11:49
 * Email: brximl@163.com
 * Name: cli 模式测试
 */

// 设置线程标题
cli_set_process_title('hyang/cmd');
$pid = getmypid();
$br = "\n";
print ' Pid: '. $pid .$br;
print '当前工作目录： '. getcwd(). $br;




sleep(2);