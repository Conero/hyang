<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/3/20 0020 10:15
 * Email: brximl@163.com
 * Name:
 */

namespace hyang\data\import;


class CsvFile
{
    protected $filename;

    /**
     * CsvFile constructor.
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * 回调函数读取
     * @param callable $callback 回调函数 {row 数据}
     */
    public function reader($callback){
        if (($handle = fopen($this->filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
            //while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    //$num = count($data);
                    //print_r($data);
                    call_user_func($callback, $data);
            }
            fclose($handle);
        }
    }
}