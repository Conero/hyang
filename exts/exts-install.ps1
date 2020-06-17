echo "ext 依赖安装命令行"
while(1){
    $dir = ""
    if ($dir)
    {
        $doc = "(error) $dir 不是有效路径，"
    }
    $dir = Read-Host $doc"请选择项目所在根目录地址"
    if (Test-Path -Path $dir){
        break
    }
}

$pkg = '';
while(1){
    $pkg = Read-Host '选择需要安装的包'
    if (($pkg) -and (Test-Path -Path "./$pkg")){
        break
    }
}


