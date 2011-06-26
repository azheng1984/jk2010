<?php
class Product extends Db {
  public function insert($id, $categoryId, $html) {
    $sql = "select * from product where id=$id";
    $row = $this->getRow($sql);
    if ($row === false) {
      $sql = "insert into product(id, category_id, html)"
        ." values($id, $categoryId, ?)";
      $this->executeNonQuery($sql, array(gzcompress($html)));
    }
  }
}