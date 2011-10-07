<?php
class Db {
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

  private static function executeByArray($parameters) {
    $sql = array_shift($parameters);
    $connection = DbConnection::get();
    $statement = $connection->prepare($sql);
    $statement->execute($parameters);
    return $statement;
  }
}