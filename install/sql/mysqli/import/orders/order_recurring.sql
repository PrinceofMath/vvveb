DROP TABLE IF EXISTS `order_recurring`;

CREATE TABLE `order_recurring` (
  `order_recurring_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(11) UNSIGNED NOT NULL,
  `reference` varchar(191) NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `product_name` varchar(191) NOT NULL,
  `product_quantity` int(11) UNSIGNED NOT NULL,
  `recurring_id` int(11) UNSIGNED NOT NULL,
  `recurring_name` varchar(191) NOT NULL,
  `recurring_description` varchar(191) NOT NULL,
  `recurring_frequency` varchar(25) NOT NULL,
  `recurring_cycle` smallint(6) NOT NULL,
  `recurring_duration` smallint(6) NOT NULL,
  `recurring_price` decimal(10,4) NOT NULL,
  `trial` tinyint(1) NOT NULL,
  `trial_frequency` varchar(25) NOT NULL,
  `trial_cycle` smallint(6) NOT NULL,
  `trial_duration` smallint(6) NOT NULL,
  `trial_price` decimal(10,4) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`order_recurring_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;