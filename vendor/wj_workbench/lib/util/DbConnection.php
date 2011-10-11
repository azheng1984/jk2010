<?php
class DbConnection {
  private static $pool = array();
  private static $currentName = 'default';
  private static $factory;

  public static function connect($name) {
    self::$currentName = $name;
  }

  public static function reset() {
    unset(self::$pool);
    unset(self::$factory);
  }

  public static function get() {
    if (!isset(self::$pool[self::$currentName])) {
      $connection = self::getFactory()->get(self::$currentName);
      $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      self::$pool[self::$currentName] = $connection;
    }
    return self::$pool[self::$currentName];
  }

  private static function getFactory() {
    if (self::$factory === null) {
      $class = defined('DB_CONNECTION_FACTORY_CLASS') ?
        DB_CONNECTION_FACTORY_CLASS : 'DbConnectionFactory';
      self::$factory = new $class;
    }
    return self::$factory;
  }
}