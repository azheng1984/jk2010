<?php
class Product extends Db {
  public function insert($id, $categoryId, $title, $html) {
    $sql = "select * from product where id=$id";
    $row = $this->getRow($sql);
    if ($row === false) {
      $sql = "insert into product(id, category_id, title, html)"
        ." values($id, $categoryId, '$title', ?)";
      $this->executeNonQuery($sql, array(gzcompress($html)));
    }
  }
}