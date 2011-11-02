<?php
class DbProductUpdate {
  public static function get($tablePrefix) {
    Db::execute('use jingdong');
    return Db::getRow('SELECT * FROM '.$tablePrefix.'_product_update LIMIT 1');
  }

  public static function delete($tablePrefix, $id) {
    Db::execute('use jingdong');
    Db::execute(
      'DELETE FROM '.$tablePrefix.'_product_update WHERE id = ?', $id
    );
  }
}