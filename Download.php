<?php
/*
 *  @ 文件下载 - 2017年1月10日 星期二
 *  问下来源： 文本内容/读取文件等       
 *  其他参数：  errorText 错误是文本提示   
 *  1. 文本内容：setConfig => [content:文本内容]
 *  2. 读取文件：          
 */
namespace hyang;
class Download{
    private static $isContent; // 执行构造文本
    private static $content;   // 下载文内容
    private static $file;      // 文件地址
    private static $name;
    private static $charset = 'utf8';
    // 文件下载失败以后
    private static $wErrorTipRpt; 
    public static function setConfig($option=[]){
        self::$isContent = isset($option['isContent'])? true:false; 
        self::$wErrorTipRpt = isset($option['errorText'])? $option['errorText']:null;
        self::$name = isset($option['name'])? $option['name']:null;     // 文件名称
        if(isset($option['content'])){
            self::$content = $option['content'];
            self::$isContent = 'text';
        }
        elseif(isset($option['file'])){
            self::$file = $option['file'];
            self::$isContent = false;
        }
    }
    // 设置文本内容
    public static function setContent($text){
        self::$content = $text;
        self::$isContent = true;
    }    
    // 设置文件地址
    public static function filename($file){
        self::$file = $file;
        self::$isContent = false;
    } 
    // 用于链式处理
    public static function __callStatic($name, $arguments){
        echo "Calling static method '$name' ". implode(', ', $arguments). "\n";die;
    }
    // 文件下载
    public static function load(){
        if(self::$isContent == true) self::loadContent();
        else self::loadFile();        
    }
    // 构造下载文本内容
    public static function loadContent($content=null)
    {
        $content = $content? $content:self::$content;
        if(empty(self::$name)) self::$name = time().'.txt';
        if(empty($content)){
            $br = "\r\n";
            $content = self::$wErrorTipRpt? self::$wErrorTipRpt :
                date('Y-m-d H:i:s',time()).'>>'.
                $br. "文件下载失败，文件可能不存在或者已经损坏".
                $br. "来源@ Conero Inc.".
                $br. "来源@ Joshua Yang Doeeking."
                ;
        }
        header('Content-type: application/octet-stream; charset='.self::$charset);     //  下载动作的关键
        header("Accept-Ranges: bytes");
        header('Content-Disposition: attachment; filename='.self::$name);
        echo $content;
        exit();
    }
    // 从文件中下载
    public static function loadFile()
    {
        $file = self::$file;
        if(empty($file) || !is_file($file)){            
            self::loadContent();
            return;
        }
        header("Content-Type: application/force-download");
        header('Content-Disposition: attachment; filename="'.(self::getLoadFileName($file)).'"');
        header( "Content-Length: " . filesize ($file) );  
        ob_clean();
        flush();
        readfile($file);
        exit();
    }
    // 获取文件名称
    private static function getLoadFileName($file){
        $file = str_replace('\\','/',$file);
        if(substr_count($file,'/') > 0){
            $arr = explode('/',$file);
            // println($arr,$arr[count($arr)-1],array_pop($arr));die;
            // return $arr[count($arr)-1];
            return array_pop($arr);
        }
        return $file;
    }
}
