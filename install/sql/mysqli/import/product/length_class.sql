DROP TABLE IF EXISTS `length_class`;

CREATE TABLE `length_class` (
  `length_class_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `value` decimal(15,8) NOT NULL,
  PRIMARY KEY (`length_class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;