<?php
class DbTask {
  private static $current;

  public static function initialize($tasks) {
    if (self::isEmpty() === true) {
      foreach ($tasks as $type => $arguments) {
        self::add($type, $arguments);
      }
      return;
    }
    $sql = "select id from task where is_running = 1";
    $result = Db::getRow($sql);
    if ($result !== false) {
      $sql = "delete from task where id > ?";
      Db::executeNonQuery($sql, array($result['id']));
    }
  }

  public static function getLastRow() {
    $sql = "select * from task order by id desc limit 1";
    return Db::getRow($sql);
  }

  public static function setRunning($id) {
    $sql = "update task set is_running = 1 where id = ?";
    Db::executeNonQuery($sql, array($id));
  }

  public static function add($type, $arguments) {
    $sql = "insert into task(type, arguments) values(?, ?)";
    Db::executeNonQuery($sql, array($type, var_export($arguments, true)));
  }

  private static function fail($result) {
    if (self::$current['is_retry'] === 1) {
      DbTaskRetry::insert(self::$current);
    }
    DbTaskRetryHistory::insert(self::$current['id'], $result);
  }

  public static function remove($task) {
    $sql = "delete from task where id = ?";
    Db::executeNonQuery($sql, array($task['id']));
  }

  private static function isEmpty() {
    $sql = "select * from task limit 1";
    $result = Db::getRow($sql);
    return $result['count(*)'] === '0';
  }
}