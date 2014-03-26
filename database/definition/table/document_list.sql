CREATE TABLE `[category]_document_list` (
  `id` int(11) NOT NULL,
  `update_time` datetime NOT NULL,
  `page` int(11) NOT NULL,
  `content_cache` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_UNIQUE` (`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;