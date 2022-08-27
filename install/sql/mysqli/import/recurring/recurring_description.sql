DROP TABLE IF EXISTS `recurring_description`;

CREATE TABLE `recurring_description` (
  `recurring_id` int(11) UNSIGNED NOT NULL,
  `language_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  PRIMARY KEY (`recurring_id`,`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;