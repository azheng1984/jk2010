<?php
class DbCategory {
  public static function getOrNewId($name, $parentId = null) {
    $sql = 'select * from category where '
      .Db::getFilter('parent_id', $parentId).' and `name` = ?';
    $row = Db::getRow($sql, array($parentId, $name));
    if ($row === false) {
      Db::executeNonQuery(
        'insert into category(parent_id, `name`) values(?, ?)',
        array($parentId, $name)
      );
      return Db::getLastInsertId();
    }
    return $row['id'];
  }
}