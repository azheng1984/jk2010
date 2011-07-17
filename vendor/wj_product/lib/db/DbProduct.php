<?php
class DbProduct {
  public static function getIndex($id) {
    return Db::getRow(
      'SELECT * FROM global_product_index WHERE product_id = ?', $id
    );
  }

  public static function get($tablePrefix, $id) {
    return Db::getRow(
      'SELECT * FROM '.$tablePrefix.'_product WHERE id = ?', $id
    );
  }
}