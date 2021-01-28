<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/16 0016 14:36
 * Email: brximl@163.com
 * Name:
 */

namespace hyang\xml;


class Reader
{
    protected $content;
    protected $opt = [];
    protected $onEvents = [];
    /**
     * @var \XMLReader
     */
    protected $xmlReader;

    /**
     * Reader constructor.
     * @param null|string|array $data
     */
    public function __construct($data=null)
    {
        if(is_string($data)){
            $this->content = $data;
        }
        if(is_array($data)){
            $this->opt = $data;
            $this->content = $data['content'] ?? '';
            $this->load();
        }
    }

    /**
     * 重文件中加载
     * @param null $filename
     * @return bool
     */
    function load($filename=null){
        $filename = $filename? $filename : ($this->opt['filename'] ?? null);
        if($filename && is_file($filename)){
            $this->content = file_get_contents($filename);
            return true;
        }
        return false;
    }
    /**
     * xml 读取
     * @param null|string $content
     * @return bool
     */
    function read($content=null){
        $content = $content? $content: $this->content;
        if($content){
            $xrd = $this->xmlReader;
            if(!$xrd){
                $xrd = new \XMLReader();
                $this->xmlReader = $xrd;
            }
            $encoding = $this->opt['encoding'] ?? null;
            $xrd->xml($content, $encoding);
            // 解析处理
            while ($xrd->read()){
                switch ($xrd->nodeType){
                    case \XMLReader::ELEMENT:
                        $onRead = $this->getEvent('read');
                        if($onRead){
                            call_user_func($onRead, $xrd, $xrd->expand());
                        }
                        break;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 事件绑定
     * @param string $name
     * @param callable $callback
     * @return $this
     */
    function on($name, $callback){
        if(is_callable($callback)){
            $this->onEvents[$name] = $callback;
        }
        return $this;
    }

    /**
     * 事件获取
     * @param $name
     * @return mixed|null
     */
    protected function getEvent($name){
        return $this->onEvents[$name] ?? null;
    }

    /**
     * @param \DOMNode $node
     * @return array
     */
    static function getAttrMap($node){
        $attrIns = $node->attributes;
        $row = [];
        for ($i = 0; $i<$attrIns->length; $i++){
            $item = $attrIns->item($i);
            $row[$item->nodeName] = $item->nodeValue;
        }
        return $row;
    }
}