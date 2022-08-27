DROP TABLE IF EXISTS `return_reason`;

CREATE TABLE `return_reason` (
  `return_reason_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `language_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`return_reason_id`,`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;