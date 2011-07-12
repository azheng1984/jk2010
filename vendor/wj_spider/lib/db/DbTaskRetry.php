<?php
class DbTaskRetry {
  public static function insert($task) {
    $sql = 'replace into task_retry(task_id, type, arguments) values(?, ?, ?)';
    Db::executeNonQuery($sql, array(
      $task['id'], $task['type'], $task['arguments']
    ));
  }

  public static function getByTaskId($taskId) {
    $sql = 'select * from task_retry where task_id = ?';
    return Db::getRow($sql, array($taskId));
  }

  public static function delete($id) {
    $sql = 'delete from task_retry where id = ?';
    Db::executeNonQuery($sql, array($id));
  }

  public static function getAll() {
    $sql = 'select * from task_retry';
    return Db::getAll($sql);
  }
}