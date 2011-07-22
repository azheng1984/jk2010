<?php
class DbCategory {
  public static function getOrNewId($name, $parentId = null) {
    $parameters = array();
    $sql = 'SELECT id FROM category WHERE '
      .Db::getFilter('parent_id', $parentId, $parameters).' AND `name` = ?';
    $parameters[] = $name;
    $id = Db::getColumn($sql, $parameters);
    if ($id === false) {
      $sql = 'INSERT INTO category(parent_id, `name`) VALUES(?, ?)';
      Db::executeNonQuery($sql, array($parentId, $name));
      return Db::getLastInsertId();
    }
    return $id;
  }
}