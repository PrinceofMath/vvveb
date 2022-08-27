DROP TABLE IF EXISTS `product_reward`;

CREATE TABLE `product_reward` (
  `product_reward_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `user_group_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `points` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`product_reward_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;