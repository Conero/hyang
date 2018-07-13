<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/6/28 0028 9:48
 * Email: brximl@163.com
 * Name:
 */

namespace app\cmd;

use app\common\Util;
use hyang\surong\cmd\Controller;
use hyang\surong\cmd\Fmt;
use sr\Log;

class Build extends Controller
{
    protected $baseDir;     // 项目基础目录
    protected $fileCt = 0;  // 文件统计数
    protected $dirCt = 0;   // 目录统计数
    protected $_pharRequireScptQ = [];  // 引入程序

    public function DefaultAction()
    {
        // TODO: Implement DefaultAction() method.
    }

    function Main(){
        $action = $this->action;
        if('.' == $action){
            $this->project($this->cwd);
        }else if($action && is_dir($action)){
            $this->project($action);
        }
    }

    /**
     * @param $dir
     * @param \Phar $phar
     */
    protected function mkPharScandDir($dir, $phar){
        $dir = standDir($dir);
        if(is_dir($dir)){
            foreach (scandir($dir) as $n){
                if(in_array($n, ['.', '..'])){
                    continue;
                }
                $path = $dir. $n;
                $pharName = '/' . str_replace($this->baseDir, '', $path);
                // 检测忽律表
                if($ignore = ($this->ignore)){
                    $pBreak = false;    // 父类循环中断
                    foreach (explode(',', $ignore) as $ig){
                        $igLen = strlen($ig);
                        //print_r([$ig, substr($pharName, -1* $igLen), $pharName]);
                        //if(substr_count($ig, '*') == 0 && $ig == substr($pharName, -1* $igLen)){
                        if(substr_count($ig, '*') == 0 && $ig == $pharName){
                            $pBreak = true;
                            break;
                        }elseif (substr_count($ig, '*') > 0){       // 正则匹配
                            $preg = Util::str2preg($ig);
                            if($preg && preg_match($preg, $pharName)){
                                $pBreak = true;
                                break;
                            }
                        }
                    }
                    if($pBreak){
                        break;
                    }
                }
                if(is_dir($path)){
                    $this->dirCt += 1;
                    $this->mkPharScandDir($path, $phar);
                }
                else if ($suffix = ($this->suffix)){    // 后缀筛选
                    $pathDt = pathinfo($pharName);
                    if($pathDt['extension'] == 'phar'){
                        continue;
                    }
                    if('*' == $suffix){
                        $this->fileCt += 1;
                        $phar->addFile($path, $pharName);
                    }else{
                        if(in_array($pathDt['extension'], explode(',', $suffix))){
                            $this->fileCt += 1;
                            $phar->addFile($path, $pharName);
                        }
                    }
                }
                else if('.php' == substr($n, -4)){
                    $this->fileCt += 1;
                    $phar->addFile($path, $pharName);
                    //echo $name . Cli_BR;
                }
            }
        }
    }
    /**
     * 项目打包
     * @param $dir
     */
    function project($dir){
        $dir = standDir($dir);
        $this->baseDir = $dir;
        //echo $dir. Cli_BR;
        if(is_dir($dir)){
            $pahrName = $this->name ?? basename($dir);
            $distDir = standDir($this->dist ?? $this->cwd . 'dist/');
            if(!is_dir($distDir)){
                mkdir($distDir);
            }
            $fname = $distDir . $pahrName. '.phar';

            // 删除历史值
            if(is_file($fname)){
                unlink($fname);
            }

            echo '      系统正在压缩： '.$fname. Cli_BR;
            $tmCtt = getRunTimes();
            $phar = new \Phar($fname);
            //$phar = new \Phar($fname, \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::KEY_AS_FILENAME, $pahrName.'.phar');

            // 默认运行参数
            if($indexFile = ($this->cmdArgs['index'] ?? false)){
                $phar->setStub($phar->createDefaultStub($indexFile . '.php'));
            }

            $this->mkPharScandDir($dir, $phar);

            echo '      本地打包包括目录： '.$this->dirCt.
                '; 文件： '.$this->fileCt.
                ' ;用时秒 '.$tmCtt().'s'
                . Cli_BR;

            // 生成脚本
            if(!in_array('no-require-script', $this->cmdOption)){
                $this->createRequireScpt($distDir);
            }
        }
    }

    /**
     * phar 引入程序
     * @param $dir
     * @param $baseDir
     */
    protected function _pharRequireScpt($dir, $baseDir){
        $dir = standDir($dir);
        foreach (scandir($dir) as $v){
            if(!in_array($v, ['.', '..'])){
                $path = $dir . $v;
                $v = strtolower($v);
                if('.phar' == substr($v, -5)){
                    $path = str_replace($baseDir, '', $path);
                    $this->_pharRequireScptQ[] = $path;
                }
            }
        }
    }
    /**
     * @param $distDir
     */
    function createRequireScpt($distDir){
        $name = $distDir. '/require.php';
        $scpt = '';
        $this->mkPharScandDir($distDir, $distDir);

        foreach ($this->_pharRequireScptQ as $v){
            if('.phar' == substr($v, -5)){
                $scpt .= 'require_once(__DIR__.\'/'. $v .'\');'. Cli_BR;
            }
        }

        if(!empty($scpt)){
            $scpt = '<?php'. Cli_BR
                . $scpt
                ;
            file_put_contents($name, $scpt);
        }
    }
}