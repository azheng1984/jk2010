<?php
class DbProduct {
  public static function getAll($source) {
    Db::execute('USE '.$source);
    $sql = 'SELECT * FROM product WHERE status != "NOT_MODIFIED"';
    return Db::getAll($sql);
  }
}