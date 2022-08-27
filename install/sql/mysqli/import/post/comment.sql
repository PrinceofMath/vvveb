DROP TABLE IF EXISTS `comment`;

CREATE TABLE `comment` (
  `comment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `author` tinytext  NOT NULL,
  `email` varchar(100)  NOT NULL DEFAULT '',
  `url` varchar(200)  NOT NULL DEFAULT '',
  `ip` varchar(100)  NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '2022-05-01 00:00:00',
  `date_gmt` datetime NOT NULL DEFAULT '2022-05-01 00:00:00',
  `content` text  NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(20)  NOT NULL DEFAULT '',
  `votes` SMALLINT(3) NOT NULL DEFAULT 0,
  `parent` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `post_id` (`post_id`),
  KEY `approved_date_gmt` (`status`,`date_gmt`),
  KEY `date_gmt` (`date_gmt`),
  KEY `parent` (`parent`),
  KEY `email` (`email`(10))
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
