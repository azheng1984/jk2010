<?php
class DbId {
  public static function get(
    $table, $columnList, $uniqueColumnNameList = null
  ) {
    if ($uniqueColumnNameList === null) {
      $uniqueColumnNameList = array_keys($columnList);
    }
    $where = implode(' = ? AND ', $uniqueColumnNameList).' = ?';
    $sql = 'SELECT id FROM '.$table.' WHERE '.$where;
    $argumentList = array($sql);
    foreach ($uniqueColumnNameList as $item) {
      $argumentList[$item] = $columnList[$item];
    }
    $result = call_user_func_array('Db::getColumn', $argumentList);
    if ($result !== false) {
    	return $result;
    }
    Db::insert($table, $columnList);
    return Db::getLastInsertId();
  }
}