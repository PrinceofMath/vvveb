DROP TABLE IF EXISTS `product_related`;

CREATE TABLE `product_related` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `related_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`product_id`,`related_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
