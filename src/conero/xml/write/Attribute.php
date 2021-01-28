<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/17 0017 13:57
 * Email: brximl@163.com
 * Name: 属性值
 */

namespace hyang\conero\xml\write;


class Attribute
{
    /**
     * @var \XMLWriter
     */
    protected $xmlWt;
    public function __construct($xmlWt)
    {
        $this->xmlWt = $xmlWt;
    }
}