<?php
class DbSearchCategory {
  public static function insert($id) {
    $sql = 'INSERT INTO `wj_search`.`category`(`id`, `alphabet_index`)'
      .' VALUES(?, ?)';
    Db::execute($sql, $id, 'a');
  }
}