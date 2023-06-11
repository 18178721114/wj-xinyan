CREATE TABLE `app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` char(18) NOT NULL DEFAULT '' COMMENT '应用ID',
  `app_name` varchar(128) NOT NULL DEFAULT '' COMMENT '应用名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:删除，1:正常',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `remark` text COMMENT '备注',
  `secret_key` char(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `appid` (`appid`),
  UNIQUE KEY `app_name` (`app_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='应用信息';

alter table organizer add column `system_id` tinyint(1) DEFAULT '0' COMMENT '0:盘古系统;1:女娲系统';
alter table organizer add column `is_glg` tinyint(1) DEFAULT '0' COMMENT '0:不是；1：是';
alter table user add column `system_id` tinyint(1) DEFAULT '0' COMMENT '0:盘古系统;1:女娲系统';

#增加视光字段索引
alter table organizer add index (`is_glg`);

alter table app add column rules json DEFAULT NULL COMMENT '应用限制规则配置信息';

CREATE TABLE `app_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` char(18) NOT NULL DEFAULT '' COMMENT '应用ID',
  `service_name` varchar(128) NOT NULL COMMENT '服务名称',
  `method_name` varchar(128) NOT NULL COMMENT '方法名称',
  `rules` json DEFAULT NULL COMMENT '限制规则配置信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0:删除，1:正常',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `appid` (`appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='应用访问接口权限表';


insert into app_permission(appid, service_name, method_name, rules) values('pg25e683e9557f616e', 'user_center', 'get_balance', '{}');
insert into app_permission(appid, service_name, method_name, rules) values('pg25e683e9557f616e', 'user_center', 'minus_balance', '{}');
insert into app_permission(appid, service_name, method_name, rules) values('pg25e683e9557f616e', 'user_center', 'search_organizer', '{}');
insert into app_permission(appid, service_name, method_name, rules) values('pg25e683e9557f616e', 'user_center', 'search_user', '{}');
insert into app_permission(appid, service_name, method_name, rules) values('pg25e683e9557f616e', 'user_center', 'user_login', '{}');
insert into app_permission(appid, service_name, method_name, rules) values('pg25e683e9557f616e', 'user_center', 'get_org_list', '{}');
insert into app_permission(appid, service_name, method_name, rules) values('pg25e683e9557f616e', 'user_center', 'get_user_list', '{}');
