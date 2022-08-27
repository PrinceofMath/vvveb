DROP TABLE IF EXISTS `shipping_courier`;

CREATE TABLE `shipping_courier` (
  `shipping_courier_id` int(11) UNSIGNED NOT NULL,
  `shipping_courier_code` varchar(191) NOT NULL,
  `shipping_courier_name` varchar(191) NOT NULL,
  PRIMARY KEY (`shipping_courier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;