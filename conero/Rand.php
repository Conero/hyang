<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/20 0020 13:51
 * Email: brximl@163.com
 * Name: 轻量级随机数
 */

namespace hyang;


class Rand
{
    const AsciiLower1 = 97;
    const AsciiLower2 = 122;
    const AsciiUpper1 = 65;
    const AsciiUpper2 = 90;
    const AsciiNumber1 = 48;
    const AsciiNumber2 = 57;
    const UnicodeZhHex1 = '4E00';
    const UnicodeZhHex2 = '9FA5';

    static protected $UnicodeZhDec1 = -1;
    static protected $UnicodeZhDec2 = -1;

    /**
     * @return float|int
     */
    static function zhDec1(){
        if(self::$UnicodeZhDec1 === -1){
            self::$UnicodeZhDec1 = hexdec(self::UnicodeZhHex1);
        }
        return self::$UnicodeZhDec1;
    }

    /**
     * @return float|int
     */
    static function zhDec2(){
        if(self::$UnicodeZhDec2 === -1){
            self::$UnicodeZhDec2 = hexdec(self::UnicodeZhHex2);
        }
        return self::$UnicodeZhDec2;
    }

    /**
     * 获取数据ID
     * @param null|int $byte
     * @return string
     */
    static function getId($byte=null){
        $id = '';
        if(empty($byte)){
            $id = uniqid();
        }elseif (is_numeric($byte)){
            $id = openssl_random_pseudo_bytes($byte);
        }
        return $id;
    }

    /**
     * 小写字母串
     * @param int $byte
     * @return string
     */
    static function getLower($byte=1){
        $str = '';
        $i = 1;
        while ($i<=$byte){
            $str .= self::lower();
            $i++;
        }
        return $str;
    }

    /**
     * 获取大写字母串： ASCII: 65-90
     * @param int $byte
     * @return string
     */
    static function getUpper($byte=1){
        $str = '';
        $i = 1;
        while ($i<=$byte){
            $str .= self::upper();
            $i++;
        }
        return $str;
    }

    /**
     * 获取字符串
     * @param int $byte
     * @return string
     */
    static function getLetter($byte=1){
        $str = '';
        $i = 1;
        while ($i<=$byte){
            $str .= self::letter();
            $i++;
        }
        return $str;
    }

    /**
     * 获取字母
     * @param int $byte
     * @return string
     */
    static function getCharacter($byte=1){
        $str = '';
        $i = 1;
        while ($i<=$byte){
            $str .= self::character();
            $i++;
        }
        return $str;
    }

    /**
     * 获取数字： ASCII:48-57
     * @param int $byte
     * @return string
     */
    static function getNumber($byte=1){
        $str = '';
        $i = 1;
        while ($i<=$byte){
            $str .= self::number();
            $i++;
        }
        return $str;
    }

    /**
     * 获取中文(字符串): Unicode \u4E00-\u9FA5(47,000-117,645)
     * @param int $byte
     * @return string
     */
    static function getZh($byte=1){
        $str = '';
        $i = 1;
        while ($i<=$byte){
            $str .= self::zh();
            $i++;
        }
        return $str;
    }

    /**
     * 格式化： {number}{form}，a字母，c字符，l小写, n数字,u大写，z中文
     * @param $format
     * @return mixed
     */
    static function getFormat($format){
        $reg = '/[0-9]+[aclnuz]{1}/i';
        preg_match_all($reg, $format, $matched);
        $matched = $matched[0] ?? [];
        $rplQueue = [];$i = 1;
        $is = $format == '2u10c';
        foreach ($matched as $fmt){
            $n = intval(substr($fmt, 0, -1));
            $n = $n ?? 1;
            $f = substr($fmt, -1);
            $k = $i.'_'.sha1($fmt).'_'.$i;
            $rplV = '';
            switch (strtolower($f)){
                case 'a': $rplV = self::getLetter($n); break;
                case 'c': $rplV = self::getCharacter($n); break;
                case 'l': $rplV = self::getLower($n); break;
                case 'n': $rplV = self::getNumber($n); break;
                case 'u': $rplV = self::getUpper($n); break;
                case 'z': $rplV = self::getZh($n); break;
            }
            // 替换列表
            $rplQueue[$k] = $rplV;
            $format = str_replace($fmt, $k, $format);
            $i++;

        }
        foreach ($rplQueue as $k=>$v){
            $format = str_replace($k, $v, $format);
        }
        return $format;
    }
    /**
     * 获取小写字母： ASCII: 97-122
     * @return string
     */
    static function lower(){
        $ascii = mt_rand(self::AsciiLower1, self::AsciiLower2);
        $alpha = chr($ascii);
        return $alpha;
    }

    /**
     * 获取大写字母： ASCII: 65-90
     * @return string
     */
    static function upper(){
        $ascii = mt_rand(self::AsciiUpper1, self::AsciiUpper2);
        $alpha = chr($ascii);
        return $alpha;
    }

    /**
     * 获取字符串
     * @return string
     */
    static function letter(){
        static $dick = [];
        if(empty($dick)){
            $dick = array_merge($dick, range(self::AsciiUpper1, self::AsciiUpper2));
            $dick = array_merge($dick, range(self::AsciiLower1, self::AsciiLower2));
        }
        $num = mt_rand(0, count($dick));
        return chr($dick[$num]);
    }

    /**
     * 获取字母
     * @return string
     */
    static function character(){
        static $dick = [];
        if(empty($dick)){
            $dick = range(self::AsciiNumber1, self::AsciiNumber2);
            $dick = array_merge($dick, range(self::AsciiUpper1, self::AsciiUpper2));
            $dick = array_merge($dick, range(self::AsciiLower1, self::AsciiLower2));
        }
        $num = mt_rand(0, count($dick));
        return chr($dick[$num]);
    }
    /**
     * 获取数字： ASCII:48-57
     * @return string
     */
    static function number(){
        $ascii = mt_rand(self::AsciiNumber1, self::AsciiNumber2);
        $alpha = chr($ascii);
        return $alpha;
    }

    /**
     * 获取中文(字符串): Unicode \u4E00-\u9FA5(47,000-117,645)
     */
    static function zh(){
        $num = mt_rand(self::zhDec1(), self::zhDec2());
        $str = '\u'.dechex($num);
        $jsonStr = '{"zh":"'.$str.'"}';
        $data = json_decode($jsonStr, true);
        return ($data['zh'] ?? null);
    }

}