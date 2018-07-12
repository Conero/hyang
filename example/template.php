<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/12 0012 10:32
 * Email: brximl@163.com
 * Name: 模板测试
 */

require_once __DIR__.'/testLoader.php';


// 字符串解析
(function(){
    $tpl = new \hyang\Template();
    $tplStr = '
    系统字符串， 
    ${value} 模板 ${name}  ,
    时间等 ${date} + ${test}
    ';
    echo $tpl->tpl2Str($tplStr). Br;
    echo $tpl->tpl2pVar($tplStr). Br;
})();



// 文件
(function(){
    $tpl = new \hyang\Template([
        'tplFile'   => __DIR__.'/tplfiles/base.php'
    ]);
    $tpl->saveAsFile(__DIR__.'/tplfiles/base-Render.php');
})();