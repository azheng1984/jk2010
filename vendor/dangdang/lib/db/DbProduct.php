<?php
class DbProduct {
  public static function insert($id, $categoryId, $html) {
    $sql = "select * from product where id=$id";
    $row = Db::getRow($sql);
    if ($row === false) {
      $sql = "insert into product(id, category_id, html)"
        ." values($id, $categoryId, ?)";
      Db::executeNonQuery($sql, array(gzcompress($html)));
    }
  }

  public static function updatePrice($id, $listPrice, $price, $promotionPrice) {
    $sql = 'update product set list_price='
      ."$listPrice, price=$price, promotion_price=$promotionPrice"
      ." where id=$id";
    Db::executeNonQuery($sql);
  }

  public static function addProperty($id, $propertyValueId) {
    $sql = "select * from product-property_value where product_id=$id".
      " and property_value_id=$propertyValueId";
    $row = Db::getRow($sql);
    if ($row === false) {
      $sql = "insert into product(product_id, , property_value_id)"
        ." values($id, $propertyValueId)";
      Db::executeNonQuery($sql);
    }
  }
}