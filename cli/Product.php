<?php
class Product {
  public function insert($id, $categoryId, $title, $html) {
    $conncetion = new DatabaseConnection;
    $sql = "select * from `product` where id=$id";
    $row = $conncetion->getRow($sql);
    if ($row === false) {
      $sql = "insert into `product`(id, category_id, `title`, html)"
        ." values($id, $categoryId, '$title', ?)";
      $conncetion->executeNonQuery($sql, array(gzcompress($html)));
    }
  }
}