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

  public static function getContentInfo($tablePrefix, $merchantProductId) {
    return Db::getRow(
      'SELECT id, content_md5 FROM '.$tablePrefix.'_product'
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
    $lowestPrice,
    $highestPrice
  ) {
    $sql = 'INSERT INTO '.$tablePrefix.'_product('
      .'merchant_product_id, category_id, title, description, content_md5,'
      .' lowest_price, highest_price, index_time)'
      .' VALUES(?, ?, ?, ?, ?, ?, ?, NOW())';
    Db::execute(
      $sql,
      $merchantProductId,
      $categoryId,
      $title,
      $description,
      $contentMd5,
      $lowestPrice,
      $highestPrice
    );
    return DbConnection::get()->getLastInsertId();
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

  public static function updateContent(
    $tablePrefix, $id, $categoryId, $title,
    $description, $propertyList, $contentMd5
  ) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET'
      .' category_id = ?,  title = ?, description = ?, content_md5 = ?'
      .' WHERE id = ?)',
      $categoryId, $title, $description, $contentMd5, $id
    );
  }

  public static function updateImageInfo(
    $tablePrefix, $id, $imageMd5, $imageLastModified
  ) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET'
      .' image_md5 = ?,  image_last_modified = ? WHERE id = ?)',
      $imageMd5, $imageLastModified, $id
    );
  }

  public static function createTable($tablePrefix) {
    if (
      Db::getColumn('SHOW TABLES LIKE ?', $tablePrefix.'_product') === false
    ) {
      $sql = "CREATE TABLE `".$tablePrefix."_product` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `merchant_product_id` int(11) unsigned NOT NULL,
        `category_id` int(11) unsigned NOT NULL,
        `title` varchar(511) DEFAULT NULL,
        `description` text NOT NULL,
        `content_md5` varchar(45) DEFAULT NULL,
        `image_md5` varchar(45) DEFAULT NULL,
        `image_last_modified` datetime DEFAULT NULL,
        `lowest_price` decimal(9,2) DEFAULT NULL,
        `highest_price` decimal(9,2) DEFAULT NULL,
        `index_time` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `category_id` (`category_id`) USING BTREE
      ) ENGINE=InnoDB AUTO_INCREMENT=1000734745 DEFAULT CHARSET=utf8";
      Db::execute($sql);
    }
  }
}