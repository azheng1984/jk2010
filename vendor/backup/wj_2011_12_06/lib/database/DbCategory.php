<?php
class DbCategory {
  public static function get($id) {
    return Db::getRow('SELECT * FROM wj_web.category WHERE id = ?', $id);
  }

  public static function getList($parentId = 0) {
    return Db::getAll(
      'SELECT * FROM global_category WHERE parent_id = ?', $parentId
    );
  }

  public static function getByName($name) {
    return Db::getRow(
      'SELECT * FROM wj_web.category WHERE `name` = ?', $name
    );
  }
}