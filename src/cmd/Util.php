<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 15:23
 * Email: brximl@163.com
 * Name:
 */

namespace hyang\surong\cmd;


class Util
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
}