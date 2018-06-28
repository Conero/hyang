<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/6/28 0028 9:49
 * Email: brximl@163.com
 * Name: 命令行控制器
 */

namespace sr;


use sr\cmd\Router;

class CmdCtrl
{
    protected $_args = array();
    protected $_option = [];
    protected $_action;
    function __construct()
    {
        $this->_args = Router::getArgs();
        $this->_option = Router::getOption();
        $this->_action = Router::getAction();
    }

    /**
     * @param $name
     * @return mixed|null
     */
    function __get($name)
    {
        $value = $this->_args[$name] ?? null;
        return $value;
    }
}