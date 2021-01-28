<?php
/**
*  php 地址定位器/利用网络接口
**/
namespace hyang\conero;
use Exception;
class Location{
    private static $pingIp;    // 获取IP
    public static function setIp($ip){
        self::$pingIp = $ip;
    }
    // 获取地址信息
    public static function getLocation()
    {
        $lookIp = self::$pingIp;
        if(empty($lookIp)){
            $ipArr = json_decode(trim(self::getContent('http://httpbin.org/ip')),true);
            $lookIp = $ipArr['origin'];
        }
        $info = json_decode(trim(self::getContent('http://ip.taobao.com/service/getIpInfo.php?ip='.$lookIp)),true);
        if(isset($info['code']) && $info['code'] == 1){//数据获取失败
            $info = array();
            $iplookup = getStr('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js');//未知IP省获取位置
            $info['data'] = json_decode(str_replace('var remote_ip_info = ','',rtrim(trim($iplookup),';')),true);//var remote_ip_info = {"ret":1,"start":-1,"end":-1,"country":"\u4e2d\u56fd","province":"\u9ed1\u9f99\u6c5f","city":"\u54c8\u5c14\u6ee8","district":"","isp":"","type":"","desc":""};
            $info['data']['ip'] = $ipArr['origin'];
        }
        $info['stamp'] = date('Y-m-d H:i:s');//timestamp时间戳
        return $info;
    }
    // 将地址信息序列化
    public static function serialize(){
        return serialize(self::getLocation());
    }
    // base64 加密
    public static function base64(){
        return base64_encode(json_encode(self::getLocation()));
    }
    // 获取html 文本内容
    public static function getContent($opt)
    {
        $result = '';
        try{
            $post = isset($opt['data'])? $opt['data']:null;
            $url = is_string($opt)? $opt:$opt['url'];
            if(empty($url)) return '';
            if($post){// POST-data
                if(!is_array($post)) $post = json_decode(trim($post),true);
                if(is_array($post)){
                    $postStr =  http_build_query($post);
                    $opts = ['http' =>
                        [
                            'method'  => 'POST',
                            'header'  => 'Content-type: application/x-www-form-urlencoded',
                            'content' => $postStr
                        ]
                    ];
                    $context  = stream_context_create($opts);
                    $result = file_get_contents($url, false, $context);
                }
            }else{
                $result = file_get_contents($url);
            }
        }catch(Exception $e){}
        return $result;
    }
}