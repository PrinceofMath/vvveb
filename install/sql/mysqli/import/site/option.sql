DROP TABLE IF EXISTS `option`;

CREATE TABLE `option` (
-- `option_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `site_id` tinyint(6) UNSIGNED NOT NULL DEFAULT 0,
  `key` varchar(128) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`site_id`, `key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
