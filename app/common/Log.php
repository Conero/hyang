<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/13 0013 14:11
 * Email: brximl@163.com
 * Name:
 */

namespace app\common;


use hyang\surong\cmd\Io;

class Log
{
    protected static $cacheDir; // 缓存目录

    /**
     * 获取缓存文件
     * @return string
     */
    static function getCacheDir(){
        if(!self::$cacheDir){
            $baseDir = \hyang\surong\cmd\Cmd::getDir();
            self::$cacheDir = $baseDir.'.cache/';
            Io::mkdirs(self::$cacheDir);
        }
        return self::$cacheDir;
    }

    /**
     * @param string $msg
     */
    static function msg($msg){
        $file = self::getCacheDir(). date('Y-m-d') . '.log';
        $fh = fopen($file, 'a');
        $msg = '['.date('H:i:s'). ']```````````````````````````````````'. "\r\n"
            . $msg
        ;
        fwrite($fh, $msg);
        fclose($fh);
    }
    /**
     * @param mixed ...$datas
     */
    static function debug(...$datas){
        if(!empty($datas) && count($datas) == 1){
            $datas = $datas[0];
        }
        if(!empty($datas)){
            if(is_array($datas) || is_object($datas)){
                $datas = is_object($datas)? (array)$datas : $datas;
                $datas = print_r($datas, true);
            }
            self::msg($datas);
        }
    }
}