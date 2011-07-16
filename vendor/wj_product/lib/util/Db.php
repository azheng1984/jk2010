<?php
class Db {
  private static $connections = array();
  private static $connectionName;

  public static function execute($sql/*, $parameter, ...*/) {
    $parameters = $this->getParameters(func_get_args());
    return self::executeByArray($sql, $parameters);
  }

  public static function getColumn($sql/*, $parameter, ...*/) {
    $parameters = $this->getParameters(func_get_args());
    return self::executeByArray($sql, $parameters)->fetchColumn();
  }

  public static function getRow($sql/*, $parameter, ...*/) {
    $parameters = $this->getParameters(func_get_args());
    return self::executeByArray($sql, $parameters)->fetch(PDO::FETCH_ASSOC);
  }

  public static function getAll($sql/*, $parameter, ...*/) {
    $parameters = $this->getParameters(func_get_args());
    return self::executeByArray($sql, $parameters)->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getLastInsertId() {
    return self::getConnection()->lastInsertId();
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

  private static function getParameters($arguments) {
    if (count($arguments) === 1) {
      return array();
    }
    array_shift($arguments);
    return $arguments;
  }

  private static function executeByArray($sql, $parameters) {
    $connection = self::getConnection();
    $statement = $connection->prepare($sql);
    $statement->execute($parameters);
    return $statement;
  }

  private static function getConnection() {
    if (!isset(self::$connections[$this->connectionName])) {
      $class = 'Db'.$this->connectionName.'Connection';
      self::$connections[$this->connectionName] = $class::getConnection();
    }
    return self::$connections[$this->connectionName];
  }
}