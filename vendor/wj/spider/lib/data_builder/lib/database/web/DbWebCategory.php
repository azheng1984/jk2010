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

  public static function insert($name, $alphabetIndex) {
    Db::execute('INSERT INTO `wj_web`.`category`'
      .'(`name`, alphabet_index) VALUES(?, ?)', $name, $alphabetIndex);
    return DbConnection::get()->lastInsertId();
  }
}