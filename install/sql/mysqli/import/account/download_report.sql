DROP TABLE IF EXISTS `download_report`;

CREATE TABLE `download_report` (
  `download_report_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `download_id` int(11) UNSIGNED NOT NULL,
  `site_id` tinyint(6) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `country` varchar(2) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`download_report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;