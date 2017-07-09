<?php
/*  2017年2月12日 星期日
 *  hyang 处理类基本配置
*/
namespace hyang;
class Config{
    const dir = __DIR__;
    public static $basedir;
    // 获取hyang库下的目录
    public static function getDir($childDir=null)
    {
        $childDir = $childDir? $childDir:'';
        self::$basedir = str_replace('\\','/',self::dir).'/';
        return self::$basedir.$childDir;
    }
    // 获取资源（目录下json文件读取）
    public static function getResource($item){
        $file = self::getDir('resource/'.$item);
        $content = '';
        if(is_file($file)){
            $content = file_get_contents($file);
            // 为 json 文件时返回数组
            if(substr_count($file,'.json')>0) return json_decode($content,true);
        }
        return $content;
    }
}