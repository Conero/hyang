# SrPhar phar 管理包工具

> 2018年6月28日 星期四
>
> Joshua Conero



## 描述

- 程序包打包工具
- 模式
  - php cli 程序



## 命令行程序

- 格式
  - php sr.phar {command} {action}  {args}
- build b  **phar 打包工具**
  - action 参数
    -  *.*   当前模块
    - project_dir 项目目录
  - 可选参数
    - name=phar 名称， 默认目录名称
    - dist=发布目录，默认工作目录下 /dist/
    - index=index    默认执行文件，不可添加后缀
    - ignore=name,``*tt*``, ``loh/dd*.dddd``       忽略列表， ``*`` 表任意
    - suffix=php,js  后缀名称,              ``*`` 表示所有; 为空时仅仅打包 php 文件
  - --options 选项
    - --no-require-script 	无require脚本
- help ?     **文档说明**



## 其他注意项

### phar

1.  *内部代码，引入文件时，尽量使用 ```__DIR__``` 代替 ```./```*
2.  *如Windows系统php对大小写不免感，但是对phar内部却敏感，因此这回造成压缩包与源码之间表现的不一致*

