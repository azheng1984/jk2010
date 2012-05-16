<?php
class Db {
  public static function getColumn($sql/*, $parameter, ...*/) {
    return self::call(func_get_args())->fetchColumn();
  }

  public static function getRow($sql/*, $parameter, ...*/) {
    return self::call(func_get_args())->fetch(PDO::FETCH_ASSOC);
  }

  public static function getAll($sql/*, $parameter, ...*/) {
    return self::call(func_get_args())->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getLastInsertId() {
    return DbConnection::get()->lastInsertId();
  }

  public static function execute($sql/*, $parameter, ...*/) {
    return self::call(func_get_args());
  }

  public static function insert($table, $columnList) {
    self::execute(
      'INSERT INTO '.$table.'('.implode(array_keys($columnList), ', ')
        .') VALUES('.str_repeat('?, ', count($columnList) - 1).'?)',
      array_values($columnList)
    );
  }

  public static function update($table, $columnList, $where = ''
    /*, $parameter, ...*/) {
    $parameterList = array_values($columnList);
    if ($where !== '') {
      $where = ' WHERE '.$where;
      $parameterList = array_merge(
        $parameterList, array_slice(func_get_args(), 3)
      );
    }
    self::execute(
      'UPDATE '.$table.' SET '.implode(array_keys($columnList), ' = ?, ')
        .' = ?'.$where, $parameterList
    );
  }

  public static function delete($table, $where = ''/*, $parameter, ...*/) {
    $parameterList = array();
    if ($where !== '') {
      $where = ' WHERE '.$where;
      $parameterList = array_slice(func_get_args(), 2);
    }
    self::execute('DELETE FROM '.$table.$where, $parameterList);
  }

  public static function bind(
    $table, $columnList, $filterNameList = null, &$isNew = null
  ) {
    if ($filterNameList === null) {
      $filterNameList = array_keys($columnList);
    }
    $sql = 'SELECT id FROM '.$table.' WHERE '
      .implode(' = ? AND ', $filterNameList).' = ?';
    $argumentList = array($sql);
    foreach ($filterNameList as $item) {
      $argumentList[] = $columnList[$item];
    }
    $result = self::call($argumentList)->fetchColumn();
    if ($isNew !== null) {
      $isNew = $result === false;
    }
    if ($result !== false) {
      return $result;
    }
    self::insert($table, $columnList);
    return self::getLastInsertId();
  }

  private static function call($parameterList) {
    $connection = DbConnection::get();
    $sql = array_shift($parameterList);
    //echo $sql;
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