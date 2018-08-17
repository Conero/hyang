<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/17 0017 13:40
 * Email: brximl@163.com
 * Name: xml 写入器
 */

namespace hyang\xml;


use hyang\xml\write\Element;

class Writer
{
    /**
     * @var \XMLWriter
     */
    protected $xmlWt;
    /**
     * @var Element
     */
    protected $curElem = null;

    /**
     * Writer constructor.
     */
    public function __construct()
    {
        $this->xmlWt = new \XMLWriter();
        $this->xmlWt->openMemory();
    }

    /**
     * @param string $xml
     * @return $this
     */
    function xml($xml){
        $this->xmlWt->writeRaw($xml);
        return $this;
    }

    /**
     * @param $tag
     * @return Element
     */
    function tag($tag){
        // 存在元素时结束
        if($this->curElem){
            $this->curElem->close();
        }
        $this->curElem = new Element($this->xmlWt);
        $this->curElem->createTag($tag);
        return $this->curElem;
    }
    /**
     * @return string
     */
    function getXmlStr(){
        // 存在元素时结束
        if($this->curElem){
            $this->curElem->close();
        }
        return $this->xmlWt->outputMemory();
    }
}