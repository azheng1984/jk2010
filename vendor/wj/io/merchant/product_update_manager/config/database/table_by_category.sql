CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `type` enum(
    'NEW', 'CATEGORY', 'TITLE', 'PRICE', 'IMAGE', 'SALE_RANK'
  ) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_product_id` bigint(20) NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `title` varchar(511) NOT NULL,
  `image_md5` varchar(32) DEFAULT NULL,
  `image_last_modified` varchar(29) DEFAULT NULL,
  `sale_rank` int(11) unsigned NOT NULL,
  `price_from_x_100` int(11) unsigned DEFAULT NULL,
  `price_to_x_100` int(11) unsigned DEFAULT NULL,
  `list_price_x_100` int(11) unsigned DEFAULT NULL,
  `index_time` datetime NOT NULL,
  `is_updated` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `merchant_product_id` (`merchant_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `property_key` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL,
  `name` varchar(127) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id-name` (`category_id`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `property_value` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key_id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_id-name` (`key_id`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `product-property_value` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `property_value_id` int(11) unsigned NOT NULL,
  `is_updated` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id-property_value_id` (`product_id`, `property_value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;