<?php
class DbPropertyValue {
  public static function getByName($keyId, $name) {
    $sql = 'SELECT * FROM property_value WHERE key_id = ? AND `name` = ?';
    return Db::getRow($sql, $keyId, $name);
  }

  public static function get($valueId) {
    $sql = 'SELECT * FROM property_value WHERE id = ?';
    return Db::getRow($sql, $valueId);
  }
}