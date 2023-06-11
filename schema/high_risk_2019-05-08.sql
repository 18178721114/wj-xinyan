# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: rm-2zedt4j7u183g922n.mysql.rds.aliyuncs.com (MySQL 5.7.20-log)
# Database: eye_production
# Generation Time: 2019-05-08 03:40:18 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table alarm
# ------------------------------------------------------------

DROP TABLE IF EXISTS `alarm`;



# Dump of table answer
# ------------------------------------------------------------

DROP TABLE IF EXISTS `answer`;

CREATE TABLE `answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `answer` WRITE;
/*!40000 ALTER TABLE `answer` DISABLE KEYS */;

INSERT INTO `answer` (`id`, `name`, `status`, `created`, `updated`)
VALUES
	(1,X'E794B7',1,'2019-04-26 15:07:03','2019-04-26 15:07:03'),
	(2,X'E5A5B3',1,'2019-04-26 15:07:03','2019-04-26 15:07:03'),
	(3,X'E4B88DE590B8E7839F',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(4,X'E5B091E4BA8E35E694AF',1,'2019-04-26 15:07:04','2019-04-28 13:13:30'),
	(5,X'35EFBD9E3230E694AF',1,'2019-04-26 15:07:04','2019-04-28 13:13:57'),
	(6,X'E5A49AE4BA8E3230E694AF',1,'2019-04-26 15:07:04','2019-04-28 13:14:22'),
	(7,X'E4B88DE590B8E7839F',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(8,X'E5B08FE4BA8E35E5B9B4',1,'2019-04-26 15:07:04','2019-04-28 13:15:38'),
	(9,X'35EFBD9E3135E5B9B4',1,'2019-04-26 15:07:04','2019-04-28 13:16:34'),
	(10,X'E5A4A7E4BA8E3135E5B9B4',1,'2019-04-26 15:07:04','2019-04-28 13:16:37'),
	(11,X'E4B88DE5969DE98592',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(12,X'E4B880E4B8A4E799BDE98592E68896E4B8A4E793B6E595A4E98592',1,'2019-04-26 15:07:04','2019-04-28 13:18:40'),
	(13,X'E4B889E4B8A4E4BBA5E58685E799BDE98592E68896E59B9BE793B6E595A4E98592',1,'2019-04-26 15:07:04','2019-04-28 13:22:32'),
	(14,X'E4B889E4B8A4E4BBA5E4B88AE799BDE98592E68896E4BA94E793B6E4BBA5E4B88AE595A4E98592',1,'2019-04-26 15:07:04','2019-04-28 13:22:58'),
	(15,X'E79DA1E79CA0E78AB6E586B5E889AFE5A5BD',1,'2019-04-26 15:07:04','2019-04-28 13:24:55'),
	(16,X'E581B6E5B094E5A4B1E79CA0EFBC8831EFBD9E32E6ACA1EFBC89',1,'2019-04-26 15:07:04','2019-04-28 13:27:12'),
	(17,X'E8BE83E5A49AE5A4B1E79CA0EFBC8833EFBD9E35E6ACA1EFBC89',1,'2019-04-26 15:07:04','2019-04-28 13:27:33'),
	(18,X'E587A0E4B98EE6AF8FE5A4A9E79DA1E79CA0E78AB6E586B5E983BDE5BE88E5B7AE',1,'2019-04-26 15:07:04','2019-04-28 13:27:55'),
	(19,X'E4B88DE8BF90E58AA8',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(20,X'E581B6E5B094EFBC88E5B9B3E59D87E6AF8FE591A8317E32E6ACA1EFBC89',1,'2019-04-26 15:07:04','2019-04-30 13:12:20'),
	(21,X'E7BB8FE5B8B8EFBC88E5B9B3E59D87E6AF8FE591A8327E33E6ACA1EFBC89',1,'2019-04-26 15:07:04','2019-04-30 13:12:24'),
	(22,X'E587A0E4B98EE6AF8FE5A4A9',1,'2019-04-26 15:07:04','2019-04-28 13:30:43'),
	(23,X'E6B885E6B7A1',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(24,X'E5818FE592B8',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(25,X'E5818FE6B2B9',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(26,X'E5818FE7949C',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(27,X'E5BFABE4B990',1,'2019-04-26 15:07:04','2019-05-06 21:33:32'),
	(28,X'E6ADA3E5B8B8',1,'2019-04-26 15:07:04','2019-05-06 21:33:32'),
	(29,X'E58E8BE68A91',1,'2019-04-26 15:07:04','2019-05-06 21:33:32'),
	(30,X'E6B2A1E69C89',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(31,X'E7B396E5B0BFE79785',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(32,X'E9AB98E8A180E58E8B',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(33,X'E88081E5B9B4E797B4E59186',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(34,X'E8BE83E8BDBB',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(35,X'E581A5E5BAB7',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(36,X'E882A5E88396',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(37,X'E8B685E9878D',1,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(38,X'E5B895E98791E6A3AE',1,'2019-04-28 13:38:51','2019-04-28 13:38:51');

/*!40000 ALTER TABLE `answer` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table check_answer_map
# ------------------------------------------------------------

CREATE TABLE `check_answer_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_id` int(11) DEFAULT '0',
  `answer_id` int(11) DEFAULT '0',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table check_hrd_map
# ------------------------------------------------------------

CREATE TABLE `check_hrd_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `check_id` int(11) NOT NULL DEFAULT '0',
  `high_risk_disease_id` int(11) NOT NULL DEFAULT '0',
  `patient_range` varchar(255) NOT NULL DEFAULT '',
  `normal_range` varchar(255) NOT NULL DEFAULT '',
  `risk` varchar(255) NOT NULL DEFAULT '',
  `risk_index` int(11) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table disease_answer_map
