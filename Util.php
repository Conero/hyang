<?php
namespace hyang;
use Exception;
use Closure;
class Util{
    private static $_mkdirs_cur;
    // 多级目录生成器 - 兼容 dir 函数
    public static function mkdirs($path,$isfle=false){
        $path = empty(self::$_mkdirs_cur)? str_replace('\\','/',$path) : $path;
        if($isfle){
            if(is_file($path)) return false;
            $path = pathinfo($path)['dirname']; 
        }
        if(!is_dir($path)){            
            if(empty(self::$_mkdirs_cur)){
                // 尝试直接使用 mkdir 函数
                // try{if(mkdir($path)) return true;}catch(Exception $e){}
                // 兼容 mkdir 函数
                if(is_dir(dirname($path))){
                    return mkdir($path);
                }
                self::$_mkdirs_cur = $path;
            }
            self::mkdirs(dirname($path));
        }
        else{
            if(self::$_mkdirs_cur){
                $firstDir = self::$_mkdirs_cur;
                self::$_mkdirs_cur = null;
                $_basedir = $path;
                $firstDir = str_replace($_basedir,'',$firstDir);
                if(strpos($firstDir,'/') === 0) $firstDir = substr($firstDir,1);
                foreach(explode('/',$firstDir) as $v){
                    if(!is_dir($_basedir.'/'.$v)){
                        mkdir($_basedir.'/'.$v);
                        $_basedir = $_basedir.'/'.$v;
                    }
                }
            }
        }
    }
    // 删除目录下所有文件以及子目录
    public static function cleardir($path,$alls=false)
    {
        // 删除单文件
        if(is_file($path)) return unlink($path);        
        elseif(is_dir($path)){           
            $ctt = 0;
            $dirCtt = 0;
            foreach(scandir($path) as $v){
                if(in_array($v,['.','..'])) continue;
                elseif(is_file($path.'/'.$v)){
                    if(unlink($path.'/'.$v)) $ctt += 1;
                }
                elseif($alls === true && is_dir($path.'/'.$v)){ // 删除制定目录下所有文件以及目录
                    $ret = self::cleardir($path.'/'.$v,true);
                    $ctt += $ret['file'];
                    $dirCtt += $ret['dir'];
                    if(rmdir($path.'/'.$v)) $dirCtt += 1;
                }
            }
            if($alls === true) return ['file'=>$ctt,'dir'=>$dirCtt];
            return $ctt;
        }
        else return 0;
    }

    /**
     * 删除多级目录
     * @param string $path 目录
     * @return boolean
     */
    public static function rmdirs($path){
        // 不是目录，表名无需删除返回成功
        if(!is_dir($path)){
            $isSuccess = true;
        }else{
            foreach (scandir($path) as $v){
                if(in_array($v, ['.', '..'])){
                    continue;
                }
                $nPath = $path.(substr($path, -1) == '/'? '':'/').$v;
                if(is_file($nPath)){
                    unlink($nPath);
                }elseif (is_dir($nPath)){
                    self::rmdirs($nPath);
                }
            }
            $isSuccess = rmdir($path);
        }
        return $isSuccess;
    }
    
    /**
     * 系统调试输出
     * @param mixed  $data      输出变量/回掉函数
     * @param bool   $feek      输出到屏幕/文件
     * @param bool   $original  原始信息设置
     * @return mixed
     */
     public static function debug($data,$feek=true,$original=false)
     {
         // 支持回调函数- 用于代码测试-面向过程化编程
        if(is_callable($data)){
            $dAdrees = null;
            $callback =  $data($dAdrees);
            if(empty($dAdrees)) goto useReturn;
            else goto useAddrss;
            useAddrss:
                $data = $dAdrees;
                $feek = true;
                if($callback) self::debug($data,true,true);
                else self::debug($data,true);
                return;
            useReturn:
                if(empty($callback)) return;
                if(isset($callback['data'])){// 重写参数
                    $data = $callback['data'];
                    if(isset($callback['feek'])) $feek = $callback['feek']? true:false;
                    if(isset($callback['original'])) $original = $callback['original']? true:false;
                }elseif(isset($callback['feek'])){
                    unset($callback['feek']);$data = $callback;
                }else $data = $callback;
        }
        // 打印类型
        if(is_string($feek) && 'type' == strtolower($feek)){
            echo'<pre>';
            var_dump($data);
            echo'<pre>';
            return;
        }
        // 如果存在进打印或显示
        if(is_string($feek) && in_array(strtolower($feek),['if','if+'])){
            if(empty($data)) return;
            if('if' == strtolower($feek)) $feek = true;
            else $feek = false;
        }
        $ret = "\r\n快速调试变量(Common/Function)：".date('Y-m-d H:i:s').":::\r\n";
        if(is_object($data) || is_array($data)){
            $data = is_object($data)? (array)$data:$data;
            $data = print_r($data,true);
            $ret = $original? '':$ret;
            $ret .= $feek? '<pre>'.$data.'</pre>':$data;
        }else $ret = ($original? '':$ret).$data;
        if($feek){
            echo $ret;
            return;
        }
        $dir = defined('Debug_Dir')? APP_ROOT.Debug_Dir:__DIR__;
        if(!is_dir($dir)) mkdir($dir);
        $fname = defined('Debug_Suffix')? Debug_Suffix:'.log';
        $fname = $dir.date('Y-m-d').$fname;
        if(is_file($fname)){
            $fh = fopen($fname,'a+');
            fwrite($fh,$ret);//只能写入string
            fclose($fh);
        }else file_put_contents($fname,$ret);
     }

