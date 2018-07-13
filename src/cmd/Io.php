<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 20:46
 * Email: brximl@163.com
 * Name:
 */

namespace hyang\surong\cmd;


use app\common\Log;

class Io
{
    /**
     * 多级目生成
     * @param string $dir
     * @return bool
     */
    static function mkdirs($dir){
        if(!is_dir($dir)){
            $dir = str_replace('\\', '/', $dir);
            $pDirQue = [];
            foreach (explode('/', $dir) as $name){
                $pDirQue[] = $name;
                $pDir = implode('/', $pDirQue);
                if(!is_dir($pDir)){
                    mkdir($pDir);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 目录删除
     * @param string $dir
     */
    static function rmdirs($dir){
        if(is_dir($dir)){
            $dir = self::standir($dir);
            foreach (scandir($dir) as $v){
                if(in_array($v, ['.', '..'])){
                    continue;
                }
                $path = $dir. $v;
                if(is_dir($path)){
                    self::rmdirs($path);
                }else{
                    unlink($path);
                }
            }
            rmdir($dir);
        }
    }

    /**
     * 标准目录
     * @param string $dir
     * @return mixed|string
     */
    static function standir($dir){
        $dir = str_replace('\\', '/', $dir);
        $dir = substr($dir, -1) != '/'? $dir.'/': $dir;
        return $dir;
    }

    /**
     * 目录复制执行
     * @param string $src
     * @param string $tar
     * @param null|string $bSrc
     * @param null|string $bTar
     */
    static function execCloneDir($src, $tar, $bSrc=null, $bTar=null){
        // 复制顶级目录
        $bSrc = $bSrc ? $bSrc : $src;
        $bTar = $bTar? $bTar : $tar;
        // 父类
        $src = self::standir($src);
        foreach (scandir($src) as $v){
            if(in_array($v, ['.', '..'])){
                continue;
            }
            $path = $src . $v;
            Log::debug($src , $v);
            $path2 = str_replace($bSrc, $bTar, $path);
            if(is_dir($path)){
                self::mkdirs($path2);
                self::execCloneDir($path, $path2, $bSrc, $bTar);
            }
            else{
                //Fmt::println($path, $path2);
                //Log::debug($path, $path2);
                copy($path, $path2);
            }
        }
    }
    /**
     * 目录赋值
     * @param string $src
     * @param string $tar
     * @return bool
     */
    static function cloneDir($src, $tar){
        if(is_dir($src)){
            $src = self::standir($src);
            $tar = self::standir($tar);
            self::rmdirs($tar);
            self::execCloneDir($src, $tar);
            return true;
        }
        else{
            return false;
        }
    }
}