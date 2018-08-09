drop table d100w;
create table d100w(
  "id" varchar2(1000) default sys_guid() not null,
  "time" date default sysdate not null,
  "random" number,
  primary key("id")
)
;