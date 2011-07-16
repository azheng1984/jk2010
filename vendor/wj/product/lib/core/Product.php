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

  public static function getById($id) {
    $table = $GLOBALS['category']->getTablePrefix().'_product';
    $sql = "select * from $table where id=$id";
    $statement = Db::get($sql);
    $statement->execute();
    return $statement->fetch();
  }

  public function getContent() {
    $table = $GLOBALS['category']->getTablePrefix().'_property_value';
    $sql = "select * from $table where id in (".$this->data['property_value_list'].")";
    $statement = Db::get($sql);
    $statement->execute();
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    $result = '';
    foreach ($rows as $row) {
      $keyTable = $GLOBALS['category']->getTablePrefix().'_property_key';
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
}//http://www.wj.com/%E6%95%B0%E7%A0%81/%E7%94%B5%E5%AD%90/?search=%E6%88%91%E4%BB%AC&%E5%93%81%E7%89%8C=%E8%A5%BF%E9%97%A8%E5%AD%90+%E5%85%8B%E9%87%8C%E6%96%AF%E5%AE%9A