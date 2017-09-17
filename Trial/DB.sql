/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.6.14 : Database - pls
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`pls` /*!40100 DEFAULT CHARACTER SET utf8 */;

/*Table structure for table `tbl_user_profile` */

DROP TABLE IF EXISTS `tbl_user_profile`;

CREATE TABLE `tbl_user_profile` (
  `profile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`profile_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tbl_user_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_user_profile` */

LOCK TABLES `tbl_user_profile` WRITE;

insert  into `tbl_user_profile`(`profile_id`,`user_id`,`name`) values (19,15,'Gautam kumar'),(20,16,'Alex');

UNLOCK TABLES;

/*Table structure for table `tbl_users` */

DROP TABLE IF EXISTS `tbl_users`;

CREATE TABLE `tbl_users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_GUID` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `entry_date` datetime NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0 = pending, 1 = approved',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_users` */

LOCK TABLES `tbl_users` WRITE;

insert  into `tbl_users`(`user_id`,`user_GUID`,`email`,`password`,`entry_date`,`status`) values (15,'6a30587243ba085f37bda4a08e271132','gautam@example.com','e10adc3949ba59abbe56e057f20f883e','2016-03-17 12:14:49','0'),(16,'51d159ba716d6ee788fdc9ed1e6f1855','alex@mailinator.com','e10adc3949ba59abbe56e057f20f883e','2016-03-17 12:15:04','0');

UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
