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
    $sql = "select id from task where is_running=1";
    $result = Db::getRow($sql);
    if ($result !== false) {
      $sql = "delete from task where id>{$result['id']}";
      Db::executeNonQuery($sql);
    }
  }

  public static function get() {
    return self::$current;
  }

  public static function moveToNext() {
    $sql = "select * from task order by id desc limit 1";
    $result = Db::getRow($sql);
    if ($result === false) {
      self::$current = null;
      return false;
    }
    $sql = "update task set is_running=1 where id={$result['id']}";
    Db::executeNonQuery($sql);
    self::$current = $result;
    return true;
  }

  public static function add($type, $arguments) {
    $sql = "insert into task(type, arguments)"
      ." values('$type', ?)";
    Db::executeNonQuery($sql, array(var_export($arguments, true)));
  }

  private static function fail($result) {
    if (self::$current['is_retry'] === 1) {
      DbTaskRetry::insert(self::$current);
    }
    DbTaskRetryHistory::insert(self::$current['id'], $result);
  }

  public static function remove() {
    $id = self::$current['id'];
    $sql = "delete from task where id={$id}";
    Db::executeNonQuery($sql);
  }

  private static function isEmpty() {
    $sql = "select * from task limit 1";
    $result = Db::getRow($sql);
    return $result['count(*)'] === '0';
  }
}