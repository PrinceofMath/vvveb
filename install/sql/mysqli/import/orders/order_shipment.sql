DROP TABLE IF EXISTS `order_shipment`;

CREATE TABLE `order_shipment` (
  `order_shipment_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(11) UNSIGNED NOT NULL,
  `date_added` datetime NOT NULL,
  `shipping_courier_id` varchar(191) NOT NULL,
  `tracking_number` varchar(191) NOT NULL,
  PRIMARY KEY (`order_shipment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;