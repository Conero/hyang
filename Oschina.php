<?php
/**
 * Auther: Joshua Conero
 * Date: 2017/5/23 0023 14:33
 * Email: brximl@163.com
 * Name: 开源中国码源api服务php封装
 */

namespace hyang;


class Oschina
{
    protected $client_id;       // 应用id
    protected $access_token;    // 授权ID
    protected $owner;           // 用户
    protected $num;             // 分页显示数
    public function __construct($config=null)
    {
        if(isset($config['client_id'])) $this->client_id = $config['client_id'];
        if(isset($config['access_token'])) $this->access_token = $config['access_token'];
        if(isset($config['owner'])) $this->owner = $config['owner'];
        $this->num = isset($data['num'])? $data['num']:20;
    }
    public function getAccessToken($redirect_uri=null){
        $url = 'http://git.oschina.net/oauth/authorize?client_id='.
            $this->client_id.'&response_type=code';
        if($redirect_uri) $url .= '&'.$this->UrlParse(['redirect_uri'=>$redirect_uri]);
        return $this->get($url);
    }

    /**
     * 获取用户信息
     * @return mixed|string
     */
    public function getUser(){
        $url = 'https://git.oschina.net/api/v5/user?access_token='.
            $this->access_token;
        Net::setUrl($url);
        $jsonStr = Net::get();
        return $jsonStr? json_decode($jsonStr,true):$jsonStr;
    }

    /**
     * 获取项目的贡献者
     * @param $prjname
     * @return mixed|string
     */
    public function getDeveloper($prjname){
        $url = 'https://git.oschina.net/api/v5/repos/'.$this->owner.'/'.$prjname.'/contributors?access_token='.
            $this->access_token;
        return $this->get($url);
    }

    /**
     * 获取用户的所有项目
     * @return mixed|string
     */
    public function getProject($user=null){
        $user = $user? $user:$this->owner;
        $url = 'http://git.oschina.net/api/v5/users/'.$user.'/repos';
        return $this->get($url);
    }

    /**
     * 获取项目的所有关注者
     * @param $prj
     * @param int $page
     * @param int $num
     * @return mixed|string
     */
    public function getPrjStar($prj,$page=1){
        $url = 'http://git.oschina.net/api/v5/repos/'
        .$this->owner.'/'.$prj.'/stargazers?access_token='
        .$this->access_token.'&page='.$page.'&per_page='.$this->num;
        return $this->get($url);
    }
    /**
     * 获取关注者信息
     * @param int $pages
     * @param int $num
     * @return mixed|string
     */
    public function getFollows($pages=1){
        $url = 'http://git.oschina.net/api/v5/users/'
            .$this->owner.'/following?access_token='
            .$this->access_token.'&page='.$pages.'&per_page='.$this->num;
        return $this->get($url);
    }
    public function myStared(){
        $url = 'http://git.oschina.net/api/v5/user/starred?access_token='.
            $this->access_token.'&sort=created&direction=desc&page=1&per_page='.$this->num;

        return $this->get($url);
    }
    /**
     * url 解析-> array->string/string->array
     * @param $param
     * @return array|null|string
     */
    public function UrlParse($param){
        if(is_array($param)){ // array -> string
            return http_build_query($param);
        }else{
            $data = parse_url($param);
            if(!empty($data['query'])){
                $tmpArray = explode('&',$data['query']);
                $retVal = [];
                foreach ($tmpArray as $v){
                    $idx = strpos($v,'=');
                    $key = substr($v,0,$idx);
                    $retVal[$key] = substr($v,$idx+1);
                }
                return $retVal;
            }
        }
        return null;
    }

    /**
     * 内部数据请求方式
     * @param $url
     * @return mixed|string
     */
    private function get($url){
        Net::setUrl($url);
        $jsonStr = Net::get();
        //$jsonStr = Net::curl();
        // 经过测试， Net 的get比curl方法后去的方法更好
        return $jsonStr? json_decode($jsonStr,true):$jsonStr;
    }
}