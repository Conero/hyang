<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/1/4 0004 23:23
 * Email: brximl@163.com
 * Name: 数据集处理
 */

namespace hyang;


class Dataset
{
    protected $dataset = array();
    public function __construct($data=null)
    {
        if(is_array($data)){
            $this->dataset = $data;
        }
    }

    /**
     * 设置键值
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function assign($key, $value){
        $this->dataset[$key] = $value;
        return $this;
    }

    /**
     * 获取数据
     * @param $key
     * @param null $def
     * @return mixed|null
     */
    public function get($key, $def=null){
        $value = isset($this->dataset[$key])? $this->dataset[$key]: $def;
        return $value;
    }
    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->assign($name, $value);
    }

    /**
     * 获取数据
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * 获取数据集
     * @param bool $reset 是否重置
     * @return array|null
     */
    public function getSets($reset=false){
        $data = $this->dataset;
        if($reset){
            $this->dataset = [];
        }
        return $data;
    }
}