<?php
class DbProperty {
  public static function getByValueId($valueId) {
    $sql = 'SELECT * FROM wj_web.property_value  WHERE id = ?';
    return Db::getRow($sql, $valueId);
  }

  public static function getList($tablePrefix) {
    $sql = 'SELECT * FROM '.$tablePrefix.'_property_key';
    $results = Db::getAll($sql);
    foreach ($results as &$key) {
      $sql = 'SELECT * FROM '.$tablePrefix.'_property_value WHERE key_id = ? order by rank';
      $key['values'] = Db::getAll($sql, $key['id']);
    }
    return $results;
  }

  public static function getKeyByName($tablePrefix, $name) {
    $sql = 'SELECT * FROM '.$tablePrefix.'_property_key WHERE `key` = ?';
    return Db::getRow($sql, $name);
  }

  public static function getValueByKeyIdAndName($tablePrefix, $keyId, $name) {
    $sql = 'SELECT * FROM '.$tablePrefix.'_property_value WHERE `key_id` = ? AND `value` = ?';
    return Db::getRow($sql, $keyId, $name);
  }
}