<?php
/*
 * 网络请求
 * 2017年1月17日 星期二
 */
namespace hyang;
use hyang\Util;
class Net{
    private static $netUrl;
    private static $netSourceText;              // 最新的文本内容
    private static $netSourceTextLast;          // 上一次装换的内容，必须转码以后
    // 普通数据获取 - 流式数据请求
    // $data => {url:,post:}/ string-url
    public static function get($data=[])
    {
        $url = is_string($data)? $data: (isset($data['url'])? $data['url']:'');
        $url = $url? $url:self::$netUrl;
        if(isset($data['post'])){
            $post = is_string($data['post'])? json_decode($data['post'],true): $data['post'];
            $postStr =  http_build_query($post);
            $opts = ['http' =>
                [
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postStr
                ]
            ];
            $context  = stream_context_create($opts);
            $res = file_get_contents($url, false, $context);
        }
        else $res = file_get_contents($url);
        self::$netSourceText = $res;
        return $res;
    }
    // curl 获取数据 * 设置 $data 时 为POST/否则GET
    // $data = {url:请求地址,type:post,post:array,curlopt:curl 参数值}/string; 
    public static function curl($data=[])
    {
        $url = isset($data['url'])? $data['url']: self::$netUrl;
        if(empty($url) && is_string($data)){
            $url = $data;
            $data = [];
        }
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        // post 类型
        if(isset($data['type']) && strtolower($data['type']) == 'post' && !isset($data['post'])) curl_setopt ($ch, CURLOPT_POST, 1 );
        elseif(isset($data['post'])){
            if(!is_array($data['post'])) $data['post'] = json_decode($data['post'],true);
            curl_setopt ($ch, CURLOPT_POST, 1 );
		    curl_setopt ($ch, CURLOPT_POSTFIELDS, $data['post']);
            curl_setopt ($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0');
        }
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        //  可自定义curl 参数
        if(isset($data['curlopt'])) curl_setopt_array($ch,$data['curlopt']);
        $res = curl_exec($ch);
        curl_close($ch);
        self::$netSourceText = $res;
        return $res;
    }
    // 通过 cookie 值保存 http 当前会话
    public static function sessionSave($url)
    {
        $path = __DIR__.'/__cache/Net/'.date('Y-m-d').'/';
        $saveFile = $path.'cookie_'.md5($url);
        if('delete' == $url){    // 清空目录
            Util::cleardir($path);
            return null;
        }
        if(is_file($saveFile)) $cookieString = file_get_contents($saveFile);
        else{
            $heads = get_headers($url,true);
            if(isset($heads['Set-Cookie'])){
                $cookieString = is_array($heads['Set-Cookie'])? implode(';',$heads['Set-Cookie']): $heads['Set-Cookie'];
            }
            else $cookieString = ""; 
            if($cookieString){
                if(!is_dir($path)) Util::mkdirs($path);
                file_put_contents($saveFile,$cookieString);
            }
        }
        return $cookieString;
    }
    // 设置 url 
    public static function setUrl($url){
        self::$netUrl = $url;
    }       
    // 解析为数组
    public static function toArray()
    {
        $res = [];
        if(!empty(self::$netSourceText)) $res = json_decode(self::$netSourceText,true);
        return $res;
    }
    // 转码
    public static function decode($targetCharset=null,$sourceCharset=null)
    {
        $text = self::$netSourceText;
        if($text){
            $targetCharset = $targetCharset? $targetCharset:"UTF-8";
            $sourceCharset = $sourceCharset? $sourceCharset: "";
            self::$netSourceText = iconv($sourceCharset,$targetCharset,$text);
            self::$netSourceTextLast = $text;
            return true;
        }
        return false;
    }
    // 转码还原
    public static function recover()
    {
        $last = self::$netSourceTextLast;
        if($last){
            self::$netSourceText = $last;
            self::$netSourceTextLast = null;
            return true;
        }
        return false;
    }

    /**
     * 获取网络地址中的ip，如果获取失败则获取当前的ip
     * @return mixed
     */
    public static function getNetIp(){
        $url = 'http://httpbin.org/ip';
        self::get($url);
        $data = self::toArray();
        if(isset($data['origin'])) return $data['origin'];
        return request()->ip();
    }

    /**
     * 获取基础当前请求域名
     * @return string
     */
    public static function getBaseUrl(){
        return $_SERVER['REQUEST_SCHEME']
                .'://'.$_SERVER["HTTP_HOST"]
                .($_SERVER["QUERY_STRING"]? str_replace('?'.$_SERVER["QUERY_STRING"],'',$_SERVER["REQUEST_URI"]):'/');
    }
    /**
     * 更加get参数更新地址
     * @param null $url
     * @param array $query
     * @return null|string
     */
    public static function setQuery($url=null,$query=[]){
        if(empty($url)){
            $query = array_merge($_GET,$query);
            $query = !empty($query)? '?'.http_build_query($query):'';
            $url = self::getBaseUrl();
        }else{
            $tmp = parse_url($url);
            $parseData = isset($tmp['query'])? parse_str($tmp['query']):[];
            $query = array_merge($parseData,$query);
            $query = !empty($query)? '?'.http_build_query($query):'';
        }
        $url .= ($url? $url:'').$query;
        return $url;
    }
}