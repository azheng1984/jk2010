<?php
class DbProduct {
  public static function getPrice($tablePrefix, $id) {
    return Db::getRow(
      'SELECT lowest_price, highest_price FROM '.$tablePrefix
      .'_product WHERE id = ?', $id
    );
  }

  public static function getEtag($tablePrefix, $id) {
    return Db::getColumn(
      'SELECT etag FROM '.$tablePrefix.'_product WHERE id = ?', $id
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
    $etag
  ) {
    $sql = 'INSERT INTO '.$tablePrefix.'_product(id, category_id, title, property_list,'
      .' description, etag, lowest_price, highest_price, index_time)'
      .' VALUES(?, ?, ?, ?, ?, ?, ?, ?, NOW())';
    Db::execute(
      $sql,
      $id,
      $categoryId,
      $title,
      $propertyList,
      $description,
      $etag,
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
}