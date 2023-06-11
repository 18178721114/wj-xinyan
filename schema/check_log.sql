CREATE TABLE `check_log_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_id` int(10) NOT NULL,
  `event` varchar(32) DEFAULT '',
  `content` varchar(2048) DEFAULT '',
  `status` tinyint(1) DEFAULT '1' COMMENT '1:  新建；0： 删除',
  `created` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '时间',
  `updated` datetime DEFAULT '1971-01-01 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `remark` text COMMENT 'whatever',
  `extra` text COMMENT 'serialized',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人',
  `client` varchar(30) NOT NULL DEFAULT '' COMMENT '设备',
  PRIMARY KEY (`id`),
  KEY `check_id` (`check_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;



CREATE TABLE `check_log_pre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pcode` varchar(64) NOT NULL DEFAULT '',
  `status` tinyint(1) DEFAULT '1' COMMENT '1:  新建；0： 删除',
  `created` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '时间',
  `updated` datetime DEFAULT '1971-01-01 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `extra` text COMMENT 'serialized 事件相关信息',
  PRIMARY KEY (`id`),
  KEY `pcode` (`pcode`),
  KEY `created` (`created`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 comment 'check_log预备表';


CREATE TABLE `check_log_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(64) NOT NULL DEFAULT '' COMMENT '事件类型',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
  `level` tinyint(1) DEFAULT '0' COMMENT '事件等级',
  `group` varchar(32) NOT NULL DEFAULT '' COMMENT '事件所属类别',
  `stage` varchar(32) NOT NULL DEFAULT '' COMMENT '事件所属阶段',
  `created` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '时间',
  `updated` datetime DEFAULT '1971-01-01 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `event` (`event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (1, 'change_diagnose_admin', '质控诊断并重新推送', 5, '质控', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (2, 'change_diagnose_admin_notpush', '质控诊断但不重新推送', 5, '质控', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (3, 'check_info_update', 'check_info数据表变更', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (4, 'check_disease_map_update', '检查单did变更', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (5, 'check_disease_map_create', '检查单did创建', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (6, 'check_info_extra_update', 'check_info_extra表变更', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (7, 'check_info_inspect_detail_update', 'check_info_inspect_detail表变更', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (8, 'wechat_user_check_create', 'wechat_user_check_create表变更', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (9, 'wechat_user_check_update', 'wechat_user_check_create表变更', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (10, 'delete_pdf_after_local_review', '删除报告等待重新生成', 5, '生成报告', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (11, 'del_pdf_after_update_patient', '删除报告等待重新生成患者信息', 5, '生成报告', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (12, 'check_agent_map_update', 'check_agent_map表变更', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (13, 'patient_info_update', '更新患者信息', 5, '更新患者信息', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (14, 'patient_update', 'patient表更新', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (15, 'change_agent_num', '更新业务员工号', 5, '更新业务员信息', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (16, 'create_org_report', '创建报告样例', 5, '创建检查单', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (17, 'alarm_update', 'alarm表更新', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (18, 'delete_alarm', '删除警示单', 5, '质控', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (19, 'add_suggestion', '众佑添加建议', 5, '阅读报告', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (20, 'review_stop_add', '超过3次重复体检号', 5, '创建检查单', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (21, 'check_info_create', 'check_Info表变更', 10, '', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (22, 'check_info_extra_create', 'check_info_extra表变更', 10, '', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (23, 'auto_add_checkinfo', '自动上传检查单成功', 5, '创建检查单', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (24, 'manual_add_checkinfo', '手动上传检查单成功', 5, '创建检查单', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (25, 'patient_code_update', 'patient_code表变更', 10, '', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (26, 'check_agent_map_create', 'check_agent_map表变更', 10, '', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (27, 'check_image_map_create_or_update', 'check_image_map表变更', 10, '', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (28, 'change_diagnose', '质控诊断审核成功', 5, '质控', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (29, 'receive_base_info_checkstate', '收到基础信息', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (30, 'manual_open_checkstate', '手动设置为促销', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:39:09');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (31, 'update_card_checkstate', '修改体检号，有基础信息', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:39:23');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (32, 'receive_detail_plus_base_checkstate', '无基础信息收到冲突项', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:39:36');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (33, 'receive_detail_upgrade_to_d_checkstate', '无基础信息收到冲突项，并升级到D', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:39:53');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (34, 'yp_diagnose_checkstate', '首次评估完成', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:40:13');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (35, 'yp_diagnose_detail_checkstate', '冲突项后首次评估', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:41:48');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (36, 'yp_diagnose_3_checkstate', 'D套餐首次评估完成', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:42:11');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (37, 'yp_diagnose_4_checkstate', 'E套餐首次评估完成', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:42:26');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (38, 'yp_diagnose_4_no_detail_checkstate', 'E套餐首次评估完成,未收到冲突项', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:42:40');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (39, 'first_check_3_checkstate', 'D套餐初次质控', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:42:58');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (40, 'first_check_4_checkstate', 'E套餐初次质控', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:43:32');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (41, 'first_check_3_no_detail_checkstate', 'D套餐初次质控-未收到冲突项', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:43:37');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (42, 'receive_detail_3_checkstate', 'D套餐首次评估后收到冲突项', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:43:53');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (43, 'receive_detail_4_checkstate', 'E套餐首次评估后收到冲突项', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:44:01');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (44, 'yp_diagnose_5001_checkstate', '爱康体验机构，首次评估完成', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:44:10');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (45, 'yp_revert_status_checkstate', '回滚状态到待终审', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (46, 'yp_diagnose_last_checkstate', '冲突项后首次评估，自动最终质控', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:44:28');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (47, 'no_base_info_timeout_checkstate', '超时未收到基础信息', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:44:37');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (48, 'no_detail_info_timeout_checkstate', '超时未收到冲突项', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:44:47');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (49, 'receive_detail_info_checkstate', 'ABC套餐收到冲突项（需质控）', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:45:11');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (50, 'single_check_checkstate', '设置为单项', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (51, 'single_check_pk3_checkstate', '设置为单项', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:45:23');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (52, 'receive_detail_info_to_last_checkstate', 'ABC套餐收到冲突项（不需质控）', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:45:58');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (53, 'senior_review_checkstate', '专家评估', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:46:06');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (54, 'senior_review_same_checkstate', '专家评估质控一致 ', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:46:13');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (55, 'last_review_checkstate', 'ABC套餐最终质控', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:46:23');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (56, 'review_check_done_checkstate', '本地评估完成', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:46:36');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (57, 'review_check_done_3_checkstate', 'DE套餐最终质控完成', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:46:48');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (58, 'review_stop_checkstate', '重复体检号（中止）', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:47:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (59, 'revert_0_checkstate', '回滚到等待基础信息', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:47:12');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (60, 'revert_1_checkstate', '回滚到待评估', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:47:14');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (61, 'revert_2_checkstate', '回滚到首次评估完成', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:47:23');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (62, 'revert_15_checkstate', '回滚待复核', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:47:34');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (63, 'revert_from_review_done_checkstate', '从评估完成回滚到待评估', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '2022-06-23 10:47:45');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (64, 'push2puhui_failed', '普惠获取推送PDF失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (65, 'push2puhui_success', '普惠获取推送PDF成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (66, 'arteriovenous_algo_ex', '动静脉分割排除', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (67, 'arteriovenous_algo_succeed', '动静脉分割成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (68, 'arteriovenous_algo_failed', '动静脉分割失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (69, 'delete_check_admin', '检查单删除', 5, '质控', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (70, 'repush_check', '重新推送检查单', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (71, 'repush_check_alarm', '重大阳性重新推送', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (72, 'push_weilaibaobei_report_success', '推送报告到未来宝贝成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (73, 'push_weilaibaobei_report_failed', '推送报告到未来宝贝失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (74, 'save_alarm', '生成警示单', 5, '质控', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (75, 'update_alarm', '更新警示单', 5, '质控', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (76, 'check_download', 'PDF报告发送到用户邮箱成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (77, 'check_download_failed', 'PDF报告发送到用户邮箱失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (78, 'add_check_pdf_map', 'check_pdf_map表变更', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (79, 'generate_report', '', NULL, '', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (80, 'pass_diagnose', '检查单审核通过', 5, '质控', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (81, 'patient_info_register_robot', '第三方添加用户信息成功', 10, '创建患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (82, 'add_patient', 'patient表更新', 10, '数据变化', '', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (83, 'patient_info_register', '添加用户信息成功', 5, '创建患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (84, 'update_patient_info', '更新用户信息成功', 5, '修改患者信息', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (85, 'receive_base_info', '接收第三方用户信息成功', 5, '创建患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (86, 'receive_detail_info', '接收第三方冲突项推送成功', 5, '创建患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (87, 'receive_glg_report', '接收GLG报告成功', 5, '视光检查单处理', '检查后', '2022-06-20 21:13:46', '2022-06-23 16:18:15');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (88, 'wechat_push_success', '微信推送成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (89, 'wechat_push_success_again', '微信重推成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (90, 'wechat_push_success_from_qr', '通过扫描推送成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (91, 'wechat_push_failed', '微信推送失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (92, 'wechat_send_msg_failed', '微信推送失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (93, 'wechat_send_msg_sucess', '微信推送成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (94, 'sync_remote_review', '同步远程阅片成功', 5, '质控', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (95, 'sync_remote_review_failed', '同步远程阅片失败', 5, '质控', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (96, 'update_uuid', '更新检查码成功', 5, '更新患者信息', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (97, 'upgrade_check', '升级检查单套餐成功', 5, '更新检查单信息', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (98, 'upgrade_report', '升级报告成功', 5, '更新检查单信息', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (99, 'upgrade_ticket', '获取升级检查单', 5, '更新检查单信息', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (100, 'upgrade_ticket_success', '升级检查单套餐成功', 5, '更新检查单信息', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (101, 'verification_code_binding', '福利码绑定成功', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (102, 'diagnose_remote', '远程医生诊断审核成功', 10, '质控', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (103, 'diagnose_success', '首次评估完成', 5, '质控', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (104, 'conflict_review_type', '糖网或者标注为特殊关注或者现场病史', 5, '质控', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (105, 'diagnose_mark_dirty', '标注为镜头污损问题', 5, '质控', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (106, 'diagnose_mark_damage', '标注为设备故障的可能问题', 5, '质控', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (107, 'review_pass', '检查单审核通过', 5, '质控', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (108, 'same_patient', '标记是否为相同患者', 5, '质控', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (109, 'update_odos', '标记左右眼', 5, '质控', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (110, 'detection_algo_failed', '大相机检测失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (111, 'detection_algo_succeed', '大相机检测成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (112, 'fd16_segmentation_algo_failed', '小相机血管分割失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (113, 'fd16_segmentation_algo_succeed', '小相机血管分割成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (114, 'retina_age_algo_failed', '视网膜年龄失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (115, 'retina_age_algo_succeed', '视网膜年龄成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (116, 'retina_hai_algo_failed', 'HAI算法失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (117, 'retina_hai_algo_succeed', 'HAI算法成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (118, 'sparcspot_algo_failed', '近视弧算法失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (119, 'sparcspot_algo_succeed', '近视弧算法成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (120, 'cindex_algo_failed', '小相机心血管风险cindex失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (121, 'cindex_big_algo_failed', '大相机心血管风险cindex失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (122, 'cindex_algo_succeed', '小相机心血管风险cindex成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (123, 'cindex_big_algo_succeed', '大相机心血管风险cindex成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (124, 'anemia_algo_fd16_jump', '小相机血气不足风险算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (125, 'smoke_algo_fd16_jump', '小相机可吸入颗粒物损伤风险算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (126, 'dpgs_algo_fd16_jump', '小相机豹纹年龄眼底照片演化算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (127, 'mpod_algo_fd16_jump', '小相机黄斑区色素浓度算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (128, 'anemia_algo_big_jump', '大相机血气不足风险算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (129, 'smoke_algo_big_jump', '大相机可吸入颗粒物损伤风险算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (130, 'dpgs_algo_big_jump', '大相机豹纹年龄眼底照片演化算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (131, 'mpod_algo_big_jump', '大相机黄斑区色素浓度算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (132, 'anemia_algo_failed', '血气不足风险算法失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (133, 'smoke_algo_failed', '可吸入颗粒物损伤风险算法失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (134, 'dpgs_algo_failed', '豹纹年龄眼底照片演化算法失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (135, 'mpod_algo_failed', '黄斑区色素浓度算法失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (136, 'anemia_algo_succeed', '血气不足风险算法成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (137, 'smoke_algo_succeed', '可吸入颗粒物损伤风险算法成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (138, 'dpgs_algo_succeed', '豹纹年龄眼底照片演化算法成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (139, 'mpod_algo_succeed', '黄斑区色素浓度算法成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (140, 'anemia_algo_fd16_empty_url', '小相机血气不足风险算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (141, 'smoke_algo_fd16_empty_url', '小相机可吸入颗粒物损伤风险算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (142, 'dpgs_algo_fd16_empty_url', '小相机豹纹年龄眼底照片演化算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (143, 'mpod_algo_fd16_empty_url', '小相机黄斑区色素浓度算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (144, 'anemia_algo_not_fd16_empty_url', '大相机血气不足风险算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (145, 'smoke_algo_not_fd16_empty_url', '大相机可吸入颗粒物损伤风险算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (146, 'dpgs_algo_not_fd16_empty_url', '大相机豹纹年龄眼底照片演化算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (147, 'mpod_algo_not_fd16_empty_url', '大相机黄斑区色素浓度算法跳过', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (148, 'start_camera_success', '启动相机成功', 5, '相机', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (149, 'start_camera_failed', '启动相机失败', 5, '相机', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (150, 'delete_check_open', '通过openapi删除检查单', 5, '更新检查单信息', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (151, 'start_camera_oversea_open', '海外启动相机', 5, '相机', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (152, 'start_camera_open', '通过openapi启动相机', 5, '相机', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (153, 'xikang_start_camera_open', '健康小屋启动相机', 5, '相机', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (154, 'check_delay_print', '延迟标记为已打印', 5, '生成报告', '检查后', '2022-06-20 21:13:46', '2022-06-29 21:59:45');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (155, 'deleted_auto', '自动删除检查单', 5, '更新检查单信息', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (156, 'checkinfo_algo_v2_success', '大分类算法成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (157, 'checkinfo_algo_v2_failed', '大分类算法失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (158, 'destroy_data', '销毁冲突项数据', 5, '更新检查单信息', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (159, 'history_diff_data', '获取历史对比数据', 5, '更新检查单信息', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (160, 'push_2_ak', '推送数据到ikang', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (161, 'push_2_chunyu', '推送数据到春雨', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (162, 'push_fuxingkangyang_success', '推送报告到复兴康养成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (163, 'push_fuxingkangyang_failed', '推送报告到复兴康养失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (164, 'push_pdf_report_yunhu_success', '推送云呼报告成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (165, 'push_pdf_report_yunhu_failed', '推送云呼报告失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (166, 'push_pdf_report_zhonghong_success', '推送中宏报告成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (167, 'push_pdf_report_zhonghong_failed', '推送中宏报告失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (168, 'push_pdf_report_shiyuan_success', '推送视源报告成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (169, 'push_pdf_report_shiyuan_failed', '推送视源报告失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (170, 'push_pdf_report_manniu_success', '推送报告到蛮牛成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (171, 'push_pdf_report_manniu_failed', '推送报告到蛮牛失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (172, 'push_pdf_report_pinganjinguanjia_success', '平安金管家报告推送成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (173, 'push_pdf_report_pinganjinguanjia_failed', '平安金管家报告推送失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (174, 'push_third_report_success', '推送报告到客户平台成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (175, 'push_third_report_failed', '推送报告到客户平台失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (176, 'push_pdf_report_zhongying_success', '推送中英业务员报告成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (177, 'push_pdf_report_zhongying_failed', '推送中英业务员报告失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (178, 'push_report_jdfinance_success', '推送报告到京东金融成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (179, 'push_report_jdfinance_failed', '推送报告到京东金融失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (180, 'push_pdf_report_huatai_success', '推送华泰数据成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (181, 'push_pdf_report_huatai_failed', '推送华泰数据失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (182, 'push_third_report_v2_failed', '推送报告到客户平台失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (183, 'push_third_report_no_api_failed', '推送报告到客户平台失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (184, 'push_pdf_by_email_success', '发送报告邮件成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (185, 'push_pdf_by_email_failed', '发送报告邮件失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (186, 'push_sti_success', '推送STI数据成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (187, 'push_sti_failed', '推送STI数据失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (188, 'generate_pdf_failed', '生成PDF报告失败', 5, '生成报告', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (189, 'upload_pdf_failed', '上传PDF报告失败', 5, '生成报告', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (190, 'upload_pdf_success', '生成PDF报告成功', 5, '生成报告', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (191, 'upload_pdf_2_ftp_success', '上传报告到FTP成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (192, 'upload_pdf_2_ftp_failed', '上传报告到FTP失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (193, 'upload_pdf_partner_BSH_success', '宝石花报告FTP上传成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (194, 'upload_pdf_partner_BSH_failed', '宝石花报告FTP上传失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (195, 'upload_daoyitong_pdf_success', '导医通报告推送成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (196, 'upload_daoyitong_pdf_failed', '导医通报告推送失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (197, 'upload_p0_pdf_success', 'P0实时报告推送成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (198, 'upload_p0_pdf_failed', 'P0实时报告推送失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (199, 'upload_pdf_medical_success', '生成PDF报告成功', 5, '生成报告', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (200, 'upload_pdf_medical_failed', '上传PDF报告失败', 5, '生成报告', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (201, 'generate_pdf_medical_failed', '生成PDF报告失败', 5, '生成报告', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (202, 'wechat_push_health_advice_success', '推送微信消息成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (203, 'wechat_push_health_advice_success_again', '再次推送微信消息成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (204, 'dpgs_algo_filter_out', '过滤掉豹纹演化算法', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (205, 'classification_algo_succeed', '大分类算法成功', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (206, 'classification_algo_failed', '大分类算法失败', 5, '算法', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (207, 'new_risk_snapshot', '生成健康风险', 5, '更新检查单信息', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (208, 'get_conflict_judge', '获取冲突项判断', 5, '冲突项', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (209, 'conflict_left_corrected_vision', '冲突项左眼矫正视力', 5, '冲突项', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (210, 'conflict_right_corrected_vision', '冲突项右眼矫正视力', 5, '冲突项', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (211, 'conflict_vision', '视力冲突检测', 5, '冲突项', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (212, 'conflict_disease_without_tag', '无标签冲突', 5, '冲突项', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (213, 'conflict_health_with_tag', '有标签冲突', 5, '冲突项', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (214, 'did_consistency', 'did 一致性处理', 10, '更新检查单信息', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (215, 'sign_sync_task_success', '签名同步任务成功', 5, '签名', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (216, 'sign_sync_task_failed', '签名同步任务失败', 5, '签名', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (217, 'sign_changed', '签名变更', 5, '签名', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (218, 'slitlamp_checkstate_change', '裂隙灯检查单状态变更', 5, '检查单状态变更', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (219, 'wechat_read', '阅读微信消息', 5, '阅读报告', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (220, 'wechat_push_xinguan_success', '推送微信消息成功', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (221, 'wechat_push_xinguan_failed', '推送微信消息失败', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (222, 'wechat_callback_event', '微信扫码回调消息', 5, '微信回调', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (223, 'init_pcode', '生成pcode数据', 5, '生成pcode', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (224, 'wechat_icvd_callback_event', '微信icvd扫描回调消息', 5, '微信回调', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (225, 'patient_info_add_before_fd16', '小相机添加患者信息', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (226, 'patient_info_add_before_qr', '大相机添加患者信息，生成二维码', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (227, 'patient_info_add_oversea_start_camera_success', '海外添加患者信息，并启动相机成功', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (228, 'patient_info_add_oversea_start_camera_failed', '海外添加患者信息，并启动相机失败', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (229, 'patient_info_add_oversea_success', '海外添加患者信息成功', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (230, 'patient_info_add_qr', '添加患者信息成功', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (231, 'patient_info_add_vcode', '通过福利码获取小相机启动地址', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (232, 'patient_info_add_fd16', '小相机添加患者信息，跳转到相机启动页', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (233, 'patient_info_add_big', '大相机添加患者信息，跳转到筛查码', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (234, 'patient_info_copy_vcode', '添加患者信息，获取福利码成功', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (235, 'patient_info_copy_fd16', '小相机添加患者信息，跳转到相机启动页', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (236, 'patient_info_copy_big', '大相机添加患者信息，跳转到筛查码', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (237, 'patient_vcode_v2_fd16', '通过福利码获取小相机启动地址', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (238, 'receive_code', '生成筛查劵成功', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (239, 'wechat_third_callback_event', '第三方微信扫描回调消息', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (240, 'wechat_tzj_callback_event', '体知健微信扫描回调消息', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (241, 'wechat_ythealth_callback_event', '鹰瞳健康微信扫码回调消息', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (242, 'wechat_zy_callback_event', '众佑微信扫码回调消息', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (243, 'wxkf_callback_event', '微信开发平台第三方回调消息', 5, '更新患者信息', '检查前', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (244, 'can_not_push_report_reason', '报告未推送原因', 5, '推送', '检查后', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (245, 'handle_blur_delay_review', '因图片模糊延迟20分钟审核', 5, '更新检查单信息', '检查中', '2022-06-20 21:13:46', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (246, 'glg_check_set_status', '视光检查单设置为等待基础信息状态', 5, '视光检查单处理', '检查中', '2022-06-23 16:17:35', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (247, 'glg_check_push_nvwa', '将检查单患者和图片信息推送到女娲', 5, '视光检查单处理', '检查中', '2022-06-23 16:19:22', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (248, 'update_create_time', '更新检查单创建时间', 5, '更新检查单信息', '检查中', '2022-06-28 15:37:21', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (249, 'manual_generate_pdf', '手动触发了生成PDF报告操作', 5, '生成报告', '检查后', '2022-06-28 16:14:43', '2022-06-28 16:15:23');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (250, 'check_combine_image', '触发检查单合并高质量图片', 5, '更新检查单信息', '检查中', '2022-06-28 16:56:51', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (251, 'trans_check_submit_user', '迁移检查单所属机构账号', 5, '更新检查单信息', '检查中', '2022-06-28 17:52:48', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (252, 'upload_pdf_medical_success0', '生成PDF完整版报告成功', 5, '生成报告', '检查后', '2022-06-29 20:13:35', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (253, 'upload_pdf_medical_success1', '生成PDF简版报告成功', 5, '生成报告', '检查后', '2022-06-29 20:13:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (254, 'upload_pdf_medical_success2', '生成PDF英文完整版报告成功', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (255, 'upload_pdf_medical_success3', '生成PDF英文简版报告成功', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (256, 'upload_pdf_medical_failed0', '上传PDF完整版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (257, 'upload_pdf_medical_failed1', '上传PDF简版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (258, 'upload_pdf_medical_failed2', '上传PDF英文完整版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (259, 'upload_pdf_medical_failed3', '上传PDF英文简版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (260, 'generate_pdf_medical_failed0', '生成PDF完整版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (261, 'generate_pdf_medical_failed1', '生成PDF简版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (262, 'generate_pdf_medical_failed2', '生成PDF英文完整版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (263, 'generate_pdf_medical_failed3', '生成PDF英文简版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (264, 'generate_pdf_failed0', '生成PDF完整版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (265, 'generate_pdf_failed1', '生成PDF简版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (266, 'generate_pdf_failed2', '生成PDF英文完整版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (267, 'generate_pdf_failed3', '生成PDF英文简版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (268, 'upload_pdf_failed0', '上传PDF完整版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (269, 'upload_pdf_failed1', '上传PDF简版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (270, 'upload_pdf_failed2', '上传PDF英文完整版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (271, 'upload_pdf_failed3', '上传PDF英文简版报告失败', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (272, 'upload_pdf_success0', '生成PDF完整版报告成功', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (273, 'upload_pdf_success1', '生成PDF简版报告成功', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (274, 'upload_pdf_success2', '生成PDF英文完整版报告成功', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');
INSERT INTO `check_log_event` (`id`, `event`, `desc`, `level`, `group`, `stage`, `created`, `updated`) VALUES (275, 'upload_pdf_success3', '生成PDF英文简版报告成功', 5, '生成报告', '检查后', '2022-06-29 20:17:58', '1971-01-01 00:00:00');

update check_log_event set `desc`='大分类算法状态更新为成功' where `event`='classification_algo_succeed';
update check_log_event set `desc`='大分类算法状态更新为失败' where `event`='classification_algo_failed';
update check_log_event set `desc`='评估完成结果', `level`= 9 where `event`='diagnose_success';
update check_log_event set `desc`='生成健康风险快照' where `event`='new_risk_snapshot';
insert into check_log_event(`event`, `desc`, `level`, `group`, stage) values('risk_algo_succeed', '慢病风险算法成功', 5, '算法', '检查中');
insert into check_log_event(`event`, `desc`, `level`, `group`, stage) values('risk_algo_failed', '慢病风险算法失败', 5, '算法', '检查中');
insert into check_log_event(`event`, `desc`, `level`, `group`, stage) values('risk_algo_fd16_empty_url', '慢病风险算法不支持小相机跳过', 5, '算法', '检查中');
insert into check_log_event(`event`, `desc`, `level`, `group`, stage) values('risk_algo_not_fd16_empty_url', '慢病风险算法不支持大相机跳过', 5, '算法', '检查中');

insert into check_log_event(`event`, `desc`, `level`, `group`, stage) values('repush_check_internal_wechat', '微信重新推送', 5, '推送', '检查后');
insert into check_log_event(`event`, `desc`, `level`, `group`, stage) values('repush_check_internal_third', '第三方重新推送', 5, '推送', '检查后');
