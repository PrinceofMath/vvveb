DROP TABLE IF EXISTS `product_special`;

CREATE TABLE `product_special` (
  `product_special_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(11) UNSIGNED NOT NULL,
  `user_group_id` int(11) UNSIGNED NOT NULL,
  `priority` int(5) NOT NULL DEFAULT '1',
  `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `date_start` date NOT NULL DEFAULT '1000-01-01',
  `date_end` date NOT NULL DEFAULT '1000-01-01',
  PRIMARY KEY (`product_special_id`),

  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
