DROP TABLE IF EXISTS `product_category`;

CREATE TABLE `product_category` (
  `product_taxonomy_item_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `image` varchar(191) NOT NULL DEFAULT '',
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `top` tinyint(1) NOT NULL DEFAULT 0,
  `column` int(3) NOT NULL DEFAULT 0,
  `sort_order` int(3) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`product_taxonomy_item_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

