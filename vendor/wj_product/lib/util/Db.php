<?php
class Db {
  private static $connections = array();
  private static $name;

  public static function execute($sql/*, $parameter, ...*/) {
    return self::executeByArray(func_get_args());
  }

  public static function getColumn($sql/*, $parameter, ...*/) {
    return self::executeByArray(func_get_args())->fetchColumn();
  }

  public static function getRow($sql/*, $parameter, ...*/) {
    return self::executeByArray(func_get_args())->fetch(PDO::FETCH_ASSOC);
  }

  public static function getAll($sql/*, $parameter, ...*/) {
    return self::executeByArray(func_get_args())->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getLastInsertId() {
    return self::getConnection()->lastInsertId();
  }

  public static function beginTransaction() {
    self::getConnection()->beginTransaction();
  }

  public static function commit() {
    self::getConnection()->commit();
  }

  public static function rollBack() {
    self::getConnection()->rollBack();
  }

  public static function getFilter($key, $value, &$parameters) {
    if ($value === null) {
      return "`$key` IS NULL";
    }
    $parameters[] = $value;
    return "`$key` = ?";
  }

  public static function connect($name) {
    $this->name = $name;
  }

  private static function executeByArray($parameters) {
    $sql = array_shift($parameters);
    $connection = self::getConnection();
    $statement = $connection->prepare($sql);
    $statement->execute($parameters);
    return $statement;
  }

  private static function getConnection() {
    if (!isset(self::$connections[self::$name])) {
      $class = 'Db'.self::$name.'Connection';
      $connection = new $class;
      self::$connections[self::$name] = $connection->get();
    }
    return self::$connections[self::$name];
  }
}