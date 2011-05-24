<?php
class Property {
  public static function get($path) {
    $sql = "select * from laptop_property_key where key='$path'";
    $statement = Db::get($sql);
    $statement->bindValue(1, $path);
    $statement->execute();
    $result = $statement->fetch();
    if ($result !== false) {
      return $result;
    }
  }
}