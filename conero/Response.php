<?php


namespace hyang;



/**
 * 相应
 * Class Response
 * @package hyang
 */
class Response
{
    /**
     * 读取图像并显示到浏览器中
     * @param $filename string
     * @throws \Exception
     */
    public static function imageFile($filename){
        $size = getimagesize($filename);
        $fp=fopen($filename, 'rb');
        if ($size && $fp) {
            header("Content-type: {$size['mime']}");
            fpassthru($fp);
            exit;
        } else {
            throw new \Exception("output iamge failure: $filename.");
        }
    }
}