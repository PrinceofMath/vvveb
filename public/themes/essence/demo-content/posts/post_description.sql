DROP TABLE IF EXISTS `post_description`;

CREATE TABLE `post_description` (
  `post_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text NOT NULL,
  PRIMARY KEY (`post_id`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
