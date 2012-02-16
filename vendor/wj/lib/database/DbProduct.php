<?php
class DbProduct {
  public static function get($id) {
    return Db::getRow(
      'SELECT * FROM product WHERE id = ?', $id
    );
  }
}