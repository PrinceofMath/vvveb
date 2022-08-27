DROP TABLE IF EXISTS `product_to_download`;

CREATE TABLE `product_to_download` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `download_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`product_id`,`download_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;