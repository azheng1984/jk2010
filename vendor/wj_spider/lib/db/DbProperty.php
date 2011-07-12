<?php
class DbProperty {
  public static function getOrNewKeyId($categoryId, $name) {
    $sql = 'select * from property_key where category_id = ? and `key` = ?';
    $row = Db::getRow($sql, array($categoryId, $name));
    if ($row === false) {
      $sql = 'insert into property_key(`key`, category_id) values(?, ?)';
      Db::executeNonQuery($sql, array($name, $categoryId));
      return Db::getLastInsertId();
    }
    return $row['id'];
  }

  public static function getOrNewValueId($keyId, $name) {
    $sql = 'select * from property_value where key_id = ? and `value` = ?';
    $row = Db::getRow($sql, array($keyId, $name));
    if ($row === false) {
      $sql = 'insert into property_value(key_id, `value`) values(?, ?)';
      Db::executeNonQuery($sql, array($keyId, $name));
      return Db::getLastInsertId();
    }
    return $row['id'];
  }
}