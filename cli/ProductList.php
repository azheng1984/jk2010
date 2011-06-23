<?php
class ProductList extends Database {
  public function insert($categoryId, $property, $page, $html) {
    $connection = new DatabaseConnection;
    $sql = "select * from `list` where category_id=$categoryId and "
      .$this->getFilter('property', $property)." and page=$page";
    $row = $connection->getRow($sql);
    if ($row === false) {
      $sql = "insert into `list`(page, property, category_id, html)"
        ." values($page, ?, $categoryId, ?)";
      $connection->executeNonQuery($sql, array($property, gzcompress($html)));
    }
  }
}