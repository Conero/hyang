<?php
/**
 * Auther: Joshua Conero
 * Date: 2018/7/11 0011 15:16
 * Email: brximl@163.com
 * Name: git 程序包
 */

namespace app\cmd;


use app\common\BatGit;
use hyang\surong\cmd\Controller;
use hyang\surong\cmd\Fmt;
use hyang\surong\cmd\Io;

class Git extends Controller
{
    public function DefaultAction()
    {
        // TODO: Implement DefaultAction() method.
        Fmt::line('install url=giturl   安装包从git内部');
    }

    /**
     * url 2 name
     * @param $url
     * @return bool|mixed
     */
    protected function url2Name($url){
        preg_match('/[^\/]+.git+$/i', $url, $matched);
        $matched = $matched[0] ?? false;
        if($matched){
            $matched = str_replace('.git', '', strtolower($matched));
        }
        return $matched;
    }
    /**
     * 项目安装
     */
    function installAction(){
        print '   正在运行命令： install ...'."\n";
        $args = $this->cmdArgs;
        $url = $args['url'] ?? false;
        if($url){
            if(strtolower(substr($url, -4)) != '.git'){
                $url .= '.git';
            }
            if(strtolower(substr($url, 0, 4)) != 'http'){
                $url = 'https://'. $url;
            }
            // 目录地址
            $dirName = $this->url2Name($url);
            // 分支
            if($branch = ($args['branch'] ?? false)){
                $url .= ' --branch='.$branch;
            }

            BatGit::fetch($url, $baseDir);

            print 'git 文件已经下载成功，正在安装包！'. "\n";

            // 获取git仓库所在的目录
            $gitDir = $baseDir . $dirName. '/';
            //print $url."\n";
            $composer = $gitDir . 'composer.json';
            if(is_file($composer)){
                $composerJson = json_decode(file_get_contents($composer), true);
                $cwd = $this->cwd;
                $ppmLockFile = $cwd.'ppm.link';
                $vendor = $cwd . 'vendor/';
                $ppmLock = is_file($ppmLockFile)? json_decode(file_get_contents($ppmLockFile), true) : [];
                if($autoload = ($composerJson['autoload'] ?? false)){
                    if($psr4 = ($autoload['psr-4'] ?? false)){
                        $ppmLock['pkg'] = $ppmLock['pkg'] ?? [];
                        foreach ($psr4 as $ns => $path){
                            $ppmLock['pkg'][$ns] = $path;
                            $vendorTmp = $vendor. $dirName . '/'. $path;
                            Io::mkdirs($vendorTmp);
                            Io::cloneDir($gitDir. $path, $vendorTmp);
                        }
                        // 脚本生成
                        $phpScript = '
<?php
/**
 * Auther: Joshua Conero
 * Date: '.date('Y-m-d H:i:s').'
 * Email: brximl@163.com
 * Name:php package manage PHP 包管理
 */


spl_autoload_register(function ($class){
    // namespace => src
    $psr4 = '.var_export($ppmLock['pkg']).';
    foreach ($psr4 as $ns => $path){
        if(!$ns || !$path){
            continue;
        }
        if(substr_count($class, $ns) > 0){
            $filepath = __DIR__.\'/\'. str_replace($ns, $path, $class) . \'.php\';
            if(is_file($filepath)){
                require_once $filepath;
                return;
            }
        }
    }
});
                        ';
                        file_put_contents($vendor.'/ppm.php', $phpScript);
                        file_put_contents($ppmLockFile, json_encode($ppmLock, \JSON_UNESCAPED_UNICODE));
                    }
                }
            }else{
                print '程序包中 composer.php 文件不存('.$composer.')!'. "\n";
            }
            //Io::rmdirs($gitDir);
        }
        else{
            print 'url 参数不可为空！'."\n";
        }
    }
    // 安装 git 包
    function fetchAction(){
        $args = $this->cmdArgs;
        $url = $args['url'] ?? false;
        if($url){
            if(strtolower(substr($url, -4)) != '.git'){
                $url .= '.git';
            }
            if(strtolower(substr($url, 0, 4)) != 'http'){
                $url = 'https://'. $url;
            }
            // 分支
            if($branch = ($args['branch'] ?? false)){
                $url .= ' --branch='.$branch;
            }
            BatGit::fetch($url);
            //print $url."\n";
        }
        else{
            print 'url 参数不可为空！'."\n";
        }
    }
}