<?php
class DbLog {
  public static function insert($tablePrefix, $productId, $type) {
    Db::execute(
      'INSERT INTO '.$tablePrefix.'_log(product_id, type) VALUE(?, ?)',
      $productId, $type
    );
  }
}