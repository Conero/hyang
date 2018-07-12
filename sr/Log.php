<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/6/28 0028 11:11
 * Email: brximl@163.com
 * Name:
 */

namespace sr;


class Log
{
    static function write($content){
        if(!is_dir(RUN_DIR)){
            mkdir(RUN_DIR);
        }
        if(!is_dir(Cli_LogD)){
            mkdir(Cli_LogD);
        }
        $name = Cli_LogD . date('Ymd') . '.log';

        if(is_array($content) || is_object($content)){
            $content = print_r($content, true);
        }else{
            $content = $content."\r\n";
        }
        self::keepFile($name, $content);
    }

    /**
     * @param $fname
     * @param $content
     */
    static function keepFile($fname, $content){
        $fh = @fopen($fname, 'a');
        fwrite($fh, $content);
        fclose($fh);
    }
}