DROP TABLE IF EXISTS `product_category_to_site`;

CREATE TABLE `product_category_to_site` (
  `product_taxonomy_item_id` int(11) UNSIGNED NOT NULL,
  `site_id` tinyint(6) NOT NULL,
  PRIMARY KEY (`product_taxonomy_item_id`,`site_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
