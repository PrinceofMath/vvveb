DROP TABLE IF EXISTS `post_description`;

CREATE TABLE `post_description` (
  `post_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `language_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL DEFAULT "",
  `slug` varchar(191) NOT NULL DEFAULT "",
  `content` longtext,
  `excerpt` text,
  PRIMARY KEY (`post_id`,`language_id`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
