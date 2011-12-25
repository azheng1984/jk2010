<?php
class DbSearchValue {
  public static function insert($id, $keyId) {
    $sql = 'INSERT INTO `wj_search`.`property_value`'
      .'(`id`, `key_id`, `alphabet_index`) VALUES(?, ?, ?)';
    Db::execute($sql, $id, $keyId, 'a');
  }
}