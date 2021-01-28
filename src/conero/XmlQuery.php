<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/16 0016 13:14
 * Email: brximl@163.com
 * Name: XML query 解析器; 类 jQuery 查询
 */

namespace hyang\conero;


class XmlQuery
{
    /**
     * @var \XMLReader
     */
    protected $xReader;
    protected $xmlContent;
    protected $filename;
    protected $opt = [];

    /**
     * XmlQuery constructor.
     * @param array|string $opt
     */
    public function __construct($opt=array())
    {
        if(is_string($opt)){
            $this->xmlContent = $opt;
            $opt = [];
            $opt['xml'] = $this->xmlContent;
        }
        if($filename = ($opt['filename'] ?? false)){
            $this->load($filename);
        }
        $this->opt = $opt;
        $this->parser();
    }
    function parser(){
        $this->xReader = new \XMLReader();
        $encoding = $this->opt['encoding'] ?? null;
        if($this->xmlContent){
            $this->xReader->xml($this->xmlContent, $encoding);
        }elseif ($this->filename){
            $this->xReader->open($this->filename, $encoding);
        }
    }
    /**
     * 文件加载器
     * @param $filename
     * @return $this
     */
    function load($filename){
        if(is_file($filename)){
            $this->filename = $filename;
        }
        return $this;
    }
    function query($selector){}

    /**
     * @return \XMLReader
     */
    function getXRder(){
        return $this->xReader;
    }
}