<?php
/* 2017年2月10日 星期五
 *  文件系统处理
 *
 */
namespace hyang\conero;
class Fs{
    public static $pathname;    // 目录
    public static $dirArray;    // 目录信息数组
    // 获取单目录信息
    public static function dir($path=null)
    {
        $rets = [];
        $pathname = ($path && is_string($path))? $path : self::$pathname;
        if($pathname && is_dir($pathname)){
            foreach(scandir($pathname) as $v){
                if(in_array($v,['.','..'])) continue;
                if($path instanceof \Closure) $rets[] = $path($v);
                else $rets[] = $v;
            }
        }
        self::$dirArray = $rets;
        return $rets;
    }
    // 将结果输出为字符串
    public static function toString($delimiter=null)
    {
        $delimiter = $delimiter? $delimiter:',';
        return self::$dirArray? implode($delimiter,self::$dirArray):'';
    }

    /**
     * 标准目录格式
     * @param string $dir
     * @return mixed|string
     */
    static function getStdDir($dir){
        $dir = str_replace('\\', '/', $dir);
        if($dir && '/' != substr($dir, -1)){
            $dir .= '/';
        }
        $dir = preg_replace('/[\/]{2,}/', '/', $dir);
        return $dir;
    }
}