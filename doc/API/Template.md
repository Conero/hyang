# Template 模板解析器

> 2018年7月12日 星期四
>
> Joshua Conero 



## 模板语法

- ${value}    普通变量



### 方法

- __construct($option=[])   
- gGetVars($tpl)				模板字符解析(生成器)
- file2Str($filename=false, $data=[]) 文件模板解析为字符串
- file2pVar($filename=false)	文件模式解析为php变量
   - (后期可能删除)
- tpl2Str($tpl=false, $data=[])		字符串模板解析为字符串
- tpl2pVar($tpl=false)			模板转化为php变量
   - (后期可能删除)
- saveAsFile($file)  保存内容为文件名





#### __construct($option=[])

```php
<?php
$option = [
    'tplStr' =>  '模板字符串'
    'tplFile' =>  '模板文件'
]
```



#### gGetVars($tpl) 模板解析生成器

> ```
> yield $key => $value;
> ```





#### file2Str($filename=false, $data=[])  从文件模拟中解析到字符串内容中



