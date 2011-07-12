<?php
class DbTask {
  public static function get($id) {
    $sql = 'select * from task where id = ?';
    return Db::getRow($sql, array($id));
  }

  public static function getRunning() {
    $sql = 'select id from task where is_running = 1';
    return Db::getRow($sql);
  }

  public static function deleteByLargerThanId($id) {
    $sql = 'delete from task where id > ?';
    Db::executeNonQuery($sql, array($id));
  }

  public static function getLastRow() {
    $sql = 'select * from task order by id desc limit 1';
    return Db::getRow($sql);
  }

  public static function setRunning($id, $value = true) {
    $sql = 'update task set is_running = ? where id = ?';
    Db::executeNonQuery($sql, array($value, $id));
  }

  public static function insert($type, $arguments) {
    $sql = 'insert into task(type, arguments) values(?, ?)';
    Db::executeNonQuery($sql, array($type, var_export($arguments, true)));
  }

  public static function reinsert($id, $type, $arguments) {
    $sql = 'insert into task(id, type, arguments, is_retry)'
      .' values(?, ?, ?, true)';
    Db::executeNonQuery($sql, array($id, $type, $arguments));
  }

  public static function remove($id) {
    $sql = 'delete from task where id = ?';
    Db::executeNonQuery($sql, array($id));
  }

  public static function isEmpty() {
    $sql = 'select * from task limit 1';
    return Db::getRow($sql) === false;
  }
}