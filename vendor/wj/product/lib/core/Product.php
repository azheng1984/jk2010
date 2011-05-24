<?php
class Product {
  private $data;

  public function __construct($data) {
    $this->data = $data;
  }

  public static function getList() {
    $table = $GLOBALS['category']->getTablePrefix().'_product';
    $sql = "select * from $table";
    $statement = Db::get($sql);
    $statement->execute();
    return $statement->fetchAll();
  }

  public function getContent() {
    return $this->data['property_value_list'];
  }

  public function getTitle() {
    return $this->data['name'];
  }

  public static function get($path) {
    $table = $GLOBALS['category']->getTablePrefix().'_product';
    $sql = "select * from $table where path=?";
    $statement = Db::get($sql);
    $statement->bindValue(1, $path);
    $statement->execute();
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    if ($row !== false) {
      return new Product($row);
    }
  }
}