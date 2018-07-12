<?php
/*  2017年2月9日 星期四
 *  json特殊解析法   json 解析 支持json文本注释，不php系统函数支持的更广
 *  贪婪解析法
 */
 namespace hyang;
 use hyang\Net;
 class Json{
     private static $errorString;              // 错误提示信息
     private static $jsonStringArray = [];      // json字符串数据
     private static $rawJsonStringArray = [];   // 原始json字符串数
     // 字符串解析 - 通过处理预处理字符串在在php库解析
     // {"k1":"v1"}   "k1":"v1","k2":"v2"  {"k1":"v1",'k1':'v2'},{"k1":"v2"/*json字符串内可注释*/,"k1":"v2"}
     public static function decode($json)
     {
         if(empty($json)) return null;
         self::$rawJsonStringArray[] = $json;
         $json = preg_replace('/\s/','',$json); // 删除空格
         if(empty($json)) return null;
         $json = preg_replace('/(\/\*).+(\*\/)/','',$json); // 全注释
         if(empty($json)) return null;
         $json = self::doubleClear($json);  // 字符重影
         if(empty($json)) return null;     
        //  println($json,json_decode($json,true));
        self::$jsonStringArray[] = $json;
        return json_decode($json,true);
     }
     // 字符重影、非法删除
     private static function doubleClear($str){
         if(empty($str)) return "";
         $tpl = [
             '/,[\}]/'  => '}',              // 边界,清除
             '/,[\]]/'  => ']',              // 边界,清除
             "/'{1,}/"  => '"',              // 重影
             '/"{2,}/'  => '"',
             '/,{2,}/'  => ',',
             '/:{2,}/'  => ':'
         ];
         foreach($tpl as $k=>$v){
             $str = preg_replace($k,$v,$str);
         }
         return $str;
     }
     // 清除json字符串组
     public static function clearJsonString(){
         self::$jsonStringArray = [];
         self::$rawJsonStringArray = [];
    }
    //  获取json字符串组
    public static function getJsonString(){return self::$jsonStringArray;}
    //  获取json字符串组
    public static function getRawJsonString(){return self::$rawJsonStringArray;}
    // 读取文件并解析
    public static function decode_file($filename)
    {
         if(!is_file($filename)) return '';
         $handle = @fopen($filename, "r");
         $content = '';
         if ($handle) {
             $size = filesize($filename);
             $mutiLine = false;
             while (($buffer = fgets($handle, $size)) !== false) {                 
                 $buffer = trim($buffer);                 
                 if(empty($buffer)) continue;
                 if($mutiLine){
                     if(substr_count($buffer,'*/')>0){
                         $buffer = trim(str_replace(substr($buffer,0,strpos($buffer,"/*")+2),"",$buffer));
                         $mutiLine = false;
                     }
                     else continue;
                 }                 
                 // 多行注释处理
                 if(substr_count($buffer,'/*')>0){
                     $buffer = trim(str_replace(substr($buffer,strpos($buffer,"/*")),"",$buffer));
                     $mutiLine = true;
                 }
                 // 单行注释去除
                 if(substr_count($buffer,"//")>0) $buffer = trim(str_replace(substr($buffer,strpos($buffer,"//")),"",$buffer));
                 if(empty($buffer)) continue;
                 $content .= $buffer;
            }
            if (!feof($handle)) {
                self::$errorString = "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
        return self::decode($content);
     }
     // 读取网络内容并解析
     public static function decode_url($url)
     {
         $content = Net::get($url);
         $content = $content? $content:'';
        //  println($content);
         return self::decode($content);         
     }
     public static function encode($array)
     {
         return json_encode($array);
     }
     // 错误信息输出
     public static function error(){return self::$errorString;}
 }