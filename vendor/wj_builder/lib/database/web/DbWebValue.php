<?php
class DbWebValue {
  public static function get($keyId, $name) {
    return Db::getRow(
      'SELECT * FROM `wj_web`.`property_value`'
      .' WHERE key_id = ? AND `name` = ?',
      $keyId, $name
    );
  }

  public static function insert($keyId, $name) {
    Db::execute(
      'INSERT INTO `wj_web`.`property_value`(`key_id`, `name`)'
      .' VALUES(?, ?)',
      $keyId, $name
    );
    return DbConnection::get()->lastInsertId();
  }
}