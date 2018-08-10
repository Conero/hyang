<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/8/9 0009 14:52
 * Email: brximl@163.com
 * Name:
 */

class Data extends AppAbstract
{
    function Text(){
        $num = $this->args('num', 100);
        $i = 0;
        $pdo = getPDO();
        $sql = 'insert into d100w("random") values(:vrandom)';
        $sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        while ($i < $num){
            $sth->execute(array(':vrandom' => rand(1, 9999999)));
            $i += 1;
        }
    }

    /**
     * 参数： num=100 数量 hideRun=默认否
     */
    protected function _tmv1(){
        $sec = sec();
        $getBtye = getByte();
        $starMt = date('Y-m-d H:i:s');
        $pdo = getPDO();
        $sth = $pdo->query('select sys_guid() as "id" from dual');
        $logId = $sth->fetchObject()->id;

        // 日志写入
        $num = $this->args('num', 100);
        $logSth = $pdo->prepare('insert into jclog ("id", "amount", "tool", "tool_desc", "tool_version", "env") 
                values(:id, :amount, :tool, :tool_desc, :tool_version, :env)',
            [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
        $logSth->execute([
            ':id' => $logId,
            ':amount' => $num,
            ':tool'     => 'php',
            ':tool_desc'=> 'php oci 测试',
            ':tool_version' => phpversion(),
            ':env'           => PHP_OS
        ]);
        //var_dump($logSth);
        //var_dump([$logSth->errorInfo(), $logSthCt, $logSth->errorCode(), 2]);

        $hideRun = $this->args('hideRun');
        $isEnLang = false;
        // php7.0 字符串处理在windows 乱码(utf8-gbk)
        $isPhp70 = substr(phpversion(), 0, 3) == '7.0';
        if($isPhp70) $isEnLang = true;

        // 子数据写入
        $code = uniqid();           // 唯一分组码
        $i = 0;
        $susccess = 0;
        $pdo = getPDO();
        $sql = 'insert into jcraw("random", "ord", "star_tm", "code", "desc", "log_id") values(:vrandom, :ord, :star_tm, :code, :vdesc, :log_id)';
        $sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        while ($i < $num){
            $susccess += $sth->execute([
                'vrandom'   => rand(1, 9999999),
                'ord'        => $i,
                'star_tm'    => $starMt,
                'code'       => $code,
                'vdesc'       => '唯一分组码',
                'log_id'     => $logId
            ]);
            if(!$hideRun){
                if($isEnLang){
                    print "  ".$i." =>  amount: ".$num."(".$susccess."{".
                        round($susccess/$num, 3)."}), rtimes: ".$sec()."s, memory: ".$getBtye()."byte\r\n";
                }
                else{
                    print "  ".$i." =>  数量： ".$num."(".$susccess."{".round($susccess/$num, 3)."}), 用时： ".$sec()."s, 内存： ".$getBtye()."字节\r\n";
                }
            }
            $i += 1;
        }

        // 数据更新

        $pdo->exec('update jclog set "rtime"='.$sec().', "memory"='.$getBtye().' where "id"=\''.$logId.'\'');
        if($isEnLang){
            print "\r\n  (php-".phpversion().")amont: ".$num."(".$susccess."), rtimes: ".$sec()."s, memory: ".$getBtye()."byte\r\n";
        }else{
            print "\r\n  (php-".phpversion().")数量： ".$num."(".$susccess."), 本次用时： ".$sec()."s, 内存： ".$getBtye()."字节\r\n";
        }
        //var_dump([$sth->errorInfo()]);
    }
    /**
     * 测试模型 v1
     * 2018年8月9日 星期四
     */
    function Tmv1(){
        try{
            $this->_tmv1();
        }catch (Exception $e){
            print($e->getMessage());
            print($e->getTraceAsString());
        }

    }
}