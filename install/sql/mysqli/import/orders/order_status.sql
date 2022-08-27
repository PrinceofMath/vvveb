DROP TABLE IF EXISTS `order_status`;

CREATE TABLE `order_status` (
  `order_status_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `language_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`order_status_id`,`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;