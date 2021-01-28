<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/17 0017 13:48
 * Email: brximl@163.com
 * Name: xml 写入元素
 */

namespace hyang\conero\xml\write;


class Element
{
    /**
     * @var \XMLWriter
     */
    protected $xmlWt;
    protected $elemOpenMk = false;  // 元素开启标识
    public function __construct($xmlWt)
    {
        $this->xmlWt = $xmlWt;
    }


    /**
     * @param string $tag
     * @return $this
     */
    function createTag($tag){
        $this->xmlWt->startElement($tag);
        $this->elemOpenMk = true;
        return $this;
    }

    /**
     * @param string $tag
     * @return Element
     */
    function tag($tag){
        $self = new self($this->xmlWt);
        return $self->createTag($tag);
    }

    /**
     * @param $tag
     * @param null $value
     * @return $this
     */
    function child($tag, $value=null){
        $this->xmlWt->writeElement($tag, $value);
        return $this;
    }

    /**
     * @param string|array $attr
     * @param mixed|null $value
     * @return $this
     */
    function attr($attr, $value=null){
        if(is_array($attr)){
            foreach ($attr as $k=>$v){
                $this->xmlWt->writeAttribute($k, $v);
            }
        }elseif ($attr && $value){
            $this->xmlWt->writeAttribute($attr, $value);
        }
        return $this;
    }

    /**
     * 编写结束
     */
    function close(){
        if($this->elemOpenMk){
            $this->xmlWt->endElement();
            $this->elemOpenMk = false;
        }
    }
}