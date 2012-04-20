<?php
class DbWebValue {
  public static function get($keyId, $name) {
    return Db::getRow(
      'SELECT * FROM `wj_web`.`property_value`'
      .' WHERE key_id = ? AND `name` = ?',
      $keyId, $name
    );
  }

  public static function getList($keyId) {
    return Db::getAll(
      'SELECT * FROM `wj_web`.`property_value` WHERE key_id = ?', $keyId
    );
  }

  public static function insert($keyId, $alphabetIndex, $name) {
    Db::execute(
      'INSERT INTO `wj_web`.`property_value`(`key_id`, alphabet_index, `name`)'
      .' VALUES(?, ?, ?)',
      $keyId, $alphabetIndex, $name
    );
    return DbConnection::get()->lastInsertId();
  }
}