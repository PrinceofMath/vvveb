DROP TABLE IF EXISTS `language`;

CREATE TABLE `language` (
  `language_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `code` varchar(5) NOT NULL,
  `locale` varchar(20) NOT NULL,
  `sort_order` int(3) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'enabled',  
  `default` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`language_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
