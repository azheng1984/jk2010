<?php
class DbProperty {
  public static function getOrNewKeyId($tablePrefix, $key) {
    $sql = 'SELECT id FROM '.$tablePrefix.'_property_key WHERE `key` = ?';
    $id = Db::getColumn($sql, $key);
    if ($id === false) {
      $sql = 'INSERT INTO '.$tablePrefix.'(`key`) VALUES(?)';
      Db::execute($sql, $key);
      return DbConnection::get()->getLastInsertId();
    }
    return $id;
  }

  public static function getOrNewValueId($tablePrefix, $keyId, $value) {
    $sql = 'SELECT id FROM '.$tablePrefix.'_property_value'
      .' WHERE `value` = ? AND key_id = ?';
    $id = Db::getRow($sql, $value, $keyId);
    if ($id === false) {
      $sql = 'INSERT INTO '.$tablePrefix.'_property_value(key_id, `value`)'
        .' VALUES(?, ?)';
      Db::execute($sql, $keyId, $value);
      return DbConnection::get()->getLastInsertId();
    }
    return $id;
  }
}