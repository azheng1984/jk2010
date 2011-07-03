<?php
class DbProductList {
  public static function insert(
    $categoryId, $propertyValueId, $url, $page, $html
  ) {
    $sql = "select * from product_list where category_id=$categoryId and "
      .Db::getFilter('property_value_id', $propertyValueId)." and page=$page";
    $row = Db::getRow($sql);
    if ($row === false) {
      $sql = "insert into product_list"
        ."(url, page, property_value_id, category_id, html)"
        ." values(?, $page, ?, $categoryId, ?)";
      Db::executeNonQuery($sql, array(
        $url, $propertyValueId, gzcompress($html)
      ));
    }
  }
}