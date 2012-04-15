<?php
class DbMerchant {
  public static function get($id) {
    return Db::getRow('SELECT * FROM merchant WHERE id = ?', $id);
  }
}