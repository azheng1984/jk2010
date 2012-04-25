<?php
class DbCategory {
  public static function getOrCreateId($name, $parentId = 0) {
    $id = Db::getColumn(
      'SELECT id FROM category WHERE parent_id = ? AND name = ?',
      $parentId, $name
    );
    if ($id === false) {
      Db::insert('category', array('parent_id' => $parentId, 'name' => $name));
      return Db::getLastInsertId();
    }
    return $id;
  }
}