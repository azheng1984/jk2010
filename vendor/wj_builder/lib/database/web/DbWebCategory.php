<?php
class DbWebCategory {
  public static function get($name) {
    return Db::getRow(
      'SELECT * FROM `wj_web`.`category` WHERE `name` = ?', $name
    );
  }

  public static function getAll() {
    return Db::getAll('SELECT * FROM `wj_web`.`category`');
  }

  public static function insert($name) {
    Db::execute('INSERT INTO `wj_web`.`category`(`name`) VALUES(?)', $name);
    return DbConnection::get()->lastInsertId();
  }
}