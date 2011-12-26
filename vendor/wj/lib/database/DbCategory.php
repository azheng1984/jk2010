<?php
class DbCategory {
  public static function get($id) {
    return Db::getRow('SELECT * FROM wj_web.category WHERE id = ?', $id);
  }

  public static function getList() {
    return Db::getAll('SELECT * FROM wj_web.category');
  }

  public static function getByName($name) {
    return Db::getRow(
      'SELECT * FROM wj_web.category WHERE `name` = ?', $name
    );
  }
}