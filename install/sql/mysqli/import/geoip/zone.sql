DROP TABLE IF EXISTS `zone`;

CREATE TABLE `zone` (
  `zone_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `country_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `code` varchar(32) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`zone_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;