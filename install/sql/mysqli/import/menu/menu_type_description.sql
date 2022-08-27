DROP TABLE IF EXISTS `menu_type_description`;

CREATE TABLE `menu_type_description` (
  `menu_type_id` int(11) UNSIGNED NOT NULL,
  `language_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`menu_type_id`,`language_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
