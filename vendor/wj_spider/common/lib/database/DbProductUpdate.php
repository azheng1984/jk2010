<?php
class DbProductUpdate {
  public static function insert($tablePrefix, $productId, $type) {
    Db::execute(
      'INSERT INTO '.$tablePrefix
      .'_product_update(product_id, type) VALUE(?, ?)',
      $productId, $type
    );
  }
}