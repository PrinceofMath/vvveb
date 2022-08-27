DROP TABLE IF EXISTS `menu_item`;

CREATE TABLE `menu_item` (
  `menu_item_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) UNSIGNED NOT NULL,
  `image` varchar(191) NOT NULL DEFAULT '',
  `url` varchar(191) NOT NULL DEFAULT '',
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `item_id` int(11) UNSIGNED DEFAULT NULL, -- post or product id
  `sort_order` int(3) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`menu_item_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

