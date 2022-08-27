DROP TABLE IF EXISTS `customer`;


CREATE TABLE `customer` (
  `customer_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_group_id` int(11) UNSIGNED NOT NULL,
  `site_id` tinyint(6) NOT NULL DEFAULT '0',
  `language_id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `phone_number` varchar(32) NOT NULL,
  `fax` varchar(32) NOT NULL,
  `password` varchar(191) NOT NULL,
  `salt` varchar(9) NOT NULL,
  `cart` text NOT NULL,
  `wishlist` text NOT NULL,
  `newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `address_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `custom_field` text NOT NULL,
  `ip` varchar(40) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `safe` tinyint(1) NOT NULL,
  `token` text NOT NULL,
  `code` varchar(40) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
