<?php
class DbTask {
  private static $current;

  public static function get() {
    return self::current;
  }

  public static function moveToNext() {
    $sql = "select * from task order by id desc limit 1";
    $result = Db::getRow($sql);
    if ($result === false) {
      self::$current = null;
      return false;
    }
    $result['arguments'] = eval('return '.$result['arguments'].';');
    self::$current = $result;
    return true;
  }

  public static function add($type, $arguments = array()) {
    $sql = "insert into task(type, arguments)"
      ." values('$type', ?)";
    Db::executeNonQuery($sql, array(var_export($arguments, true)));
  }

  public static function remove($id) {
    $sql = "delete from task where id=$id";
    Db::executeNonQuery($sql);
  }

  public static function isEmpty() {
    $sql = "select count(*) from task";
    $result = Db::getRow($sql);
    return $result['count(*)'] === '0';
  }
}