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

    /**
     * 检测是否为 git 仓库
     * @param string $dir
     * @return bool
     */
    static function isGitDir($dir){
        $isTrue = false;
        if(is_dir($dir)){
            $dir = standDir($dir);
            foreach (scandir($dir) as $v){
                if(in_array($v, ['.', '..'])){
                    continue;
                }
                $path = $dir . $v;
                if($v == '.git' && is_dir($path)){
                    $isTrue = true;
                    break;
                }
            }
        }
        return $isTrue;
    }
}