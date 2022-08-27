DROP TABLE IF EXISTS `return`;

CREATE TABLE `return` (
  `return_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(32) NOT NULL,
  `last_name` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `phone_number` varchar(32) NOT NULL,
  `product` varchar(191) NOT NULL,
  `model` varchar(64) NOT NULL,
  `quantity` int(4) NOT NULL,
  `opened` tinyint(1) NOT NULL,
  `return_reason_id` int(11) UNSIGNED NOT NULL,
  `return_action_id` int(11) UNSIGNED NOT NULL,
  `return_status_id` int(11) UNSIGNED NOT NULL,
  `comment` text NOT NULL,
  `date_ordered` date NOT NULL DEFAULT "1970-01-01",
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`return_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;