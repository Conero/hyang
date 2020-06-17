<?php

/**
 * 帮助文档工具
 * @param array $args
 */
function debug_input(...$args){
    \think\facade\Log::write($args);
}

/**
 * 调试输出接口
 * @param string|int $msg
 * @param int $code
 * @return \think\response\Json
 */
function debug_json($msg=null, int $code = 1){
    $msg = $msg? $msg: 'debug: '.mt_rand(100000, 999999);
    return json(['code' => $code, 'msg' => $msg]);
}

/**
 * 调试输出
 * @param null $msg
 * @throws Exception
 */
function throw_error($msg=null){
    $msg = $msg? $msg: mt_rand(18700000000, 18799999999);
    throw new Exception($msg);
}