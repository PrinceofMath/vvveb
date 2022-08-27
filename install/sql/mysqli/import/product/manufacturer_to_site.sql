DROP TABLE IF EXISTS `manufacturer_to_site`;

CREATE TABLE `manufacturer_to_site` (
  `manufacturer_id` int(11) UNSIGNED NOT NULL,
  `site_id` tinyint(6) NOT NULL,
  PRIMARY KEY (`manufacturer_id`,`site_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;