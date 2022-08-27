DROP TABLE IF EXISTS `order_recurring_transaction`;

CREATE TABLE `order_recurring_transaction` (
  `order_recurring_transaction_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_recurring_id` int(11) UNSIGNED NOT NULL,
  `reference` varchar(191) NOT NULL,
  `type` int(11) UNSIGNED NOT NULL,
  `amount` decimal(10,4) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`order_recurring_transaction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;