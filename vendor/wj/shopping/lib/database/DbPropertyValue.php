<?php
class DbPropertyValue {
  public static function getByKeyIdAndName($keyId, $name) {
    return Db::getRow(
      'SELECT * FROM property_value WHERE key_id = ? AND name = ?',
      $keyId, $name
    );
  }

  public static function get($id) {
    return Db::getRow('SELECT * FROM property_value WHERE id = ?', $id);
  }
}