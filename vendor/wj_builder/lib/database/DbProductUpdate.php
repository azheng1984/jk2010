<?php
class DbProductUpdate {
  public static function get($tablePrefix) {
    return Db::getRow('SELECT * FROM '.$tablePrefix.'_product_update LIMIT 1');
  }

  public static function delete($tablePrefix, $id) {
    Db::execute(
      'DELETE FROM '.$tablePrefix.'_product_update WHERE id = ?', $id
    );
  }
}