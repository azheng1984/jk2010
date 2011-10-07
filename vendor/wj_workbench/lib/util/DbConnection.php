<?php
class DbConnection {
  private static $pool = array();
  private static $currentName = 'default';
  private static $factoryClass = 'DbConnectionFactory';
  private static $factory;

  public static function initialize($factoryClass) {
    self::$factoryClass = $factoryClass;
  }

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
      self::$factory = new self::$factoryClass;
    }
    return self::$factory;
  }
}