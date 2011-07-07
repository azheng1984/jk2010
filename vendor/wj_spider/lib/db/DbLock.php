<?php
class DbLock {
  public static function insert($processId) {
    $sql = "insert into `lock`(process_id) values(?)";
    Db::executeNonQuery($sql, array($processId));
  }

  public static function getOthers($processId) {
    $sql = "select * from `lock` where process_id!=?";
    return Db::getAll($sql, array($processId));
  }

  public static function deleteOthers($processId) {
    $sql = "delete from `lock` where process_id!=?";
    Db::executeNonQuery($sql, array($processId));
  }
}