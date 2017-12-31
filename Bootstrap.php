<?php
/*
 * bootsrtap 框架生成器
*/
namespace hyang;
use think\Db;
class Bootstrap{
    public $app;// 来自页面的对象-< $this->view
    public function linkApp($obj){
        $this->app = $obj;
        return $this;
    }
    /**
     * 2017年5月16日 星期二
     * @param $clos []string 列名
     * @param $data []json|callback 数据
     * @return string
     */
    public function tbodyGrid($clos,$data){
        $xhtml = '';
        if(is_string($clos)) $clos = explode(',',$clos);
        if($data instanceof \Closure) $data = call_user_func($data);
        $data = is_array($data)? $data:[];
        $i = 1;
        foreach ($data as $v){
            $tmpxhtml = '<tr><td>'.$i.'</td>';
            foreach ($clos as $k){
                $content = '';
                if(is_string($k) && isset($v[$k])) $content = $v[$k];
                else if($k instanceof \Closure) $content = call_user_func($k,$v);
                $tmpxhtml .= '<td>'.$content.'</td>';
            }
            $xhtml .= $tmpxhtml;
            $i += 1;
        }
        return $xhtml;
    }

    /**
     * 2017年5月16日 星期二/ 搜索框
     * @param $cols []string
     * @return string
     */
    public function searchBar($cols){
        $svalue = (isset($_GET['svalue'])? $_GET['svalue']:'');
        $skey = (isset($_GET['skey'])? $_GET['skey']:'');
        $option = '';
        foreach ($cols as $k=>$v){
            $checked = '';
            if($skey && $k == $skey) $checked = 'selected';
            $option .= "<option value='$k' $checked>$v</option>";
        }
        if(empty($skey)) $option = '<option value="" selected>选择值</option>'.$option;
        $xhtml = '<form class="form-row" style="margin-left: 50%;margin-right: 2px;">'
            . '<select class="custom-select col-md-4" name="skey" id="inlineFormCustomSelect">'.$option.'</select>'
            . '<input type="text" class="form-control col-md-6" name="svalue" value="'.$svalue.'"> '
            . ' <button type="submit" class="btn btn-success col-md-2"><i class="fa fa-search" aria-hidden="true"></i> 搜索</button>'
            . '</form>'
            ;
        $xhtml = '
            <div class="card card-outline-warning" style="padding: 3px;margin-bottom: 5px;">
            '.$xhtml.'            
            </div>
        ';
        return $xhtml;
    }

    /**
     * 生成 select 控件
     * @param array|callable $config
     * @param null $selected
     * @return string
     */
    public static function SelectGrid($config,$selected=null){
        if($config instanceof \Closure) $option = call_user_func($config);
        else
            $option = isset($config['option'])? $config['option']:$config;
        $option = is_array($option)? $option:[];
        $selectXhtml = '';
        foreach ($option as $k=>$v){
            $selectXhtml .= '<option value="'.$k.'"'.($selected && $selected == $k? ' selected':'').'>'.$v.'</option>';
        }
        return $selectXhtml;
    }

