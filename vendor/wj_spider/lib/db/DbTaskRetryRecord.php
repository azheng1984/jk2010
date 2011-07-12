<?php
class DbTaskRetryRecord {
  public static function getByTaskId($taskId) {
    
  }

  public static function removeByTaskId($taskId) {
    
  }

  public static function insert($taskId, $result) {
    $sql = 'insert into retry_task_history(task_id, result, `time`)'
      .' values(?, ?, now())';
    Db::executeNonQuery($sql, array($taskId, $result));
  }
}