<?php
class DbLock {
  public static function insert($processId) {
    Db::execute('INSERT INTO `lock`(process_id) VALUES(?)', $processId);
  }

  public static function getAll() {
    return Db::getAll('SELECT * FROM `lock`');
  }

  public static function deleteOthers($processId) {
    Db::execute('DELETE FROM `lock` WHERE process_id != ?', $processId);
  }
}