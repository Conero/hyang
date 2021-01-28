<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/9/17 0017 13:33
 * Email: brximl@163.com
 * Name:
 */

namespace hyang\conero;


interface DebugImpl
{
    /**
     * @param mixed $content
     */
    function saveLog($content);

    /**
     * @param null|string $msg
     * @return mixed
     * @throws \Exception
     */
    function error($msg=null);
}