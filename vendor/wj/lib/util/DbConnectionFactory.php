<?php
class DbConnectionFactory {
  public function get() {
    return new PDO(
      'mysql:host=localhost;dbname=wj',
      'root',
      'a841107!',
      array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
    );
  }
}