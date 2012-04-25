<?php
class DbProduct {
  public static function get($tablePrefix, $id, $select = '*') {
    return Db::getRow(
      'SELECT '.$select.' FROM '.$tablePrefix.'_product'
        .' WHERE id = ?', $id
    );
  }

  public static function insert($tablePrefix, $row) {
    $row['index_time'] = 'NOW()';
    Db::insert($tablePrefix.'_product', $row);
    return Db::getLastInsertId();
  }

  public static function updateRow($tablePrefix, $columnList, $id) {
    Db::update($tablePrefix.'_product', $columnList, 'id = ?', $id);
  }
}