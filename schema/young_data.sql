# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: stagingrds.mysql.rds.aliyuncs.com (MySQL 5.7.32-log)
# Database: eye_ikang
# Generation Time: 2022-03-15 12:15:44 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table young_daily_activity_option
# ------------------------------------------------------------

LOCK TABLES `young_daily_activity_option` WRITE;
/*!40000 ALTER TABLE `young_daily_activity_option` DISABLE KEYS */;

INSERT INTO `young_daily_activity_option` (`id`, `activity_id`, `code`, `icon`, `sub_title`, `report_type`, `status`, `sort_id`, `rate`, `grade_rate`, `created`, `updated`)
VALUES
	(1,1,13,'','汽车驾驶',1,1,2,0.00,0.00,'2021-09-15 15:29:21','2021-09-15 15:29:21'),
	(2,1,14,'','夜间活动、娱乐',1,1,4,0.00,0.00,'2021-09-15 15:29:21','2021-09-15 15:29:21'),
	(3,1,0,'','每天户外活动超过3小时',1,1,6,0.00,0.00,'2021-09-15 15:29:21','2021-09-15 15:29:21'),
	(4,1,40,'','伏案工作、学习超过3小时',1,1,8,0.00,0.00,'2021-09-15 15:29:21','2021-09-15 15:29:21'),
	(5,1,20,'','长期使用电脑、手机等电子设备',1,1,10,0.00,0.00,'2021-09-15 15:29:21','2021-09-15 15:29:21'),
	(6,1,41,'','写作业时间超过2小时',1,1,12,0.00,0.00,'2021-09-15 15:29:21','2021-09-15 15:29:21'),
	(7,2,51,'','小于1小时',4,1,1,0.10,0.01,'2021-09-15 15:29:21','2021-10-29 16:32:31'),
	(8,2,52,'','1~3小时',4,1,2,0.00,0.00,'2021-09-15 15:29:21','2021-09-15 15:29:21'),
	(9,2,53,'','大于3小时',4,1,3,-0.10,0.00,'2021-09-15 15:29:21','2021-09-15 15:29:21'),
	(10,3,54,'','靠前排',4,1,1,0.00,0.00,'2021-09-15 15:29:22','2021-09-15 15:29:22'),
	(11,3,55,'','靠中间',4,1,2,0.00,0.00,'2021-09-15 15:29:22','2021-09-15 15:29:22'),
	(12,3,56,'','靠后排',4,1,3,0.00,0.00,'2021-09-15 15:29:22','2021-09-15 15:29:22'),
	(13,4,57,'','小于1小时',4,1,1,0.10,0.01,'2021-09-15 15:29:22','2021-10-29 16:32:48'),
	(14,4,58,'','大于1小时',4,1,2,0.00,-0.02,'2021-09-15 15:29:22','2021-10-29 16:32:55'),
	(15,5,59,'','小于1小时',4,1,1,0.00,-0.01,'2021-09-15 15:29:22','2021-10-29 16:33:28'),
	(16,5,60,'','1~3小时',4,1,2,0.00,0.00,'2021-09-15 15:29:22','2021-10-29 16:33:45'),
	(17,5,61,'','大于3小时',4,1,3,0.10,0.01,'2021-09-15 15:29:22','2021-10-29 16:33:47'),
	(18,6,62,'','均无高度近视',4,1,1,0.20,0.02,'2021-09-15 15:29:22','2021-10-29 16:34:04'),
	(19,6,63,'','一方有高度近视',4,1,2,0.00,0.00,'2021-09-15 15:29:22','2021-09-15 15:29:22'),
	(20,6,64,'','双方均有高度近视',4,1,3,-0.20,-0.01,'2021-09-15 15:29:22','2021-11-02 14:21:29');

/*!40000 ALTER TABLE `young_daily_activity_option` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table young_forecast
# ------------------------------------------------------------

LOCK TABLES `young_forecast` WRITE;
/*!40000 ALTER TABLE `young_forecast` DISABLE KEYS */;

