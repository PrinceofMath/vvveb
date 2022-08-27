DROP TABLE IF EXISTS `taxonomy`;

CREATE TABLE `taxonomy` (
  `taxonomy_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL DEFAULT '',
  `post_type` varchar(191) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT 'categories',  
  PRIMARY KEY (`taxonomy_id`),
  KEY `taxonomy_id` (`taxonomy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;