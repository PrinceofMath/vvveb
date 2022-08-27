DROP TABLE IF EXISTS `post_to_menu`;

CREATE TABLE `post_to_menu` (
  `post_id` int(11) UNSIGNED NOT NULL,
  `menu_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`post_id`,`menu_id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
