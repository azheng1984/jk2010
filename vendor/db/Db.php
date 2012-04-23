<?php
class Db {
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
    return DbConnection::get()->lastInsertId();
  }

  public static function execute($sql/*, $parameter, ...*/) {
    return self::executeByArray(func_get_args());
  }

  public static function insert($table, $row) {
    self::execute(
      'INSERT INTO '.$table.'('.implode(array_keys($row), ', ')
        .') VALUES('.str_repeat('?, ', count($row) - 1).'?)',
      array_values($row)
    );
  }

  public static function update($table, $row, $where = ''
    /*, $parameter, ...*/) {
    $parameterList = array_values($row);
    if ($where !== '') {
      $where = ' WHERE '.$where;
      $parameterList += array_slice(func_get_args(), 3);
    }
    self::execute('UPDATE '.$table.' SET '.implode(array_keys($row), ' = ?, ')
      .' = ?'.$where, $parameterList);
  }

  private static function executeByArray($parameterList) {
    $connection = DbConnection::get();
    $sql = array_shift($parameterList);
    if (isset($parameterList[0]) && is_array($parameterList[0])) {
      $parameterList = $parameterList[0];
    }
    $statement = $connection->prepare($sql);
    if ($statement === false) {
      throw new Exception;
    }
    $statement->execute($parameterList);
    return $statement;
  }
}