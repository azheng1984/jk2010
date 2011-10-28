<?php
class DbProduct {
  public static function getPrice($tablePrefix, $id) {
    return Db::getRow(
      'SELECT lowest_price, highest_price FROM '.$tablePrefix
      .'_product WHERE id = ?', $id
    );
  }

  public static function getUpdateInfo($tablePrefix, $id) {
    return Db::getColumn(
      'SELECT content_md5, image_md5, image_last_modified FROM '
      .$tablePrefix.'_product WHERE id = ?', $id
    );
  }

  public static function insert(
    $tablePrefix,
    $id,
    $categoryId,
    $lowestPrice,
    $highestPrice,
    $title,
    $propertyList,
    $description,
    $contentMd5,
    $imageMd5,
    $imageLastModified
  ) {
    $sql = 'INSERT INTO '.$tablePrefix.'_product(id, category_id, title,'
      .' property_list, description, content_md5, image_md5,'
      .' image_last_modified, lowest_price, highest_price, index_time)'
      .' VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())';
    Db::execute(
      $sql,
      $id,
      $categoryId,
      $title,
      $propertyList,
      $description,
      $contentMd5,
      $imageMd5,
      $imageLastModified,
      $lowestPrice,
      $highestPrice
    );
  }

  public static function updatePrice(
    $tablePrefix, $id, $lowestPrice, $highestPrice
  ) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET lowest_price = ?, highest_price = ?'
      .' WHERE id = ?',
      $lowestPrice, $highestPrice, $id
    );
  }

  public static function updateContent(
    $tablePrefix, $id, $categoryId, $title, $description, $propertyList, $etag
  ) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product SET'
      .' category_id = ?,  title = ?, description = ?,'
      .' property_list = ?, etag = ? WHERE id = ?)',
      $categoryId, $title, $description, $propertyList, $etag, $id
    );
  }

  public static function createTable($tablePrefix) {
    if (
      Db::getColumn('show tables like ?', $tablePrefix.'_product') === false
    ) {
      $sql = "CREATE TABLE `".$tablePrefix."_product` (
        `id` int(11) unsigned NOT NULL,
        `category_id` int(11) unsigned NOT NULL,
        `title` varchar(511) DEFAULT NULL,
        `property_list` text,
        `description` text NOT NULL,
        `content_md5` varchar(45) DEFAULT NULL,
        `image_md5` varchar(45) DEFAULT NULL,
        `image_last_modified` datetime DEFAULT NULL,
        `lowest_price` decimal(9,2) DEFAULT NULL,
        `highest_price` decimal(9,2) DEFAULT NULL,
        `index_time` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `category_id` (`category_id`) USING BTREE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      Db::execute($sql);
    }
  }
}