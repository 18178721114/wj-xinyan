
CREATE TABLE `check_result` (
  `check_id` int(11) NOT NULL COMMENT '检查单id',
  `image_qc` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '图片质量信息，-1未知，0双眼可读，1左眼不可读，2右眼不可读',
  `reid_check_id` json DEFAULT NULL COMMENT 'reid检查单id',
  `history_check_id` json DEFAULT NULL COMMENT '历史对比检查单id',
  `cardiovascular` float NOT NULL DEFAULT '0' COMMENT '心脑血管风险',
  `cardiovascular_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '心脑血管风险等级',
  `arteriosclerosis` float NOT NULL DEFAULT '0' COMMENT '动脉硬化风险',
  `arteriosclerosis_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '动脉硬化风险等级',
  `glycometabolism` float NOT NULL DEFAULT '0' COMMENT '糖代谢风险',
  `glycometabolism_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '糖代谢风险等级',
  `sudden_death` float NOT NULL DEFAULT '0' COMMENT '猝死风险',
  `sudden_death_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '猝死风险等级',
  `dementia` float NOT NULL DEFAULT '0' COMMENT '老年痴呆风险',
  `dementia_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '老年痴呆风险等级',
  `anemia` float NOT NULL DEFAULT '0' COMMENT '贫血风险',
  `anemia_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '贫血风险等级',
  `macular_visual_impairment` float NOT NULL DEFAULT '0' COMMENT '黄斑视力损伤风险',
  `macular_visual_impairment_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '黄斑视力损伤风险等级',
  `inhalable_particles` float NOT NULL DEFAULT '0' COMMENT '可吸入颗粒物风险',
  `inhalable_particles_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '可吸入颗粒物风险等级',
  `retina_age` int(10) NOT NULL DEFAULT '0' COMMENT '视网膜年龄',
  `retina_age_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '视网膜年龄等级',
  `brain_tumor_level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '脑肿瘤风险等级',
  `risk_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '风险状态：0 未调用；1 已调用',
  `bmi` float NOT NULL DEFAULT '0' COMMENT 'BMI',
  `bmr` float NOT NULL DEFAULT '0' COMMENT 'BMR',
  `health_cycle` int(10) NOT NULL DEFAULT '0' COMMENT '健康管理周期',
  `health_cycle_begintime` datetime DEFAULT NULL COMMENT '健康管理周期起始时间',
  `health_cycle_endtime` datetime DEFAULT NULL COMMENT '健康管理周期结束时间',
  `health_management_food` int(11) NOT NULL DEFAULT '0' COMMENT '健康管理方案-食谱',
  `health_management_food_recipes` int(11) NOT NULL DEFAULT '0' COMMENT '健康管理方案-每周食谱',
  `health_management_sport` int(11) NOT NULL DEFAULT '0' COMMENT '健康管理方案-运动',
  `health_management_sleep` int(11) NOT NULL DEFAULT '0' COMMENT '健康管理方案-睡眠',
  `health_care_management` int(11) NOT NULL DEFAULT '0' COMMENT '健康管理方案-养生与保健品',
  `health_chronic_management` int(11) NOT NULL DEFAULT '0' COMMENT '健康管理方案-慢病管理',
  `next_check_date` datetime DEFAULT NULL COMMENT '下次复查时间',
  `blood_vessel_score` float NOT NULL DEFAULT '0' COMMENT '血管评分',
  `optic_nerve_score` float NOT NULL DEFAULT '0' COMMENT '视神经评分',
  `macula_lutea_score` float NOT NULL DEFAULT '0' COMMENT '黄斑评分',
  `retina_score` float NOT NULL DEFAULT '0' COMMENT '视网膜评分',
  `choroid_score` float NOT NULL DEFAULT '0' COMMENT '脉络膜评分',
  `extra` json DEFAULT NULL COMMENT '扩展字段',
  `suggestion_level` tinyint(2) NOT NULL DEFAULT '0' COMMENT '综合建议等级',
  `suggestion` varchar(255) NOT NULL DEFAULT '' COMMENT '综合建议',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`check_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='检查单评分、历史对比、风险、建议';



CREATE TABLE `check_result_eye` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `check_id` int(11) NOT NULL COMMENT '检查单id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '眼别：0:unkown;1:left;2:right',
  `qc_bad` tinyint(1) NOT NULL DEFAULT '-1' COMMENT '图片质量：-1:unkown; 0:正常；1质量差',
  `mask` json DEFAULT NULL COMMENT 'mask图',
  `bleed_ex` json DEFAULT NULL COMMENT '出血、渗出（软性、硬性）',
  `drusen` json DEFAULT NULL COMMENT '玻璃膜疣、黄斑图片',
  `blood_vessel` json DEFAULT NULL COMMENT '血管数据：动脉当量、静脉当量、动静脉比、血管动静脉分割mask',
  `optic_nerve` json DEFAULT NULL COMMENT '视神经数据：视盘直径、视杯直径、杯盘比值、视神经图、视盘面积、视杯面积、盘沿面积、视杯/视盘面积比',
  `leopard` json DEFAULT NULL COMMENT '豹纹密度、豹纹分级',
  `mpod` json DEFAULT NULL COMMENT '黄斑色素浓度',
  `dpgs` json DEFAULT NULL COMMENT '豹纹近视演化',
  `sparcspot` json DEFAULT NULL COMMENT '近视弧',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `extra` json DEFAULT NULL COMMENT '扩展json',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`check_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='眼睛算法结果、量化、mask图';






