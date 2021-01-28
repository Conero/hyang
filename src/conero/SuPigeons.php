<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/9/16 0016 15:26
 * Email: brximl@163.com
 * Name: Pigeons php-客服端
 */

namespace hyang\conero;


class SuPigeons
{
    private $urlPref = '';
    private $openid = '';
    private $access_token = '';
    private $code = '';
    private $user = '';
    public $token = '';
    public $conero_pid = '';

    private static $instance;
    private $e;
    public $errorMsg;
    public $errorTrace;
    private $result;
    private function __construct(){}
    public $debug = false;
    /**
     * 获取 token值
     */
    public function getToken(){
        /*
        $res = null;
        try {
            if (empty($this->openid)) $this->getOpenid();
            $url = sprintf($this->urlPref . 'token/token?openid=%s', $this->openid);
            $res = Net::prepare($url)
                ->getJsonByExec()
                ;
            $this->result = $res;
            if(isset($res['token'])){
                $res = $res['token'];
                $this->token = $res;
            }else{
                $res = null;
            }
        }catch (\Exception $e){
            $this->e = $e;
            $this->errorMsg = $e->getMessage();
            $this->errorTrace = $e->getTraceAsString();
            //$this->e = $e->getMessage().
            //    ($this->debug)? $e->getTraceAsString(): '';
        }
        return $res;
        */
        $token = $this->token;
        if(!$token){
            $header = [
                'Conero-Code' => $this->code,
                'Conero-Pid'  => $this->conero_pid
            ];
            // 获取用户信息
            $options = [
                'data' => [
                    'openid' => $this->getOpenid()
                ],
                'header' => $header
            ];
            $res = $this->curl('token/token', $options);
            debugOut([
                $res,
                $options
            ]);
            if($res && $res['code'] == '200'){
                $token = $res['token'];
                $this->token = $token;
            }
        }
        return $token;
    }
    public function getOpenid($user=''){
        /*
        $res = null;
        try {
            $url = $this->urlPref . 'token/openid?' . http_build_query([
                    'access_token' => $this->access_token,
                    'code' => $this->code,
                    'user' => $user ? $user : $this->user
                ]);
            $res = Net::prepare($url)
                ->getJsonByExec();
            $this->result = $res;
            if (isset($res['code']) && '200' == $res['code']){
                $this->openid = $res['openID'];
                $res = $this->openid;
            }
        }catch (\Exception $e){
            $this->e = $e;
            $this->errorMsg = $e->getMessage();
            $this->errorTrace = $e->getTraceAsString();
            //$this->e = $e->getMessage().
            //($this->debug)? $e->getTraceAsString(): '';
        }
        return $res;
        */

        $openid = null;
        $header = [
            'Conero-Code' => $this->code,
            'Conero-Pid'  => $this->conero_pid
        ];
        // 获取用户信息
        $options = [
            'data' => [
                'user' => $user ? $user : $this->user,
                'access_token' => $this->access_token,
                'code'  => $this->code
            ],
            'header' => $header
        ];
        $res = $this->curl('token/openid', $options);
        if($res && $res['code'] == 200){
            $openid = $res['openID'];
        }
        return $openid;
    }
    /**
     * @return SuPigeons
     */
    public static function getInstance($option=array()){
        if(!self::$instance){
            self::$instance = new self();
        }
        if(isset($option['pref'])) self::$instance->urlPref = $option['pref'];
        if(isset($option['access_token'])) self::$instance->access_token = $option['access_token'];
        if(isset($option['code'])) self::$instance->code = $option['code'];
        if(isset($option['user'])) self::$instance->user = $option['user'];
        if(isset($option['conero_pid'])) self::$instance->conero_pid = $option['conero_pid'];
        return self::$instance;
    }

    /**
     * 获取项目私有对象
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if(property_exists($this, $name)){
            return $this->$name;
        }
        return false;
    }

    /**
     * @param $user string
     * @return $this
     */
    public function setUser($user){$this->user = $user;return $this;}

    /**
     * @return mixed
     */
    public function getError(){
        if($this->e instanceof \Exception){
            return $this->e->getMessage();
        }
        //debug($this->e);
        return $this->e;
    }

    /**
     * @return mixed
     */
    public function getRawResult(){
        return $this->result;
    }

    /**
     * @param $url string
     * @param $data null|array
     * @return array|mixed|null|string
     */
    public function get($url, $data=null){
        $url = $this->urlPref .$url;
        $res = null;
        try{
            $net = Net::prepare($url);
            if(!$this->token){
                $this->token = $this->getToken();
            }
            if($this->token){
                $net->setOption('header', function ($opt){
                    $header = isset($opt['header'])? $opt['header']: [];
                    $header['Conero-Token'] = $this->token;
                    if($this->conero_pid) $header['Conero-Pid'] = $this->conero_pid;
                    return $header;
                });
            }
            if($data){
                $net->setOption('data', $data);
            }
            //$res = $net->exec();
            //$res = $res ? json_decode($res, true) : [];
            $res = $net->getJsonByExec();
        }catch (\Exception $e){
            $this->e = $e;
            $this->errorMsg = $e->getMessage();
            $this->errorTrace = $e->getTraceAsString();
            //debug([$e->getMessage(), 'OP']);
            //$this->e = $e->getMessage()."\r\n".
            //($this->debug)? $e->getTraceAsString(): '';
        }
        return $res;
    }

    /**
     * @param $url
     * @param array $data
     * @return array|mixed|null
     */
    public function post($url, $data=array()){
        $url = $this->urlPref .$url;
        $res = null;
        try{
            $net = Net::prepare($url);
            if(!$this->token){
                $this->token = $this->getToken();
            }
            $net->setOption('method', 'POST');
            if($data){
                $net->setOption('data', $data);
            }
            if($this->token){
                $net->setOption('header', function ($opt){
                    $header = isset($opt['header'])? $opt['header']: [];
                    $header['Conero-Token'] = $this->token;
                    if($this->conero_pid) $header['Conero-Pid'] = $this->conero_pid;
                    return $header;
                });
            }
            //$res = $net->exec();
            //$res = $res ? json_decode($res, true) : [];
            //debug([$url, $data, $this->token, $net->getOption('header')]);
            $res = $net->getJsonByExec();
        }catch (\Exception $e){
            $this->e = $e;
            $this->errorMsg = $e->getMessage();
            $this->errorTrace = $e->getTraceAsString();
            //debug($e->getMessage());
            //$this->e = $e->getMessage().
            //($this->debug)? $e->getTraceAsString(): '';
        }
        return $res;
    }

    /**
     * 原始 网络请求
     * @param $url
     * @param array $option
     * @return array|mixed
     */
    public function rawRequest($url, $option=array()){
        $url = $this->urlPref .$url;
        //println($url);
        $method = isset($option['method'])? strtolower($option['method']):'get';    // 请求方法
        $data = isset($option['data'])? $option['data']:false;          // 发送的数据
        $net = Net::prepare($url);
        if($method == 'post'){
            $net->setOption('method', 'POST');
            //println($option);
        }else{
            $net->setOption('method', 'GET');
        }
        // 请求数据
        if($data){
            $net->setOption('data', $data);
        }
        return $net->getJsonByExec();
    }

    /**
     * curl 封装的数据请求
     * @param string $url
     * @param array $options
     * @return bool|mixed
     */
    public function curl($url, $options=array()){
        $url = $this->urlPref .$url;
        $options['url'] = $url;
        $str = Net::curls($options);
        $retVal = false;
        if($str){
            $retVal = json_decode($str, true);
        }
        return $retVal;
    }
}