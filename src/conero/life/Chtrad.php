<?php
/* 2017年2月12日 星期日
 * Chinese tradition(Ch-trad) 生肖、星座等处理  中国传统
*/
namespace hyang\conero\life;
use hyang\conero\Config;
use hyang\conero\Util;
class Chtrad{
    private static $resourceJson;       // 资源数组
    private static $ganzhiJson;         // 干支数据组
    /* 获取资源器
     * @return Array
     */
    public static function getResJson($key=null){
        $res = self::$resourceJson;
        if(empty($res)){
            $res = Config::getResource('life/Chtrad.json');
            // println($res);
            self::$resourceJson = $res;
        }
        if($key) $res = Util::arrayMap($res,$key);
        return $res;
    }    
    /* 干支生成器[0-59]60进制
     * @return Array
     */
    public static function ganzhi()
    {
        if(empty(self::$ganzhiJson)) {
            $tiangan = explode(',',self::getResJson('tiangan'));                    // [0-9]
            $dizhi = explode(',',Util::unspace(self::getResJson('dizhi')));         // [0-11]
            foreach($dizhi as $x=>$v){
                foreach($tiangan as $y=>$w){
                    if($x<2){
                        $xeven = $x == 0? true:false;
                    }else $xeven = $x%2 == 0? true:false;
                    if($y<2){
                        $yeven = $y == 0? true:false;
                    }else $yeven = $y%2 == 0? true:false;
                    if($xeven !== $yeven) continue;
                    self::$ganzhiJson[] = $w.$v;
                }
            }
        }
        return self::$ganzhiJson;
    }
    /**
    * 2017年2月12日 星期日 年份映射干支表
    * @param $year 年份默认为今年
    * @param $gyqian 公元前计算法
    * @return string
    */
    public static function yearMapGanzhi($year=null,$gyqian=false)
    {
        $ganzhi = "";
        $year = $year? $year:date('Y');
        // 公元前
        if($gyqian){
            $tianzhiMap = ["辛","庚","己","戊","丁","丙","乙","甲","壬","癸"];          //[0-9]
            $dizhiMap = ["申","未","午","巳","辰","卯","寅","丑","子","亥","戌","酉"];  //[1-12]
            
        }
        // 公元后
        else{
            $tianzhiMap = ["庚","辛","壬","癸","甲","乙","丙","丁","戊","己"];          //[0-9]
            $dizhiMap = ["酉","戌","亥","子","丑","寅","卯","辰","巳","午","未","申"];  //[1-12]
        }
        $lastNum = substr($year,-1);
        $ganzhi = isset($tianzhiMap[$lastNum])? $tianzhiMap[$lastNum]:"";
        $yushu = intval($year)%12;
        // println($yushu,$yushu == 0? 12:($yushu-1));
        $yushu = $yushu == 0? 11:($yushu-1);
        // println($yushu,$dizhiMap[$yushu]);
        $ganzhi .= isset($dizhiMap[$yushu])? $dizhiMap[$yushu]:"";
        return $ganzhi;
    }
    /**
    * 2017年2月12日 星期日 年份映射属相
    * @param $year 年份默认为今年
    * @return string
    */
    public static function yearMapShu($year=null){
        $year = $year? $year:date('Y');        
        $zodiac = explode(',',self::getResJson('zodiac'));
        $shichen = explode(',',self::getResJson('shichen'));
        if(is_numeric($year)){
            $year = intval($year);
            $yushu = intval($year)%12;
            $yushu = $yushu == 0? 11:($yushu-1);
            $dizhiMap = ["酉","戌","亥","子","丑","寅","卯","辰","巳","午","未","申"];  //[1-12]
            $ganzhi = isset($dizhiMap[$yushu])? $dizhiMap[$yushu]:"";
        }
        else $ganzhi = $year;
        $key = array_search($ganzhi,$shichen);
        return [
            $year => isset($zodiac[$key])? $zodiac[$key]:''
        ];
        
    }
    /**
    * 2017年2月12日 星期日 月日映射星座
    * @param $month  月日【默认当前】
    * @return array
    */
    public static function MonthMapStar($month=null)
    {
        $month = $month? $month:date('n.d');
        $star = array_flip(self::getResJson('star'));
        $ret = $star;
        $splitFn = function($str){
            return [
                intval(substr($str,0,strpos($str,'.'))),
                intval(substr($str,strpos($str,'.')+1))
            ];
        };
        foreach($star as $k=>$v){
            $tmpArray = explode('-',$k);
            list($minMonth,$minDay) = $splitFn($tmpArray[0]);
            list($maxMonth,$maxDay) = $splitFn($tmpArray[1]);
            list($m,$d) = $splitFn($month);
            if(
                ($m == $minMonth && $d >= $minDay) ||
                ($m == $maxMonth && $d <= $maxDay)
            ){
                $ret = [
                    $month => $v
                ];   
                break;
            }         
        }
        return $ret;
    }
    /**
    * 2017年2月12日 星期日 时间映射时辰
    * @param $time  月日【默认当前】
    * @return array
    */
    public static function timeMapShichen($time=null){
        $time = $time? $time:date('G:i');
        $shichen = array_flip(self::getResJson('shichentb'));
        $ret = $shichen;
        $splitFn = function($str){
           return [
                intval(substr($str,0,strpos($str,':'))),
                intval(substr($str,strpos($str,':')+1))
            ]; 
        };
        foreach($shichen as $k=>$v){
            $tmpArray = explode('-',$k);
            list($minHour,$minMin) = $splitFn($tmpArray[0]);
            list($maxHour,$maxMin) = $splitFn($tmpArray[1]);
            list($h,$m) = $splitFn($time);
            if(
                ($h == $minHour && $m >= $minMin) ||
                ($h == $maxHour && $m <= $maxMin)
            ){
                $ret = [
                    $time => $v
                ];   
                break;
            }         
        }
        return $ret;
    }
}