# ------------------------------------------------------------

DROP TABLE IF EXISTS `disease_answer_map`;

CREATE TABLE `disease_answer_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `disease_id` int(11) DEFAULT '0',
  `answer_id` int(11) DEFAULT '0',
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `disease_answer_map` WRITE;
/*!40000 ALTER TABLE `disease_answer_map` DISABLE KEYS */;

INSERT INTO `disease_answer_map` (`id`, `disease_id`, `answer_id`, `value`, `created`, `updated`)
VALUES
	(1,1,1,X'312E32','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(2,1,2,X'302E38','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(3,1,3,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(4,1,4,X'302E36','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(5,1,5,X'302E39','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(6,1,6,X'302E39','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(7,1,7,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(8,1,8,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(9,1,9,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(10,1,10,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(11,1,11,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(12,1,12,X'312E32','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(13,1,13,X'312E36','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(14,1,14,X'32','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(15,1,15,X'302E35','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(16,1,16,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(17,1,17,X'32','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(18,1,18,X'35','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(19,1,19,X'33','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(20,1,20,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(21,1,21,X'302E38','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(22,1,22,X'302E36','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(23,1,23,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(24,1,24,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(25,1,25,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(26,1,26,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(27,1,27,X'302E35','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(28,1,28,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(29,1,29,X'33','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(30,1,30,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(31,1,31,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(32,1,32,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(33,1,33,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(34,1,34,X'312E32','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(35,1,35,X'302E38','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(36,1,36,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(37,1,37,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(38,2,1,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(39,2,2,X'312E34','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(40,2,3,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(41,2,4,X'302E37','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(42,2,5,X'302E39','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(43,2,6,X'302E39','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(44,2,7,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(45,2,8,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(46,2,9,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(47,2,10,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(48,2,11,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(49,2,12,X'312E32','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(50,2,13,X'312E36','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(51,2,14,X'32','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(52,2,15,X'302E35','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(53,2,16,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(54,2,17,X'32','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(55,2,18,X'35','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(56,2,19,X'33','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(57,2,20,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(58,2,21,X'302E38','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(59,2,22,X'302E36','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(60,2,23,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(61,2,24,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(62,2,25,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(63,2,26,X'31','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(64,2,27,X'302E35','2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(65,2,28,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(66,2,29,X'33','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(67,2,30,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(68,2,31,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(69,2,32,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(70,2,33,X'34','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(71,2,34,X'312E32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(72,2,35,X'302E38','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(73,2,36,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(74,2,37,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(75,3,1,X'312E36','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(76,3,2,X'302E38','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(77,3,3,X'302E36','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(78,3,4,X'312E32','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(79,3,5,X'312E35','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(80,3,6,X'32','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(81,3,7,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(82,3,8,X'312E32','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(83,3,9,X'312E35','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(84,3,10,X'322E35','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(85,3,11,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(86,3,12,X'312E32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(87,3,13,X'312E36','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(88,3,14,X'32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(89,3,15,X'302E38','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(90,3,16,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(91,3,17,X'32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(92,3,18,X'33','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(93,3,19,X'32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(94,3,20,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(95,3,21,X'302E35','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(96,3,22,X'302E34','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(97,3,23,X'302E38','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(98,3,24,X'312E35','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(99,3,25,X'312E35','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(100,3,26,X'312E32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(101,3,27,X'302E38','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(102,3,28,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(103,3,29,X'32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(104,3,30,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(105,3,31,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(106,3,32,X'32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(107,3,33,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(108,3,34,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(109,3,35,X'302E38','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(110,3,36,X'312E35','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(111,3,37,X'32','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(112,4,1,X'312E33','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(113,4,2,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(114,4,3,X'302E38','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(115,4,4,X'312E32','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(116,4,5,X'312E35','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(117,4,6,X'32','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(118,4,7,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(119,4,8,X'312E32','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(120,4,9,X'312E35','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(121,4,10,X'32','2019-04-26 15:07:06','2019-04-29 13:49:12'),
	(122,4,11,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(123,4,12,X'302E38','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(124,4,13,X'312E32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(125,4,14,X'32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(126,4,15,X'302E38','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(127,4,16,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(128,4,17,X'32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(129,4,18,X'33','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(130,4,19,X'32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(131,4,20,X'31','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(132,4,21,X'302E35','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(133,4,22,X'302E34','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(134,4,23,X'302E38','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(135,4,24,X'312E35','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(136,4,25,X'312E35','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(137,4,26,X'312E32','2019-04-26 15:07:06','2019-04-26 15:07:06'),
	(138,4,27,X'302E38','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(139,4,28,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(140,4,29,X'32','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(141,4,30,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(142,4,31,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(143,4,32,X'32','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(144,4,33,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(145,4,34,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(146,4,35,X'302E38','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(147,4,36,X'312E35','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(148,4,37,X'32','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(149,5,1,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(150,5,2,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(151,5,3,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(152,5,4,X'312E32','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(153,5,5,X'312E35','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(154,5,6,X'312E39','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(155,5,7,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(156,5,8,X'312E32','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(157,5,9,X'312E35','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(158,5,10,X'32','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(159,5,11,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(160,5,12,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(161,5,13,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(162,5,14,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(163,5,15,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(164,5,16,X'312E32','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(165,5,17,X'312E35','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(166,5,18,X'32','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(167,5,19,X'32','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(168,5,20,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(169,5,21,X'302E35','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(170,5,22,X'302E34','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(171,5,23,X'302E38','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(172,5,24,X'312E32','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(173,5,25,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(174,5,26,X'32','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(175,5,27,X'302E38','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(176,5,28,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(177,5,29,X'312E35','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(178,5,30,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(179,5,31,X'312E38','2019-04-26 15:07:07','2019-05-05 23:45:41'),
	(180,5,32,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(181,5,33,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(182,5,34,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(183,5,35,X'302E38','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(184,5,36,X'312E38','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(185,5,37,X'32','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(186,6,1,X'312E32','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(187,6,2,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(188,6,3,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(189,6,4,X'312E35','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(190,6,5,X'32','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(191,6,6,X'322E35','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(192,6,7,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(193,6,8,X'312E32','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(194,6,9,X'312E35','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(195,6,10,X'32','2019-04-26 15:07:07','2019-04-29 13:49:12'),
	(196,6,11,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(197,6,12,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(198,6,13,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(199,6,14,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(200,6,15,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(201,6,16,X'312E31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(202,6,17,X'312E32','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(203,6,18,X'312E35','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(204,6,19,X'32','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(205,6,20,X'31','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(206,6,21,X'302E35','2019-04-26 15:07:07','2019-04-26 15:07:07'),
	(207,6,22,X'302E34','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(208,6,23,X'302E38','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(209,6,24,X'312E35','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(210,6,25,X'312E35','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(211,6,26,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(212,6,27,X'302E38','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(213,6,28,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(214,6,29,X'32','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(215,6,30,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(216,6,31,X'312E32','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(217,6,32,X'312E38','2019-04-26 15:07:08','2019-05-05 23:45:16'),
	(218,6,33,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(219,6,34,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(220,6,35,X'302E38','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(221,6,36,X'312E35','2019-04-26 15:07:08','2019-04-29 13:49:12'),
	(222,6,37,X'32','2019-04-26 15:07:08','2019-04-29 13:49:12'),
	(223,7,1,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(224,7,2,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(225,7,3,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(226,7,4,X'312E32','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(227,7,5,X'312E35','2019-04-26 15:07:08','2019-04-29 13:49:12'),
	(228,7,6,X'32','2019-04-26 15:07:08','2019-04-29 13:49:12'),
	(229,7,7,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(230,7,8,X'312E32','2019-04-26 15:07:08','2019-04-29 13:49:12'),
	(231,7,9,X'312E35','2019-04-26 15:07:08','2019-04-29 13:49:12'),
	(232,7,10,X'32','2019-04-26 15:07:08','2019-04-29 13:49:12'),
	(233,7,11,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(234,7,12,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(235,7,13,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(236,7,14,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(237,7,15,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(238,7,16,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(239,7,17,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(240,7,18,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(241,7,19,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(242,7,20,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(243,7,21,X'302E39','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(244,7,22,X'302E38','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(245,7,23,X'302E38','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(246,7,24,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(247,7,25,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(248,7,26,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(249,7,27,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(250,7,28,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(251,7,29,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(252,7,30,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(253,7,31,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(254,7,32,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(255,7,33,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(256,7,34,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(257,7,35,X'302E38','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(258,7,36,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(259,7,37,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(260,8,1,X'302E39','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(261,8,2,X'312E31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(262,8,3,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(263,8,4,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(264,8,5,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(265,8,6,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(266,8,7,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(267,8,8,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(268,8,9,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(269,8,10,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(270,8,11,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(271,8,12,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(272,8,13,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(273,8,14,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(274,8,15,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(275,8,16,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(276,8,17,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(277,8,18,X'302E38','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(278,8,19,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(279,8,20,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(280,8,21,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(281,8,22,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(282,8,23,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(283,8,24,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(284,8,25,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(285,8,26,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(286,8,27,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(287,8,28,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(288,8,29,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(289,8,30,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(290,8,31,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(291,8,32,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(292,8,33,X'31','2019-04-26 15:07:08','2019-04-26 15:07:08'),
	(293,8,34,X'31','2019-04-26 15:07:09','2019-04-26 15:07:09'),
	(294,8,35,X'302E38','2019-04-26 15:07:09','2019-04-26 15:07:09'),
	(295,8,36,X'31','2019-04-26 15:07:09','2019-04-26 15:07:09'),
	(296,8,37,X'31','2019-04-26 15:07:09','2019-04-26 15:07:09'),
	(297,1,38,X'32','2019-04-28 15:12:11','2019-04-28 15:12:11'),
	(298,2,38,X'31','2019-04-28 15:12:21','2019-04-28 15:12:21'),
	(299,3,38,X'31','2019-04-28 15:12:26','2019-04-28 15:12:26'),
	(300,4,38,X'31','2019-04-28 15:12:35','2019-04-28 15:12:35'),
	(301,5,38,X'31','2019-04-28 15:12:40','2019-04-28 15:12:40'),
	(302,6,38,X'31','2019-04-28 15:12:56','2019-04-28 15:12:56'),
	(303,7,38,X'31','2019-04-28 15:13:02','2019-04-28 15:13:02'),
	(304,8,38,X'31','2019-04-28 15:13:09','2019-04-28 15:13:09');

/*!40000 ALTER TABLE `disease_answer_map` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table high_risk_disease
# ------------------------------------------------------------

DROP TABLE IF EXISTS `high_risk_disease`;

CREATE TABLE `high_risk_disease` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `age_risk_range` varchar(255) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `desc` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `high_risk_disease` WRITE;
/*!40000 ALTER TABLE `high_risk_disease` DISABLE KEYS */;

INSERT INTO `high_risk_disease` (`id`, `name`, `age_risk_range`, `status`, `created`, `updated`, `desc`)
VALUES
	(1,X'E5B895E98791E6A3AE','0.1,0.3,0.5,1,5,8.5,12.5',1,'2019-04-26 15:07:03','2019-04-28 19:53:58','随着人口老龄化加剧，帕金森病患者的数量也越来越多。多见于中老年人，年龄越大，患病的风险越高，目前帕金森病也有年轻化的趋势，估计40岁为0.35%，至60岁为1%，至85岁为2%，且每40个正常人中将有1人发展为帕金森综合征。中国目前已有超过300万帕金森病人。帕金森病已成为继肿瘤、心脑血管病之后中老年人的“第三杀手”。\n'),
	(2,X'E88081E5B9B4E797B4E59186','0.2,0.3,0.8,3,9,15,30',1,'2019-04-26 15:07:03','2019-05-07 21:28:36','全球有约3650万人患有老年痴呆症，每7秒就有一个人患上此病，平均生存期只有5.9年；2011年中国老年痴呆症患者数为800万，2040年将达到2200万，是所有发达国家老年痴呆症患者数的总和。\n'),
	(3,X'E586A0E5BF83E79785','0.2,0.5,1,2.2,4.3,9.3,20',1,'2019-04-26 15:07:03','2019-04-28 19:54:16','冠心病是威胁人类健康的头号杀手，冠心病死亡率占所有心脏病死亡人数的10%～20%。2017年，我国冠心病患者超过1000万人，每年以20%的速度增加。冠心病急性心梗死亡率随年龄的增加而增加，40 岁开始显著上升，其递增趋势近似于指数关系。\n'),
	(4,X'E88491E6A2972FE88491E587BAE8A180','0.05,0.05,0.2,1.2,5.6,12.6,20',1,'2019-04-26 15:07:03','2019-05-07 21:28:36','根据《中国卫生和计划生育统计年鉴》，脑卒中是中国男性和女性的首位死因。2015年城市居民脑血管病死亡率为128.23/10万，其中脑出血52.09/10万，脑梗死41.82/10万。农村居民脑血管病死亡率为153.63/10万，其中脑出血72.26/10万，脑梗死46.99/10万'),
	(5,X'E7B396E5B0BFE79785E58F8AE5B9B6E58F91E79787','1.8,3,5,8.4,14.2,23.8,40',1,'2019-04-26 15:07:03','2019-04-28 19:54:44','2017年国际糖尿病联合会出版的IDF糖尿病地图集第8版估计，中国的糖尿病患者人数为1.14亿，全世界超过1/4的糖尿病患者来自中国。\n全球每年约400万人死于糖尿病, 占全球死因的10.7%；中国有超过84万患者死于糖尿病及其并发症, 其中33.8%的年龄小于60岁。\n'),
	(6,X'E9AB98E8A180E58E8BE58F8AE5B9B6E58F91E79787','5.5,11.7,13,22,30.3,33.3,40',1,'2019-04-26 15:07:03','2019-04-28 19:54:36','高血压是一种常见病和多发病，被称之为“慢病之王”，一般起病缓慢，患者早期常无症状，会慢慢破坏患者的心、脑、肾器官，堪称健康“隐形杀手”。有数据显示，发生心梗的患者中69%有高血压；发生卒中的患者中77%有高血压；发生心衰的患者中有74%有高血压，此外还可能引发一些严重的心律失常，如房颤的发生风险增加。\n目前我国高血压患者人数超过2.7亿人，2013年由高血压带来的直接经济负担达2103亿元，占中国卫生总费用的6.61%。高血压前期(139~120/89~80毫米汞柱)患病率高达4.35亿人，相当于每2个成人中就有1人处于高血压前期。'),
	(7,X'E88491E882BFE798A4','0.8,2.7,5,2,3.2,4.7,5',1,'2019-04-26 15:07:03','2019-04-28 19:54:26','发病率大约每年万分之一，约占全身肿瘤的2％。相比成人，儿童患上肿瘤的可能性更大，大约占其7％。每天大概130人被诊断为脑肿瘤，脑肿瘤可发生在各个年龄段，不分性别和职业。\n'),
	(8,X'E5A4B1E6988EE68896E4B8A5E9878DE8A786E58A9BE68D9FE4BCA4','2.8,4.7,7,9.9,12.8,15.4,20',1,'2019-04-26 15:07:03','2019-05-07 21:28:36','据统计，我国单纯视力残疾的人数达1230万人，全国每年，因眼部问题入院治疗的人数，多达60万人，约近1分钟就会出现1个盲人，3个低视力患者，如果不采取有力措施，到2020年，我国视力残疾人数将为目前的4倍，即将达到5000万人以上！\n');

/*!40000 ALTER TABLE `high_risk_disease` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table qa_map
# ------------------------------------------------------------

DROP TABLE IF EXISTS `qa_map`;

CREATE TABLE `qa_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) DEFAULT '0',
  `answer_id` int(11) DEFAULT '0',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `qa_map` WRITE;
