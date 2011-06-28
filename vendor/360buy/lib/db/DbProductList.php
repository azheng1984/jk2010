<?php
class DbProductList {
  public static function insert($categoryId, $propertyValueId, $page, $html) {
    $sql = "select * from list where category_id=$categoryId and "
      .Db::getFilter('property_value_id', $propertyValueId, true)
      ." and page=$page";
    $row = Db::getRow($sql);
    if ($row === false) {
      $sql = "insert into list(page, property_value_id, category_id, html)"
        ." values($page, ?, $categoryId, ?)";
      Db::executeNonQuery($sql, array($propertyValueId, gzcompress($html)));
    }
  }
}