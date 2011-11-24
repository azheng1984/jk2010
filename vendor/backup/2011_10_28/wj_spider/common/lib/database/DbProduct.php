<?php
class DbProduct {
  public static function insert($id, $categoryId, $html) {
    $sql = 'REPLACE INTO product(id, category_id, html) VALUES(?, ?, ?)';
    Db::executeNonQuery($sql, array($id, $categoryId, gzcompress($html)));
  }

  public static function addProperty($id, $propertyValueId) {
    $sql = 'REPLACE INTO product_property_value(product_id, property_value_id)'
      .' VALUES(?, ?)';
    Db::executeNonQuery($sql, array($id, $propertyValueId));
  }
}