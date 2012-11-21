<?php
class DbConnection {
  private static $name = 'default';
  private static $pool = array();
  private static $factory;
  private static $list;

  public static function connect($name, $pdo = null) {
    self::$list[] = self::$name;
    self::$name = $name;
    if ($name !== null && $pdo !== null) {
      self::$pool[$name] = $pdo;
    }
  }

  public static function close() {
    if (count(self::$list) > 0) {
      self::$name = array_pop(self::$list);
      return;
    }
    self::$name = 'default';
  }

  public static function reset() {
    unset(self::$factory);
    unset(self::$pool);
    unset(self::$list);
  }

  public static function get($name = null) {
    if ($name === null) {
      $name = self::$name;
    }
    if (isset(self::$pool[$name]) === false) {
      $connection = self::getFactory()->get($name);
      $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      self::$pool[$name] = $connection;
    }
    return self::$pool[$name];
  }

  private static function getFactory() {
    if (self::$factory === null) {
      self::$factory = new DbConnectionFactory;
    }
    return self::$factory;
  }
}