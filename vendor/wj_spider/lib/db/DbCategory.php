<?php
class DbCategory {
  public static function getOrNewId($name, $parentId = null) {
    $parameters = array();
    $sql = 'select id from category where '
      .Db::getFilter('parent_id', $parentId, $parameters).' and `name` = ?';
    $parameters[] = $name;
    $row = Db::getRow($sql, $parameters);
    if ($row === false) {
      $sql = 'insert into category(parent_id, `name`) values(?, ?)';
      Db::executeNonQuery($sql, array($parentId, $name));
      return Db::getLastInsertId();
    }
    return $row['id'];
  }
}