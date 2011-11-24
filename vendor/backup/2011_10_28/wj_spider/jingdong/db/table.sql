CREATE TABLE `price` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `price` decimal(9,2) DEFAULT NULL,
  `list_price` decimal(9,2) DEFAULT NULL,
  `promotion_price` decimal(9,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;