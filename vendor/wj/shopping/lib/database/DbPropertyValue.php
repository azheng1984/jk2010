<?php
class DbPropertyValue {
  public static function getByName($keyId, $name) {
    return Db::getRow(
      'SELECT * FROM property_value WHERE key_id = ? AND name = ?',
      $keyId, $name
    );
  }

  public static function get($valueId) {
    return Db::getRow('SELECT * FROM property_value WHERE id = ?', $valueId);
  }
}