<?php
class DbProperty {
  public static function getByValueId($tablePrefix, $valueId) {
    $keyTable = $tablePrefix.'_property_key';
    $valueTable = $tablePrefix.'_property_value';
    $sql = 'SELECT * FROM '.$valueTable.' table_value LEFT JOIN '
      .$keyTable.' table_key ON table_key.id = table_value.key_id'
      .' WHERE table_value.id = ?';
    return Db::getRow($sql, $valueId);
  }

  public static function getList($categoryId) {
    $sql = 'SELECT * FROM laptop_property_key';
    $results = Db::getAll($sql);
    foreach ($results as &$key) {
      $sql = 'SELECT * FROM laptop_property_value WHERE key_id = ?';
      $key['values'] = Db::getAll($sql, $key['id']);
    }
    return $results;
  }

  public static function getKeyByName($name) {
    $sql = 'SELECT * FROM laptop_property_key WHERE `key` = ?';
    return Db::getRow($sql, $name);
  }

  public static function getValueByKeyIdAndName($keyId, $name) {
    $sql = 'SELECT * FROM laptop_property_value WHERE `key_id` = ? AND `value` = ?';
    return Db::getRow($sql, $keyId, $name);
  }
}