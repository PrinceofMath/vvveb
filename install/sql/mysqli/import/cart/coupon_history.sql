DROP TABLE IF EXISTS `coupon_history`;

CREATE TABLE `coupon_history` (
  `coupon_history_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `customer_id` int(11) UNSIGNED NOT NULL,
  `amount` decimal(15,4) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`coupon_history_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
