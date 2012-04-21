<?php
class DbTask {
  public static function get($id) {
    $sql = 'SELECT * FROM task WHERE id = ?';
    return Db::getRow($sql, $id);
  }

  public static function getRunning() {
    $sql = 'SELECT id FROM task WHERE is_running = 1';
    return Db::getRow($sql);
  }

  public static function deleteByLargerThanId($id) {
    $sql = 'DELETE FROM task WHERE id > ?';
    Db::execute($sql, $id);
  }

  public static function getLastRow() {
    $sql = 'SELECT * FROM task ORDER BY id DESC LIMIT 1';
    return Db::getRow($sql);
  }

  public static function setRunning($id, $isRunning = 1) {
    $sql = 'UPDATE task SET is_running = ? WHERE id = ?';
    Db::execute($sql, $isRunning, $id);
  }

  public static function insert($type, $arguments) {
    $sql = 'INSERT INTO task(type, arguments) VALUES(?, ?)';
    Db::execute($sql, $type, var_export($arguments, true));
  }

  public static function reinsert($id, $type, $arguments) {
    $sql = 'INSERT INTO task(id, type, arguments, is_retry)'
      .' VALUES(?, ?, ?, 1)';
    Db::execute($sql, $id, $type, $arguments);
  }

  public static function remove($id) {
    $sql = 'DELETE FROM task WHERE id = ?';
    Db::execute($sql, $id);
  }

  public static function isEmpty() {
    $sql = 'SELECT id FROM task LIMIT 1';
    return Db::getRow($sql) === false;
  }

  public static function createTable() {
    $sql = "CREATE TABLE `task` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `type` varchar(45) DEFAULT NULL,
      `arguments` text,
      `retry_count` tinyint(4) DEFAULT '0',
      `is_running` tinyint(1) DEFAULT '0',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
  }
}