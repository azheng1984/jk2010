<?php
class DbTaskRetry {
  public static function insert($task, $result) {
    if ($task['is_retry']) {
      $sql = 'insert into retry_task(task_id, type, arguments, result, `time`)'
        .' values(?, ?, ?, now())';
      Db::executeNonQuery($sql, array(
        $task['id'], $task['type'], $task['arguments'], $task['result']
      ));
    }
  }

  public static function get($id) {
    
  }

  public static function getAll() {
    
  }

  public static function retry($id = null) {
    $sql = 'select * from retry_task';
    foreach (Db::getAll($sql) as $task) {
      $sql = "insert into task(type, arguments) values(?, ?)";
      Db::executeNonQuery($sql, array($task['type'], $task['arguments']));
    }
  }

  private static function retryAll() {
    $sql = 'select * from retry_task';
    foreach (Db::getAll($sql) as $task) {
      $sql = "insert into task(type, arguments) values(?, ?)";
      Db::executeNonQuery($sql, array($task['type'], $task['arguments']));
    }
  }
}