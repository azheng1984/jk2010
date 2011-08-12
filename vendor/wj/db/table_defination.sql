CREATE DATABASE `wj`;
USE wj;

CREATE TABLE `global_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) DEFAULT NULL,
  `name` varchar(127) DEFAULT NULL,
  `table_prefix` varchar(45) DEFAULT NULL,
  `redirect_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `global_merchant` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `global_order` (
  `id` int(11) NOT NULL,
  `merchant_id` bigint(20) DEFAULT NULL,
  `merchant_order_id` varchar(45) DEFAULT NULL,
  `status` enum('NOT_PAID','DELETE','PAID','REFUND','COMPLETE') DEFAULT 'NOT_PAID',
  `time` datetime DEFAULT NULL,
  `outgoing_category_id` int(11) DEFAULT NULL,
  `outgoing_id` bigint(20) DEFAULT NULL,
  `detail_list` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `global_product_index` (
  `product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `laptop_illegal_product` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `laptop_order_detail` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `price` decimal(9,2) DEFAULT NULL,
  `amount` decimal(9,2) DEFAULT NULL,
  `commission` decimal(9,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `laptop_outgoing` (
  `id` bigint(20) NOT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `merchant_id` bigint(20) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `session_id` bigint(20) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `laptop_price` (
  `id` bigint(20) NOT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `merchant_id` bigint(20) DEFAULT NULL,
  `price` decimal(9,2) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `laptop_price_history` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `price` decimal(9,2) DEFAULT NULL,
  `merchant_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `laptop_product` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(511) DEFAULT NULL,
  `property_value_list` text,
  `lowest_price` decimal(9,2) DEFAULT NULL,
  `highest_price` decimal(9,2) DEFAULT NULL,
  `has_image` tinyint(1) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `laptop_property_key` (
  `id` int(11) NOT NULL,
  `parent_id` varchar(45) DEFAULT NULL,
  `key` varchar(45) DEFAULT NULL,
  `type` enum('TEXT','IMAGE') DEFAULT 'TEXT',
  `rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `laptop_property_value` (
  `id` bigint(20) NOT NULL,
  `key_id` bigint(20) DEFAULT NULL,
  `value` varchar(45) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `laptop_property_value_range` (
  `id` int(11) NOT NULL,
  `key_id` varchar(45) DEFAULT NULL,
  `range` varchar(45) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;