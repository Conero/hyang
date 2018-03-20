<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/3/20 0020 10:13
 * Email: brximl@163.com
 * Name: scv 数据格式导入模式
 */

namespace hyang\data\import;


class Csv
{
    /**
     * 通过文件打开csv资源
     * @param string $filename
     * @return CsvFile
     */
    public static function open($filename){
        return new CsvFile($filename);
    }
}