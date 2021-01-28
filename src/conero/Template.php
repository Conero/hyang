<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/12 0012 10:12
 * Email: brximl@163.com
 * Name: 模板工具
 */

namespace hyang\conero;


class Template
{
    protected $tplStr;     // 模板字符串
    protected $tplFile;    // 文件名称
    protected $renderData = []; // 渲染参数
    public function __construct($option=[])
    {
        if($tplStr = ($option['tplStr'] ?? false)){
            $this->tplStr = $tplStr;
        }
        if($tplFile = ($option['tplFile'] ?? false)){
            $this->tplFile = $tplFile;
        }
        if($data = ($option['data'] ?? false)){
            $this->renderData = $data;
        }
    }
    /**
     * 模板字符解析(生成器)
     * @param string $tpl
     * @return \Generator
     */
    public function gGetVars($tpl){
        if(is_string($tpl) && !empty($tpl)){
            $pattern = '/\$\{[^\$\{\}]+\}/';         // 变量解析
            preg_match_all($pattern, $tpl, $matches);
            $matches = $matches[0] ?? [];               // 正确解析的参数
            $pattern2 = '/(^\$\{)|(\}$)/';

            foreach ($matches as $value){
                $key = preg_replace($pattern2, '', $value);
                yield $key => $value;
            }
        }
    }
    /**
     * 文件模板解析为字符串
     * @param bool|string $filename
     * @param array $data
     * @return bool|mixed
     */
    public function file2Str($filename=false, $data=[]){
        $filename = $filename === false? $this->tplFile: $filename;
        $data = empty($data)? $this->renderData: $data;
        $content = false;
        if(is_file($filename)){
            $content = $this->tpl2Str(file_get_contents($filename), $data);
        }
        return $content;
    }
    /**
     * 文件模式解析为php变量
     * @param bool|string $filename
     * @return bool|mixed
     */
    public function file2pVar($filename=false){
        $filename = $filename === false? $this->tplFile: $filename;
        $content = false;
        if(is_file($filename)){
            $content = $this->tpl2pVar(file_get_contents($filename));
        }
        return $content;
    }
    /**
     * 字符串模板解析为字符串
     * @param bool|string $tpl
     * @param array $data
     * @return bool|mixed
     */
    public function tpl2Str($tpl=false, $data=[]){
        $tpl = false === $tpl? $this->tplStr: $tpl;
        $data = empty($data)? $this->renderData: $data;
        $dick = [];
        // 生成字典
        foreach ($this->gGetVars($tpl) as $k => $v){
            $dick[$k] = $v;
        }
        // 遍历字典
        foreach ($dick as $k => $v){
            $newV = $data[$k] ?? '';
            $tpl = str_replace($v, $newV, $tpl);
        }
        return $tpl;
    }

    /**
     * 模板转化为php变量
     * @param bool|string $tpl
     * @return bool|mixed
     */
    public function tpl2pVar($tpl=false){
        $tpl = false === $tpl? $this->tplStr: $tpl;
        $dick = [];
        // 生成字典
        foreach ($this->gGetVars($tpl) as $k => $v){
            $dick[$k] = $v;
        }
        // 遍历字典
        foreach ($dick as $k => $v){
            $newV = '$'. $k;
            $tpl = str_replace($v, $newV, $tpl);
        }
        return $tpl;
    }

    /**
     * 保存内容为文件名
     * @param string $file
     */
    public function saveAsFile($file){
        $content = '';
        if($this->tplFile){
            $content = $this->file2Str();
        }
        else if($this->tplStr){
            $content = $this->tpl2Str();
        }
        file_put_contents($file, $content);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->renderData[$name] = $value;
    }

    /**
     * 参数过去
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->renderData[$name] ?? null;
    }

    /**
     * 简单的模板解析：模板 {key}|{json.key.knx}
     * @param string $s
     * @param array $vars
     * @return string
     */
    static function rend($s, $vars=[]){
        $reg = '/{[^{}]*}/';
        preg_match_all($reg, $s, $matches);
        $matches = is_array($matches) && count($matches) > 0? $matches[0]: [];
        foreach ($matches as $mt){
            $key = preg_replace('/{|}/', '', $mt);
            $key = trim($key);
            $value = '';
            // 直接的键值
            if(isset($vars[$key])){
                $value = $vars[$key];
            }elseif (strpos($key, '.') !== false){  // 多级点 "." 值访问
                $keys = explode('.', $key);
                $hasV = true;
                $tmpV = $vars;
                foreach ($keys as $k){
                    if($hasV && is_array($tmpV) && isset($tmpV[$k])){
                        $tmpV = $tmpV[$k];
                        $hasV = true;
                        continue;
                    }
                    $hasV = false;
                    break;
                }
                // 最终的值
                if($hasV){
                    $value = $tmpV;
                }
            }

            $s = str_replace($mt, $value, $s);
        }
        return $s;
    }
}