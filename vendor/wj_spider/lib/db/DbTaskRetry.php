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
/*
相对 sh7.com 的蓝色风格 和 etao 的绿色风格，大陆 buyer 更喜欢 暖色调 - 红色/桔黄 （习惯成自然：阿里巴巴，淘宝，京东，当当，新蛋，一号店） - wj 用 桔红 住要点缀色（灰色是主色调）

为 buyer 提供小工具，比如 汇率、单位换算（服装/鞋尺码）