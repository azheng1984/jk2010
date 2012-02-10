<?php
class DbPropertyValue {
  public static function get($id) {
    $sql = 'SELECT * FROM property_value  WHERE id = ?';
    return Db::getRow($sql, $id);
  }

  public static function getByName($keyId, $name) {
    $sql = 'SELECT * FROM property_value WHERE key_id=? AND `name` = ?';
    return Db::getRow($sql, $keyId, $name);
  }
}