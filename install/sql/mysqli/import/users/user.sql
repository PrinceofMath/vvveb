DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(191) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT '2022-05-01 00:00:00',
  `activation_key` varchar(191) NOT NULL DEFAULT '',
  `status` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `display_name` varchar(250) NOT NULL DEFAULT '',
  `role_id` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user` (`user`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
