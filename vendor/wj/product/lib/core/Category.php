<?php
class Category {
  private $data;
  private $parent;

  private function __construct($data, $parent) {
    $this->data = $data;
    $this->parent = $parent;
  }

  public function getDeleteLink() {
    return 'http://contributor.wj.com/category?id='.$this->data['id'];
  }

  public function getEditLink() {
    return 'http://contributor.wj.com/category/edit?id='.$this->data['id'];
  }

  public function getNewLink() {
    return 'http://contributor.wj.com/category/new?parent_id='.$this->data['id'];
  }

  public function getNewProductLink() {
    return 'http://contributor.wj.com/product/new?category_id='.$this->data['id'];
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
    $sql = "select * from global_category where name=? and parent_id";
      if ($parent !== null) {
      $sql .= "=".$parent->data['id'];
    } else {
      $sql .= " is null";
    }
    $statement = Db::get($sql);
    $statement->execute(array($name));
    $data = $statement->fetch();
    if ($data !== false) {
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