<?php
class DbConnectionFactory {
  public function get() {
    $config = require CONFIG_PATH.'database.config.php';
    return new PDO(
      $config['dsn'],
      $config['username'],
      $config['password'],
      array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
    );
  }
}