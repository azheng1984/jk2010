<?php
class DbPrice {
  public static function insert(
    $productId, $price, $listPrice, $promotionPrice
  ) {
    $sql = 'replace into price(product_id, price, list_price, promotion_price)'
      .' values(?, ?, ?, ?)';
    Db::executeNonQuery($sql, array(
      $productId, $price, $listPrice, $promotionPrice
    ));
  }
}