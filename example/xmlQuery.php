<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/16 0016 13:50
 * Email: brximl@163.com
 * Name: xml 解析测试
 */


require_once __DIR__.'/testLoader.php';

$xml = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<data>
    <!--物料信息-->
    <material               
              id="主键"
              mno="物料代码" drawing="图号"
              seriation="系列" model="型号"
              class="物料大类" unit="单位" step="研发阶段"
              surface_treat="表面处理"              
              standard="执行标准" mark="牌号">
        <!--父材料-->
        <parent
                id="主键"
                mno="物料代码" 
                qty_per="单套用量" spa_qty="随带备件" waste_rate="损耗率"
                is_opt="是否选配" ondemand="按需">
        </parent>
        <!--原(组成)材料/ 不存在时-->
    	<raw name="名称" size="尺寸" trademark="材料牌号"
             status="状态" cutting_size="下料尺寸"
             norm="定额" spa_qty="spa_qty"
             tech_norms="技术标准">
        </raw>
        <!--主制单位-->
        <stru no="主制单位代码" name="组织单位名称"/>
    </material>
</data>
EOT;

define('XML1', $xml);

class Test{
    static function xmlStr(){
        //<?xml version="1.0" encoding="UTF-8"? >
        $str = '
        <data>
            <k1 tk="tk" tk2="tk2" tk3="tk3"></k1>
            <name
                last="Conero"
                frist="Joshua" 
            />
            <remark>
                <desc>基本字符串： 字符串测试</desc>
                <data class="2">
                    <data class="3"><data class="4">tttt</data></data>
                </data>
            </remark>
        </data>
        ';
        $xml = new \hyang\XmlQuery($str);
        //print_r($xml->getXRder());
        //print_r([$xml->getXRder()->isValid()]);

        $xr = $xml->getXRder();
        $node = [];
        while ($xr->read()){
            //print $xr->readInnerXml()."\r\n";
            print $xr->name.','. $xr->localName.','. $xr->nodeType."\r\n";
            if($xr->nodeType == XMLReader::ELEMENT){
                //$node[] = $xr;
                $node[] = $xr->expand();
            }
        }
        print_r($node);
        //print_r([$node[3]->readInnerXml()]);
    }
    // xml-Reader 测试
    static function xmlReader(){
        $read = new \hyang\xml\Reader(XML1);
        $read->on('read', function ($xr, $node){
            print_r([$xr, $node]);
        });
        $read->read();
    }
}
$span = "  ";

echo $span."php-".phpversion()."： \r\n";
//Test::xmlStr();
Test::xmlReader();
