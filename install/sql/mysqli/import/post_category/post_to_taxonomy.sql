DROP TABLE IF EXISTS `post_to_taxonomy_item`;

CREATE TABLE `post_to_taxonomy_item` (
  `post_id` int(11) UNSIGNED NOT NULL,
  `taxonomy_item_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`post_id`,`taxonomy_item_id`),
  KEY `taxonomy_item_id` (`taxonomy_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
