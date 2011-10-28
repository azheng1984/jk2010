<?php
class DbTaskRecord {
  public static function getByTaskId($taskId) {
    $sql = 'SELECT * FROM task_record WHERE task_id = ?';
    return Db::getAll($sql, array($taskId));
  }

  public static function deleteByTaskId($taskId) {
    $sql = 'DELETE FROM task_record WHERE task_id = ?';
    Db::executeNonQuery($sql, array($taskId));
  }

  public static function insert($taskId, $result) {
    $sql = 'INSERT INTO task_record(task_id, result, `time`)'
      .' VALUES(?, ?, now())';
    Db::executeNonQuery($sql, array($taskId, var_export($result, true)));
  }
}