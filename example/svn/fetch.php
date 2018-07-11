<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 11:06
 * Email: brximl@163.com
 * Name: 代码运行
 */

$url = 'https://gitee.com/Doee/hyang.git';

$basedir = __DIR__.'/data';
@mkdir($basedir);

$batName = '__js.'.time(). sha1(time().mt_rand());
$batFileName = __DIR__. '/'. $batName.'.bat';
$batContent = '
cd data
git clone '.$url.' --branch=cmd
';

file_put_contents($batFileName, $batContent);
exec($batName);

@unlink($batFileName);
