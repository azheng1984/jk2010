<?php
class DbLock {
  public static function insert($processId) {
    Db::execute('INSERT INTO `lock`(process_id) VALUES(?)', $processId);
  }

  public static function getOthers($processId) {
    return Db::getAll('SELECT * FROM `lock` WHERE process_id != ?', $processId);
  }

  public static function deleteOthers($processId) {
    Db::execute('DELETE FROM `lock` WHERE process_id != ?', $processId);
  }
}