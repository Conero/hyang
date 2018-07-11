<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 15:17
 * Email: brximl@163.com
 * Name: 系统控制器
 */

namespace hyang\surong\cmd;


abstract class Controller implements InterContrl
{
    protected $cmdOption = array();
    protected $cmdArgs = array();
    protected $cwd;
    function __construct()
    {
        // TODO: 逻辑实现
        $this->cmdArgs = Cmd::getArgs();
        $this->cmdOption = Cmd::getOption();
        $this->cwd = Cmd::getDir();
    }
}