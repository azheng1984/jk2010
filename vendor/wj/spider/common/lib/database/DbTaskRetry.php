<?php
class DbTaskRetry {
  public static function insert($task) {
    $sql = 'REPLACE INTO task_retry(task_id, type, arguments) VALUES(?, ?, ?)';
    Db::execute($sql, $task['id'], $task['type'], $task['arguments']);
  }

  public static function getByTaskId($taskId) {
    $sql = 'SELECT * FROM task_retry WHERE task_id = ?';
    return Db::getRow($sql, $taskId);
  }

  public static function deleteByTaskId($id) {
    $sql = 'DELETE FROM task_retry WHERE task_id = ?';
    Db::execute($sql, $id);
  }

  public static function getAll() {
    $sql = 'SELECT * FROM task_retry';
    return Db::getAll($sql);
  }

  public static function isEmpty() {
    $sql = 'SELECT * FROM task_retry LIMIT 1';
    return Db::getRow($sql) === false;
  }

  public static function createTable() {
    if (Db::getColumn("SHOW TABLES LIKE 'task_retry'") === false) {
      $sql = "CREATE TABLE `task_retry` (
        `task_id` bigint(20) unsigned NOT NULL,
        `type` varchar(45) DEFAULT NULL,
        `arguments` text,
        PRIMARY KEY (`task_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
      Db::execute($sql);
    }
  }
}