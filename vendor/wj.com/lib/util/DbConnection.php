<?php
class DbConnection {
  public function get() {
    $connection = new PDO(
      'mysql:host=localhost;dbname=wj',
      'root',
      'a841107!',
      array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
    );
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $connection;
  }
}