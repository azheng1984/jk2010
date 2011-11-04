<?php
class DbProduct {
  public static function getPrice($tablePrefix, $merchantProductId) {
    return Db::getRow(
      'SELECT id, lowest_price, highest_price FROM '.$tablePrefix.'_product'
      .' WHERE merchant_product_id = ?', $merchantProductId
    );
  }

  public static function getImageInfo($tablePrefix, $merchantProductId) {
    return Db::getRow(
      'SELECT id, image_md5, image_last_modified FROM '.$tablePrefix.'_product'
      .' WHERE merchant_product_id = ?', $merchantProductId
    );
  }

  public static function getContentMd5AndSaleIndex($tablePrefix, $merchantProductId) {
    return Db::getRow(
      'SELECT id, content_md5, sale_index FROM '.$tablePrefix.'_product'
      .' WHERE merchant_product_id = ?', $merchantProductId
    );
  }

  public static function insert(
    $tablePrefix,
    $merchantProductId,
    $categoryId,
    $title,
    $description,
    $contentMd5,
    $saleIndex,
    $lowestPrice = null,
    $highestPrice = null
  ) {
    $sql = 'INSERT INTO '.$tablePrefix.'_product('
      .'merchant_product_id, category_id, title, description, content_md5,'
      .' sale_index, lowest_price, highest_price, index_time)'
      .' VALUES(?, ?, ?, ?, ?, ?, ?, ?, NOW())';
    Db::execute(
      $sql,
      $merchantProductId,
      $categoryId,
      $title,
      $description,
      $contentMd5,
      $saleIndex,
      $lowestPrice,
      $highestPrice
    );
    return DbConnection::get()->lastInsertId();
  }

  public static function updatePrice(
    $tablePrefix, $id, $lowestPrice, $highestPrice = null
  ) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET lowest_price = ?, highest_price = ?'
      .' WHERE id = ?',
      $lowestPrice, $highestPrice, $id
    );
  }

  public static function updateSaleIndex($tablePrefix, $id, $saleIndex) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET sale_index = ? WHERE id = ?',
      $saleIndex, $id
    );
  }

  public static function updateFlag($tablePrefix, $id) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET is_update = 1 WHERE id = ?', $id
    );
  }

  public static function updateContent(
    $tablePrefix, $id, $categoryId, $title,
    $description, $contentMd5
  ) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET'
      .' category_id = ?,  title = ?, description = ?, content_md5 = ?,'
      .' is_update = 1 WHERE id = ?',
      $categoryId, $title, $description, $contentMd5, $id
    );
  }

  public static function updateImageInfo(
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
      'UPDATE '.$tablePrefix.'_product SET is_update = 0'
    );
  }

  public static function createTable($tablePrefix) {
      if (
      Db::getColumn('SHOW TABLES LIKE ?', $tablePrefix.'_product') === false
    ) {
      $sql = "CREATE TABLE `".$tablePrefix."_product` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `web_product_id` int(11) unsigned NOT NULL DEFAULT '0',
        `merchant_product_id` bigint(20) unsigned NOT NULL,
        `category_id` int(11) unsigned NOT NULL,
        `title` varchar(511) NOT NULL,
        `description` text,
        `content_md5` varchar(32) DEFAULT NULL,
        `image_md5` varchar(32) DEFAULT NULL,
        `image_last_modified` varchar(29) DEFAULT NULL,
        `sale_index` int(11) unsigned NOT NULL,
        `lowest_price` decimal(9,2) DEFAULT NULL,
        `highest_price` decimal(9,2) DEFAULT NULL,
        `index_time` datetime NOT NULL,
        `is_update` tinyint(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`),
        UNIQUE KEY `merchant_product_id` (`merchant_product_id`) USING BTREE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      Db::execute($sql);
    }
  }
}