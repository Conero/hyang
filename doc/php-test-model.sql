-- 2018年8月9日 星期四
-- php 测试模型



-- ALTER TABLE jcraw DROP CONSTRAINT fk__log_id;
drop table jcraw;
drop table jclog;


create table jclog(
  "id"  varchar2(100) default sys_guid() not null,    -- ID
  "rtime" number default 0 not null,                  -- 运行时间
  "sr_mt" date default sysdate,                        -- 其实也截止数据
  "er_mt" date,
  "amount" numeric(9, 3),                               -- 数量
  "memory" number,                                      -- php 当前脚本运行内存： 单位是字节（byte）。
  "tool"  varchar2(100),                                 -- 工具栏目
  "tool_desc" varchar2(150),
  "tool_version" varchar2(50),                          -- 工具版本信息
  "env" varchar2(50),        -- PHP_OS
  "mtime" date default sysdate not null,
  primary key("id")
)
;

-- 全原始数据
create table jcraw(
  "id" varchar2(100) default sys_guid() not null,    -- ID
  "random" number,                                     -- 随机数
  "ord" number,
  "star_tm" date,
  "end_tm" date,
  "key" varchar2(80),                                   -- k/v 库
  "value" varchar2(150),
  "code" varchar2(80),                                  -- code/desc 数据分组
  "desc" varchar2(100),
  "remark" varchar2(254),                               -- 描述
  "json" varchar2(1000),
  "text" varchar2(1500),
  "sp_mark" varchar2(80),                               -- 特殊标记
  "mtime" date default sysdate not null,
  "log_id" varchar2(100),
  primary key("id"),
  constraint fk__log_id foreign key ("log_id") references jclog("id")
)
;

