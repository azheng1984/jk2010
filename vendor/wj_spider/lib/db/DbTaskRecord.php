<?php
class DbTaskRecord {
  public static function getByTaskId($taskId) {
    $sql = 'SELECT * FROM task_record WHERE task_id = ?';
    return Db::getAll($sql, array($taskId));
  }

  public static function removeByTaskId($taskId) {
    $sql = 'DELETE FROM task_record WHERE task_id = ?';
    Db::executeNonQuery($sql, array($taskId));
  }

  public static function insert($taskId, $result) {
    $sql = 'INSERT INTO task_record(task_id, result, `time`)'
      .' VALUES(?, ?, NOW())';
    Db::executeNonQuery($sql, array($taskId, var_export($result, true)));
  }
}