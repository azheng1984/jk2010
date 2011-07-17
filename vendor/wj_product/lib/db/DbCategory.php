<?php
class DbCategory {
  public static function get($id) {
    return Db::getRow('SELECT * FROM global_category WHERE id = ?', $id);
  }
}