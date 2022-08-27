DROP TABLE IF EXISTS `voucher_history`;

CREATE TABLE `voucher_history` (
  `voucher_history_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `voucher_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`voucher_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;