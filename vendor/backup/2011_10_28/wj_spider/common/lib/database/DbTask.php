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

  public static function setRunning($id, $isRunning = 1) {
    $sql = 'UPDATE task SET is_running = ? WHERE id = ?';
    Db::executeNonQuery($sql, array($isRunning, $id));
  }

  public static function insert($type, $arguments) {
    $sql = 'INSERT INTO task(type, arguments) VALUES(?, ?)';
    Db::executeNonQuery($sql, array($type, var_export($arguments, true)));
  }

  public static function reinsert($id, $type, $arguments) {
    $sql = 'INSERT INTO task(id, type, arguments, is_retry)'
      .' VALUES(?, ?, ?, 1)';
    Db::executeNonQuery($sql, array($id, $type, $arguments));
  }

  public static function remove($id) {
    $sql = 'DELETE FROM task WHERE id = ?';
    Db::executeNonQuery($sql, array($id));
  }

  public static function isEmpty() {
    $sql = 'SELECT id FROM task LIMIT 1';
    return Db::getRow($sql) === false;
  }
}