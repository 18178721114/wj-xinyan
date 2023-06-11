-- ----------------------------
-- 20220929 创建客户表和访问令牌表，支持第三方不登陆调用盘古中的报告接口
-- ----------------------------
CREATE TABLE `open_client_base` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `org_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联结构表ID',
  `client_no` varchar(50) NOT NULL DEFAULT '' COMMENT 'app编号',
  `client_name` varchar(50) NOT NULL DEFAULT '' COMMENT '客户名称',
  `access_secret` varchar(255) NOT NULL DEFAULT '' COMMENT '密钥',
  `client_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 默认1正常',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_client_no` (`client_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='开放客户基础信息表';

CREATE TABLE `oauth_access_token` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联客户client_id',
  `access_token` char(30) NOT NULL DEFAULT '' COMMENT '访问令牌',
  `token_type` char(10) NOT NULL DEFAULT '',
  `scope` varchar(255) DEFAULT '' COMMENT '权限范围',
  `expire_at` datetime DEFAULT NULL COMMENT '失效时间',
  `revoked` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已经被撤销',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_access_token` (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='访问令牌表';