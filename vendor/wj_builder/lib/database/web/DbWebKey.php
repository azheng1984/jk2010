<?php
class DbWebKey {
  public static function get($categoryId, $name) {
    return Db::getRow(
      'SELECT * FROM `wj_web`.`property_key`'
      .' WHERE category_id = ? AND `name` = ?',
      $categoryId, $name
    );
  }

  public static function getList($categoryId) {
    return Db::getAll(
      'SELECT * FROM `wj_web`.`property_key` WHERE category_id = ?',
      $categoryId
    );
  }

  public static function insert($categoryId, $name, $mvaIndex) {
    Db::execute(
      'INSERT INTO `wj_web`.`property_key`(`category_id`, `name`, `mva_index`)'
      .' VALUES(?, ?, ?)',
      $categoryId, $name, $mvaIndex
    );
    return DbConnection::get()->lastInsertId();
  }
}