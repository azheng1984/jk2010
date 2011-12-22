<?php
class DbSearchKey {
  public static function insert($id, $categoryId) {
    $sql = 'INSERT INTO `wj_search`.`property_key`'
      .'(`id`, `category_id`, `alphabet_index`) VALUES(?, ?)';
    Db::execute($sql, $id, $categoryId, 'a');
  }
}