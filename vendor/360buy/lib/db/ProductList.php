<?php
class ProductList extends Db {
  public function insert($categoryId, $propertyValueId, $page, $html) {
    $connection = new DbConnection;
    $sql = "select * from `list` where category_id=$categoryId and "
      .$this->getFilter('property_value_id', $propertyValueId, true)." and page=$page";
    $row = $connection->getRow($sql);
    if ($row === false) {
      $sql = "insert into `list`(page, property_value_id, category_id, html)"
        ." values($page, $propertyValueId, $categoryId, ?)";
      $connection->executeNonQuery($sql, array(gzcompress($html)));
    }
  }
}