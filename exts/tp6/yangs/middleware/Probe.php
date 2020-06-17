<?php
/*
 * 探针中间件
 * 2020年3月18日 星期三
 */

namespace tp6\yangs\middleware;


use think\Request;
use think\Response;

class ProbeMiddleware
{
    /**@var Request **/
    protected $request;
    /**@var Response **/
    protected $response;


    /**
     * 中间件入口
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    function handle(Request $request, \Closure $next){
        $this->request = $request;
        return $next($request);
    }

    /**
     * 访问结束
     * @param Response $response
     */
    function end(Response $response){
        $this->response = $response;
        //$this->_recodeApis();
    }

    /**
     * API 接口数据调试
     */
    protected function _recodeApis(){
        $request = $this->request;
        $response = $this->response;

        $url = $request->url();
        $method = $request->method();

        $date = date('Ymd H:i:s');

        $filename = app()->getRootPath() . '/runtime/probe_records.log';
        $filename = fopen($filename, 'a');
        $output = $response->getData();
        $outputStr = "";
        if(is_array($output)) $outputStr = ", OUTPUT: ".json_encode($output, JSON_UNESCAPED_UNICODE);

        $content = "$date) $method: $url, INPUT: " .json_encode(input()) .$outputStr. "\r\n";
        fwrite($filename, $content);
        fclose($filename);
    }
}