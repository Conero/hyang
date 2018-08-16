# php 语法学习





## xml

> xml 读取四种方法

​	字符串方式直接读写、DOMDocument读写、XMLWrite写和XMLReader读、SimpleXML读写 



SimpleXML和DOM扩展是属于基于树的解析器，把整个文档存储为树的数据结构中，需要把整个文档都加载到内存中才能工作，所以当处理大型XML文档的时候，性能会剧减。XMLReader则是属于基于流的解析器，它不会一次把整个文档加载到内存中，而是每次分别读取其中的一个节点并允许实时与之交互，这种方式效率高，而且占内存少 



参考: [PHP读写XML文件的四种方法](https://www.cnblogs.com/wujuntian/p/6128297.html)