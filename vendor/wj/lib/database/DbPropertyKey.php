<?php
class DbPropertyKey {
  public static function get($id) {
    $sql = 'SELECT * FROM property_key WHERE id = ?';
    return Db::getRow($sql, $id);
  }

  public static function getByName($categoryId, $name) {
    $sql = 'SELECT * FROM property_key WHERE category_id = ? AND `name` = ?';
    return Db::getRow($sql, $categoryId, $name);
  }
}