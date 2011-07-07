<?php
class DbProduct {
  public static function insert($id, $categoryId, $html) {
    $sql = "replace into product(id, category_id, html) values(?, ?, ?)";
    Db::executeNonQuery($sql, array($id, $categoryId, gzcompress($html)));
  }

  public static function addProperty($id, $propertyValueId) {
    $sql = "replace into product_property_value(product_id, property_value_id)"
      ." values(?, ?)";
    Db::executeNonQuery($sql, array($id, $propertyValueId));
  }
}