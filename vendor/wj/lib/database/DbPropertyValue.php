<?php
class DbPropertyValue {
  public static function getByName($keyId, $name) {
    $sql = 'SELECT * FROM property_value WHERE key_id = ? AND `name` = ?';
    return Db::getRow($sql, $keyId, $name);
  }
}