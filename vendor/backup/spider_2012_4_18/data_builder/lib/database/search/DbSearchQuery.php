<?php
class DbSearchQuery {
  public static function insert($id, $alphabetIndex, $name, $pinyin, $amount) {
    $sql = 'INSERT INTO `wj_search`.`query`(id, `name`, amount)'
      .' VALUES(?, ?, ?)';
    $sql = 'INSERT INTO `wj_search`.`query`(id, alphabet_index, `name`,'
      .' `pinyin`, amount) VALUES(?, ?, ?, ?, ?)';
    Db::execute($sql, $id, $alphabetIndex, $name, $pinyin, $amount);
  }
}