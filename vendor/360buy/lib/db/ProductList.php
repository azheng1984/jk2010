<?php
class ProductList extends Db {
  public function insert($categoryId, $propertyValueId, $page, $html) {
    $sql = "select * from list where category_id=$categoryId and "
      .$this->getFilter('property_value_id', $propertyValueId, true)
      ." and page=$page";
    $row = $this->getRow($sql);
    if ($row === false) {
      $sql = "insert into list(page, property_value_id, category_id, html)"
        ." values($page, ?, $categoryId, ?)";
      $this->executeNonQuery($sql, array($propertyValueId, gzcompress($html)));
    }
  }
}