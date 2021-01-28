<?php
/* 2017年2月16日 星期四
 *  Js 压缩工具
 *
 */
namespace hyang\conero;
use hyang\conero\Util;
class Jscompress{
    const author = 'Joshua Conero';
    private $Jsfname;           // 获取到的js文件
    private $mintext;           // 压缩后的文件
    private $targetDir;         // 目标地址
    private $targetSuffix;      // 编译后的后缀名称 ， 默认 min.js
    public $rateCompress;       // 压缩率
    public $jsonCompress=[];    // 压手过程中产生的json数据
    private $errorInfo;         // 错误信息
    private $cpsfilenotes;      // 压缩文件注释
    private $cfnotesJson=[];    // 压缩文件注释束
    private $cprserConfig=[];      // 压缩器的原始配置文件
    /* config = {
     *     "dir": targetDir
     *     "ext": targetSuffix
     * }
     *
    */
    public function __construct($config=[]){
        $this->targetDir = isset($config['dir'])? str_replace('//','/',$config['dir']):null;
        $this->targetSuffix = isset($config['ext'])? $config['ext']:'.min.js';
        $this->fSuffix = isset($config['fext'])? $config['fext']:'.js';
        if(isset($config['cprsnotes'])) $this->cpsfilenotes = $config['cprsnotes'];//: "@time:".date('Y-m-d H:i:s').";@author: Joshua Conero";
        if($config) $this->cprserConfig = $config;
    }
    // 压缩执行
    public function compress($fname=null,$onlyGetstr=false)
    {
        if($fname) $this->Jsfname = $fname;
        if($this->Jsfname && substr(strtolower($this->Jsfname),-2) != 'js') $this->Jsfname .= '.js';
        $fname = $this->Jsfname;
        $content = '';
        if($fname && is_file($fname)){            
            $this->mintext = null;
            $handle = @fopen($fname, "r");
            $content = '';
            $countLine = 0;$compressLine = 0;
            $sourceSize = filesize($fname);
            if ($handle) {
                $size = filesize($fname);
                $mutiLine = false;$timeStart = Util::getMs();
                while (($buffer = fgets($handle, $size)) !== false) {
                    $countLine += 1;                 
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
                        // 多行注释写为单行
                        if(substr($buffer,-2) == '*/'){
                            continue;
                        }
                        $buffer = trim(str_replace(substr($buffer,strpos($buffer,"/*")),"",$buffer));
                        $mutiLine = true;
                    }
                    // 单行注释去除
                    if(substr_count($buffer,"//")>0) $buffer = trim(str_replace(substr($buffer,strpos($buffer,"//")),"",$buffer));
                    $compressLine += 1;
                    $content .= $buffer;
                }
                if($content){
                    $this->mintext = $content;
                    $this->rateCompress = ($countLine - $compressLine)/$countLine;
                    $this->jsonCompress = [
                        'comline' => $compressLine,
                        'ctline'  => $countLine,
                        'sourceSize'    => $sourceSize,
                        'dtms'          => round(Util::getMs($timeStart) * 1000,5).' ms'
                    ];
                    $this->createNoteText(['srctime'=>filemtime($fname),'ctime'=>time()]);
                }
                if(!$onlyGetstr) $this->saveCompress();
            }
        }
        else $this->_error('Error:js文件名称无效，压缩失败.');            
        return $content;    
    }
    // 保存压缩文件
    public function saveCompress($filename=null){
        if(empty($this->targetDir)) $this->targetDir = str_replace('//','/',dirname($this->Jsfname)).'/';
        $dir = $this->targetDir;
        $content = $this->mintext;
        // println($dir);
        if($content && is_dir($dir)){
            // $filename = $dir.'/'.str_replace($this->fSuffix,$this->targetSuffix,basename($this->Jsfname));
            $filename = $filename? $filename:$this->getComplierName();
            // println($filename);
            $content = (empty($this->cpsfilenotes)? $this->createNoteText():$this->cpsfilenotes).$content;
            Util::mkdirs($filename,true);
            $ret = file_put_contents($filename,$content);
            if($ret){
                $this->jsonCompress['complierSize'] = filesize($filename);
                if(isset($this->jsonCompress['sourceSize'])) $this->jsonCompress['srate'] = ($this->jsonCompress['sourceSize'] - $this->jsonCompress['complierSize']) / $this->jsonCompress['sourceSize'];
            }
            return $ret;
        }
        return false;
    }
    // 获取当前的文件名称
    public function getComplierName($fname=null,$afterCheck=null){
        if($fname) $this->Jsfname = $fname;
        if($this->Jsfname && substr(strtolower($this->Jsfname),-2) != 'js') $this->Jsfname .= '.js';
        $fname = $this->Jsfname;
        if(empty($this->targetDir)) $this->targetDir = str_replace('//','/',dirname($fname)).'/';
        $dir = $this->targetDir;
        $filename = $dir.'/'.str_replace($this->fSuffix,$this->targetSuffix,basename($fname));
        if($afterCheck instanceof \Closure) $afterCheck($this,is_file($filename),$filename);
        return $filename;
    }
    // 压缩后注释生成器
    private function createNoteText($setArray=null){
        if(empty($this->cfnotesJson)){
            $this->cfnotesJson = [
                'time'      => date('Y-m-d H:i:s'),
                'author'    => self::author
            ];
        }
        if($setArray && is_array($setArray)){
            $this->cfnotesJson = array_merge($this->cfnotesJson,$setArray);
        }
        else{
            if(is_array($this->cfnotesJson)){
                $tmpTxt = [];
                foreach($this->cfnotesJson as $k=>$v){
                    $tmpTxt[] = '@'.$k.': '.$v;
                }
                return '/*'.implode(',',$tmpTxt)."*/\r\n";
            }
        }
        return "";
    }
    // 压缩报告
    public function rptCompress($print=false)
    {
        $array = $this->jsonCompress;
        $array = is_array($array)? $array:[];
        $array['rate'] = $this->rateCompress;
        if($print) echo print_r($array,true);
        else return $array;
    }
    // 信错设置新增
    private function _error($str=null){
        if(empty($str)) return $this->errorInfo;
        $this->errorInfo = $str;
    }
}