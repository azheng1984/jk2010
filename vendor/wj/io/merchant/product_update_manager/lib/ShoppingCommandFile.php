<?php
class ShoppingCommandFile {
  private static $sqlList = array();

  public static function insertCategory($id, $name) {
    self::$sqlList[] = 'INSERT INTO category(id,name) VALUES('
      .$id.','.DbConnection::get('shopping')->quote($name).')';
  }
}