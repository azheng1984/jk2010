<?php
class DbProduct {
  public static function getPrice($tablePrefix, $id) {
    return Db::getRow(
      'SELECT id, lowest_price_x_100, highest_price_x_100,'
        .'list_lowest_price_x_100  FROM '.$tablePrefix.'_product'
        .' WHERE id = ?', $id
    );
  }

  public static function getImageMeta($tablePrefix, $id) {
    return Db::getRow(
      'SELECT id, image_md5, image_last_modified FROM '.$tablePrefix.'_product'
      .' WHERE id = ?', $id
    );
  }

  public static function getContentMd5AndSaleRankByMerchantProductId(
    $tablePrefix, $merchantProductId
  ) {
    return Db::getRow(
      'SELECT id, content_md5, sale_rank FROM '.$tablePrefix.'_product'
      .' WHERE merchant_product_id = ?', $merchantProductId
    );
  }

  public static function insert(
    $tablePrefix,
    $merchantProductId,
    $uri,
    $categoryId,
    $title,
    $description,
    $contentMd5,
    $saleRank,
    $lowestPriceX100 = null,
    $highestPriceX100 = null,
    $lowestListPriceX100 = null
  ) {
    $sql = 'INSERT INTO '.$tablePrefix.'_product('
      .'merchant_product_id, $uri, category_id, title, description, content_md5,'
      .' sale_rank, lowest_price_x_100, highest_price_x_100,'
      .'list_lowest_price_x_100, index_time)'
      .' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
    Db::execute(
      $sql,
      $merchantProductId,
      $uri,
      $categoryId,
      $title,
      $description,
      $contentMd5,
      $saleRank,
      $lowestPriceX100,
      $highestPriceX100,
      $lowestListPriceX100
    );
    return DbConnection::get()->lastInsertId();
  }

  public static function updatePrice(
    $tablePrefix,
    $id,
    $lowestPriceX100,
    $highestPriceX100 = null,
    $lowestListPriceX100 = null
  ) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET '
      .'lowest_price_x_100 = ?,'
      .'highest_price_x_100 = ?,'
      .'lowest_list_price_x_100 = ?'
      .' WHERE id = ?',
      $lowestPriceX100, $highestPriceX100, $lowestListPriceX100, $id
    );
  }

  public static function updateSaleRank($tablePrefix, $id, $saleRank) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET sale_rank = ? WHERE id = ?',
      $saleRank, $id
    );
  }

  public static function updateFlag($tablePrefix, $id) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET is_updated = 1 WHERE id = ?', $id
    );
  }

  public static function update(
    $tablePrefix,
    $id,
    $categoryId,
    $title,
    $description,
    $contentMd5,
    $saleRank
  ) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET'
      .' category_id = ?,  title = ?, description = ?, content_md5 = ?,'
      .'sale_rank, is_updated = 1 WHERE id = ?',
      $categoryId, $title, $description, $contentMd5, $saleRank, $id
    );
  }

  public static function updateImageMeta(
    $tablePrefix, $id, $imageMd5, $imageLastModified
  ) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET'
      .' image_md5 = ?,  image_last_modified = ? WHERE id = ?',
      $imageMd5, $imageLastModified, $id
    );
  }

  public static function expireAll($tablePrefix) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET is_updated = 0'
    );
  }

  public static function createTable($tablePrefix) {
    if (
      Db::getColumn('SHOW TABLES LIKE ?', $tablePrefix.'_product') === false
    ) {
      $sql = "CREATE TABLE `".$tablePrefix."_product` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `merchant_product_id` bigint(20) NOT NULL,
        `category_id` int(11) unsigned NOT NULL,
        `title` varchar(511) NOT NULL,
        `description` text,
        `content_md5` varchar(32) DEFAULT NULL,
        `image_md5` varchar(32) DEFAULT NULL,
        `image_last_modified` varchar(29) DEFAULT NULL,
        `sale_rank` int(11) unsigned NOT NULL,
        `lowest_price_x_100` int(11) unsigned DEFAULT NULL,
        `highest_price_x_100` int(11) unsigned DEFAULT NULL,
        `list_lowest_price_x_100` int(11) unsigned DEFAULT NULL,
        `index_time` datetime NOT NULL,
        `is_updated` tinyint(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`),
        UNIQUE KEY `merchant_product_id` (`merchant_product_id`) USING BTREE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      Db::execute($sql);
    }
  }
}