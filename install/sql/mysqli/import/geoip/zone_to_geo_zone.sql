DROP TABLE IF EXISTS `zone_to_geo_zone`;

CREATE TABLE `zone_to_geo_zone` (
  `zone_to_geo_zone_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `country_id` int(11) UNSIGNED NOT NULL,
  `zone_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `geo_zone_id` int(11) UNSIGNED NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`zone_to_geo_zone_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;