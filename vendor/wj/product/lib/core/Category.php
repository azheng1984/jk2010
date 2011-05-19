<?php
class Category {
  public static function getList($parentId = null) {
    $sql = 'select * from global_category where parent_id';
    if ($parentId === null) {
      $sql .= ' is null';
    } else {
      $sql .= '=' . $parentId;
    }
    return Db::execute($sql);
  }

  public static function get($uniqueName, $parentId = null) {
    $sql = "select * from global_category where name='$uniqueName'";
    if ($parentId !== null) {
      $sql .= " and parent_id=".$parentId;
    } else {
      $sql .= ' and parent_id is null';
    }
    $data = Db::execute($sql);
    if ($data !== false) {
      return $data->fetch();
    }
  }

  public static function save($name, $parentId) {
    $parent = 'null';
    if ($parentId !== null) {
      $parent = "'$parentId'";
    }
    $sql = "insert into global_category(name, parent_id) values('$name', $parent)";
    Db::execute($sql);
    $_GET['category'][] = self::get($name);
  }
}