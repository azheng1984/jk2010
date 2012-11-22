<?php
class DbConnection {
  private static $current = null;
  private static $pool = array();
  private static $stack;
  private static $factory;

  public static function connect(
    $name = 'default', $pdo = null, $isPersistent = true
  ) {
    if (self::$current !== null) {
      self::$stack[] = self::$current;
    }
    if ($pdo !== null) {
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    if ($pdo === null) {
      $pdo = self::get($name, $isPersistent);
    }
    self::$current = $pdo;
  }

  public static function close() {
    if (count(self::$stack) > 0) {
      self::$current = array_pop(self::$stack);
      return;
    }
    self::$current = null;
  }

  public static function getCurrent() {
    if (self::$current === null) {
      self::connect();
    }
    return self::$current;
  }

  public static function reset() {
    unset(self::$current);
    unset(self::$factory);
    unset(self::$pool);
    unset(self::$stack);
  }

  private static function get($name, $isPersistent) {
    if (isset(self::$pool[$name])) {
      return self::$pool[$name];
    }
    $pdo = self::getFactory()->get($name);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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