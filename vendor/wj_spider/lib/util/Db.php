<?php
class Db {
  private static $connection;

  public static function executeNonQuery($sql, $parameters = array()) {
    self::execute($sql, $parameters);
  }

  public static function getRow($sql, $parameters = array()) {
    return self::execute($sql, $parameters)->fetch(PDO::FETCH_ASSOC);
  }

  public static function getAll($sql, $parameters = array()) {
    return self::execute($sql, $parameters)->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getLastInsertId() {
    return self::$connection->lastInsertId();
  }

  public static function getFilter($key, $value, &$parameters) {
    if ($value === null) {
      return "`$key` is null";
    }
    $parameters[] = $value;
    return "`$key` = ?";
  }

  private static function execute($sql, $parameters) {
    $connection = self::getConnection();
    $statement = $connection->prepare($sql);
    $statement->execute($parameters);
    return $statement;
  }

  private static function getConnection() {
    if (self::$connection === null) {
      self::$connection = new PDO(
        "mysql:host=localhost;dbname=".DB_NAME,
        "root",
        "a841107!",
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
      );
      self::$connection->setAttribute(
        PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION
      );
    }
    return self::$connection;
  }
}