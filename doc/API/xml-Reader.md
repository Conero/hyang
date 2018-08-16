# xml\Reader

> Joshua Conero
>
> 2018年8月16日 星期四




> XMLReader 封装函数

## Method



### construct($data=null)

**string**

> $data = string xml 解析内容

**array**

```php
$data = [
    'content'  => 'xml 内容字符串',
    'filename' => 'xml 文件名',
    'encoding' => '编码'
];
```



$data['content']  = string xml 字符串



#### load($filename=null)

> 文件名加载器



#### read($content=null)

> xml 解析



### on($name, $callback)

> 事件绑定

```php
//xml 读取插件
class::on('read', function(XMLReader $xr, DOMNode $node){})
```

