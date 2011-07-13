<?php
class DbTask {
  public static function get($id) {
    $sql = 'SELECT * FROM task WHERE id = ?';
    return Db::getRow($sql, array($id));
  }

  public static function getRunning() {
    $sql = 'SELECT id FROM task WHERE is_running = 1';
    return Db::getRow($sql);
  }

  public static function deleteByLargerThanId($id) {
    $sql = 'DELETE FROM task WHERE id > ?';
    Db::executeNonQuery($sql, array($id));
  }

  public static function getLastRow() {
    $sql = 'SELECT * FROM task ORDER BY id DESC LIMIT 1';
    return Db::getRow($sql);
  }

  public static function setRunning($id, $value = true) {
    $sql = 'UPDATE task SET is_running = ? WHERE id = ?';
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
    $sql = 'DELETE FROM task WHERE id = ?';
    Db::executeNonQuery($sql, array($id));
  }

  public static function isEmpty() {
    $sql = 'SELECT * FROM task LIMIT 1';
    return Db::getRow($sql) === false;
  }
}