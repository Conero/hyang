<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/9 0009 14:52
 * Email: brximl@163.com
 * Name:
 */

abstract class AppAbstract
{
    protected $_args = [];
    public function __construct()
    {
        $this->_args = \hyang\surong\cmd\Cmd::getArgs();
    }

    /**
     * 获取参数数据
     * @param $key
     * @param null $def
     * @return mixed|null
     */
    protected function args($key, $def=null){
        return $this->_args[$key] ?? $def;
    }
}