-- tinyint     0~127
-- smallint    0~32767
-- int         0~2187483647
-- bigint
-- engine=InnoDB  or  engine=MyISAM

-- md5 len = 32

-- 表名前缀说明
-- b: 运营后台的表
-- d: 运维相关的表

drop table if exists d_domain_pool;
create table d_domain_pool(
    id                  int unsigned not null auto_increment,

    domain              varchar(255) not null default '',
    domain_type         tinyint not null default 1 comment '1: A级域名 2: B级域名 3：C级域名',
    state               varchar(255) not null default '' comment 'ok: 可用, killed:封杀, error:创建失败',
    remark              varchar(255) not null default '',

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    unique key key_domain(`domain`),
    index idx_state(`state`)
)engine=InnoDB default charset=utf8;


drop table if exists d_dns;
create table d_dns(
    id                  int unsigned not null auto_increment,

    record_id           varchar(255) not null default '',
    domain              varchar(255) not null default '',
    rr                  varchar(255) not null default '',           # 解析记录
    record_type         varchar(32) not null default '',            # 记录类型
    value               varchar(255) not null default '',           # 记录值

    pool_seq            int not null default 0,                     # 泛解析域名生成域名池的序列号
    state               varchar(255) not null default '',
    remark              varchar(255) not null default '',

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    unique key key_domain_rr(`domain`, `rr`)
)engine=InnoDB default charset=utf8;

drop table if exists d_cdn_domain;
create table d_cdn_domain(
    id                  int unsigned not null auto_increment,

    domain              varchar(255) not null default '',
    source              varchar(255) not null default '',           # addr
    source_type         varchar(32) not null default '',            # 
    source_port         int unsigned not null default 80,
    cdn_type            varchar(32) not null default '',
    cname               varchar(255) not null default '',
    state               varchar(255) not null default '',
    remark              varchar(255) not null default '',

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    unique key key_domain(`domain`)
)engine=InnoDB default charset=utf8;

drop table if exists d_oss;
create table d_oss(
    id                  int unsigned not null auto_increment,

    name                varchar(255) not null default '',
    extranet_domain     varchar(255) not null default '',           #
    state               varchar(255) not null default '' comment 'ok: 可用, killed:封杀, error:创建失败',
    remark              varchar(255) not null default '',

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    unique key key_name(`name`)
)engine=InnoDB default charset=utf8;

drop table if exists b_employee;
create table b_employee(
    id                  int unsigned not null auto_increment,

    account             varchar(32) not null default '',
    passwd              char(32) not null default '',

    name                varchar(255) not null default '',
    phone               char(11) not null default '',
    state               tinyint not null default 0,                 # 用户状态

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_account(`account`),
    index idx_phone(`phone`)
)engine=InnoDB default charset=utf8;
insert into b_employee(account,passwd,name,phone,state,ctime)values('admin',md5('admin123'),'管理员','13800138000',1,unix_timestamp());
