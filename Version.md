# 更新日志
- 2018年7月11日 星期三
- Joshua Conero



## v 0.0.2/20180712

- (+) *src/cmd/Fmt* 新增 cmd 输出格式类，实现换行输出以及错误/正常输出
- (优化) *src/cmd/Cmd*  优化格式解析，当格式解析时返回为 true 表示自定义路由中断
  - (+) 新增类常量， rUnfind/rEmpty 为路由失败以及空路由处理



## v 0.0.1/20180711

- 项目搭建，确定包名字为 **hyang/surong/cmd**
- *src/cmd/Cmd* 类实现简单的路由，根据历史项目的经验实现
    - 支持模板路由模板解析 [command]/[action]
- example 为测试例子，不用于实际项目中