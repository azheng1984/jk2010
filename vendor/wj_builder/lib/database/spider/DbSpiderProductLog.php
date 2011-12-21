<?php
class DbSpiderProductLog {
  public static function get($tablePrefix) {
    return Db::getRow(
      'SELECT * FROM jingdong.'.$tablePrefix.'_product_log LIMIT 1'
    );
  }

  public static function delete($tablePrefix, $id) {
    Db::execute(
      'DELETE FROM jingdong.'.$tablePrefix.'_product_log WHERE id = ?', $id
    );
  }
}