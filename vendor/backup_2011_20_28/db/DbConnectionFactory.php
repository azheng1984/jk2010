<?php
class DbConnectionFactory {
  private static $config;

  public function get($name) {
    $config = $this->getConfig($name);
    if (isset($config['dsn']) && isset($config['username'])
      && isset($config['password'])) {
      return new PDO(
        $config['dsn'],
        $config['username'],
        $config['password'],
        array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
      );
    }
    throw new Exception;
  }

  private function getConfig($name) {
    if (self::$config === null) {
      self::$config = require CONFIG_PATH.'database.config.php';
    }
    if ($name === 'default' && isset(self::$config['dsn'])
      && is_string(self::$config['dsn'])) {
      return self::$config;
    }
    if (isset(self::$config[$name])) {
      return self::$config[$name];
    }
    throw new Exception;
  }
}