<?php
class DbPropertyValue {
  public static function getByValueId($valueId) {
    $sql = 'SELECT * FROM wj_web.property_value  WHERE id = ?';
    return Db::getRow($sql, $valueId);
  }

  public static function getByKeyId($keyId) {
    $sql = 'SELECT * FROM wj_web.property_key  WHERE id = ?';
    return Db::getRow($sql, $keyId);
  }

  public static function getKeyByName($categoryId, $name) {
    $sql = 'SELECT * FROM wj_web.property_key WHERE category_id=? AND `key` = ?';
    return Db::getRow($sql, $categoryId, $name);
  }

  public static function getValueByName($keyId, $name) {
    $sql = 'SELECT * FROM wj_web.property_value WHERE key_id=? AND `value` = ?';
    return Db::getRow($sql, $keyId, $name);
  }
  

  public static function getValueIdList($keyId) {
    $sql = 'SELECT id FROM wj_web.property_value  WHERE `key_id` = ?';
    return Db::getAll($sql, $keyId);
  }

  public static function getList() {
    
  }
}