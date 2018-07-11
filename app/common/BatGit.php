<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 16:26
 * Email: brximl@163.com
 * Name: windows 命令行程序
 */
namespace app\common;

use hyang\surong\cmd\Io;

class BatGit
{
    /**
     * @param $clone
     * @param null $baseDir
     */
    static function fetch($clone, &$baseDir=null){
        $batName = '__js.'.time(). sha1(time().mt_rand());
        $baseDir = \hyang\surong\cmd\Cmd::getDir();
        $baseDir .= '.cache/gits/';
        Io::rmdirs($baseDir);
        Io::mkdirs($baseDir);
        $batFileName = $baseDir. '/'. $batName.'.bat';
        $batContent = '
cd '.$baseDir.'
git clone '.$clone.'
@REM pause ..
';

        file_put_contents($batFileName, $batContent);
        exec($batFileName);
        @unlink($batFileName);
    }
}