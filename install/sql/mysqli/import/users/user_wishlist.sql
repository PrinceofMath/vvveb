DROP TABLE IF EXISTS `user_wishlist`;

CREATE TABLE `user_wishlist` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`user_id`,`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;