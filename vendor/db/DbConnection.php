<?php
class DbConnection {
  private static $current = null;
  private static $pool = array();
  private static $factory;
  private static $list;

  public static function connect(
    $name = 'default', $pdo = null, $isPersistent = true
  ) {
    if (self::$current !== null) {
      self::$list[] = self::$current;
    }
    if ($pdo === null) {
      $pdo = self::get($name, $isPersistent);
    }
    self::$current = $pdo;
    if ($isPersistent && $name !== null) {
      self::$pool[$name] = $pdo;
    }
  }

  public static function close() {
    if (count(self::$list) > 0) {
      self::$current = array_pop(self::$list);
      return;
    }
    self::$current = null;
  }

  public static function getCurrent() {
    return self::$current;
  }

  public static function reset() {
    unset(self::$current);
    unset(self::$factory);
    unset(self::$pool);
    unset(self::$list);
  }

  private static function get($name, $isPersistent) {
    $pdo = null;
    if (isset(self::$pool[$name]) === false) {
      $connection = self::getFactory()->get($name);
      $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo = $connection;
    }
    if ($isPersistent) {
      self::$pool[$name] = $pdo;
    }
    return $pdo;
  }

  private static function getFactory() {
    if (self::$factory === null) {
      self::$factory = new DbConnectionFactory;
    }
    return self::$factory;
  }
}