DROP TABLE IF EXISTS `product_description`;

CREATE TABLE `product_description` (
  `product_id` int(11) UNSIGNED NOT NULL,
  `language_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL DEFAULT "",
  `slug` varchar(191) NOT NULL DEFAULT "",
  `description` text,
  `tag` text,
  `meta_title` varchar(191) NOT NULL DEFAULT "",
  `meta_description` varchar(191) NOT NULL DEFAULT "",
  `meta_keyword` varchar(191) NOT NULL DEFAULT "",
  PRIMARY KEY (`product_id`,`language_id`),
  KEY `name` (`name`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
