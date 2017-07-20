<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/7/20 0020 22:44
 * Email: brximl@163.com
 * Name: redis 封装,用于框架再次利用, 需呀安装 php_redis
 */

namespace hyang;
use Redis as PHPRedis;

class HRedis
{
    public static $kv = null;
    public static $ref = null; // 值前缀
    /**
     * 连接函数
     * @param array $config
     */
    public static function connect($config=[]){
        if(empty(self::$kv)){
            if(empty($config['host'])) $config['host'] = '127.0.0.1';
            if(empty($config['part'])) $config['part'] = 6379;
            self::$kv = new PHPRedis();
            self::$kv->connect($config['host'],$config['part']);
            $rds = new PHPRedis();
            $rds->connect($config['host'],$config['part']);
        }
        if(empty(self::$ref)) self::$ref = isset($data['ref'])? $data['ref']:'hyang';
    }

    /**
     * 序列化存储
     * @param $key
     * @param null $value
     * @return mixed|null
     */
    public static function serialize($key,$value=null){
        $vkey = self::$ref.':'.$key;
        if(null === $value){
            return self::$kv->exists($vkey)? unserialize(self::$kv->get($vkey)) : null;
        }else if($value){
            return self::$kv->set($vkey,serialize($value));
        }
    }
}