<?php
class DbConnection {
  private static $pool = array();
  private static $currentName;
  private static $factoryClass = 'DbConnectionFactory';
  private static $factory;

  public static function initialize($factoryClass) {
    self::$factoryClass= $factoryClass;
  }

  public static function connect($name) {
    self::$currentName = $name;
  }

  public static function reset($name = null) {
    if ($name === null) {
      unset(self::$pool[self::$currentName]);
      return;
    }
    unset(self::$pool[self::$name]);
  }

  public static function getLastInsertId() {
    return self::get()->lastInsertId();
  }

  public static function beginTransaction() {
    self::get()->beginTransaction();
  }

  public static function commit() {
    self::get()->commit();
  }

  public static function rollBack() {
    self::get()->rollBack();
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