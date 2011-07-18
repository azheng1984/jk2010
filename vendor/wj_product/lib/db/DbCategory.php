<?php
class DbCategory {
  public static function get($id) {
    return Db::getRow('SELECT * FROM global_category WHERE id = ?', $id);
  }

  public static function getList($parentId = null) {
    $sql = 'SELECT * FROM global_category WHERE parent_id ';
    if ($parentId === null) {
      return Db::getAll($sql.'IS NULL');
    }
    return Db::getAll($sql.'= ?', $parentId);
  }
}