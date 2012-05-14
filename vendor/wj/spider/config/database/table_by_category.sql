CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `type` enum('PRICE','CONTENT','IMAGE','SALE_RANK') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_product_id` bigint(20) NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `uri` varchar(127) NOT NULL,
  `title` varchar(511) NOT NULL,
  `property_list` text,
  `content_md5` varchar(32) DEFAULT NULL,
  `image_md5` varchar(32) DEFAULT NULL,
  `image_last_modified` varchar(29) DEFAULT NULL,
  `sale_rank` int(11) unsigned NOT NULL,
  `lowest_price_x_100` int(11) unsigned DEFAULT NULL,
  `highest_price_x_100` int(11) unsigned DEFAULT NULL,
  `lowest_list_price_x_100` int(11) unsigned DEFAULT NULL,
  `index_time` datetime NOT NULL,
  `is_updated` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchant_product_id` (`merchant_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;