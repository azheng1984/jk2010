<?php
class DbTaskRetry {
  public static function insert($task) {
    $sql = 'REPLACE INTO task_retry(task_id, type, arguments) VALUES(?, ?, ?)';
    Db::executeNonQuery($sql, array(
      $task['id'], $task['type'], $task['arguments']
    ));
  }

  public static function getByTaskId($taskId) {
    $sql = 'SELECT * FROM task_retry WHERE task_id = ?';
    return Db::getRow($sql, array($taskId));
  }

  public static function deleteByTaskId($id) {
    $sql = 'DELETE FROM task_retry WHERE task_id = ?';
    Db::executeNonQuery($sql, array($id));
  }

  public static function getAll() {
    $sql = 'SELECT * FROM task_retry';
    return Db::getAll($sql);
  }

  public static function isEmpty() {
    $sql = 'SELECT * FROM task_retry LIMIT 1';
    return Db::getRow($sql) === false;
  }
}