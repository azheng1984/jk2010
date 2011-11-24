<?php
class DbLock {
  public static function insert($processId) {
    $sql = 'INSERT INTO `lock`(process_id) VALUES(?)';
    Db::executeNonQuery($sql, array($processId));
  }

  public static function getOthers($processId) {
    $sql = 'SELECT * FROM `lock` WHERE process_id != ?';
    return Db::getAll($sql, array($processId));
  }

  public static function deleteOthers($processId) {
    $sql = 'DELETE FROM `lock` WHERE process_id != ?';
    Db::executeNonQuery($sql, array($processId));
  }
}