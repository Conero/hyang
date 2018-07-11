<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 20:46
 * Email: brximl@163.com
 * Name:
 */

namespace hyang\surong\cmd;


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
            // 复制执行函数
            static $cloneDirHdFn = null;
            $cloneDirHdFn = function ($s, $t) use ($src, $tar, $cloneDirHdFn){
                $s = self::standir($s);
                foreach (scandir($s) as $v){
                    if(in_array($v, ['.', '..'])){
                        continue;
                    }
                    $path = $s . $v;
                    $path2 = str_replace($src, $tar, $path);
                    if(is_dir($path)){
                        self::mkdirs($path2);
                        call_user_func($cloneDirHdFn, $path, $path2);
                    }
                    else{
                        copy($path, $path2);
                    }
                }
            };
            call_user_func($cloneDirHdFn, $src, $tar);
            return true;
        }
        else{
            return false;
        }
    }
}