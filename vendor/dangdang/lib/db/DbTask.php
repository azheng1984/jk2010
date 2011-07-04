<?php
class DbTask {
  private static $current;

  public static function get() {
    return self::$current;
  }

  public static function initialize() {
    $sql = "select id from task where is_running=1";
    $result = Db::getRow($sql);
    if ($result !== false) {
      $sql = "delete task where id>{$result['id']}";
      Db::executeNonQuery($sql);
    }
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

  public static function add($type, $arguments = array()) {
    $sql = "insert into task(type, arguments)"
      ." values('$type', ?)";
    Db::executeNonQuery($sql, array(var_export($arguments, true)));
  }

  public static function remove() {
    $id = self::$current['id'];
    $sql = "delete from task where id={$id}";
    Db::executeNonQuery($sql);
  }

  public static function fail($result) {
    $sql = 'insert into task_fail(type, arguments, result, `time`)'
      .' values(?, ?, ?, now())';
    Db::executeNonQuery($sql, array(
     self::$current['type'], self::$current['arguments'], $result
    ));
  }

  public static function isEmpty() {
    $sql = "select count(*) from task";
    $result = Db::getRow($sql);
    return $result['count(*)'] === '0';
  }
}