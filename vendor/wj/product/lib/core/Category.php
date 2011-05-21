<?php
class Category {
  private $data;
  private $parent;

  private function __construct($data, $parent) {
    $this->data = $data;
    $this->parent = $parent;
  }

  public static function getList($parent = null) {
    $sql = "select * from global_category where parent_id";
    if ($parent !== null) {
      $sql .= "=".$parent->data['id'];
    } else {
      $sql .= " is null";
    }
    $statement = Db::get($sql);
    $statement->execute();
    return $statement->fetchAll();
  }

  public function isLeaf() {
    return $this->data['table_prefix'] !== null;
  }

  public static function get($name, $parent) {
    echo $name;
    $sql = "select * from global_category where name=? and parent_id";
      if ($parent !== null) {
      $sql .= "=".$parent->data['id'];
    } else {
      $sql .= " is null";
    }
    $statement = Db::get($sql);
    $statement->execute(array($name));
    $data = $statement->fetch();
    if ($data !== null) {
      return new Category($data, $parent);
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