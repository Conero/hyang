<?php
// 前端编译加载工具 2016年12月29日 星期四 - 目前致辞 json 数据
namespace hyang;
use Exception;
class FrontBuild{
    const author = 'JOSHUA CONERO';
    const edittm = '2016年12月29日';
    public $basebir;  // 文件地址
    public $target;  // 目标地址
    public $config;
    public $debug;
    public $name;
    public function __construct($option=array())
    {                
        $this->name = isset($option['name'])? $option['name']:'';        
        $this->target = isset($option['target'])? $option['target']: __DIR__.'/_FrontBuild_/';
        $this->basebir = isset($option['basebir'])? $option['basebir']: __DIR__.'/_FrontBuild_/';
        $this->config = isset($option['config'])? $option['config']:'package.json'; // 配置文件名字
        $this->config = is_file($this->config)? $this->config : $this->basebir.$this->config;
        $this->debug = isset($option['debug'])? $option['debug']: true;
        // 测试效果未明显
        $this->expired();
    }
    // 获取编译文件
    public function import()
    {
        $script = '';
        $name = $this->name;
        $name = is_array($name)? $name:[$name];//println($name);
        foreach($name as $v){
            $res = $this->getFromCompile($v);            
            if(empty($res)){
                $sorceFile = $this->config;
                $res = $this->startComplie($sorceFile,$v);                              
            }
            $script .= $res;
        }        
        return $script;
    }
    // 文件的有效性检测
    public function expired()
    {        
        if(is_file($this->config)){
            $mtime = filectime($this->config);
            $name = sha1($mtime.(self::author).(self::edittm));
            $file = $this->target.$name;
            if(!is_file($file)){
                //println('文件以及失效');die;
                $this->clearComplieFile();
                file_put_contents($file,$name);
            }
        }
    }    
    // 从编译结果里面搜索
    public function getFromCompile($name)
    {
        $file = $this->target.sha1((self::author).$name);
        //println(filectime($this->basebir.$this->config));
        if(is_file($file)) return file_get_contents($file);
        return false;
    }
    // 文件导出
    public function startComplie($file,$name){
        if(is_file($file)){
            $data = json_decode(file_get_contents($file),true);
            if(isset($data['extends']) && isset($data['extends'][$name])){
                $script = '';
                // 设置配置信息
                $setting = isset($data['setting'])? $data['setting']:array();
                $baseurl = isset($setting['__baseurl__'])? $setting['__baseurl__']:'';              
                foreach($data['extends'][$name] as $k=>$v){
                   $type = strtolower($k);
                    switch($type){
                        case 'js':
                            $params = is_array($v)? $v:[$v];
                            foreach($params as $vv){
                                $ext = substr_count($vv,'.js') >0? $vv:$vv.'.js';
                                $script .= '<script src="'.$baseurl.$ext.'"></script>';
                            }                                        
                            break;
                        case 'css':
                            $params = is_array($v)? $v:[$v];
                            foreach($params as $vv){
                                $ext = substr_count($vv,'.css') >0? $vv:$vv.'.css';
                                $script .= '<link rel="stylesheet" href="'.$baseurl.$ext.'">';
                            }                                        
                            break;
                    }
                }
                if(empty($script)){
                    $this->error('从配置文件在中解析语句失败!!');
                    return '';
                }
                // 保存编译数据
                if(!is_dir($this->target)) mkdir($this->target);
                $target = $this->target.sha1((self::author).$name);
                file_put_contents($target,$script);
                return $script;
            }
            else $this->error($file.'文件未发现extends对象');
        }
        else $this->error('无法获取到目标文件，['.$file.']文件不存在或无法找到!');
        return '';
    }
    // 删除编译文件
    public function clearComplieFile()
    {
        $dir = $this->target;
        if(is_dir($dir)){
            $data = scandir($dir);
            foreach($data as $v){
                if($v == '.' || $v == '..') continue;
                unlink($dir.$v);
            }
        }
    }
    protected function error($msg)
    {
        if($this->debug) throw new Exception($msg);
    }
}