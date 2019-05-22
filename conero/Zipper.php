<?php
/**
 * Created by PhpStorm.
 * User: Joshua Conero
 * Date: 2019/5/22
 * Time: 11:24
 * Email: conero@163.com
 */

namespace hyang;


use ZipArchive;

class Zipper
{
    private static $_basedir;
    /**
     * @var ZipArchive
     */
    private static $_zip;
    private static $_filtration;

    /**
     * @param string $name
     * @param string $dir
     * @param array $filtration
     * @return bool
     */
    static function dirZip($name, $dir, $filtration=[]){
        $zip = new ZipArchive;

        self::$_zip = $zip;
        self::$_filtration = $filtration;
        $dir = Fs::getStdDir($dir);

        $res = is_dir($dir)? $zip->open($name, ZIPARCHIVE::CREATE|ZIPARCHIVE::OVERWRITE) : false;
        $success = false;
        if($res === true){
            self::$_basedir = $dir;
            self::_addTheFile();
            $zip->close();
            $success = true;
        }
        return $success;
    }

    /**
     * 添加文件
     * @param null|string $dir
     */
    private static function _addTheFile($dir=null){
        $dir = $dir? $dir : self::$_basedir;
        $dir = Fs::getStdDir($dir);
        foreach(scandir($dir) as $v){
            if(in_array($v, ['.', '..'])){continue;}
            $path = $dir . $v;
            $name = str_replace(self::$_basedir, '', $path);
            if(is_dir($path)){
                self::$_zip -> addEmptyDir($name);
                self::_addTheFile($path.'/');
            }else{
                self::$_zip -> addFile($path, $name);
            }
        }
    }

    /**
     * @return ZipArchive
     */
    static function getZip(){
        return self::$_zip;
    }
}