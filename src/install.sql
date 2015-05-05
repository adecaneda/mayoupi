-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.27 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for mayoupi
CREATE DATABASE IF NOT EXISTS `mayoupi` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `mayoupi`;


-- Dumping structure for table mayoupi.images
CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) unsigned NOT NULL,
  `url` varchar(64) NOT NULL,
  `type` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `User` (`id_user`),
  CONSTRAINT `FK_images_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- Dumping data for table mayoupi.images: ~16 rows (approximately)
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
INSERT INTO `images` (`id`, `id_user`, `url`, `type`) VALUES
  (1, 1, 'avatar_1.jpg', 'old_avatar'),
  (2, 1, 'avatar_1_1430771993.JPG', 'old_avatar'),
  (3, 1, 'avatar_1_1430772955.JPG', 'old_avatar'),
  (4, 1, 'avatar_1_1430773040.JPG', 'old_avatar'),
  (5, 1, 'avatar_1_1430773162.JPG', 'old_avatar'),
  (6, 1, 'avatar_1_1430773208.JPG', 'old_avatar'),
  (7, 1, 'avatar_1_1430773231.JPG', 'old_avatar'),
  (8, 1, 'avatar_1_1430773247.JPG', 'old_avatar'),
  (9, 1, 'avatar_1_1430773671.jpg', 'old_avatar'),
  (10, 1, 'avatar_1_1430773780.JPG', 'old_avatar'),
  (11, 1, '', 'old_avatar'),
  (12, 1, 'avatar_1_1430782933.jpg', 'old_avatar'),
  (13, 32, 'avatar_32_1430794013.JPG', 'old_avatar'),
  (14, 32, 'avatar_32_1430794131.jpg', 'old_avatar'),
  (15, 32, 'avatar_32_1430794205.jpg', 'old_avatar'),
  (16, 32, 'avatar_32_1430794397.jpg', 'old_avatar'),
  (17, 1, 'avatar_1_1430795863.JPG', 'old_avatar'),
  (18, 1, 'avatar_1_1430796082.JPG', 'avatar'),
  (19, 33, 'avatar_33_1430796488.jpg', 'avatar'),
  (20, 32, 'avatar_32_1430796744.JPG', 'avatar');
/*!40000 ALTER TABLE `images` ENABLE KEYS */;


-- Dumping structure for table mayoupi.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
  `google_id` varchar(128) DEFAULT NULL COMMENT 'Google Plus identifier',
  `instagram_id` varchar(128) DEFAULT NULL COMMENT 'Instagram identifier',
  `token` varchar(128) DEFAULT NULL,
  `id_image` int(11) unsigned DEFAULT NULL COMMENT 'Avatar',
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(128) DEFAULT NULL COMMENT 'Encrypted password',
  `role` varchar(16) NOT NULL,
  `tac_accepted` tinyint(4) NOT NULL COMMENT 'Terms and Conditions accepted?',
  `last_sign_in` datetime DEFAULT NULL COMMENT 'Last time the user signed in',
  `last_image_upload` datetime DEFAULT NULL COMMENT 'Last time the user upload a profile image',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Email` (`email`),
  UNIQUE KEY `Facebook` (`google_id`),
  UNIQUE KEY `Instagram` (`instagram_id`),
  UNIQUE KEY `Token` (`token`),
  KEY `FK_users_images` (`id_image`),
  CONSTRAINT `FK_users_images` FOREIGN KEY (`id_image`) REFERENCES `images` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- Dumping data for table mayoupi.users: ~9 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `google_id`, `instagram_id`, `token`, `id_image`, `name`, `email`, `password`, `role`, `tac_accepted`, `last_sign_in`, `last_image_upload`) VALUES
  (1, NULL, NULL, '912ec803b2ce49e4a541068d495ab570912ec803b2ce49e4a541068d495ab570', 18, 'Alejandro', 'alejandro@mail.com', '912ec803b2ce49e4a541068d495ab570', 'admin', 0, '2015-05-05 05:11:12', '2015-05-05 05:21:22'),
  (19, NULL, NULL, '812ec803b2ce49e4a541068d495ab570912ec803b2ce49e4a541068d495ab570', NULL, 'Kevin Johansen', 'kevin@mail.com', '912ec803b2ce49e4a541068d495ab570', 'user', 1, '2015-05-05 02:05:01', NULL),
  (20, NULL, NULL, NULL, NULL, 'pepe', 'ale@mail.com', '', 'user', 1, '0000-00-00 00:00:00', NULL),
  (21, NULL, NULL, NULL, NULL, 'pepe', 'alejo@mail.com', '', 'user', 1, '0000-00-00 00:00:00', NULL),
  (22, NULL, NULL, NULL, NULL, 'pepe', 'alejo2@mail.com', '912ec803b2ce49e4a541068d495ab570', 'user', 1, '2015-04-19 11:27:43', NULL),
  (23, NULL, NULL, NULL, NULL, 'pepito', 'pepito@mail.com', '912ec803b2ce49e4a541068d495ab570', 'user', 0, '2015-04-19 23:36:03', NULL),
  (24, NULL, NULL, NULL, NULL, 'andres', 'andres@mail.com', '912ec803b2ce49e4a541068d495ab570', 'user', 1, '2015-04-19 23:42:44', NULL),
  (30, NULL, NULL, '8f1b68c06f85c18e7038ac97adcf67de', NULL, 'asdf', 'asdf@asdf.com', '912ec803b2ce49e4a541068d495ab570', 'user', 1, '2015-05-03 09:59:15', NULL),
  (32, '117224139873075536995', NULL, 'ya29.agGUrlkMO7wd4Pu33ms4y79mLSq3s4J_mqultbwZUV37HopW5XDcKmoCqmq1iVnmpr7lk7ciIyoIGA', 20, 'Alejandro D. Caneda', 'adecaneda@gmail.com', '', '', 1, '2015-05-05 05:32:07', '2015-05-05 05:32:24'),
  (33, NULL, NULL, '0ea7fd4e01efba897170ee2c8f6a756b', 19, 'asdfa', 'asdfasdf@asdf.com', '912ec803b2ce49e4a541068d495ab570', 'user', 1, '2015-05-05 05:27:45', '2015-05-05 05:28:08');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
