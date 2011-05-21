<?php
class Db {
  public static function get($sql) {
    $db = new PDO(
      "mysql:host=localhost;dbname=wj",
      'root',
      'a841107!',
      array (PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
    );
    return $db->prepare($sql);
  }
}