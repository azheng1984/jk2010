<?php
class DbProperty {
  public static function getOrNewKeyId($categoryId, $name) {
    $sql = 'SELECT id FROM property_key WHERE category_id = ? AND `key` = ?';
    $row = Db::getRow($sql, array($categoryId, $name));
    if ($row === false) {
      $sql = 'INSERT INTO property_key(`key`, category_id) VALUES(?, ?)';
      Db::executeNonQuery($sql, array($name, $categoryId));
      return Db::getLastInsertId();
    }
    return $row['id'];
  }

  public static function getOrNewValueId($keyId, $name) {
    $sql = 'SELECT id FROM property_value WHERE key_id = ? AND `value` = ?';
    $row = Db::getRow($sql, array($keyId, $name));
    if ($row === false) {
      $sql = 'INSERT INTO property_value(key_id, `value`) VALUES(?, ?)';
      Db::executeNonQuery($sql, array($keyId, $name));
      return Db::getLastInsertId();
    }
    return $row['id'];
  }
}