<?php
class Product {
  private $data;

  public function __construct($data) {
    $this->data = $data;
  }

  public static function getList($category) {
    $table = $category->getTablePrefix().'_product';
    $sql = "select * from $table";
    $statement = Db::get($sql);
    $statement->execute();
    return $statement->fetchAll();
  }

  public static function getById($category, $id) {
    $table = $category->getTablePrefix().'_product';
    $sql = "select * from $table where id=$id";
    $statement = Db::get($sql);
    $statement->execute();
    return $statement->fetch();
  }

  public static function update($tablePrefix, $id, $name) {
    $sql = "update {$tablePrefix}_product set name=? where id=?";
    $command = Db::get($sql);
    $command->execute(array($name, $id));
  }

  public function getContent($category) {
    $table = $category->getTablePrefix().'_property_value';
    $sql = "select * from $table where id in (".$this->data['property_value_list'].")";
    $statement = Db::get($sql);
    $statement->execute();
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    $result = '';
    foreach ($rows as $row) {
      $keyTable = $category->getTablePrefix().'_property_key';
      $sql = "select * from $keyTable where id=".$row['key_id'];
      $statement = Db::get($sql);
      $statement->execute();
      $key = $statement->fetch(PDO::FETCH_ASSOC);
      if ($key['type'] === 'IMAGE') {
        $row['value'] = '<img src="http://image.wj.com/1/'.$row['value'].'" />';
      }
      $result .= '<div>'.$key['key'].':'.$row['value'].'</div>';
    }
    return $result;
  }

  public function getEditContent($category) {
    $table = $category->getTablePrefix().'_property_value';
    $sql = "select * from $table where id in (".$this->data['property_value_list'].")";
    $statement = Db::get($sql);
    $statement->execute();
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    $result = '';
    foreach ($rows as $row) {
      $keyTable = $category->getTablePrefix().'_property_key';
      $sql = "select * from $keyTable where id=".$row['key_id'];
      $statement = Db::get($sql);
      $statement->execute();
      $key = $statement->fetch(PDO::FETCH_ASSOC);
      $result .= '<div>'.$key['key'].':<input name="'.$key['key'].'" style="width:400px"  value="'.$row['value'].'" />';
      $result .= '</div>';
    }
    return $result;
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