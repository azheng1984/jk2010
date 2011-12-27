<?php
class DbWebQuery {
  public static function insert($categoryId, $name, $amount) {
    $sql = 'INSERT INTO `wj_web`.`query`(category_id, `name`, amount)'
      .' VALUES(?, ?, ?)';
    Db::execute($sql, $categoryId, $name, $amount);
    return DbConnection::get()->lastInsertId();
  }
}