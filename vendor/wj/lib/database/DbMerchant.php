<?php
class DbMerchant {
  public static function get($id) {
    $sql = 'SELECT * FROM merchant WHERE id = ?';
    return Db::getRow($sql, $id);
  }
}