/*!40000 ALTER TABLE `qa_map` DISABLE KEYS */;

INSERT INTO `qa_map` (`id`, `question_id`, `answer_id`, `created`, `updated`)
VALUES
	(1,1,3,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(2,1,4,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(3,1,5,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(4,1,6,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(5,2,7,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(6,2,8,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(7,2,9,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(8,2,10,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(9,3,11,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(10,3,12,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(11,3,13,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(12,3,14,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(13,4,15,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(14,4,16,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(15,4,17,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(16,4,18,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(17,5,19,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(18,5,20,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(19,5,21,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(20,5,22,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(21,6,23,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(22,6,24,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(23,6,25,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(24,6,26,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(25,7,27,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(26,7,28,'2019-04-26 15:07:04','2019-04-26 15:07:04'),
	(27,7,29,'2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(28,8,30,'2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(29,8,31,'2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(30,8,32,'2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(31,8,33,'2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(32,9,34,'2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(33,9,35,'2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(34,9,36,'2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(35,9,37,'2019-04-26 15:07:05','2019-04-26 15:07:05'),
	(36,8,38,'0000-00-00 00:00:00','2019-04-28 13:39:40');

/*!40000 ALTER TABLE `qa_map` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table question
# ------------------------------------------------------------

DROP TABLE IF EXISTS `question`;

CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `show_name` varchar(255) NOT NULL,
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '1:单选；2:多选',
  `status` int(2) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;

INSERT INTO `question` (`id`, `name`, `show_name`, `type`, `status`, `created`, `updated`)
VALUES
	(1,X'E682A8E5B9B3E59D87E6AF8FE5A4A9E590B8E7839FE5A49AE5B091EFBC9F','吸烟频率',1,1,'2019-04-26 15:07:03','2019-04-28 17:13:32'),
	(2,X'E682A8E590B8E7839FE5A49AE5B091E5B9B4EFBC9F','吸烟历史',1,1,'2019-04-26 15:07:03','2019-04-28 17:13:41'),
	(3,X'E682A8E5B9B3E59D87E6AF8FE5A4A9E5969DE98592E5A49AE5B091EFBC9F','喝酒频率',1,1,'2019-04-26 15:07:03','2019-04-28 17:13:50'),
	(4,X'E682A8E69C80E8BF91E4B8A4E591A8E79A84E79DA1E79CA0E78AB6E586B5EFBC9F','睡眠状况',1,1,'2019-04-26 15:07:03','2019-04-28 17:14:01'),
	(5,X'E682A8E698AFE590A6E7BB8FE5B8B8E8BF90E58AA8EFBC9F','运动情况',1,1,'2019-04-26 15:07:03','2019-04-28 17:14:08'),
	(6,X'E682A8E79A84E9A5AEE9A39FE4B9A0E683AFEFBC88E5A49AE98089EFBC89EFBC9F','饮食习惯',2,1,'2019-04-26 15:07:03','2019-04-28 17:14:13'),
	(7,X'E682A8E69C80E8BF91E4B8A4E591A8E79A84E5BF83E68385EFBC9F','心情状况',1,1,'2019-04-26 15:07:03','2019-04-28 17:14:45'),
	(8,X'E682A8E79A84E79BB4E7B3BBE4BAB2E5B19EE4B8ADE698AFE590A6E69C89E4BABAE682A3E69C89E4B88BE58897E796BEE79785EFBC88E5A49AE98089EFBC89EFBC9F','家族史',2,1,'2019-04-26 15:07:03','2019-04-28 17:14:59');

/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
