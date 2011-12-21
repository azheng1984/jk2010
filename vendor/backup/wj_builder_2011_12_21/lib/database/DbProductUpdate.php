<?php
class DbProductUpdate {
  public static function get($tablePrefix) {
    return Db::getRow(
      'SELECT * FROM jingdong.'.$tablePrefix.'_product_update LIMIT 1'
    );
  }

  public static function delete($tablePrefix, $id) {
    Db::execute(
      'DELETE FROM jingdong.'.$tablePrefix.'_product_update WHERE id = ?', $id
    );
  }
}