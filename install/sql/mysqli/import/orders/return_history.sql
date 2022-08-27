DROP TABLE IF EXISTS `return_history`;

CREATE TABLE `return_history` (
  `return_history_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `return_id` int(11) UNSIGNED NOT NULL,
  `return_status_id` int(11) UNSIGNED NOT NULL,
  `notify` tinyint(1) NOT NULL,
  `comment` text NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`return_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;