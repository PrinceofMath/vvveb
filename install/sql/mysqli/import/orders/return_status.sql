DROP TABLE IF EXISTS `return_status`;

CREATE TABLE `return_status` (
  `return_status_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `language_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`return_status_id`,`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;