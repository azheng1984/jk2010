<?php
class DbSearchQuery {
  public static function insert($id, $name, $amount) {
    $sql = 'INSERT INTO `wj_search`.`query`(id, `name`, amount)'
      .' VALUES(?, ?, ?)';
    Db::execute($sql, $id, $name, $amount);
  }
}