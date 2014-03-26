CREATE TABLE `document` (
  `id` int(11) unsigned NOT NULL,
  `update_time` datetime NOT NULL,
  `title` varchar(255) NOT NULL,
  `url_name` varchar(127) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `default_image_url` int(11) unsigned DEFAULT NULL,
  `time` varchar(19) DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `people` varchar(255) DEFAULT NULL,
  `source_id` int(11) unsigned NOT NULL,
  `source_url` text NOT NULL,
  `list_page_id` int(11) unsigned DEFAULT NULL,
  `related_documents_cache` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;