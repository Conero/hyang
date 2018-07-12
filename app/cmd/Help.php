<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/6/28 0028 13:59
 * Email: brximl@163.com
 * Name: 帮助文档
 */

namespace app\cmd;

use hyang\surong\cmd\Controller;

class Help extends Controller
{
    function Main(){
        $action = $this->action;
        if(empty($action)){
            $this->DefaultAction();
        }
    }
    function DefaultAction(){
        echo
'
    命令行格式： [command] action key=v k2=v2
    
    command list
        build   b       phar 包打包
        help    ?       帮助说明
'
        ;
    }
}