    /**
     * 生成 radio 控件
     * @param $option array|string
     * @param null $checked
     * @return string
     */
    public static function RadioGrid($option,$checked=null){
        $attr = is_string($option)? $option:'';
        if(empty($attr)) {
            $attrArr = [];
            foreach ($option as $k => $v) {
                $attrArr[] = $k . ($v ? '="' . $v . '"' : '');
            }
            $attr = ($attrArr? ' '.implode(' ',$attr):'');
        }
        if($checked) $attr .= ' checked';
        if($attr) $attr = ' '.$attr;
        $xhtml = '<input type="radio"'.$attr.'>';
        return $xhtml;
    }
    /**
     * 主键控件自动生成 2017年5月21日 星期日
     * @param null $data 数据 默认获取 param()
     * @param null $pk 主键默认 listid
     * @param bool $rawMk 原始字符
     * @return string
     */
    public static function formPkGrid($data=null,$pk=null, $rawMk=false){
        $data = $data? $data: request()->param();
        $pk = $pk? $pk:'listid';
        $xhtml = '';
        if(isset($data[$pk])){
            $xhtml = '<input type="hidden" name="pk" value="'.($rawMk? $data[$pk]: base64_encode($data[$pk])).'">';
        }
        return $xhtml;
    }
    /**
     * 2017年5月16日 星期二/获取采筛选条件
     * @param null $where
     * @param string|array $alis 前缀名 array ['默认/_col_'=>alias,'列名'=>b/匹配值 或者 b.nick_col
     * @return array|null
     */
    public function getWhere($where=null,$alias=null){
        $where = is_array($where)? $where:[];
        $key = isset($_GET['skey'])? $_GET['skey']:'';
        if($key && $alias){
            if(is_array($alias)){
                if(isset($alias[$key])) $key = strlen($alias[$key])>1 ? $alias[$key]:$alias[$key].'.'.$key;
                else $key = $alias['_col_'].'.'.$key;
            }
            else $key = $alias.'.'.$key;
        }
        $value = isset($_GET['svalue'])? $_GET['svalue']:'';
        if($value && $key){
            $where[$key] = ['like',"%$value%"];
        }
        return $where;
    }
    /* 2016年11月22日 星期二
     * 表格生成器
     * {
     *   option:
     *          id + ; cols =>[列名/string,]
     *   data:  
     *          1. table ~ $Fn=function
     *          2. source/cols
     * }
    */
    public function tableGrid($opt,$data,$Fn=null)
    {
        $id = isset($opt['id'])? $opt['id']:null;
        $th = '';
        if(isset($opt['cols']) && is_array($opt['cols'])){
            foreach($opt['cols'] as $v){
                $th .= '<th>'.$v.'</th>';
            }
            if($th) $th = '<tr>'.$th.'</tr>';
        }
        $trs = '';
        if(is_array($data)){
            if(isset($data['table']) && is_callable($Fn)){
                $source = $Fn(Db::table($data['table']));
            }
            elseif(isset($data['source'])) $source = $data['source'];
            $cols = isset($data['cols'])? $data['cols']:$opt['cols'];
            $dataId = isset($data['dataid'])? $data['dataid']:null;
            $i = 1;
            foreach($source as $v){
                $tmp = '';
                foreach($cols as $key){                    
                    if(is_array($key)){
                        $label = $key['key'];
                        if(isset($key['link'])){// 支持超链接 {key=>name,link=>/col={:col}/}
                            $engin = str_engin($key['link']);
                            $url = '';$text='';
                            foreach($engin as $k=>$col){
                                $url = str_replace($k,(array_key_exists($col,$v)? $v[$col]:''),$key['link']);
                            }
                            $url = $url? $url:'javascript:void(0);';
                            $tmp .= '<td><a href="'.$url.'">'.$v[$label].'</a></td>'; 
                        }
                    }
                    // elseif(is_callable($key)) $tmp .= '<td>'.$key($v).'</td>';   // 回调函数
                    elseif($key instanceof \Closure){ $tmp .= '<td>'.$key($v).'</td>';}   // 回调函数
                    elseif(array_key_exists($key,$v)) $tmp .= '<td>'.$v[$key].'</td>'; 
                    else $tmp .= '<td>'.$key.'</td>'; 
                }
                $editHtml = '';
                if(isset($data['edit']) && $tmp){                    
                    if(isset($data['edit']['link'])){
                        foreach($data['edit']['link'] as $edit){
                            $engin = str_engin($edit['url']);
                            $url = '';
                            foreach($engin as $k=>$col){
                                $url = str_replace($k,(array_key_exists($col,$v)? $v[$col]:''),$edit['url']);;
                            }
                            $url = $url? $url:'javascript:void(0);';
                            $attr = isset($edit['attr']) && is_array($edit['attr'])? implode(' ',$edit['attr']):(isset($edit['attr'])? $edit['attr']:'');
                            $attr = $attr? ' '.$attr:'';
                            $editHtml .= '<a href="'.$url.'"'.$attr.'>'.$edit['label'].'</a>';
                        }
                    }
                    if(isset($data['edit']['html'])) $editHtml .= $data['edit']['html'];
                    if($editHtml) $editHtml = '<td>'.$editHtml.'</td>';
                }
                if($tmp) $trs .= '<tr'.($dataId && array_key_exists($dataId,$v)? ' dataid="'.$v[$dataId].'"':'').'>'.(isset($data['orderQuit'])? '':'<td>'.$i.'</td>').$tmp.$editHtml.'</tr>';
                $i++;
            }
        }
        // 直接渲染
        if(isset($opt['__viewTr__'])){//debugOut($this->app->view);->
            if(isset($opt['__this__']) && is_object($opt['__this__'])) $opt['__this__']->assign($opt['__viewTr__'],$trs);
            elseif(is_object($this->app)) $this->app->assign($opt['__viewTr__'],$trs);
            return;
        }
        elseif(is_string($opt) && 'feek' == strtolower($opt)) return $trs;
        $html = '
            <div class="container">
                <div class="row"'.($id? ' id="'. $id.'"':'').'>
                    <div class="col-md-12">
                        <table class="table">
                            '.$th.'
                            '.$trs.'
                        </table>
                    </div>
                </div>
            </div>
        ';
        // 直接渲染    
        if(isset($opt['__viewTable__'])){
            if(isset($opt['__this__']) && is_object($opt['__this__'])) $opt['__this__']->assign($opt['__viewTable__'],$html);
            elseif(is_object($this->app)) $this->app->assign($opt['__viewTable__'],$html);
            return;
        }
        return $html;
    }
    // 分页-> GET['page']/data => max [ count* / num+ ; key+ ; type+ ; jsFn+]
    public function pageBar($data=null,$name=null)
    {
        $html = '
            <nav><ul class="pagination pagination-sm"><li><a>1</a></li></ul></nav>
        ';
        if($data){
            $key = isset($data['key'])? $data['key']:'page';            
            if(isset($data[$key])) $page = intval($data[$key]);             
            else $page = $this->page_decode();
            $count = is_numeric($page)? intval($data):null;
            if(isset($data['count'])) $count = intval($data['count']);
            if(empty($count)) return '';
            $num = isset($data['num'])? intval($data['num']):30;// 默认30行
            $pages = ceil($count/$num);
            if($pages == 1) return null;// 单页时不显示分页
            $start = floor($page/10)*10+1;
            $end = $start + 9; $end = $end > $pages? $pages:$end;
            
            $jsClick = false;
            if(isset($data['type']) && $data['type'] == 'js'){// js 点击事件翻页
                $url = isset($data['jsFn'])? $data['jsFn']:'pageTo';
                $url = 'javascript:'.$url.'(\'';
                $jsClick = true;
            }
            else{// PHP url 翻页
                // URL get 语法
                $req = $_GET;
                if(isset($req[$key])) unset($req[$key]);
                if(count($req)>0){
                    $arr = [];$i = 0;
                    foreach($req as $k=>$v){
                        $arr[] = ($i == 0? '?':'').$k.'='.$v;
                        $i++;
                    }
                    $url = implode('&',$arr).'&'.$key.'=';
                }
                else $url = '?'.$key.'=';
            }      
            $lis = '';
            for($i=$start; $i<=$end; $i++){
                $lis .= '<li'.($i == $page? ' class="active page-item"':' class="page-item"').'><a href="'.$url.($this->page_decode($i)).($jsClick? '\');':'').'" class="page-link">'.$i.'</a></li>';
            }
            if($lis){
                if($start>1) $lis = '<li><a href="'.$url.($this->page_decode(($start-10<1? 1:($start-10)))).($jsClick? '\');':'').'">&laquo;</a></li>'.$lis;
                if($end<$pages) $lis .= '<li><a href="'.$url.($this->page_decode($end+1>$pages? $pages:($end+1))).($jsClick? '\');':'').'">&raquo;</a></li>';
                // 概述
                $descript = '<li><a href="javascript:void(0);" title="总页数'.$pages.'，加载数据'.$count.'条!">详情</a></li>';
                //$html = '<nav><ul class="pagination pagination-sm">'.$lis.$descript.'</ul></nav>';
                $html = '<ul class="pagination pagination-sm">'.$lis.$descript.'</ul>';
            }
        }
        $name = $name? $name:'pageBar';
        if(is_object($this->app)){$this->app->assign($name,$html);return;}
        return $html;
    }
    // 页码解析法
    public function page_decode($str=null,$key=null){        
        if($str){// 页码解析
            return base64_encode($str.'#'.time());
        }
        $key = $key? $key:'page';
        if(isset($_GET[$key])){// 页码获取
            $page = base64_decode($_GET[$key]);
            $tmp = explode('#',$page);$page = $tmp[0];
            $d1 = date_create('now');$d2 = date_create(date('Y-m-d h:i:s',$tmp[1]));
            $obj = date_diff($d2,$d1);
            $diff = abs($obj->y);
            if($diff>2){// 安全考虑- 前后两天内有效
                echo '<b>错误的请求地址</b>';
                die;
            }
            return $page;
        }
        return 1;
    }
    // 静态 formGrid 生成器 [label=>string,value=>string/function,+helptext=>string]
    public function staticFormGrids($data){
        $html = '';
        $htmlCreator = function($data){       
            $value = $data['value'];
            $value = is_callable($value)? $value($data):$value;
            $helpText = isset($data['helptext'])? $data['helptext']:null;
            return '
                <div class="form-group">
                    <label class="col-sm-2 control-label">'.$data['label'].'</label>
                    <div class="col-sm-10">
                    <p class="form-control-static">'.$value.'</p>
                    '.($helpText? '<span class="help-block">'.$helpText.'</span>':'').'
                    </div>
                </div>
            ';
        };
        // foreach($data as $v) $html .= $htmlCreator($v);
        foreach($data as $v) $html .= call_user_func($htmlCreator,$v);
        return $html;
    }
    /**
     * 2017年3月2日 星期四 / PHP 动态化 列表组
     * @param $option array          选项
     * @param $data array/function   数据     {col:键值(string/function), __view__:渲染键名,type:类型（默认为空）,order:是否有序号(默认为有，boolean),hasEnd:是否含有结尾符(boolean/默认为是的)}
     * @return string/boolean
    **/
    public function listGrid($option,$data)
    {
        // 类型
        $type = isset($option['type'])? $option['type']:'';
        if($type){
            if(in_array($type,['success','info','warning','danger'])) $type = ' list-group-item-'.$type;
            else $type = '';
        }
        // 视图直接渲染
        $name = isset($option['__view__'])? $option['__view__']:null;
        // li 内容
        $key = isset($option['col'])? $option['col'] : '标题实例';
        // 有序号
        $order = isset($option['order'])? $option['order'] : true;
        // 结尾符
        $hasEnd = isset($option['hasEnd'])? $option['hasEnd'] : true;
        if($data instanceof \Closure) $data = call_user_func($data);
        $xhtml = '';
        $ctt = 1;
        foreach($data as $v){
            if($key instanceof \Closure) $subHtml = call_user_func($key,$v);
            elseif(array_key_exists($key,$v)) $subHtml = $v[$key];
            else $subHtml = $key;
            if($order){
                $subHtml = $ctt.'. '.$subHtml;$ctt++; 
            }
            $xhtml .= '<li class="list-group-item'.$type.'">'.$subHtml.'</li>';
        }
        if($xhtml && $hasEnd) $xhtml = '<ul class="list-group">'.$xhtml.'</ul>';
        if(is_object($this->app) && $name){$this->app->assign($name,$xhtml);return true;}
        return $xhtml;
    }
}