INSERT INTO `young_forecast` (`id`, `type`, `mark`, `position`, `age`, `num`, `gender`, `unit`, `status`, `created`, `updated`)
VALUES
	(1,1,1,1,6,25.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(2,1,1,1,7,-75.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(3,1,1,1,8,-175.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(4,1,1,1,9,-275.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(5,1,1,1,10,-350.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(6,1,1,1,11,-425.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(7,1,1,1,12,-500.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(8,1,1,1,13,-525.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(9,1,1,1,14,-550.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(10,1,1,1,15,-575.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(11,1,1,1,16,-592.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(12,1,1,1,17,-609.00,1,'D',1,'2021-09-09 19:18:00','2021-09-13 10:48:15'),
	(13,1,1,1,18,-625.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(14,1,1,2,6,25.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(15,1,1,2,7,-83.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(16,1,1,2,8,-191.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(17,1,1,2,9,-300.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(18,1,1,2,10,-358.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(19,1,1,2,11,-416.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(20,1,1,2,12,-475.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(21,1,1,2,13,-500.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(22,1,1,2,14,-525.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(23,1,1,2,15,-550.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(24,1,1,2,16,-575.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(25,1,1,2,17,-600.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(26,1,1,2,18,-625.00,1,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(27,1,1,1,6,-25.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(28,1,1,1,7,-75.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(29,1,1,1,8,-175.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(30,1,1,1,9,-275.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(31,1,1,1,10,-333.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(32,1,1,1,11,-391.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(33,1,1,1,12,-450.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(34,1,1,1,13,-492.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(35,1,1,1,14,-534.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(36,1,1,1,15,-575.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(37,1,1,1,16,-592.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(38,1,1,1,17,-609.00,2,'D',1,'2021-09-09 19:18:01','2021-09-13 10:48:15'),
	(39,1,1,1,18,-625.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(40,1,1,2,6,-25.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(41,1,1,2,7,-100.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(42,1,1,2,8,-175.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(43,1,1,2,9,-250.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(44,1,1,2,10,-325.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(45,1,1,2,11,-400.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(46,1,1,2,12,-475.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(47,1,1,2,13,-517.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(48,1,1,2,14,-559.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(49,1,1,2,15,-600.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(50,1,1,2,16,-608.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(51,1,1,2,17,-616.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(52,1,1,2,18,-625.00,2,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(53,1,2,1,6,125.00,1,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(54,1,2,1,7,50.00,1,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(55,1,2,1,8,-25.00,1,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(56,1,2,1,9,-100.00,1,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(57,1,2,1,10,-167.00,1,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(58,1,2,1,11,-234.00,1,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(59,1,2,1,12,-300.00,1,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(60,1,2,1,13,-333.00,1,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(61,1,2,1,14,-366.00,1,'D',1,'2021-09-09 19:18:02','2021-09-13 10:48:15'),
	(62,1,2,1,15,-400.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(63,1,2,1,16,-416.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(64,1,2,1,17,-432.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(65,1,2,1,18,-450.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(66,1,2,2,6,125.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(67,1,2,2,7,42.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(68,1,2,2,8,-41.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(69,1,2,2,9,-125.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(70,1,2,2,10,-175.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(71,1,2,2,11,-225.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(72,1,2,2,12,-275.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(73,1,2,2,13,-308.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(74,1,2,2,14,-341.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(75,1,2,2,15,-375.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(76,1,2,2,16,-400.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(77,1,2,2,17,-425.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(78,1,2,2,18,-450.00,1,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(79,1,2,1,6,75.00,2,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(80,1,2,1,7,17.00,2,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(81,1,2,1,8,-41.00,2,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(82,1,2,1,9,-100.00,2,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(83,1,2,1,10,-150.00,2,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(84,1,2,1,11,-200.00,2,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(85,1,2,1,12,-250.00,2,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(86,1,2,1,13,-300.00,2,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(87,1,2,1,14,-350.00,2,'D',1,'2021-09-09 19:18:03','2021-09-13 10:48:15'),
	(88,1,2,1,15,-400.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(89,1,2,1,16,-416.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(90,1,2,1,17,-432.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(91,1,2,1,18,-450.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(92,1,2,2,6,75.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(93,1,2,2,7,25.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(94,1,2,2,8,-25.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(95,1,2,2,9,-75.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(96,1,2,2,10,-142.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(97,1,2,2,11,-209.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(98,1,2,2,12,-275.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(99,1,2,2,13,-325.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(100,1,2,2,14,-375.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(101,1,2,2,15,-425.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(102,1,2,2,16,-433.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(103,1,2,2,17,-441.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(104,1,2,2,18,-450.00,2,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(105,1,3,1,6,200.00,1,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(106,1,3,1,7,133.00,1,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(107,1,3,1,8,66.00,1,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(108,1,3,1,9,0.00,1,'D',1,'2021-09-09 19:18:04','2021-09-09 19:18:04'),
	(109,1,3,1,10,-50.00,1,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(110,1,3,1,11,-100.00,1,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(111,1,3,1,12,-150.00,1,'D',1,'2021-09-09 19:18:04','2021-09-13 10:48:15'),
	(112,1,3,1,13,-183.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(113,1,3,1,14,-216.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(114,1,3,1,15,-250.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(115,1,3,1,16,-275.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(116,1,3,1,17,-300.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(117,1,3,1,18,-325.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(118,1,3,2,6,200.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(119,1,3,2,7,125.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(120,1,3,2,8,50.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(121,1,3,2,9,-25.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(122,1,3,2,10,-58.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(123,1,3,2,11,-91.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(124,1,3,2,12,-125.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(125,1,3,2,13,-158.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(126,1,3,2,14,-191.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(127,1,3,2,15,-225.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(128,1,3,2,16,-258.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(129,1,3,2,17,-291.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(130,1,3,2,18,-325.00,1,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(131,1,3,1,6,150.00,2,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(132,1,3,1,7,100.00,2,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(133,1,3,1,8,50.00,2,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(134,1,3,1,9,0.00,2,'D',1,'2021-09-09 19:18:05','2021-09-09 19:18:05'),
	(135,1,3,1,10,-33.00,2,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(136,1,3,1,11,-66.00,2,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(137,1,3,1,12,-100.00,2,'D',1,'2021-09-09 19:18:05','2021-09-13 10:48:15'),
	(138,1,3,1,13,-150.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(139,1,3,1,14,-200.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(140,1,3,1,15,-250.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(141,1,3,1,16,-275.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(142,1,3,1,17,-300.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(143,1,3,1,18,-325.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(144,1,3,2,6,150.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(145,1,3,2,7,108.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(146,1,3,2,8,66.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(147,1,3,2,9,25.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(148,1,3,2,10,-25.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(149,1,3,2,11,-75.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(150,1,3,2,12,-125.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(151,1,3,2,13,-175.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(152,1,3,2,14,-225.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(153,1,3,2,15,-275.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(154,1,3,2,16,-292.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(155,1,3,2,17,-309.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(156,1,3,2,18,-325.00,2,'D',1,'2021-09-09 19:18:06','2021-09-13 10:48:15'),
	(157,2,0,0,7,30.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(158,2,0,0,8,30.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(159,2,0,0,9,30.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(160,2,0,0,10,40.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(161,2,0,0,11,40.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(162,2,0,0,12,40.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(163,2,0,0,13,50.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(164,2,0,0,14,50.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(165,2,0,0,15,50.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(166,2,0,0,16,50.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(167,2,0,0,17,50.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(168,2,0,0,18,50.00,0,'%',1,'2021-09-13 14:43:01','2021-09-13 14:43:01'),
	(169,5,0,0,6,22.99,1,'mm',1,'2021-09-14 20:19:14','2021-09-14 20:19:14'),
	(170,5,0,0,7,23.55,1,'mm',1,'2021-09-14 20:19:14','2021-09-14 20:19:14'),
	(171,5,0,0,8,23.99,1,'mm',1,'2021-09-14 20:19:14','2021-09-14 20:19:14'),
	(172,5,0,0,9,24.32,1,'mm',1,'2021-09-14 20:19:14','2021-09-14 20:19:14'),
	(173,5,0,0,10,24.56,1,'mm',1,'2021-09-14 20:19:14','2021-09-14 20:19:14'),
	(174,5,0,0,11,24.72,1,'mm',1,'2021-09-14 20:19:14','2021-09-14 20:19:14'),
	(175,5,0,0,12,24.83,1,'mm',1,'2021-09-14 20:19:14','2021-09-14 20:19:14'),
	(176,5,0,0,13,24.90,1,'mm',1,'2021-09-14 20:19:14','2021-09-14 20:19:14'),
	(177,5,0,0,14,24.96,1,'mm',1,'2021-09-14 20:19:14','2021-09-14 20:19:14'),
	(178,5,0,0,15,25.01,1,'mm',1,'2021-09-14 20:19:14','2021-09-14 20:19:14'),
	(179,5,0,0,16,25.04,1,'mm',1,'2021-09-14 20:19:14','2021-09-14 20:19:14'),
	(180,5,0,0,6,22.54,2,'mm',1,'2021-09-14 20:19:56','2021-09-14 20:19:56'),
	(181,5,0,0,7,23.05,2,'mm',1,'2021-09-14 20:19:56','2021-09-14 20:19:56'),
	(182,5,0,0,8,23.44,2,'mm',1,'2021-09-14 20:19:56','2021-09-14 20:19:56'),
	(183,5,0,0,9,23.72,2,'mm',1,'2021-09-14 20:19:56','2021-09-14 20:19:56'),
	(184,5,0,0,10,23.93,2,'mm',1,'2021-09-14 20:19:56','2021-09-14 20:19:56'),
	(185,5,0,0,11,24.07,2,'mm',1,'2021-09-14 20:19:56','2021-09-14 20:19:56'),
	(186,5,0,0,12,24.16,2,'mm',1,'2021-09-14 20:19:56','2021-09-14 20:19:56'),
	(187,5,0,0,13,24.23,2,'mm',1,'2021-09-14 20:19:56','2021-09-14 20:19:56'),
	(188,5,0,0,14,24.29,2,'mm',1,'2021-09-14 20:19:56','2021-09-14 20:19:56'),
	(189,5,0,0,15,24.37,2,'mm',1,'2021-09-14 20:19:56','2021-09-14 20:19:56'),
	(190,5,0,0,16,24.43,2,'mm',1,'2021-09-14 20:19:56','2021-09-14 20:19:56'),
	(191,3,0,0,7,0.41,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(192,3,0,0,8,0.41,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(193,3,0,0,9,0.41,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(194,3,0,0,10,0.19,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(195,3,0,0,11,0.19,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(196,3,0,0,12,0.19,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(197,3,0,0,13,0.10,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(198,3,0,0,14,0.10,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(199,3,0,0,15,0.10,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(200,3,0,0,16,0.08,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(201,3,0,0,17,0.08,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(202,3,0,0,18,0.08,1,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(203,3,0,0,7,0.40,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(204,3,0,0,8,0.40,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(205,3,0,0,9,0.40,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(206,3,0,0,10,0.20,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(207,3,0,0,11,0.20,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(208,3,0,0,12,0.20,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(209,3,0,0,13,0.12,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(210,3,0,0,14,0.12,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(211,3,0,0,15,0.12,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(212,3,0,0,16,0.06,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(213,3,0,0,17,0.06,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(214,3,0,0,18,0.06,2,'mm',1,'2021-09-14 21:34:50','2021-09-14 21:34:50'),
	(215,4,0,0,9,0.30,0,'mm',1,'2021-09-14 21:36:19','2021-09-14 21:36:19'),
	(216,4,0,0,12,0.50,0,'mm',1,'2021-09-14 21:36:19','2021-09-14 21:36:19'),
	(217,4,0,0,15,0.75,0,'mm',1,'2021-09-14 21:36:19','2021-09-14 21:36:19'),
	(218,4,0,0,18,1.12,0,'mm',1,'2021-09-14 21:36:19','2021-09-14 21:36:19'),
	(219,6,0,0,4,250.00,1,'D',1,'2021-10-26 13:34:51','2021-10-26 13:34:51'),
	(220,6,0,0,5,225.00,1,'D',1,'2021-10-26 13:34:51','2021-10-26 13:34:51'),
	(221,6,0,0,6,200.00,1,'D',1,'2021-10-26 13:34:51','2021-10-26 13:34:51'),
	(222,6,0,0,7,175.00,1,'D',1,'2021-10-26 13:34:51','2021-10-26 13:34:51'),
	(223,6,0,0,8,150.00,1,'D',1,'2021-10-26 13:34:51','2021-10-26 13:34:51'),
	(224,6,0,0,9,125.00,1,'D',1,'2021-10-26 13:34:51','2021-10-26 13:34:51'),
	(225,6,0,0,10,100.00,1,'D',1,'2021-10-26 13:34:51','2021-10-26 13:34:51'),
	(226,6,0,0,11,75.00,1,'D',1,'2021-10-26 13:34:52','2021-10-26 13:34:52'),
	(227,6,0,0,12,50.00,1,'D',1,'2021-10-26 13:34:52','2021-10-26 13:34:52'),
	(228,6,0,0,4,250.00,2,'D',1,'2021-10-26 13:34:52','2021-10-26 13:34:52'),
	(229,6,0,0,5,225.00,2,'D',1,'2021-10-26 13:34:52','2021-10-26 13:34:52'),
	(230,6,0,0,6,200.00,2,'D',1,'2021-10-26 13:34:52','2021-10-26 13:34:52'),
	(231,6,0,0,7,175.00,2,'D',1,'2021-10-26 13:34:52','2021-10-26 13:34:52'),
	(232,6,0,0,8,150.00,2,'D',1,'2021-10-26 13:34:52','2021-10-26 13:34:52'),
	(233,6,0,0,9,125.00,2,'D',1,'2021-10-26 13:34:52','2021-10-26 13:34:52'),
	(234,6,0,0,10,100.00,2,'D',1,'2021-10-26 13:34:52','2021-10-26 13:34:52'),
	(235,6,0,0,11,75.00,2,'D',1,'2021-10-26 13:34:53','2021-10-26 13:34:53'),
	(236,6,0,0,12,50.00,2,'D',1,'2021-10-26 13:34:53','2021-10-26 13:34:53'),
	(237,7,0,0,6,0.30,1,'100%',1,'2021-11-30 21:48:23','2021-11-30 21:48:23'),
	(238,7,0,0,7,0.30,1,'100%',1,'2021-11-30 21:48:23','2021-11-30 21:48:23'),
	(239,7,0,0,8,0.30,1,'100%',1,'2021-11-30 21:48:23','2021-11-30 21:48:23'),
	(240,7,0,0,9,0.30,1,'100%',1,'2021-11-30 21:48:23','2021-11-30 21:48:23'),
	(241,7,0,0,10,0.60,1,'100%',1,'2021-11-30 21:48:24','2021-11-30 21:48:24'),
	(242,7,0,0,11,0.60,1,'100%',1,'2021-11-30 21:48:24','2021-11-30 21:48:24'),
	(243,7,0,0,12,0.60,1,'100%',1,'2021-11-30 21:48:24','2021-11-30 21:48:24'),
	(244,7,0,0,13,0.90,1,'100%',1,'2021-11-30 21:48:24','2021-11-30 21:48:24'),
	(245,7,0,0,14,0.90,1,'100%',1,'2021-11-30 21:48:24','2021-11-30 21:48:24'),
	(246,7,0,0,15,0.90,1,'100%',1,'2021-11-30 21:48:24','2021-11-30 21:48:24'),
	(247,7,0,0,16,1.20,1,'100%',1,'2021-11-30 21:48:24','2021-11-30 21:48:24'),
	(248,7,0,0,17,1.20,1,'100%',1,'2021-11-30 21:48:24','2021-11-30 21:48:24'),
	(249,7,0,0,18,1.20,1,'100%',1,'2021-11-30 21:48:24','2021-11-30 21:48:24'),
	(250,7,0,0,6,0.30,2,'100%',1,'2021-11-30 21:48:25','2021-11-30 21:48:25'),
	(251,7,0,0,7,0.30,2,'100%',1,'2021-11-30 21:48:25','2021-11-30 21:48:25'),
	(252,7,0,0,8,0.30,2,'100%',1,'2021-11-30 21:48:25','2021-11-30 21:48:25'),
	(253,7,0,0,9,0.30,2,'100%',1,'2021-11-30 21:48:25','2021-11-30 21:48:25'),
	(254,7,0,0,10,0.60,2,'100%',1,'2021-11-30 21:48:25','2021-11-30 21:48:25'),
	(255,7,0,0,11,0.60,2,'100%',1,'2021-11-30 21:48:25','2021-11-30 21:48:25'),
	(256,7,0,0,12,0.60,2,'100%',1,'2021-11-30 21:48:25','2021-11-30 21:48:25'),
	(257,7,0,0,13,0.90,2,'100%',1,'2021-11-30 21:48:25','2021-11-30 21:48:25'),
	(258,7,0,0,14,0.90,2,'100%',1,'2021-11-30 21:48:26','2021-11-30 21:48:26'),
	(259,7,0,0,15,0.90,2,'100%',1,'2021-11-30 21:48:26','2021-11-30 21:48:26'),
	(260,7,0,0,16,1.20,2,'100%',1,'2021-11-30 21:48:26','2021-11-30 21:48:26'),
	(261,7,0,0,17,1.20,2,'100%',1,'2021-11-30 21:48:26','2021-11-30 21:48:26'),
	(262,7,0,0,18,1.20,2,'100%',1,'2021-11-30 21:48:26','2021-11-30 21:48:26');

/*!40000 ALTER TABLE `young_forecast` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table young_myopia_grade
# ------------------------------------------------------------

LOCK TABLES `young_myopia_grade` WRITE;
/*!40000 ALTER TABLE `young_myopia_grade` DISABLE KEYS */;

INSERT INTO `young_myopia_grade` (`id`, `grade`, `rate`, `age`, `min_num`, `max_num`, `unit`, `status`, `created`, `updated`)
VALUES
	(1,'A+',0.90,6,175.00,201.00,'D',1,'2021-10-29 16:28:22','2021-10-29 16:28:22'),
	(2,'A',0.80,6,150.00,175.00,'D',1,'2021-10-29 16:28:22','2021-10-29 16:28:22'),
	(3,'A-',0.70,6,125.00,150.00,'D',1,'2021-10-29 16:28:22','2021-10-29 16:28:22'),
	(4,'B+',0.60,6,100.00,125.00,'D',1,'2021-10-29 16:28:22','2021-10-29 16:28:22'),
	(5,'B',0.50,6,75.00,100.00,'D',1,'2021-10-29 16:28:22','2021-10-29 16:28:22'),
	(6,'B-',0.40,6,0.00,75.00,'D',1,'2021-10-29 16:28:22','2021-10-29 16:28:22'),
	(7,'C+',0.30,6,-25.00,0.00,'D',1,'2021-10-29 16:28:22','2021-10-29 16:28:22'),
	(8,'C',0.20,6,-100.00,-25.00,'D',1,'2021-10-29 16:28:22','2021-10-29 16:28:22'),
	(9,'C-',0.10,6,-3000.00,-100.00,'D',1,'2021-10-29 16:28:22','2021-10-29 16:28:22'),
	(10,'A+',0.90,7,125.00,176.00,'D',1,'2021-10-29 16:28:22','2021-10-29 16:28:22'),
	(11,'A',0.80,7,100.00,125.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(12,'A-',0.70,7,75.00,100.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(13,'B+',0.60,7,50.00,75.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(14,'B',0.50,7,25.00,50.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(15,'B-',0.40,7,-50.00,25.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(16,'C+',0.30,7,-75.00,-50.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(17,'C',0.20,7,-150.00,-75.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(18,'C-',0.10,7,-3000.00,-150.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(19,'A+',0.90,8,75.00,151.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(20,'A',0.80,8,50.00,75.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(21,'A-',0.70,8,25.00,50.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(22,'B+',0.60,8,0.00,25.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(23,'B',0.50,8,-25.00,0.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(24,'B-',0.40,8,-100.00,-25.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(25,'C+',0.30,8,-125.00,-100.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(26,'C',0.20,8,-200.00,-125.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(27,'C-',0.10,8,-3000.00,-200.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(28,'A+',0.90,9,25.00,126.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(29,'A',0.80,9,0.00,25.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(30,'A-',0.70,9,-25.00,0.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(31,'B+',0.60,9,-50.00,-25.00,'D',1,'2021-10-29 16:28:23','2021-10-29 16:28:23'),
	(32,'B',0.50,9,-75.00,-50.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(33,'B-',0.40,9,-150.00,-75.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(34,'C+',0.30,9,-175.00,-150.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(35,'C',0.20,9,-250.00,-175.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(36,'C-',0.10,9,-3000.00,-250.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(37,'A+',0.90,10,0.00,101.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(38,'A',0.80,10,-25.00,0.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(39,'A-',0.70,10,-50.00,-25.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(40,'B+',0.60,10,-75.00,-50.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(41,'B',0.50,10,-100.00,-75.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(42,'B-',0.40,10,-175.00,-100.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(43,'C+',0.30,10,-200.00,-175.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(44,'C',0.20,10,-275.00,-200.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(45,'C-',0.10,10,-3000.00,-275.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(46,'A+',0.90,11,-25.00,76.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(47,'A',0.80,11,-50.00,-25.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(48,'A-',0.70,11,-75.00,-50.00,'D',1,'2021-10-29 16:28:24','2021-10-29 16:28:24'),
	(49,'B+',0.60,11,-100.00,-75.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(50,'B',0.50,11,-125.00,-100.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(51,'B-',0.40,11,-200.00,-125.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(52,'C+',0.30,11,-225.00,-200.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(53,'C',0.20,11,-300.00,-225.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(54,'C-',0.10,11,-3000.00,-300.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(55,'A+',0.90,12,-50.00,51.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(56,'A',0.80,12,-75.00,-50.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(57,'A-',0.70,12,-100.00,-75.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(58,'B+',0.60,12,-125.00,-100.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(59,'B',0.50,12,-150.00,-125.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(60,'B-',0.40,12,-225.00,-150.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(61,'C+',0.30,12,-250.00,-225.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(62,'C',0.20,12,-325.00,-250.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(63,'C-',0.10,12,-3000.00,-325.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(64,'A+',0.90,13,-100.00,1.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(65,'A',0.80,13,-125.00,-100.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(66,'A-',0.70,13,-150.00,-125.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(67,'B+',0.60,13,-175.00,-150.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(68,'B',0.50,13,-200.00,-175.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(69,'B-',0.40,13,-275.00,-200.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(70,'C+',0.30,13,-300.00,-275.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(71,'C',0.20,13,-375.00,-300.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(72,'C-',0.10,13,-3000.00,-375.00,'D',1,'2021-10-29 16:28:25','2021-10-29 16:28:25'),
	(73,'A+',0.90,14,-100.00,1.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(74,'A',0.80,14,-125.00,-100.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(75,'A-',0.70,14,-150.00,-125.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(76,'B+',0.60,14,-175.00,-150.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(77,'B',0.50,14,-200.00,-175.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(78,'B-',0.40,14,-250.00,-200.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(79,'C+',0.30,14,-300.00,-250.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(80,'C',0.20,14,-400.00,-300.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(81,'C-',0.10,14,-3000.00,-400.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(82,'A+',0.90,15,-100.00,1.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(83,'A',0.80,15,-125.00,-100.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(84,'A-',0.70,15,-150.00,-125.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(85,'B+',0.60,15,-175.00,-150.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(86,'B',0.50,15,-200.00,-175.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(87,'B-',0.40,15,-250.00,-200.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(88,'C+',0.30,15,-300.00,-250.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(89,'C',0.20,15,-400.00,-300.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(90,'C-',0.10,15,-3000.00,-400.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(91,'A+',0.90,16,-100.00,1.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(92,'A',0.80,16,-125.00,-100.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(93,'A-',0.70,16,-150.00,-125.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(94,'B+',0.60,16,-175.00,-150.00,'D',1,'2021-10-29 16:28:26','2021-10-29 16:28:26'),
	(95,'B',0.50,16,-200.00,-175.00,'D',1,'2021-10-29 16:28:27','2021-10-29 16:28:27'),
	(96,'B-',0.40,16,-250.00,-200.00,'D',1,'2021-10-29 16:28:27','2021-10-29 16:28:27'),
	(97,'C+',0.30,16,-300.00,-250.00,'D',1,'2021-10-29 16:28:27','2021-10-29 16:28:27'),
	(98,'C',0.20,16,-400.00,-300.00,'D',1,'2021-10-29 16:28:27','2021-10-29 16:28:27'),
	(99,'C-',0.10,16,-3000.00,-400.00,'D',1,'2021-10-29 16:28:27','2021-10-29 16:28:27');

/*!40000 ALTER TABLE `young_myopia_grade` ENABLE KEYS */;
UNLOCK TABLES;

INSERT INTO `young_eye_change_config` (`id`, `age`, `sphere`, `imgs_num`, `leopard_level`, `step`, `created`, `updated`)
VALUES
	(1,6,0,24,24,36,'2022-01-04 18:05:39','2022-01-04 18:05:40'),
	(2,7,-150,22,22,39,'2022-01-04 18:05:39','2022-01-04 18:05:40'),
	(3,8,-275,20,20,41,'2022-01-04 18:05:39','2022-01-04 18:05:40'),
	(4,9,-425,18,18,47,'2022-01-04 18:05:39','2022-01-04 18:05:40'),
	(5,10,-525,16,16,51,'2022-01-04 18:05:39','2022-01-04 18:05:40'),
	(6,11,-600,14,14,53,'2022-01-04 18:05:39','2022-01-04 18:05:40'),
	(7,12,-700,12,12,60,'2022-01-04 18:05:40','2022-01-04 18:05:40'),
	(8,13,-750,10,10,60,'2022-01-04 18:05:40','2022-01-04 18:05:40'),
	(9,14,-800,8,8,60,'2022-01-04 18:05:40','2022-01-04 18:05:40'),
	(10,15,-850,6,6,60,'2022-01-04 18:05:40','2022-01-04 18:05:40'),
	(11,16,-900,4,4,60,'2022-01-04 18:05:40','2022-01-04 18:05:40'),
	(12,17,-950,2,2,60,'2022-01-04 18:05:40','2022-01-04 18:05:40'),
	(13,18,-1000,0,0,0,'2022-01-04 18:05:40','2022-01-04 18:05:40');



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;