<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/6/28 0028 14:39
 * Email: brximl@163.com
 * Name: phar 工具栏管理包助手
 */

namespace sr;


class uPhar
{
    /**
     * 获取程序名字通过路径
     * @param string $path
     * @return bool
     */
    static function getNameByPath($path){
        $data = pathinfo($path);
        return $data['filename'] ?? false;
    }
}