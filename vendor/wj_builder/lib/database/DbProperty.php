<?php
class DbProperty {
  public static function getList($merchantProductId) {
    return Db::getAll(
      'SELECT * FROM `jingdong`.`food_product_property`'
      .' WHERE merchant_product_id = ?',
      $merchantProductId
    );
  }

  public static function getKey($keyId) {
    return Db::getRow(
      'SELECT * FROM `jingdong`.`food_property_key` WHERE id = ?', $keyId
    );
  }

  public static function getValue($valueId) {
    return Db::getRow(
      'SELECT * FROM `jingdong`.`food_property_value` WHERE id = ?', $valueId
    );
  }

  public static function getWebKey($categoryId, $key) {
    return Db::getRow(
      'SELECT * FROM `wj_web`.`property_key` WHERE category_id = ? AND `key` = ?',
      $categoryId, $key
    );
  }

  public static function insertIntoWebKey($categoryId, $key) {
    Db::execute(
      'INSERT INTO `wj_web`.`property_key`(`category_id`, `key`)'
      .' VALUES(?, ?)',
      $categoryId, $key
    );
    return array(
      'id' => DbConnection::get()->lastInsertId(),
      'category_id' => $categoryId,
      'key' => $key
    );
  }

  public static function getWebValue($keyId, $value) {
    return Db::getRow(
      'SELECT * FROM `wj_web`.`property_value` WHERE key_id = ? AND `value` = ?',
      $keyId, $value
    );
  }

  public static function insertIntoWebValue($keyId, $value) {
    Db::execute(
      'INSERT INTO `wj_web`.`property_value`(`key_id`, `value`)'
      .' VALUES(?, ?)',
      $keyId, $value
    );
    return array(
      'id' => DbConnection::get()->lastInsertId(),
      'key_id' => $keyId,
      'value' => $value
    );
  }
}