<?php
/* 2017年1月17日 星期二
 * 数据验证
 */
// preg_match()
namespace hyang;
class Validate{
    private static $rules = [
        //'IPV4'      => '/[0-9.]{8,12}/',                                          // ipv4
        'IPV4'      => '/((2[0-4]\d|25[0-5]|[01]?\d\d?)\.){3}(2[0-4]\d|25[0-5]|[01]?\d\d?)/',
                                                                                    // ipv4
        'DATE'      => '/([\d]{4})+(-|\/)+([\d]{2})+(-|\/)+([\d]{2})/',             // date
        'EMAIL'     => '/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/',         // email
    ];
    // 是否时ip
    public static function ipv4($ipv4)
    {
        $preg = self::$rules['IPV4'];
        if(preg_match($preg,$ipv4) > 0) return true;
        return false;
    }
    // 是否为合法日期 2017-07-06
    public static function isDate($value){
        $preg = self::$rules['DATE'];
        if(preg_match($preg,$value)) return true;
        return false;
    }
    // 是否为合法的邮箱 2017年6月18日 星期日
    public static function isEmail($value){
        $preg = self::$rules['EMAIL'];
        if(preg_match($preg,$value)) return true;
        return false;
    }
}