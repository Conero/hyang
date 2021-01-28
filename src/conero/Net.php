<?php
/*
 * 网络请求
 * 2017年1月17日 星期二
 */
namespace hyang\conero;
class Net{
    private static $netUrl;
    private static $netSourceText;              // 最新的文本内容
    private static $netSourceTextLast;          // 上一次装换的内容，必须转码以后
    private $execOption = array();
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
            $res = @file_get_contents($url, false, $context);
        }
        else $res = @file_get_contents($url);
        self::$netSourceText = $res;
        return $res;
    }

    /**
     * @return Net
     */
    public static function prepare($url){
        $instance = new self();
        $instance->setOption('url', $url);
        return $instance;
    }

    /**
     * @param $key
     * @param null|callable $value  function($opt){
     * @return $this
     */
    public function setOption($key, $value=null){
        if($value instanceof \Closure){
            $value = call_user_func($value, $this->execOption);
        }
        if($value){
            $this->execOption[$key] = $value;
        }else if(is_array($key)){
            $this->execOption = array_merge($this->execOption, $key);
        }
        return $this;
    }
    /**
     * @param $key
     * @param null $def
     * @return mixed|null
     */
    public function getOption($key, $def=null){
        return isset($this->execOption[$key])? $this->execOption[$key]: $def;
    }

    /**
     * 参数： {method: string, header:[], protocol: string, data: []}
     * @return null|string
     */
    public function exec(){
        $url = $this->getOption('url', null);
        $res = null;
        if($url){
            $method = $this->getOption('method', 'GET');
            $header = $this->getOption('header', []);
            $protocol = $this->getOption('protocol', (0 === stripos($url, 'https')? 'https': 'http'));
            $data = $this->getOption('data');
            $opts = [];
            $opts[$protocol] = [
                'method' => $method,
            ];
            if($data){
                if('POST' == strtoupper($method)){
                    $opts[$protocol]['content'] = http_build_query($data);//debug([$header, 1]);
                    $header[] = 'Content-type: application/x-www-form-urlencoded';//debug([$header, 2]);
                }
                // GET 传参数
                elseif ('GET' == strtoupper($method)){
                    //debug([http_build_query($data)]);
                    //$url =
                    if(is_array($data)){
                        $tmpUrl = parse_url($url);
                        $baseData = [];
                        if(isset($tmpUrl['query'])){
                            $url = str_replace('?'.$tmpUrl['query'], '', $url);
                            parse_str($tmpUrl['query'], $baseData);
                        }
                        $queryStr = http_build_query(array_merge($baseData, $data));
                        $url .= '?'.$queryStr;
                    }
                }
            }
            if($header){
                $newHeader = [];
                foreach ($header as $hdKey=>$hdValue){
                    if(is_int($hdKey)){
                        $newHeader[] = $hdValue;
                        continue;
                    }
                    if(!is_array($hdValue)) $newHeader[] = $hdKey.': '.$hdValue;
                }
                $opts[$protocol]['header'] = $newHeader;
            }
            //debug([$opts, $url]);
            //ifdebug(strpos($url, 'openId') !== false, $opts, $url);
            //println($opts);
            $context  = stream_context_create($opts);
            $res = file_get_contents($url, false, $context);
        }
        return $res;
    }

    /**
     * https 请求
     * @return array
     */
    public function sslOption($data=null){
        $opt = [];
        return $opt;
    }
    /**
     * 直接获取json，通过相应
     * @return array|mixed
     */
    public function getJsonByExec(){
        $res = $this->exec();
        return $res? json_decode($res, true): [];
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
        return self::ip();
    }

    /**
     * @return null|string
     */
    static function ip(){
        return $_SERVER['REMOTE_ADDR'] ?? $_SERVER['SERVER_ADDR'] ?? null;
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
     * 获取当前 host 页面
     * @return string
     */
    static function getHost(){
        $scheme = $_SERVER['REQUEST_SCHEME'];
        $port = $_SERVER['SERVER_PORT'];
        $host = $scheme
            . '://'. $_SERVER['SERVER_NAME'];

        $noNeedPort = ($scheme == 'http' && $port == '80') || ($scheme == 'https' && $port == '443');
        if(!$noNeedPort){
            $host .= ':'.$port;
        }
        $host .= "/";
        return $host;
    }

    /**
     * 获取标准url地址
     * @param string $url
     * @return string
     */
    static function stdUrl($url){
        if(strpos($url, 'http') === false){
            $url = self::getHost(). $url;
            $url = str_replace('//', '/', $url);
        }
        return $url;
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
            if(isset($tmp['query']) && !empty($tmp['query'])){
                $parseData = parse_str($tmp['query']);
                $url = substr($url,0,strpos($url,'?'));
            }else $parseData = [];
            $query = array_merge($parseData,$query);
            $query = !empty($query)? '?'.http_build_query($query):'';
        }
        $url = ($url? $url:'').$query;
        return $url;
    }
    /**
     * 地址跳转
     * @param $url
     */
    public static function go($url){
        header('Location: '.$url);
        exit;
    }

    /**
     * 支持 https 的请求封装，测试 php7+, 需要在 php5 环境下测试
     * @param array|string $option {url, data, method, header}
     * @param bool $err
     * @return mixed|string
     */
    public static function curls($option, &$err=false){
        // 数据预处理
        $method ='GET';
        $data = null;
        /**
        $headers = array(
        　　 'api-key:'.$key,
        　　 'authorization:'.$authorization,
        );
         */
        $header = null;
        if(is_array($option)){
            if(isset($option['data'])){ // 头部信息
                $data = $option['data'];
            }
            if(isset($option['header'])){ // 头部信息
                $header = $option['header'];
            }
            $url = $option['url'];
            $method = isset($option['method'])? $option['method']: $method;
        }
        else{
            $url = $option;
        }
        $url = self::stdUrl($url);
        $method = strtoupper($method);
        $ch = curl_init();
        if($method == 'GET' && is_array($data)){
            $tmpUrl = parse_url($url);
            $baseData = [];
            if(isset($tmpUrl['query'])){
                $url = str_replace('?'.$tmpUrl['query'], '', $url);
                parse_str($tmpUrl['query'], $baseData);
            }
            $queryStr = http_build_query(array_merge($baseData, $data));
            $url .= '?'.$queryStr;
        }
        curl_setopt($ch,CURLOPT_URL,$url);
        // 头部信息设置
        if($header){
            $stdHeader = [];
            foreach ($header as $key=>$value){
                if(is_numeric($key)){
                    $stdHeader[] = $value;
                }else{
                    $stdHeader[] = $key.':'.$value;
                }
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $stdHeader);
        }
        // post 类型
        if('POST' == $method){
            curl_setopt ($ch, CURLOPT_POST, 1 );
            if($data){
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            }
            //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
            //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($fields)));
        }
        //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);    // 不限制请求超时

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        $res = curl_exec($ch);
        if($res === false){
            $err = curl_error($ch);
        }
        curl_close($ch);
        return $res;
    }
}