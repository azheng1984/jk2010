<?php
class DbSearchValue {
  public static function insert($id, $categoryId) {
    $sql = 'INSERT INTO `wj_search`.`property_value`'
      .'(`id`, `category_id`, `alphabet_index`) VALUES(?, ?)';
    Db::execute($sql, $id, $categoryId, 'a');
  }
}