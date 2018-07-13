<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/13 0013 14:55
 * Email: brximl@163.com
 * Name:
 */

namespace app\cmd;


use hyang\surong\cmd\Controller;
use hyang\surong\cmd\Fmt;
use hyang\surong\cmd\Io;

class Test extends Controller
{
    public function DefaultAction(){
        Fmt::line('项目测试');
        //$this->testCloneDir();
    }
    function testCloneDir(){
        $cwd = $this->cwd;
        $test1 = $cwd . 'test1/';
        Io::mkdirs($test1);
        Io::cloneDir($cwd.'.cache/gits/hyang/', $test1);
    }
}