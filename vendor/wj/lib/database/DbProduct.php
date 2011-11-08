<?php
class DbProduct {
  public static function getIndex($id) {
    return Db::getRow(
      'SELECT * FROM global_product_index WHERE product_id = ?', $id
    );
  }

  public static function get($id) {
    return Db::getRow(
      'SELECT * FROM wj_web.product WHERE id = ?', $id
    );
  }

  public static function getList($tablePrefix) {
    return Db::getAll('SELECT * FROM '.$tablePrefix.'_product LIMIT 10');
  }

  public static function getPropertyListById($tablePrefix, $id) {
    $table = $tablePrefix.'_property_key';
    $sql = 'SELECT * FROM '.$table.' WHERE id = ';
  }
}