     /**
     * 不定参数- 打印
     * @param mixed  $data      输出变量/回掉函数
     * @return 输出屏幕
     */
    public static function println(){
        /*
        $numargs = func_num_args();
        $args = func_get_args();
        if($numargs>0) debug($args,true);
        */
        $numargs = func_num_args();
        $args = func_get_args();
        // 回调函数
        if($numargs>1 && is_callable($args[$numargs-1])){
            $args[$numargs-1](array_pop($args));
        }
        elseif($numargs == 1) self::debug($args[0],true);
        elseif($numargs>0) self::debug($args,true);
    }
    /**
     * 2017年2月10日 星期五 回调函数执行器
     * @param mixed  $data      输出
     * @return mixed
     */
     public static function runClosure($data){
         if(is_array($data)){
            foreach($data as $k=>&$v){
                if($v instanceof Closure) $v = $v();              
            }
         }
         elseif($data instanceof Closure) $data = $data();
         return $data;
     }
     /**
     * 2017年2月12日 星期日  数组map
     * @param Array  $data  数组
     * @return string
     */
     public static function arrayMap($array,$key=null){
         $ret = '';
         $data = ($array instanceof Closure)? $array():$array;
         if(is_array($data)){
             if($key && array_key_exists($key,$data)) return $data[$key];                    
         }         
         $ret = $data;
         return $ret;
     }

    /**
     * 重复某个值构造数组
     * @param $col
     * @param $count
     * @return array
     */
    public static function ReplaceArray($col,$count){
        $NewArray = [];
        $i = 0;
        while ($i<$count){
            $NewArray[] = $col;
            $i++;
        }
        return $NewArray;
    }
     /**
     * 2017年2月12日 星期日  字符串删除空格
     * @param string  $str
     * @return string
     */
     public static function unspace($str){
         return preg_replace('/\s/','',$str);
     }     
     /**
     * 2017年2月13日 星期一  数组删除空值
     * @param array  $data
     * @return array
     */
    public static function unEmptyArray($data){
        $ret = [];
        foreach($data as $k=>$v){
            if($v){
                if(is_int($k)) $ret[] = $v;
                else $ret[$k] = $v;
            }
        }
        return $ret;
    }
    /**
     * 2017年2月16日 星期四  获取毫秒数
     * @param float  $time_start 起始时间
     * @return float
     */
    public static function getMs($time_start=null)
    {
        list($usec, $sec) = explode(" ", microtime());
        $time_end = ((float)$usec + (float)$sec);
        return $time_start? ($time_end-$time_start):$time_end;
    }
    /**
     * 2017年2月25日 星期六   数据(数组)清洗
     * @param array  $data 数组
     * @param mixed  $option  选项， 为空时值清洗数据中空值 [in/inornull/preg,[v1,v2,v3]]
     * @param bool  $optInNull  有选项时是否删除空值，即空值与其并列
     * @return array
     */
     public static function dataClear($data,$option=null,$optInNull=false){
         // 清除数组中的非空字符
         if(empty($option)){
             $data = is_array($data)? $data:[];
             $tmpArray = [];
             foreach($data as $k=>$v){
                 if(empty($v)) continue;
                 $tmpArray[$k] = $v;
             }
             $data = $tmpArray;
         }
         // 根据 option 数组条件清洗数据
         elseif($option && is_array($option)){
             foreach($option as $k=>$v){
                 // 键值非空 - 默认为空检测
                 if(is_numeric($k)){
                     if(isset($data[$v]) && empty($data[$v])) unset($data[$v]);
                 }elseif(isset($data[$k])){
                     if(is_array($v)){
                         // [in,array]
                         list($type,$value) = $v;
                         $type = is_string($type)? strtolower($type):$type;
                         if($type == 'in' || $type == 'inornull'){
                             $optInNull = ($type == 'inornull')? true:$optInNull;
                             if(empty($data[$k]) && $optInNull){
                                 unset($data[$k]);
                                 continue;
                             }
                             $value = is_array($value)? $value:[$value];
                             if(in_array($data[$k],$value)) unset($data[$k]);
                         }
                         elseif($type == 'preg' || $type == 'pregornull'){
                             $optInNull = ($type == 'pregornull')? true:$optInNull;
                             if(empty($data[$k]) && $optInNull){
                                 unset($data[$k]);
                                 continue;
                             }
                             if(preg_match($value,$data[$k])) unset($data[$k]);
                         }
                     }
                     elseif($data[$k] == $v) unset($data[$k]);  // 为指定值时清洗掉
                 }
             }
         }
         return $data;
     }
     /**
     * 2017年2月27日 星期一   获取星期
     * @param mixed  $dt  日期参数， 可为时间对象或者时间字符串
     * @return string
     */
     public static function getWeek($dt)
     {
         $dt = is_object($dt)? $dt: (new \DateTime($dt));
         $wk = $dt->format('N');
         $zhTpl = [
             '1' => '星期一',
             '2' => '星期二',
             '3' => '星期三',
             '4' => '星期四',
             '5' => '星期五',
             '6' => '星期六',
             '7' => '星期天',
         ];
         return $zhTpl[$wk];
     }

