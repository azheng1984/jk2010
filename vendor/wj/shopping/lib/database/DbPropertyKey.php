<?php
class DbPropertyKey {
  public static function get($id) {
    return Db::getRow('SELECT * FROM property_key WHERE id = ?', $id);
  }

  public static function getByCatgoryIdAndName($categoryId, $name) {
    return Db::getRow(
      'SELECT * FROM property_key WHERE category_id = ? AND name = ?',
      $categoryId, $name
    );
  }
}