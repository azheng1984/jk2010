<?php
class DbConnection {
  private static $name = 'default';
  private static $pool = array();
  private static $factory;

  public static function connect($name) {
    self::$name = $name;
  }

  public static function reset() {
    unset(self::$pool);
    unset(self::$factory);
  }

  public static function get() {
    if (!isset(self::$pool[self::$name])) {
      $connection = self::getFactory()->get(self::$name);
      $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      self::$pool[self::$name] = $connection;
    }
    return self::$pool[self::$name];
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