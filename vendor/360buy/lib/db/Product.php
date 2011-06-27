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

  public function updatePrice($id, $listPrice, $price, $promotionPrice) {
    $sql = 'update product set list_price='
      ."$listPrice, price=$price, promotion_price=$promotionPrice"
      ." where id=$id";
    $this->executeNonQuery($sql);
  }
}