     /**
     * 2017年3月1日 星期三   根据键值提取 对应的值，可采用默认。 数组值可以保持住
     * @param array  $data  数组
     * @param string  $key  数组键
     * @param string  $default  固定值
     * @return string
     */
     private static $_arrayValCache;
     // 数组注册
     public static function arrayRegister($array){
         self::$_arrayValCache = $array;
     }
     public static function arrayVal($key,$default="",$data=null)
     {
         if($data) self::$_arrayValCache = $data;
         $data = $data? $data : self::$_arrayValCache;
         if(array_key_exists($key,$data)) return $data[$key];
         return $default;
     }
     /**
     * 2017年3月10日 星期五   单引号中单引号自动恢复 string _ transferred => '*yhhs*' => "'yhhs'"
     * @param string  $value  数组键
     * @param string  $limiter  固定值
     * @return string
     */
     public static function strtrans($value,$limiter="*")
     {
         $value = str_replace($limiter,"'",$value);
         return $value;
     }

     /**
     * 2017年3月12日 星期日   获取本地IP(win)
     * @return string
     */
    public static function getLocalIp()
    {
        $ip4 = '127.0.0.1';
        $ipconfig = `ipconfig`;
        foreach(explode("\n",$ipconfig) as $v){
            if(empty($v)) continue;
            $ipv4 = '/ipv4/i';
            if(preg_match($ipv4,$v)){
                $pattern = '/[\d]{1,3}.[\d]{1,3}.[\d]{1,3}.[\d]{1,3}/';
                preg_match($pattern,$v,$match);
                if(isset($match[0]) && !empty($match[0])) $ip4 = $match[0];
            }       
        }
        return $ip4;
    }
    /**
     * 2017年3月12日 星期日   获取本地ipv4/ipv6(win)
     * @return array  => [ipv4=>[],ipv6=>[]]
     */
    public static function ipConfig()
    {
        $data = [];$ip4 = [];$ip6 = [];
        $ipconfig = `ipconfig`;
        foreach(explode("\n",$ipconfig) as $v){
            if(empty($v)) continue;
            $ipv4 = '/ipv4/i';
            $ipv6 = '/ipv6/i';
            if(preg_match($ipv4,$v)){
                $pattern = '/[\d]{1,3}.[\d]{1,3}.[\d]{1,3}.[\d]{1,3}/';
                preg_match($pattern,$v,$match);
                if(isset($match[0]) && !empty($match[0])) $ip4[] = $match[0];
            }
            elseif(preg_match($ipv6,$v)){
                //     FF01:0:0:0:0:0:0:1101 → FF01::1101
                // 　　0:0:0:0:0:0:0:1 → ::1
                // 　　0:0:0:0:0:0:0:0 → ::
                $pattern = '/[a-z\d%]{0,8}:{1,2}[a-z\d%]{1,8}:{1,2}[a-z\d%]{0,}:{0,}[a-z\d%]{0,}:{0,}[a-z\d%]{0,}:{0,}[a-z\d%]{0,}:{0,}[a-z\d%]{0,}:{0,}[a-z\d%]{0,}/i';
                preg_match($pattern,$v,$match);
                if(isset($match[0]) && !empty($match[0])) $ip6[] = $match[0];
            }
        }
        if(count($ip4) > 0) $data['ipv4'] = $ip4;
        if(count($ip6) > 0) $data['ipv6'] = $ip6;
        return $data;
    }
    /**
      * 2017年4月1日 星期六 更具键值过滤数据
      * @param $data json
      * @param $filter []string/function 过滤参数 - 存在则过滤不存在无影响
      * @return list(json,json) => (过滤后的值，过滤掉的值)
      */
     public static function dataFilter($data,$filter){
         $filterData = [];$lestData = [];
         if($filter instanceof Closure) $filter = call_user_func($filter);
         if(!is_array($filter)) $filter = [];
         foreach ($data as $k=>$v){
            if(in_array($k,$filter)) $filterData[$k] = $v;
             else $lestData[$k] = $v;
         }
         return [$filterData,$lestData];
     }
     /**
      * 2017年4月1日 星期六 更具键值过滤数据
      * @param $data json
      * @param $filter []string/function 过滤参数 - 存在则过滤不存在无影响
      * @return list(json,json) => (过滤后的值，过滤掉的值)
      */
     public static function dataUnset($data,$filter){
         if($filter instanceof Closure) $filter = call_user_func($filter);
         if(!is_array($filter)) $filter = [];
         foreach ($data as $k=>$v){
            if(in_array($k,$filter)) unset($data[$k]);
         }
         return $data;
     }
    /**
     * 获取并删除键值数据
     * @param $key
     * @param $map
     * @return null
     */
    public static function getKeyAndDel($key, &$map){
        $value = null;
        if(isset($map[$key])){
            $value = $map[$key];
            unset($map[$key]);
        }
        return $value;
    }
    /**
     * 删除数据键值列表
     * @param array $arr
     * @param string|array $keys 字符串(带,符号),数组
     * @return array
     */
    public static function clearArrKey($arr, $keys){
        if(is_string($keys) && false !== strpos($keys, ',')){
            $keys = explode(',', preg_replace('/\s/', '', $keys));
        }
        $keys = is_array($keys)? $keys:[$keys];
        foreach ($keys as $k){
            if(isset($arr[$k])){
                unset($arr[$k]);
            }
        }
        return $arr;
    }
    /**
     * 获取GET/POST 请求的值
     * @param null $key 键值
     * @param string $ref   默认值
     * @return array|mixed|string
     */
    public static function RequestData($key=null,$ref=''){
        $data = array_merge($_GET,$_POST);
        $data = is_array($data)? $data:[];
        if($key){
            return array_key_exists($key,$data)? $data[$key]:$ref;
        }
        return $data;
    }
    /**
     * 获取速记字符串
     * @param int $bit
     * @return string
     */
    public static function randStr($bit=6){
        $rand = '';
        $i = 1;
        $ascii = array_merge(range(48,57),range(65,90),range(97,122));
        while ($i<=$bit){
            $rand .= chr($ascii[array_rand($ascii)]);
            $i++;
        }
        return $rand;
    }

    /**
     * 页码计算
     * @param $count 中数据条数
     * @param int $num 单页数据
     * @return float|int 总页码
     */
    public static function getAllPage($count,$num=20){
        $num = is_int($num)? $num:20;
        $count = is_int($count)? $count:0;
        if(0 === $count) return $count;
        else if ($count < $num) return 1;
        return ceil($count/$num);
    }

    /**
     * json 字符串 base64 解码编码
     * @param $data
     * @return mixed|string
     */
    public static function BaseJson($data){
        if(is_array($data)) return base64_encode(json_encode($data));
        else if(is_string($data)){
            // 自动识别是否为 标准的json字符
            if(preg_match('/[\{"\}\:]{3,}/',$data)) return json_decode($data,true);
            return json_decode(base64_decode($data),true);
        }
        return '';
    }
    /**
     * @return string
     */
    public static function getBaseUrl($url=''){
        $port = $_SERVER['SERVER_PORT'];
        $scheme = $_SERVER['REQUEST_SCHEME'];
        if(($port == 80 && $scheme == 'http') || ($port == 443 && $scheme == 'https')){
            $port = '';
        }
        else{
            $port = ':'. $port;
        }
        return $scheme.'://'.$_SERVER['SERVER_NAME'].$port.$url;
    }
}