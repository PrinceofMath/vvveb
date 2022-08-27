DROP TABLE IF EXISTS `taxonomy_description`;

CREATE TABLE `taxonomy_description` (
  `taxonomy_id` int(11) UNSIGNED NOT NULL,
  `language_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`taxonomy_id`,`language_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
