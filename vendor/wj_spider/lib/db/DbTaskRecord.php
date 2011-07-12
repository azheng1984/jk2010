<?php
class DbTaskRecord {
  public static function getByTaskId($taskId) {
    $sql = 'select * from task_record where task_id = ?';
    return Db::getAll($sql, array($taskId));
  }

  public static function removeByTaskId($taskId) {
    $sql = 'delete from task_record where task_id = ?';
    Db::executeNonQuery($sql, array($taskId));
  }

  public static function insert($taskId, $result) {
    $sql = 'insert into task_record(task_id, result, `time`)'
      .' values(?, ?, now())';
    Db::executeNonQuery($sql, array($taskId, var_export($result, true)));
  }
}