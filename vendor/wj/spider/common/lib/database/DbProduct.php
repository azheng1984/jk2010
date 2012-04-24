<?php
class DbProduct {
  public static function get($tablePrefix, $id, $select = '*') {
    return Db::getRow(
      'SELECT '.$select.' FROM '.$tablePrefix.'_product'
        .' WHERE id = ?', $id
    );
  }

  public static function insert($tablePrefix, $row) {
    $row['index_time'] = 'NOW()';
    Db::insert($tablePrefix.'_product', $row);
    return Db::getLastInsertId();
  }

  public static function updateRow($tablePrefix, $columnList, $id) {
    Db::update($tablePrefix.'_product', $columnList, 'id = ?', $id);
  }

  public static function tryCreateTable($tablePrefix) {
    if (
      Db::getColumn('SHOW TABLES LIKE ?', $tablePrefix.'_product') === false
    ) {
      $sql = "CREATE TABLE `".$tablePrefix."_product` (
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
        UNIQUE KEY `merchant_product_id` (`merchant_product_id`) USING BTREE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      Db::execute($sql);
    }
  }
}