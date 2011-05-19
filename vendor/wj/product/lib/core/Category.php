<?php
class Category {
  private $data;

  public function __construct($data) {
    $this->data = $data;
  }

  public static function getList($parentId = null) {
    $sql = 'select * from global_category where parent_id';
    if ($parentId === null) {
      $sql .= ' is null';
    } else {
      $sql .= '=' . $parentId;
    }
    return Db::execute($sql);
  }

  public static function get($name, $parent) {
    $sql = "select * from global_category where name='$name'";
    if ($parent !== null) {
      $sql .= " and parent_id=".$parent->data['id'];
    } else {
      $sql .= ' and parent_id is null';
    }
    $data = Db::execute($sql);
    if ($data !== false) {
      return new Category($data->fetch());
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