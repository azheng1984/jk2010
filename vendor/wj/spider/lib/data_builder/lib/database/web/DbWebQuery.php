<?php
class DbWebQuery {
  public static function insert(
    $categoryId, $alphabetIndex, $name, $pinyin, $amount
  ) {
    $sql = 'INSERT INTO `wj_web`.`query`(category_id, alphabet_index, `name`,'
      .' `pinyin`, amount) VALUES(?, ?, ?, ?, ?)';
    Db::execute($sql, $categoryId, $alphabetIndex, $name, $pinyin, $amount);
    return DbConnection::get()->lastInsertId();
  }
}