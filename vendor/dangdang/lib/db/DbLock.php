<?php
class DbLock {
  public static function insert($processId) {
    $sql = "insert into `lock`(process_id) values($processId)";
    Db::executeNonQuery($sql);
  }

  public static function getOthers($processId) {
    $sql = "select * from `lock` where process_id!=$processId";
    return Db::getAll($sql);
  }

  public static function deleteOthers($processId) {
    $sql = "delete from `lock` where process_id!=$processId";
    Db::executeNonQuery($sql);
  }
}