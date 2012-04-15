<?php
class DbCategory {
  public static function getOrNewId($name, $parentId = 0) {
    $id = Db::getColumn(
      'SELECT id FROM category WHERE parent_id = ? AND `name` = ?',
      $parentId, $name
    );
    if ($id === false) {
      Db::execute(
        'INSERT INTO category(parent_id, `name`) VALUES(?, ?)', $parentId, $name
      );
      return DbConnection::get()->lastInsertId();
    }
    return $id;
  }
}